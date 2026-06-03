// SQLite persistence (built-in node:sqlite — no native build step).
import { DatabaseSync } from "node:sqlite";
import { fileURLToPath } from "node:url";
import { dirname, join } from "node:path";
import { mkdirSync } from "node:fs";
import { seedProducts } from "./data/products.js";
import { hashPassword } from "./auth.js";

const __dirname = dirname(fileURLToPath(import.meta.url));
const dataDir = join(__dirname, "data");
mkdirSync(dataDir, { recursive: true });

const db = new DatabaseSync(join(dataDir, "ozonecare.db"));
db.exec("PRAGMA journal_mode = WAL;");

db.exec(`
  CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    slug TEXT UNIQUE NOT NULL,
    title TEXT NOT NULL,
    model_number TEXT,
    category TEXT,
    price TEXT,
    price_value INTEGER,
    featured INTEGER DEFAULT 0,
    status TEXT DEFAULT 'active',
    sort_order INTEGER DEFAULT 100,
    has_concentration_table INTEGER DEFAULT 0,
    warranty TEXT,
    tagline TEXT,
    short_description TEXT,
    full_description TEXT,
    highlights TEXT,
    features TEXT,
    specifications TEXT,
    accessories TEXT,
    items TEXT,
    images TEXT,
    brochure_pdf TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    updated_at TEXT NOT NULL DEFAULT (datetime('now'))
  );

  CREATE TABLE IF NOT EXISTS enquiries (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    phone TEXT NOT NULL,
    email TEXT,
    interested_product TEXT,
    enquiry_type TEXT DEFAULT 'Product Information',
    message TEXT,
    status TEXT DEFAULT 'New',
    admin_note TEXT,
    source TEXT DEFAULT 'website',
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    updated_at TEXT NOT NULL DEFAULT (datetime('now'))
  );

  CREATE TABLE IF NOT EXISTS admins (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    name TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now'))
  );

  CREATE TABLE IF NOT EXISTS downloads (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_slug TEXT,
    brochure TEXT,
    ip TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now'))
  );

  CREATE TABLE IF NOT EXISTS settings (
    key TEXT PRIMARY KEY,
    value TEXT
  );
`);

const J = (v) => JSON.stringify(v ?? null);
const P = (v, fallback) => {
  try { return v == null ? fallback : JSON.parse(v); } catch { return fallback; }
};

// Map a DB row to the public product shape the frontend expects.
function toProduct(row) {
  if (!row) return null;
  const images = P(row.images, []);
  return {
    id: row.slug,
    dbId: row.id,
    slug: row.slug,
    name: row.title,
    title: row.title,
    model: row.model_number,
    model_number: row.model_number,
    category: row.category,
    price: row.price,
    priceValue: row.price_value,
    featured: !!row.featured,
    status: row.status,
    sortOrder: row.sort_order,
    warranty: row.warranty,
    tagline: row.tagline,
    shortDescription: row.short_description,
    longDescription: row.full_description,
    highlights: P(row.highlights, []),
    features: P(row.features, []),
    specs: P(row.specifications, {}),
    includedAccessories: P(row.accessories, []),
    items: P(row.items, []),
    images,
    image: images[0] || "/assets/img/products/device-card.svg",
    thumb: images[0] || "/assets/img/products/device-card.svg",
    gallery: images,
    brochure: row.brochure_pdf || "",
    hasConcentrationTable: !!row.has_concentration_table,
    createdAt: row.created_at,
    updatedAt: row.updated_at,
  };
}

export const Products = {
  _toProduct: toProduct,
  all({ includeInactive = false } = {}) {
    const rows = db.prepare(`SELECT * FROM products ORDER BY sort_order ASC, id ASC`).all();
    return rows.map(toProduct).filter((p) => includeInactive || p.status === "active");
  },
  allRaw() {
    return db.prepare(`SELECT * FROM products ORDER BY sort_order ASC, id ASC`).all().map(toProduct);
  },
  bySlug(slug) {
    return toProduct(db.prepare(`SELECT * FROM products WHERE slug = ?`).get(slug));
  },
  byId(id) {
    return toProduct(db.prepare(`SELECT * FROM products WHERE id = ?`).get(id));
  },
  count() {
    return db.prepare(`SELECT COUNT(*) AS n FROM products`).get().n;
  },
  create(p) {
    const stmt = db.prepare(`
      INSERT INTO products
        (slug,title,model_number,category,price,price_value,featured,status,sort_order,
         has_concentration_table,warranty,tagline,short_description,full_description,
         highlights,features,specifications,accessories,items,images,brochure_pdf)
      VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    `);
    const info = stmt.run(
      p.slug, p.title, p.model_number ?? "", p.category ?? "", p.price ?? "On Request",
      p.price_value ?? null, p.featured ? 1 : 0, p.status ?? "active", p.sort_order ?? 100,
      p.has_concentration_table ? 1 : 0, p.warranty ?? "", p.tagline ?? "",
      p.short_description ?? "", p.full_description ?? "",
      J(p.highlights ?? []), J(p.features ?? []), J(p.specifications ?? {}),
      J(p.accessories ?? []), J(p.items ?? []), J(p.images ?? []), p.brochure_pdf ?? ""
    );
    return this.byId(info.lastInsertRowid);
  },
  update(id, p) {
    const cur = db.prepare(`SELECT * FROM products WHERE id = ?`).get(id);
    if (!cur) return null;
    const stmt = db.prepare(`
      UPDATE products SET
        slug=?,title=?,model_number=?,category=?,price=?,price_value=?,featured=?,status=?,
        sort_order=?,has_concentration_table=?,warranty=?,tagline=?,short_description=?,
        full_description=?,highlights=?,features=?,specifications=?,accessories=?,items=?,
        images=?,brochure_pdf=?,updated_at=datetime('now')
      WHERE id=?
    `);
    stmt.run(
      p.slug ?? cur.slug, p.title ?? cur.title, p.model_number ?? cur.model_number,
      p.category ?? cur.category, p.price ?? cur.price,
      p.price_value === undefined ? cur.price_value : p.price_value,
      p.featured === undefined ? cur.featured : (p.featured ? 1 : 0),
      p.status ?? cur.status, p.sort_order ?? cur.sort_order,
      p.has_concentration_table === undefined ? cur.has_concentration_table : (p.has_concentration_table ? 1 : 0),
      p.warranty ?? cur.warranty, p.tagline ?? cur.tagline,
      p.short_description ?? cur.short_description, p.full_description ?? cur.full_description,
      p.highlights !== undefined ? J(p.highlights) : cur.highlights,
      p.features !== undefined ? J(p.features) : cur.features,
      p.specifications !== undefined ? J(p.specifications) : cur.specifications,
      p.accessories !== undefined ? J(p.accessories) : cur.accessories,
      p.items !== undefined ? J(p.items) : cur.items,
      p.images !== undefined ? J(p.images) : cur.images,
      p.brochure_pdf ?? cur.brochure_pdf, id
    );
    return this.byId(id);
  },
  remove(id) {
    return db.prepare(`DELETE FROM products WHERE id = ?`).run(id).changes > 0;
  },
};

export const Enquiries = {
  create(e) {
    const stmt = db.prepare(`
      INSERT INTO enquiries (name,phone,email,interested_product,enquiry_type,message,source)
      VALUES (?,?,?,?,?,?,?)
    `);
    const info = stmt.run(
      e.name, e.phone, e.email ?? null, e.interested_product ?? null,
      e.enquiry_type ?? "Product Information", e.message ?? null, e.source ?? "website"
    );
    return this.get(info.lastInsertRowid);
  },
  get(id) { return db.prepare(`SELECT * FROM enquiries WHERE id = ?`).get(id); },
  all() { return db.prepare(`SELECT * FROM enquiries ORDER BY created_at DESC, id DESC`).all(); },
  count() { return db.prepare(`SELECT COUNT(*) AS n FROM enquiries`).get().n; },
  countByStatus(status) { return db.prepare(`SELECT COUNT(*) AS n FROM enquiries WHERE status = ?`).get(status).n; },
  setStatus(id, status) {
    db.prepare(`UPDATE enquiries SET status=?, updated_at=datetime('now') WHERE id=?`).run(status, id);
    return this.get(id);
  },
  setNote(id, note) {
    db.prepare(`UPDATE enquiries SET admin_note=?, updated_at=datetime('now') WHERE id=?`).run(note, id);
    return this.get(id);
  },
  update(id, { status, admin_note }) {
    const cur = this.get(id);
    if (!cur) return null;
    db.prepare(`UPDATE enquiries SET status=?, admin_note=?, updated_at=datetime('now') WHERE id=?`)
      .run(status ?? cur.status, admin_note ?? cur.admin_note, id);
    return this.get(id);
  },
  remove(id) { return db.prepare(`DELETE FROM enquiries WHERE id = ?`).run(id).changes > 0; },
};

export const Admins = {
  byEmail(email) { return db.prepare(`SELECT * FROM admins WHERE email = ?`).get(String(email).toLowerCase()); },
  byId(id) { return db.prepare(`SELECT * FROM admins WHERE id = ?`).get(id); },
  count() { return db.prepare(`SELECT COUNT(*) AS n FROM admins`).get().n; },
  create({ email, password_hash, name }) {
    const info = db.prepare(`INSERT INTO admins (email,password_hash,name) VALUES (?,?,?)`)
      .run(String(email).toLowerCase(), password_hash, name ?? "Admin");
    return this.byId(info.lastInsertRowid);
  },
};

export const Downloads = {
  log({ product_slug, brochure, ip }) {
    db.prepare(`INSERT INTO downloads (product_slug,brochure,ip) VALUES (?,?,?)`)
      .run(product_slug ?? null, brochure ?? null, ip ?? null);
  },
  count() { return db.prepare(`SELECT COUNT(*) AS n FROM downloads`).get().n; },
  recent(limit = 50) { return db.prepare(`SELECT * FROM downloads ORDER BY created_at DESC LIMIT ?`).all(limit); },
};

export const Settings = {
  get(key, fallback = null) {
    const row = db.prepare(`SELECT value FROM settings WHERE key = ?`).get(key);
    return row ? P(row.value, row.value) : fallback;
  },
  set(key, value) {
    db.prepare(`INSERT INTO settings (key,value) VALUES (?,?)
      ON CONFLICT(key) DO UPDATE SET value=excluded.value`).run(key, J(value));
    return value;
  },
  all() {
    const rows = db.prepare(`SELECT key,value FROM settings`).all();
    const out = {};
    for (const r of rows) out[r.key] = P(r.value, r.value);
    return out;
  },
};

// --- one-time seeding ---
export function seedIfEmpty() {
  if (Products.count() === 0) {
    for (const p of seedProducts) Products.create(p);
    console.log(`   Seeded ${seedProducts.length} products.`);
  }
  if (Admins.count() === 0) {
    const email = (process.env.ADMIN_EMAIL || "medicalozonecare@gmail.com").toLowerCase();
    const password = process.env.ADMIN_PASSWORD || "admin12345";
    Admins.create({ email, password_hash: hashPassword(password), name: "Medical Ozone Care Admin" });
    console.log(`   Seeded admin: ${email}  (password: ${process.env.ADMIN_PASSWORD ? "from ADMIN_PASSWORD env" : "admin12345 — change it!"})`);
  }
}

export default db;
