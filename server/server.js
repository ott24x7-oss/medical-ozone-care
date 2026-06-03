// Medical Ozone Care — Express server (API + admin + static frontend).
import express from "express";
import { fileURLToPath } from "node:url";
import { dirname, join, extname } from "node:path";
import { mkdirSync, writeFileSync, existsSync } from "node:fs";
import { Products, Enquiries, Admins, Downloads, Settings, seedIfEmpty } from "./db.js";
import { verifyPassword, signToken, verifyToken } from "./auth.js";
import { sendEnquiryNotification, mailerEnabled } from "./mailer.js";
import { concentrationTable, company, categories, enquiryTypes } from "./data/products.js";
import { defaultSettings, MEDICAL_DISCLAIMER, legalSlugs } from "./data/defaults.js";

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = join(__dirname, "..");
const PUBLIC_DIR = join(ROOT, "public");
const UPLOAD_DIR = join(PUBLIC_DIR, "assets", "img", "products", "uploads");
const DOCS_DIR = join(PUBLIC_DIR, "assets", "docs");
mkdirSync(UPLOAD_DIR, { recursive: true });
mkdirSync(DOCS_DIR, { recursive: true });

const PORT = process.env.PORT || 3000;
seedIfEmpty();

const app = express();
app.set("trust proxy", 1);
app.disable("x-powered-by");
app.use(express.json({ limit: "200kb" }));
app.use(express.urlencoded({ extended: true }));

app.use((req, _res, next) => {
  if (req.path.startsWith("/api")) console.log(`${new Date().toISOString()}  ${req.method} ${req.path}`);
  next();
});

// ---------- helpers ----------
const isEmail = (s) => typeof s === "string" && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(s);
const isPhone = (s) => typeof s === "string" && s.replace(/[^\d]/g, "").length >= 7;
const clean = (s, max = 2000) => (typeof s === "string" ? s.trim().slice(0, max) : "");
const slugify = (s) => clean(s, 80).toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/^-+|-+$/g, "");

const hits = new Map();
function rateLimit(max = 8, windowMs = 60_000) {
  return (req, res, next) => {
    const ip = req.ip || "unknown";
    const now = Date.now();
    const rec = hits.get(ip) || { count: 0, reset: now + windowMs };
    if (now > rec.reset) { rec.count = 0; rec.reset = now + windowMs; }
    rec.count++;
    hits.set(ip, rec);
    if (rec.count > max) return res.status(429).json({ ok: false, error: "Too many requests. Please try again shortly." });
    next();
  };
}

function auth(req, res, next) {
  const header = req.get("authorization") || "";
  const token = header.startsWith("Bearer ") ? header.slice(7) : req.get("x-admin-token");
  const payload = verifyToken(token);
  if (!payload) return res.status(401).json({ ok: false, error: "Unauthorised. Please sign in." });
  req.admin = payload;
  next();
}

function getMergedSettings() {
  return {
    contact: { ...defaultSettings.contact, ...(Settings.get("contact") || {}) },
    seo: { ...defaultSettings.seo, ...(Settings.get("seo") || {}) },
    legal: { ...defaultSettings.legal, ...(Settings.get("legal") || {}) },
    disclaimer: MEDICAL_DISCLAIMER,
  };
}

// ============================ PUBLIC API ============================
app.get("/api/health", (_req, res) =>
  res.json({ ok: true, service: "medical-ozone-care", email: mailerEnabled ? "configured" : "console-fallback", time: new Date().toISOString() }));

app.get("/api/company", (_req, res) => res.json({ ok: true, company, disclaimer: MEDICAL_DISCLAIMER }));
app.get("/api/categories", (_req, res) => res.json({ ok: true, categories }));
app.get("/api/enquiry-types", (_req, res) => res.json({ ok: true, enquiryTypes }));
app.get("/api/concentration-table", (_req, res) => res.json({ ok: true, concentrationTable }));
app.get("/api/settings/public", (_req, res) => res.json({ ok: true, settings: getMergedSettings() }));

app.get("/api/products", (req, res) => {
  let list = Products.all();
  const { category, q, featured } = req.query;
  if (category) list = list.filter((p) => p.category.toLowerCase() === String(category).toLowerCase());
  if (featured === "true") list = list.filter((p) => p.featured);
  if (q) {
    const needle = String(q).toLowerCase();
    list = list.filter((p) =>
      [p.name, p.model, p.category, p.shortDescription, p.tagline].join(" ").toLowerCase().includes(needle));
  }
  res.json({ ok: true, count: list.length, products: list });
});

app.get("/api/products/:slug", (req, res) => {
  const product = Products.bySlug(req.params.slug);
  if (!product || product.status !== "active") return res.status(404).json({ ok: false, error: "Product not found" });
  res.json({ ok: true, product });
});

// Brochure download (logged for admin stats), redirects to the PDF.
app.get("/api/brochure/:slug", (req, res) => {
  const product = Products.bySlug(req.params.slug);
  if (!product || !product.brochure) return res.status(404).send("Brochure not available for this product.");
  Downloads.log({ product_slug: product.slug, brochure: product.brochure, ip: req.ip });
  res.redirect(product.brochure);
});

// Submit enquiry
app.post("/api/enquiries", rateLimit(8, 60_000), async (req, res) => {
  const name = clean(req.body.name, 120);
  const phone = clean(req.body.phone, 40);
  const email = clean(req.body.email, 160);
  const interested_product = clean(req.body.interested_product || req.body.product, 160);
  let enquiry_type = clean(req.body.enquiry_type, 60) || "Product Information";
  const message = clean(req.body.message, 4000);
  const source = clean(req.body.source, 40) || "website";
  const honeypot = clean(req.body.company_website, 100);

  if (honeypot) return res.json({ ok: true, id: 0 }); // bot
  if (!name) return res.status(400).json({ ok: false, error: "Please enter your name." });
  if (!isPhone(phone)) return res.status(400).json({ ok: false, error: "Please enter a valid phone number." });
  if (email && !isEmail(email)) return res.status(400).json({ ok: false, error: "Please enter a valid email address." });
  if (!enquiryTypes.includes(enquiry_type)) enquiry_type = "Product Information";

  const row = Enquiries.create({ name, phone, email, interested_product, enquiry_type, message, source });
  console.log(`📩 Enquiry #${row.id}: ${name} (${phone}) · ${enquiry_type}${interested_product ? " · " + interested_product : ""}`);
  sendEnquiryNotification(row).catch((e) => console.error("notify error", e.message));
  res.status(201).json({ ok: true, id: row.id, message: "Thank you! Your enquiry has been received. Our team will contact you shortly." });
});

// ============================ ADMIN API ============================
app.post("/api/admin/login", rateLimit(10, 60_000), (req, res) => {
  const email = clean(req.body.email, 160).toLowerCase();
  const password = clean(req.body.password, 200);
  const admin = Admins.byEmail(email);
  if (!admin || !verifyPassword(password, admin.password_hash))
    return res.status(401).json({ ok: false, error: "Invalid email or password." });
  const token = signToken({ id: admin.id, email: admin.email, name: admin.name });
  res.json({ ok: true, token, admin: { id: admin.id, email: admin.email, name: admin.name } });
});

app.get("/api/admin/me", auth, (req, res) => res.json({ ok: true, admin: req.admin }));

app.get("/api/admin/stats", auth, (_req, res) => {
  res.json({
    ok: true,
    stats: {
      enquiries: Enquiries.count(),
      new: Enquiries.countByStatus("New"),
      contacted: Enquiries.countByStatus("Contacted"),
      quoted: Enquiries.countByStatus("Quoted"),
      closed: Enquiries.countByStatus("Closed"),
      products: Products.count(),
      downloads: Downloads.count(),
    },
  });
});

// Products CRUD
app.get("/api/admin/products", auth, (_req, res) => res.json({ ok: true, products: Products.allRaw() }));

function normalizeProductBody(b) {
  return {
    slug: clean(b.slug, 80) ? slugify(b.slug) : slugify(b.title || ""),
    title: clean(b.title, 160),
    model_number: clean(b.model_number, 80),
    category: clean(b.category, 80),
    price: clean(b.price, 40) || "On Request",
    price_value: b.price_value === "" || b.price_value == null ? null : Number(b.price_value) || null,
    featured: !!b.featured,
    status: b.status === "inactive" ? "inactive" : "active",
    sort_order: Number(b.sort_order) || 100,
    has_concentration_table: !!b.has_concentration_table,
    warranty: clean(b.warranty, 60),
    tagline: clean(b.tagline, 240),
    short_description: clean(b.short_description, 600),
    full_description: clean(b.full_description, 6000),
    highlights: Array.isArray(b.highlights) ? b.highlights.map((s) => clean(s, 200)).filter(Boolean) : [],
    features: Array.isArray(b.features) ? b.features : [],
    specifications: b.specifications && typeof b.specifications === "object" ? b.specifications : {},
    accessories: Array.isArray(b.accessories) ? b.accessories : [],
    items: Array.isArray(b.items) ? b.items : [],
    images: Array.isArray(b.images) ? b.images.map((s) => clean(s, 400)).filter(Boolean) : [],
    brochure_pdf: clean(b.brochure_pdf, 400),
  };
}

app.post("/api/admin/products", auth, (req, res) => {
  const p = normalizeProductBody(req.body);
  if (!p.title) return res.status(400).json({ ok: false, error: "Title is required." });
  if (!p.slug) return res.status(400).json({ ok: false, error: "Could not derive a slug from the title." });
  if (Products.bySlug(p.slug)) return res.status(409).json({ ok: false, error: "A product with this slug already exists." });
  res.status(201).json({ ok: true, product: Products.create(p) });
});

app.put("/api/admin/products/:id", auth, (req, res) => {
  const id = Number(req.params.id);
  const existing = Products.byId(id);
  if (!existing) return res.status(404).json({ ok: false, error: "Product not found." });
  const p = normalizeProductBody(req.body);
  const clash = Products.bySlug(p.slug);
  if (clash && clash.dbId !== id) return res.status(409).json({ ok: false, error: "Another product already uses this slug." });
  res.json({ ok: true, product: Products.update(id, p) });
});

app.delete("/api/admin/products/:id", auth, (req, res) => {
  const ok = Products.remove(Number(req.params.id));
  if (!ok) return res.status(404).json({ ok: false, error: "Product not found." });
  res.json({ ok: true });
});

// File upload (base64) — images + brochure PDFs
app.post("/api/admin/upload", auth, express.json({ limit: "25mb" }), (req, res) => {
  const { filename, dataUrl, kind } = req.body || {};
  if (!dataUrl || typeof dataUrl !== "string") return res.status(400).json({ ok: false, error: "No file data." });
  const m = dataUrl.match(/^data:([^;]+);base64,(.+)$/s);
  if (!m) return res.status(400).json({ ok: false, error: "Invalid file data URL." });
  const mime = m[1];
  const buf = Buffer.from(m[2], "base64");
  if (buf.length > 20 * 1024 * 1024) return res.status(413).json({ ok: false, error: "File too large (max 20 MB)." });

  const imageTypes = { "image/png": ".png", "image/jpeg": ".jpg", "image/webp": ".webp", "image/svg+xml": ".svg", "image/gif": ".gif" };
  const isPdf = mime === "application/pdf";
  if (!imageTypes[mime] && !isPdf) return res.status(415).json({ ok: false, error: "Unsupported file type." });

  const safeBase = slugify((filename || "file").replace(/\.[^.]+$/, "")) || "file";
  const stamp = Date.now().toString(36);
  if (isPdf) {
    const name = `${safeBase}-${stamp}.pdf`;
    writeFileSync(join(DOCS_DIR, name), buf);
    return res.json({ ok: true, path: `/assets/docs/${name}`, kind: "brochure" });
  }
  const ext = imageTypes[mime];
  const name = `${safeBase}-${stamp}${ext}`;
  writeFileSync(join(UPLOAD_DIR, name), buf);
  res.json({ ok: true, path: `/assets/img/products/uploads/${name}`, kind: "image" });
});

// Enquiries management
app.get("/api/admin/enquiries", auth, (_req, res) => res.json({ ok: true, count: Enquiries.count(), enquiries: Enquiries.all() }));
app.patch("/api/admin/enquiries/:id", auth, (req, res) => {
  const allowed = ["New", "Contacted", "Quoted", "Closed"];
  const status = allowed.includes(req.body.status) ? req.body.status : undefined;
  const admin_note = req.body.admin_note !== undefined ? clean(req.body.admin_note, 2000) : undefined;
  const row = Enquiries.update(Number(req.params.id), { status, admin_note });
  if (!row) return res.status(404).json({ ok: false, error: "Enquiry not found." });
  res.json({ ok: true, enquiry: row });
});
app.delete("/api/admin/enquiries/:id", auth, (req, res) => {
  const ok = Enquiries.remove(Number(req.params.id));
  if (!ok) return res.status(404).json({ ok: false, error: "Enquiry not found." });
  res.json({ ok: true });
});

// Downloads + settings
app.get("/api/admin/downloads", auth, (_req, res) => res.json({ ok: true, count: Downloads.count(), downloads: Downloads.recent(100) }));
app.get("/api/admin/settings", auth, (_req, res) => res.json({ ok: true, settings: getMergedSettings() }));
app.put("/api/admin/settings", auth, express.json({ limit: "2mb" }), (req, res) => {
  const { contact, seo, legal } = req.body || {};
  if (contact && typeof contact === "object") Settings.set("contact", { ...defaultSettings.contact, ...contact });
  if (seo && typeof seo === "object") Settings.set("seo", { ...defaultSettings.seo, ...seo });
  if (legal && typeof legal === "object") Settings.set("legal", { ...defaultSettings.legal, ...legal });
  res.json({ ok: true, settings: getMergedSettings() });
});

// ============================ SEO ============================
app.get("/robots.txt", (_req, res) => {
  const base = (Settings.get("seo")?.baseUrl) || defaultSettings.seo.baseUrl;
  res.type("text/plain").send(`User-agent: *\nAllow: /\nDisallow: /admin\n\nSitemap: ${base}/sitemap.xml\n`);
});

app.get("/sitemap.xml", (_req, res) => {
  const base = ((Settings.get("seo")?.baseUrl) || defaultSettings.seo.baseUrl).replace(/\/$/, "");
  const staticUrls = ["/", "/products", "/instructions", "/about", "/contact", "/faq", "/terms", "/privacy", "/disclaimer", "/warranty", "/usage"];
  const productUrls = Products.all().map((p) => `/product?id=${p.slug}`);
  const urls = [...staticUrls, ...productUrls];
  const body =
    `<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n` +
    urls.map((u) => `  <url><loc>${base}${u}</loc></url>`).join("\n") +
    `\n</urlset>\n`;
  res.type("application/xml").send(body);
});

// ============================ STATIC + PAGES ============================
app.use(express.static(PUBLIC_DIR, { extensions: ["html"] }));

const PAGES = ["index", "products", "product", "instructions", "about", "contact", "faq", "admin"];
app.get("/", (_req, res) => res.sendFile(join(PUBLIC_DIR, "index.html")));
for (const page of PAGES) app.get("/" + page, (_req, res) => res.sendFile(join(PUBLIC_DIR, page + ".html")));
for (const slug of Object.keys(legalSlugs)) app.get("/" + slug, (_req, res) => res.sendFile(join(PUBLIC_DIR, "legal.html")));

app.use("/api", (_req, res) => res.status(404).json({ ok: false, error: "Unknown API endpoint" }));
app.use((_req, res) => res.status(404).sendFile(join(PUBLIC_DIR, "404.html")));

app.listen(PORT, () => {
  console.log("\n🫧  Medical Ozone Care server running");
  console.log(`    Local:   http://localhost:${PORT}`);
  console.log(`    Admin:   http://localhost:${PORT}/admin`);
  console.log(`    Email:   ${mailerEnabled ? "SMTP configured" : "console fallback (set SMTP_* env to enable)"}`);
});
