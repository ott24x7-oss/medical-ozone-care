/* =========================================================
   Medical Ozone Care — shared front-end script
   Header/footer injection, nav, quote modal, icons, reveals
   ========================================================= */
const MOC = {
  name: "Medical Ozone Care",
  person: "Shekhar Pathak",
  phone: "+91 99588 03980",
  phoneRaw: "919958803980",
  emails: ["shekharaiims@gmail.com", "medicalozonecare@gmail.com"],
  address: "B-87, Madhu Vihar, Uttam Nagar, New Delhi – 110059, India",
  website: "www.medicalozonecare.co.in",
};
MOC.waMsg = "Hello Medical Ozone Care,\nI am interested in your medical ozone equipment.\nName:\nLocation:\nRequirement:\nPlease share quotation and details.";
MOC.waFor = (product) =>
  `https://wa.me/${MOC.phoneRaw}?text=` +
  encodeURIComponent(
    `Hello Medical Ozone Care,\nI am interested in ${product || "your medical ozone equipment"}.\nName:\nLocation:\nRequirement:\nPlease share quotation and details.`
  );
MOC.waLink = MOC.waFor("");
MOC.telLink = `tel:+${MOC.phoneRaw}`;
MOC.mailLink = `mailto:${MOC.emails[0]}`;

/* ---------- Icons ---------- */
const I = {
  shield: '<path d="M12 2l8 3v6c0 5-3.5 8.5-8 11-4.5-2.5-8-6-8-11V5l8-3z"/>',
  sliders: '<path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/>',
  atom: '<circle cx="12" cy="12" r="1.6"/><ellipse cx="12" cy="12" rx="10" ry="4.5"/><ellipse cx="12" cy="12" rx="10" ry="4.5" transform="rotate(60 12 12)"/><ellipse cx="12" cy="12" rx="10" ry="4.5" transform="rotate(120 12 12)"/>',
  globe: '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2c3 3 3 17 0 20M12 2c-3 3-3 17 0 20"/>',
  plug: '<path d="M9 2v6M15 2v6M6 8h12v3a6 6 0 0 1-12 0V8zM12 17v5"/>',
  badge: '<circle cx="12" cy="8" r="6"/><path d="M8.5 13.5L7 22l5-3 5 3-1.5-8.5"/>',
  gauge: '<path d="M12 14l4-4M4.5 19a9 9 0 1 1 15 0"/><circle cx="12" cy="14" r="1"/>',
  check: '<path d="M20 6L9 17l-5-5"/>',
  checkc: '<circle cx="12" cy="12" r="10"/><path d="M8 12l3 3 5-5"/>',
  phone: '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3-8.6A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/>',
  mail: '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 7l-10 6L2 7"/>',
  pin: '<path d="M21 10c0 6-9 12-9 12s-9-6-9-12a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
  wa: '<path d="M17.5 14.4c-.3-.2-1.7-.8-2-.9-.3-.1-.5-.2-.7.2-.2.3-.7.9-.9 1.1-.2.2-.3.2-.6.1-1.6-.8-2.7-1.5-3.7-3.3-.3-.5.3-.5.8-1.5.1-.2 0-.4 0-.5 0-.2-.7-1.6-.9-2.2-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.2.2 2.1 3.3 5.2 4.6 1.9.8 2.6.9 3.6.7.6-.1 1.7-.7 1.9-1.4.2-.7.2-1.2.2-1.4-.1-.1-.3-.2-.6-.4z"/><path d="M12 2a10 10 0 0 0-8.5 15.2L2 22l4.9-1.4A10 10 0 1 0 12 2z" fill="none"/>',
  menu: '<path d="M3 12h18M3 6h18M3 18h18"/>',
  arrow: '<path d="M5 12h14M13 6l6 6-6 6"/>',
  clock: '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
  award: '<circle cx="12" cy="8" r="6"/><path d="M15.5 13.5L17 22l-5-3-5 3 1.5-8.5"/>',
  droplet: '<path d="M12 2.7l5.7 5.7a8 8 0 1 1-11.4 0z"/>',
  wind: '<path d="M3 8h10a2.5 2.5 0 1 0-2.5-2.5M3 16h14a2.5 2.5 0 1 1-2.5 2.5M3 12h18"/>',
  settings: '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.6 1.6 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.6 1.6 0 0 0-2.7 1.1V21a2 2 0 1 1-4 0v-.1A1.6 1.6 0 0 0 7 19.4a1.6 1.6 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.6 1.6 0 0 0-1.1-2.7H1a2 2 0 1 1 0-4h.1A1.6 1.6 0 0 0 2.6 7a1.6 1.6 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.6 1.6 0 0 0 1.8.3H7a1.6 1.6 0 0 0 1-1.5V1a2 2 0 1 1 4 0v.1a1.6 1.6 0 0 0 2.7 1.1 1.6 1.6 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.6 1.6 0 0 0-.3 1.8V7a1.6 1.6 0 0 0 1.5 1H23a2 2 0 1 1 0 4h-.1a1.6 1.6 0 0 0-1.5 1z"/>',
  star: '<path d="M12 2l3 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.9 21l1.2-6.8-5-4.9 6.9-1z"/>',
  send: '<path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>',
  x: '<path d="M18 6L6 18M6 6l12 12"/>',
  leaf: '<path d="M11 20A7 7 0 0 1 4 13c0-6 7-11 16-11 0 9-5 16-11 16zM4 21c2-6 6-9 9-10"/>',
  heart: '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 1 0-7.8 7.8l1 1 7.8 7.8 7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z"/>',
  users: '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
  truck: '<rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7zM5.5 18.5a2 2 0 1 0 0 .01M18.5 18.5a2 2 0 1 0 0 .01"/>',
  headset: '<path d="M4 14v-2a8 8 0 0 1 16 0v2"/><path d="M4 14a2 2 0 0 0 2 2h1v-5H6a2 2 0 0 0-2 2zM20 14a2 2 0 0 1-2 2h-1v-5h1a2 2 0 0 1 2 2zM18 16v1a3 3 0 0 1-3 3h-3"/>',
  flask: '<path d="M9 2h6M10 2v6L5 19a1.5 1.5 0 0 0 1.4 2h11.2A1.5 1.5 0 0 0 19 19l-5-11V2"/><path d="M7.5 14h9"/>',
  microscope: '<path d="M6 18h12M8 22h8M9 14a5 5 0 1 0 6-8M7 16l3-3M9 3l3 3-3 3-3-3z"/>',
  doc: '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h8M8 9h2"/>',
  zap: '<path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>',
  fb: '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
  ig: '<rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/>',
  yt: '<path d="M22 8.2a3 3 0 0 0-2.1-2.1C18 5.5 12 5.5 12 5.5s-6 0-7.9.6A3 3 0 0 0 2 8.2 31 31 0 0 0 1.6 12 31 31 0 0 0 2 15.8a3 3 0 0 0 2.1 2.1c1.9.6 7.9.6 7.9.6s6 0 7.9-.6a3 3 0 0 0 2.1-2.1A31 31 0 0 0 22.4 12 31 31 0 0 0 22 8.2z"/><path d="M10 15l5-3-5-3z" fill="#0b3a52"/>',
  li: '<rect x="2" y="2" width="20" height="20" rx="3"/><path d="M7 10v7M7 7v.01M11 17v-4a2 2 0 0 1 4 0v4M11 17v-7" stroke="#0b3a52"/>',
};
function icon(name, cls = "") {
  const p = I[name] || "";
  return `<svg class="${cls}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${p}</svg>`;
}
function iconFill(name, cls = "") {
  const p = I[name] || "";
  return `<svg class="${cls}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">${p}</svg>`;
}

/* ---------- Header ---------- */
function buildHeader() {
  const el = document.getElementById("site-header");
  if (!el) return;
  const path = location.pathname.replace(/\/$/, "") || "/";
  const links = [
    ["/", "Home"],
    ["/products", "Products"],
    ["/instructions", "Instructions"],
    ["/faq", "FAQ"],
    ["/about", "About"],
    ["/contact", "Contact"],
  ];
  const linkHtml = links
    .map(([href, label]) => {
      const active = href === path || (href !== "/" && path.startsWith(href)) ? "active" : "";
      return `<li><a href="${href}" class="${active}">${label}</a></li>`;
    })
    .join("");
  el.innerHTML = `
  <nav class="nav ${el.dataset.overDark === "true" ? "over-dark" : "solid"}" id="nav">
    <div class="container nav-inner">
      <a class="brand" href="/"><img class="logo-dark" src="/assets/img/logo.svg" alt="${MOC.name}" width="180" height="47"><img class="logo-light" src="/assets/img/logo-light.svg" alt="${MOC.name}" width="180" height="47"></a>
      <ul class="nav-links" id="navLinks">${linkHtml}
        <li class="only-mobile"><a href="${MOC.telLink}">${MOC.phone}</a></li>
      </ul>
      <div class="nav-cta">
        <a href="/contact" class="btn btn-primary" data-quote>Request a Quote</a>
        <button class="nav-toggle" id="navToggle" aria-label="Menu"><span></span><span></span><span></span></button>
      </div>
    </div>
  </nav>`;

  const nav = document.getElementById("nav");
  const onScroll = () => nav.classList.toggle("scrolled", window.scrollY > 30);
  onScroll();
  window.addEventListener("scroll", onScroll, { passive: true });

  const toggle = document.getElementById("navToggle");
  const navLinks = document.getElementById("navLinks");
  toggle.addEventListener("click", () => navLinks.classList.toggle("open"));
  navLinks.addEventListener("click", (e) => { if (e.target.tagName === "A") navLinks.classList.remove("open"); });
}

/* ---------- Footer ---------- */
function buildFooter() {
  const el = document.getElementById("site-footer");
  if (!el) return;
  el.innerHTML = `
  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <div>
          <div class="brand"><img src="/assets/img/logo-light.svg" alt="${MOC.name}" width="200" height="52"></div>
          <p class="muted">Precision medical ozone therapy equipment — generators, oxygen regulators and genuine accessories. German technology, CE-certified, backed by expert support.</p>
          <div class="social">
            <a href="${MOC.waLink}" aria-label="WhatsApp" target="_blank" rel="noopener">${iconFill("wa")}</a>
            <a href="${MOC.mailLink}" aria-label="Email">${icon("mail")}</a>
            <a href="${MOC.telLink}" aria-label="Call">${icon("phone")}</a>
          </div>
        </div>
        <div>
          <h4>Products</h4>
          <ul>
            <li><a href="/product?id=aot-md-520">Ozone Generator</a></li>
            <li><a href="/products?category=Oxygen%20Regulators">Oxygen Regulators</a></li>
            <li><a href="/product?id=accessory-kit">Accessory Kit</a></li>
            <li><a href="/products">All Products</a></li>
          </ul>
        </div>
        <div>
          <h4>Company</h4>
          <ul>
            <li><a href="/about">About Us</a></li>
            <li><a href="/faq">FAQ</a></li>
            <li><a href="/instructions">Instructions</a></li>
            <li><a href="/terms">Terms &amp; Conditions</a></li>
            <li><a href="/privacy">Privacy Policy</a></li>
            <li><a href="/warranty">Warranty Policy</a></li>
            <li><a href="/disclaimer">Disclaimer</a></li>
          </ul>
        </div>
        <div>
          <h4>Get in touch</h4>
          <ul class="foot-contact">
            <li>${icon("pin")}<span>${MOC.address}</span></li>
            <li>${icon("phone")}<a href="${MOC.telLink}">${MOC.phone}</a></li>
            <li>${icon("mail")}<a href="mailto:${MOC.emails[0]}">${MOC.emails[0]}</a></li>
            <li>${icon("clock")}<span>Mon–Sat, 10:00–19:00 IST</span></li>
          </ul>
        </div>
      </div>
      <p class="muted" style="font-size:.82rem;margin-top:18px;border-top:1px solid rgba(255,255,255,.08);padding-top:16px">
        <strong style="color:#cbd9e2">Disclaimer:</strong> This website provides medical equipment information only. Use of medical ozone equipment should be under qualified medical supervision and applicable local regulations.
      </p>
      <div class="footer-bottom">
        <span>© <span id="yr"></span> ${MOC.name}. All rights reserved.</span>
        <span>${MOC.website} · Contact: ${MOC.person} · <a href="/usage">Usage Disclaimer</a></span>
      </div>
    </div>
  </footer>`;
  const yr = document.getElementById("yr");
  if (yr) yr.textContent = new Date().getFullYear();
}

/* ---------- Floating WhatsApp ---------- */
function buildFab() {
  if (document.querySelector(".fab")) return;
  const a = document.createElement("a");
  a.className = "fab";
  a.href = MOC.waLink;
  a.target = "_blank";
  a.rel = "noopener";
  a.setAttribute("aria-label", "Chat on WhatsApp");
  a.innerHTML = iconFill("wa");
  document.body.appendChild(a);
}

/* ---------- Quote modal ---------- */
function buildModal() {
  if (document.getElementById("quoteModal")) return;
  const wrap = document.createElement("div");
  wrap.id = "quoteModal";
  wrap.style.cssText = "position:fixed;inset:0;z-index:200;display:none;align-items:center;justify-content:center;padding:20px;";
  wrap.innerHTML = `
    <div class="qm-backdrop" style="position:absolute;inset:0;background:rgba(8,42,60,.55);backdrop-filter:blur(3px)"></div>
    <div class="form-card" role="dialog" aria-modal="true" aria-label="Request a quote"
         style="position:relative;max-width:520px;width:100%;max-height:92vh;overflow:auto;animation:fade .3s">
      <button id="qmClose" aria-label="Close" style="position:absolute;top:14px;right:14px;background:#f1f6f8;border:0;border-radius:10px;width:38px;height:38px;cursor:pointer;color:var(--navy)">${icon("x")}</button>
      <span class="eyebrow">${icon("send")} Request a Quote</span>
      <h3 style="margin:6px 0 4px">Tell us what you need</h3>
      <p class="form-note" style="margin-bottom:18px">We'll get back within one business day with pricing and availability.</p>
      <div class="form-msg" id="qmMsg"></div>
      <form id="qmForm" novalidate>
        <div class="field hp"><label>Company website</label><input type="text" name="company_website" tabindex="-1" autocomplete="off"></div>
        <div class="form-row">
          <div class="field"><label>Name *</label><input name="name" required placeholder="Your full name"></div>
          <div class="field"><label>Phone *</label><input name="phone" required placeholder="Mobile number" inputmode="tel"></div>
        </div>
        <div class="field"><label>Email</label><input name="email" type="email" placeholder="you@example.com"></div>
        <div class="form-row">
          <div class="field"><label>Enquiry type</label>
            <select name="enquiry_type">
              <option>Quote Request</option>
              <option>Product Information</option>
              <option>Distributor Enquiry</option>
              <option>Service/Support</option>
              <option>Accessories</option>
            </select>
          </div>
          <div class="field"><label>Product of interest</label>
            <select name="interested_product" id="qmProduct">
              <option value="">Select a product…</option>
              <option>Medical Ozone Generator AOT-MD-520</option>
              <option>Medical Ozone Generator — Digital (Touch Screen)</option>
              <option>Ozone Water &amp; Oil System</option>
              <option>Oxygen Regulator</option>
              <option>Ozone Therapy Accessory Kit</option>
              <option>Complete Set</option>
              <option>Other / Not sure</option>
            </select>
          </div>
        </div>
        <div class="field"><label>Message</label><textarea name="message" placeholder="Quantity, application, city, any questions…"></textarea></div>
        <input type="hidden" name="source" value="quote-modal">
        <button class="btn btn-primary btn-block btn-lg" type="submit" id="qmSubmit">${icon("send")} Send Enquiry</button>
        <p class="form-note text-center" style="margin:12px 0 0">Or WhatsApp us directly at <a href="${MOC.waLink}" target="_blank" rel="noopener">${MOC.phone}</a></p>
      </form>
    </div>`;
  document.body.appendChild(wrap);

  const close = () => { wrap.style.display = "none"; document.body.style.overflow = ""; };
  wrap.querySelector(".qm-backdrop").addEventListener("click", close);
  document.getElementById("qmClose").addEventListener("click", close);
  document.addEventListener("keydown", (e) => { if (e.key === "Escape") close(); });

  document.getElementById("qmForm").addEventListener("submit", async (e) => {
    e.preventDefault();
    const form = e.target;
    const msg = document.getElementById("qmMsg");
    const btn = document.getElementById("qmSubmit");
    const data = Object.fromEntries(new FormData(form).entries());
    msg.className = "form-msg";
    if (!data.name || !data.phone) {
      msg.className = "form-msg err"; msg.textContent = "Please enter your name and phone number."; return;
    }
    btn.disabled = true; btn.style.opacity = .7; btn.innerHTML = "Sending…";
    try {
      const res = await fetch("/api/enquiries", {
        method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(data),
      });
      const json = await res.json();
      if (json.ok) {
        msg.className = "form-msg ok"; msg.textContent = json.message || "Thank you! We'll be in touch shortly.";
        form.reset();
        setTimeout(close, 2600);
      } else {
        msg.className = "form-msg err"; msg.textContent = json.error || "Something went wrong. Please try again.";
      }
    } catch {
      msg.className = "form-msg err"; msg.textContent = "Network error. Please WhatsApp or call us instead.";
    } finally {
      btn.disabled = false; btn.style.opacity = 1; btn.innerHTML = `${icon("send")} Send Enquiry`;
    }
  });
}
function openQuote(product) {
  buildModal();
  const wrap = document.getElementById("quoteModal");
  if (product) {
    const sel = document.getElementById("qmProduct");
    const p = product.toLowerCase();
    const opt = [...sel.options].find((o) => {
      const v = o.value.toLowerCase();
      return v && (v.includes(p) || p.includes(v) || (v.split(" ")[0].length > 3 && p.includes(v.split(" ")[0])));
    });
    if (opt) sel.value = opt.value;
    else { const o = new Option(product, product, true, true); sel.add(o); }
  }
  wrap.style.display = "flex"; document.body.style.overflow = "hidden";
  setTimeout(() => wrap.querySelector('input[name="name"]').focus(), 50);
}
document.addEventListener("click", (e) => {
  const t = e.target.closest("[data-quote]");
  if (t) { e.preventDefault(); openQuote(t.dataset.quote || ""); }
});

/* ---------- Inline icon injection ([data-icon="name"]) ---------- */
function injectIcons(root = document) {
  root.querySelectorAll("[data-icon]").forEach((el) => {
    const name = el.dataset.icon;
    const fill = el.hasAttribute("data-fill");
    el.innerHTML = fill ? iconFill(name) : icon(name);
    el.removeAttribute("data-icon");
  });
}

/* ---------- Reveal on scroll ---------- */
function initReveals() {
  const els = document.querySelectorAll(".reveal");
  if (!("IntersectionObserver" in window)) { els.forEach((e) => e.classList.add("in")); return; }
  const io = new IntersectionObserver((entries) => {
    entries.forEach((en) => { if (en.isIntersecting) { en.target.classList.add("in"); io.unobserve(en.target); } });
  }, { threshold: 0.12 });
  els.forEach((e) => io.observe(e));
}

/* ---------- Count up ---------- */
function initCounters() {
  const nums = document.querySelectorAll("[data-count]");
  if (!nums.length) return;
  const io = new IntersectionObserver((entries) => {
    entries.forEach((en) => {
      if (!en.isIntersecting) return;
      const el = en.target; const target = parseFloat(el.dataset.count); const suf = el.dataset.suffix || "";
      let cur = 0; const step = target / 48;
      const t = setInterval(() => {
        cur += step;
        if (cur >= target) { cur = target; clearInterval(t); }
        el.textContent = (Number.isInteger(target) ? Math.floor(cur) : cur.toFixed(1)) + suf;
      }, 22);
      io.unobserve(el);
    });
  }, { threshold: 0.5 });
  nums.forEach((n) => io.observe(n));
}

/* ---------- boot ---------- */
document.addEventListener("DOMContentLoaded", () => {
  buildHeader();
  buildFooter();
  buildFab();
  buildModal();
  injectIcons();
  initReveals();
  initCounters();
});
