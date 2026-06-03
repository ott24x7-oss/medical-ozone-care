/* =========================================================
   Medical Ozone Care — Admin panel
   ========================================================= */
(function () {
  const $ = (id) => document.getElementById(id);
  let TOKEN = localStorage.getItem("moc_token") || "";
  let ADMIN = null;
  let PRODUCTS = [];
  let CATS = ["Medical Ozone Generator", "Oxygen Regulator", "Accessories", "Spare Parts"];
  let editing = null; // product being edited
  let modalImgs = [];

  const esc = (s) => (s == null ? "" : String(s).replace(/[<>&"]/g, (c) => ({ "<": "&lt;", ">": "&gt;", "&": "&amp;", '"': "&quot;" }[c])));
  const toast = (msg, err = false) => {
    const t = $("toast"); t.textContent = msg; t.className = "toast show" + (err ? " err" : "");
    setTimeout(() => (t.className = "toast"), 2600);
  };

  async function api(path, opts = {}) {
    const res = await fetch(path, {
      ...opts,
      headers: { "Content-Type": "application/json", Authorization: "Bearer " + TOKEN, ...(opts.headers || {}) },
    });
    if (res.status === 401) { logout(); throw new Error("unauthorised"); }
    const json = await res.json().catch(() => ({}));
    if (!res.ok || json.ok === false) throw new Error(json.error || "Request failed");
    return json;
  }

  // ---------- auth ----------
  function showApp() { $("login").hidden = true; $("app").hidden = false; }
  function showLogin() { $("login").hidden = false; $("app").hidden = true; }
  function logout() { TOKEN = ""; localStorage.removeItem("moc_token"); showLogin(); }

  async function login() {
    const msg = $("loginMsg"); msg.className = "form-msg";
    const email = $("email").value.trim(); const password = $("password").value;
    if (!email || !password) { msg.className = "form-msg err"; msg.textContent = "Enter email and password."; return; }
    try {
      const r = await fetch("/api/admin/login", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify({ email, password }) });
      const j = await r.json();
      if (!j.ok) { msg.className = "form-msg err"; msg.textContent = j.error || "Login failed."; return; }
      TOKEN = j.token; ADMIN = j.admin; localStorage.setItem("moc_token", TOKEN);
      boot();
    } catch { msg.className = "form-msg err"; msg.textContent = "Network error."; }
  }

  // ---------- views ----------
  const TITLES = { dashboard: "Dashboard", products: "Products", enquiries: "Enquiries", brochures: "Brochures", contact: "Contact Settings", seo: "SEO Settings", legal: "Legal Pages" };
  function setView(name) {
    document.querySelectorAll("#nav button").forEach((b) => b.classList.toggle("active", b.dataset.view === name));
    $("viewTitle").textContent = TITLES[name] || name;
    ({ dashboard: viewDashboard, products: viewProducts, enquiries: viewEnquiries, brochures: viewBrochures, contact: viewContact, seo: viewSeo, legal: viewLegal }[name] || viewDashboard)();
  }

  async function viewDashboard() {
    $("view").innerHTML = '<div class="a-stats" id="st"></div><div class="panel"><h2>Recent enquiries</h2><div id="recent">Loading…</div></div>';
    try {
      const { stats } = await api("/api/admin/stats");
      const cards = [["Total enquiries", stats.enquiries], ["New", stats.new], ["Contacted", stats.contacted], ["Quoted", stats.quoted], ["Closed", stats.closed], ["Products", stats.products], ["Brochure downloads", stats.downloads]];
      $("st").innerHTML = cards.map((c) => `<div class="a-stat"><div class="n">${c[1]}</div><div class="l">${c[0]}</div></div>`).join("");
      const { enquiries } = await api("/api/admin/enquiries");
      $("recent").innerHTML = enquiries.length ? `<table class="a-table"><thead><tr><th>When</th><th>Name</th><th>Type</th><th>Product</th><th>Status</th></tr></thead><tbody>${enquiries.slice(0, 6).map((e) => `<tr><td>${esc((e.created_at || "").slice(0, 16))}</td><td><b>${esc(e.name)}</b><br><span class="who">${esc(e.phone)}</span></td><td>${esc(e.enquiry_type)}</td><td>${esc(e.interested_product) || "—"}</td><td><span class="pill ${esc(e.status)}">${esc(e.status)}</span></td></tr>`).join("")}</tbody></table>` : "<p class='who'>No enquiries yet.</p>";
    } catch (e) { if (e.message !== "unauthorised") $("view").innerHTML = `<p>${esc(e.message)}</p>`; }
  }

  async function viewProducts() {
    $("view").innerHTML = `<div class="panel"><div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px"><h2 style="margin:0">All products</h2><button class="btn btn-primary mini" id="addP">+ Add product</button></div><div id="plist">Loading…</div></div>`;
    $("addP").onclick = () => openProductModal(null);
    try {
      const { products } = await api("/api/admin/products");
      PRODUCTS = products;
      $("plist").innerHTML = `<table class="a-table"><thead><tr><th></th><th>Title</th><th>Category</th><th>Price</th><th>Status</th><th></th></tr></thead><tbody>${products.map((p) => `
        <tr>
          <td><img src="${esc(p.image)}" alt=""></td>
          <td><b>${esc(p.name)}</b><br><span class="who">${esc(p.model)} · /${esc(p.slug)}${p.featured ? " · ★" : ""}</span></td>
          <td>${esc(p.category)}</td>
          <td>${esc(p.price)}</td>
          <td><span class="pill ${esc(p.status)}">${esc(p.status)}</span></td>
          <td><div class="a-actions"><button class="btn btn-outline mini" data-edit="${p.dbId}">Edit</button><button class="btn btn-outline mini" data-del="${p.dbId}" style="color:var(--danger)">Delete</button></div></td>
        </tr>`).join("")}</tbody></table>`;
      $("plist").querySelectorAll("[data-edit]").forEach((b) => b.onclick = () => openProductModal(PRODUCTS.find((p) => p.dbId == b.dataset.edit)));
      $("plist").querySelectorAll("[data-del]").forEach((b) => b.onclick = () => delProduct(b.dataset.del));
    } catch (e) { if (e.message !== "unauthorised") $("plist").innerHTML = `<p>${esc(e.message)}</p>`; }
  }

  async function delProduct(id) {
    const p = PRODUCTS.find((x) => x.dbId == id);
    if (!confirm(`Delete "${p?.name}"? This cannot be undone.`)) return;
    try { await api("/api/admin/products/" + id, { method: "DELETE" }); toast("Product deleted"); viewProducts(); }
    catch (e) { toast(e.message, true); }
  }

  // ---------- product modal ----------
  const linesToArr = (t) => (t || "").split("\n").map((s) => s.trim()).filter(Boolean);
  const featuresToText = (arr) => (arr || []).map((f) => `${f.icon || "badge"} | ${f.title || ""} | ${f.text || ""}`).join("\n");
  const parseFeatures = (t) => linesToArr(t).map((l) => { const p = l.split("|").map((s) => s.trim()); return p.length >= 3 ? { icon: p[0], title: p[1], text: p.slice(2).join(" ") } : { icon: "badge", title: p[0], text: p[1] || "" }; });
  const specsToText = (o) => Object.entries(o || {}).map(([k, v]) => `${k} | ${v}`).join("\n");
  const parseSpecs = (t) => { const o = {}; linesToArr(t).forEach((l) => { const i = l.indexOf("|"); if (i > 0) o[l.slice(0, i).trim()] = l.slice(i + 1).trim(); }); return o; };
  const accToText = (arr) => (arr || []).map((a) => `${a.name} | ${a.qty || ""}`).join("\n");
  const parseAcc = (t) => linesToArr(t).map((l) => { const p = l.split("|").map((s) => s.trim()); return { name: p[0], qty: p[1] || "" }; });

  function renderThumbs() {
    const box = $("imgThumbs");
    if (!box) return;
    box.innerHTML = modalImgs.map((src, i) => `<div class="t"><img src="${esc(src)}"><button data-i="${i}">×</button></div>`).join("") || '<span class="who">No images yet.</span>';
    box.querySelectorAll("button").forEach((b) => b.onclick = () => { modalImgs.splice(+b.dataset.i, 1); renderThumbs(); });
  }

  function openProductModal(p) {
    editing = p || null;
    modalImgs = p ? [...(p.images || [])] : [];
    $("modalTitle").textContent = p ? "Edit product" : "Add product";
    $("modalBody").innerHTML = `
      <div class="grid2">
        <div class="fld"><label>Title *</label><input id="f_title" value="${esc(p?.name || "")}"></div>
        <div class="fld"><label>Slug (URL)</label><input id="f_slug" value="${esc(p?.slug || "")}" placeholder="auto from title"></div>
      </div>
      <div class="grid2">
        <div class="fld"><label>Model number</label><input id="f_model" value="${esc(p?.model || "")}"></div>
        <div class="fld"><label>Category</label><select id="f_cat">${CATS.map((c) => `<option ${p?.category === c ? "selected" : ""}>${c}</option>`).join("")}</select></div>
      </div>
      <div class="grid2">
        <div class="fld"><label>Price (display)</label><input id="f_price" value="${esc(p?.price || "On Request")}" placeholder="₹65,000 or On Request"></div>
        <div class="fld"><label>Price value (number, for SEO/sort)</label><input id="f_pricev" type="number" value="${p?.priceValue ?? ""}" placeholder="65000"></div>
      </div>
      <div class="grid2">
        <div class="fld"><label>Status</label><select id="f_status"><option value="active" ${p?.status !== "inactive" ? "selected" : ""}>active</option><option value="inactive" ${p?.status === "inactive" ? "selected" : ""}>inactive</option></select></div>
        <div class="fld"><label>Sort order</label><input id="f_sort" type="number" value="${p?.sortOrder ?? 100}"></div>
      </div>
      <div class="grid2">
        <div class="fld"><label><input type="checkbox" id="f_feat" ${p?.featured ? "checked" : ""}> Featured</label></div>
        <div class="fld"><label><input type="checkbox" id="f_chart" ${p?.hasConcentrationTable ? "checked" : ""}> Show concentration chart</label></div>
      </div>
      <div class="fld"><label>Warranty</label><input id="f_warranty" value="${esc(p?.warranty || "1 year")}"></div>
      <div class="fld"><label>Tagline</label><input id="f_tagline" value="${esc(p?.tagline || "")}"></div>
      <div class="fld"><label>Short description</label><textarea id="f_short">${esc(p?.shortDescription || "")}</textarea></div>
      <div class="fld"><label>Full description</label><textarea id="f_full">${esc(p?.longDescription || "")}</textarea></div>
      <div class="fld"><label>Highlights <span class="hint">one per line</span></label><textarea id="f_high">${esc((p?.highlights || []).join("\n"))}</textarea></div>
      <div class="fld"><label>Features <span class="hint">one per line: icon | title | text</span></label><textarea id="f_feats">${esc(featuresToText(p?.features))}</textarea></div>
      <div class="fld"><label>Specifications <span class="hint">one per line: key | value</span></label><textarea id="f_specs">${esc(specsToText(p?.specs))}</textarea></div>
      <div class="fld"><label>Accessories included <span class="hint">one per line: name | qty</span></label><textarea id="f_acc">${esc(accToText(p?.includedAccessories))}</textarea></div>
      <div class="fld"><label>Images</label><div class="thumbs" id="imgThumbs"></div>
        <div style="display:flex;gap:8px;margin-top:8px;flex-wrap:wrap">
          <label class="btn btn-outline mini" style="cursor:pointer">Upload image<input type="file" id="imgUp" accept="image/*" hidden></label>
          <input id="imgUrl" placeholder="…or paste an image path/URL" style="flex:1;min-width:160px;padding:8px 10px;border:1.5px solid var(--line);border-radius:9px">
          <button class="btn btn-outline mini" id="imgAdd">Add</button>
        </div>
        <div class="hint">Tip: use the built-in SVG mockups under /assets/img/products/ or upload a real photo.</div>
      </div>`;
    renderThumbs();
    $("imgUp").onchange = (e) => uploadFile(e.target.files[0], "image").then((path) => { if (path) { modalImgs.push(path); renderThumbs(); } });
    $("imgAdd").onclick = () => { const v = $("imgUrl").value.trim(); if (v) { modalImgs.push(v); $("imgUrl").value = ""; renderThumbs(); } };
    $("modalBg").classList.add("open");
  }

  function gatherProduct() {
    const base = editing || {};
    return {
      slug: $("f_slug").value.trim(),
      title: $("f_title").value.trim(),
      model_number: $("f_model").value.trim(),
      category: $("f_cat").value,
      price: $("f_price").value.trim() || "On Request",
      price_value: $("f_pricev").value === "" ? null : Number($("f_pricev").value),
      status: $("f_status").value,
      sort_order: Number($("f_sort").value) || 100,
      featured: $("f_feat").checked,
      has_concentration_table: $("f_chart").checked,
      warranty: $("f_warranty").value.trim(),
      tagline: $("f_tagline").value.trim(),
      short_description: $("f_short").value.trim(),
      full_description: $("f_full").value.trim(),
      highlights: linesToArr($("f_high").value),
      features: parseFeatures($("f_feats").value),
      specifications: parseSpecs($("f_specs").value),
      accessories: parseAcc($("f_acc").value),
      items: base.items || [],
      images: modalImgs,
      brochure_pdf: base.brochure || "",
    };
  }

  async function saveProduct() {
    const body = gatherProduct();
    if (!body.title) { toast("Title is required", true); return; }
    try {
      if (editing) await api("/api/admin/products/" + editing.dbId, { method: "PUT", body: JSON.stringify(body) });
      else await api("/api/admin/products", { method: "POST", body: JSON.stringify(body) });
      $("modalBg").classList.remove("open");
      toast("Product saved");
      viewProducts();
    } catch (e) { toast(e.message, true); }
  }

  // ---------- file upload (base64) ----------
  function uploadFile(file, kind) {
    return new Promise((resolve) => {
      if (!file) return resolve(null);
      const reader = new FileReader();
      reader.onload = async () => {
        try {
          const j = await api("/api/admin/upload", { method: "POST", body: JSON.stringify({ filename: file.name, dataUrl: reader.result, kind }) });
          toast("Uploaded"); resolve(j.path);
        } catch (e) { toast(e.message, true); resolve(null); }
      };
      reader.readAsDataURL(file);
    });
  }

  // ---------- enquiries ----------
  async function viewEnquiries() {
    $("view").innerHTML = `<div class="panel"><div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px" id="efilters"></div><div id="elist">Loading…</div></div>`;
    const statuses = ["All", "New", "Contacted", "Quoted", "Closed"];
    let filter = "All";
    $("efilters").innerHTML = statuses.map((s) => `<button class="btn btn-outline mini" data-f="${s}">${s}</button>`).join("");
    let data = [];
    const draw = () => {
      const list = filter === "All" ? data : data.filter((e) => e.status === filter);
      $("efilters").querySelectorAll("button").forEach((b) => { const on = b.dataset.f === filter; b.style.background = on ? "var(--teal-d)" : ""; b.style.color = on ? "#fff" : ""; });
      $("elist").innerHTML = list.length ? `<table class="a-table"><thead><tr><th>When</th><th>Contact</th><th>Type</th><th>Product</th><th>Message / Note</th><th>Status</th><th></th></tr></thead><tbody>${list.map((e) => `
        <tr>
          <td>${esc((e.created_at || "").slice(0, 16))}</td>
          <td><b>${esc(e.name)}</b><br><a href="tel:${esc(e.phone)}">${esc(e.phone)}</a>${e.email ? `<br><a href="mailto:${esc(e.email)}">${esc(e.email)}</a>` : ""}</td>
          <td>${esc(e.enquiry_type)}</td>
          <td>${esc(e.interested_product) || "—"}</td>
          <td style="max-width:240px">${esc(e.message) || "—"}<div style="margin-top:6px"><input data-note="${e.id}" value="${esc(e.admin_note || "")}" placeholder="admin note…" style="width:100%;padding:6px 8px;border:1px solid var(--line);border-radius:8px;font-size:.82rem"></div></td>
          <td><select data-status="${e.id}">${["New", "Contacted", "Quoted", "Closed"].map((s) => `<option ${e.status === s ? "selected" : ""}>${s}</option>`).join("")}</select></td>
          <td><button class="btn btn-outline mini" data-del="${e.id}" style="color:var(--danger)">Del</button></td>
        </tr>`).join("")}</tbody></table>` : "<p class='who'>No enquiries.</p>";
      $("elist").querySelectorAll("[data-status]").forEach((sel) => sel.onchange = async () => {
        try { await api("/api/admin/enquiries/" + sel.dataset.status, { method: "PATCH", body: JSON.stringify({ status: sel.value }) }); const it = data.find((x) => x.id == sel.dataset.status); if (it) it.status = sel.value; toast("Status updated"); draw(); } catch (er) { toast(er.message, true); }
      });
      $("elist").querySelectorAll("[data-note]").forEach((inp) => inp.onchange = async () => {
        try { await api("/api/admin/enquiries/" + inp.dataset.note, { method: "PATCH", body: JSON.stringify({ admin_note: inp.value }) }); toast("Note saved"); } catch (er) { toast(er.message, true); }
      });
      $("elist").querySelectorAll("[data-del]").forEach((b) => b.onclick = async () => {
        if (!confirm("Delete this enquiry?")) return;
        try { await api("/api/admin/enquiries/" + b.dataset.del, { method: "DELETE" }); data = data.filter((x) => x.id != b.dataset.del); toast("Deleted"); draw(); } catch (er) { toast(er.message, true); }
      });
    };
    $("efilters").querySelectorAll("button").forEach((b) => b.onclick = () => { filter = b.dataset.f; draw(); });
    try { const { enquiries } = await api("/api/admin/enquiries"); data = enquiries; draw(); }
    catch (e) { if (e.message !== "unauthorised") $("elist").innerHTML = `<p>${esc(e.message)}</p>`; }
  }

  // ---------- brochures ----------
  async function viewBrochures() {
    $("view").innerHTML = `<div class="panel"><h2>Brochure manager</h2><p class="who" style="margin-top:-6px">Upload a PDF brochure per product. Visitors download via a tracked link.</p><div id="blist">Loading…</div></div>`;
    try {
      const [{ products }, dl] = await Promise.all([api("/api/admin/products"), api("/api/admin/downloads")]);
      PRODUCTS = products;
      const counts = {};
      (dl.downloads || []).forEach((d) => { counts[d.product_slug] = (counts[d.product_slug] || 0) + 1; });
      $("blist").innerHTML = `<table class="a-table"><thead><tr><th>Product</th><th>Brochure</th><th>Downloads</th><th></th></tr></thead><tbody>${products.map((p) => `
        <tr>
          <td><b>${esc(p.name)}</b><br><span class="who">${esc(p.model)}</span></td>
          <td>${p.brochure ? `<a href="${esc(p.brochure)}" target="_blank">View PDF ↗</a>` : '<span class="who">none</span>'}</td>
          <td>${counts[p.slug] || 0}</td>
          <td><div class="a-actions"><label class="btn btn-outline mini" style="cursor:pointer">Upload PDF<input type="file" accept="application/pdf" data-up="${p.dbId}" hidden></label>${p.brochure ? `<button class="btn btn-outline mini" data-clear="${p.dbId}">Remove</button>` : ""}</div></td>
        </tr>`).join("")}</tbody></table>`;
      $("blist").querySelectorAll("[data-up]").forEach((inp) => inp.onchange = async (e) => {
        const p = PRODUCTS.find((x) => x.dbId == inp.dataset.up);
        const path = await uploadFile(e.target.files[0], "brochure");
        if (path) { await saveProductFields(p, { brochure_pdf: path }); toast("Brochure uploaded"); viewBrochures(); }
      });
      $("blist").querySelectorAll("[data-clear]").forEach((b) => b.onclick = async () => {
        const p = PRODUCTS.find((x) => x.dbId == b.dataset.clear);
        await saveProductFields(p, { brochure_pdf: "" }); toast("Brochure removed"); viewBrochures();
      });
    } catch (e) { if (e.message !== "unauthorised") $("blist").innerHTML = `<p>${esc(e.message)}</p>`; }
  }

  // full-payload update preserving all fields, applying overrides
  function productToPayload(p, ov = {}) {
    return {
      slug: p.slug, title: p.name, model_number: p.model, category: p.category,
      price: p.price, price_value: p.priceValue, featured: p.featured, status: p.status,
      sort_order: p.sortOrder ?? 100, has_concentration_table: p.hasConcentrationTable,
      warranty: p.warranty, tagline: p.tagline, short_description: p.shortDescription,
      full_description: p.longDescription, highlights: p.highlights, features: p.features,
      specifications: p.specs, accessories: p.includedAccessories, items: p.items,
      images: p.images, brochure_pdf: p.brochure || "", ...ov,
    };
  }
  async function saveProductFields(p, ov) {
    return api("/api/admin/products/" + p.dbId, { method: "PUT", body: JSON.stringify(productToPayload(p, ov)) });
  }

  // ---------- settings (contact / seo / legal) ----------
  let SETTINGS = null;
  async function loadSettings() { if (!SETTINGS) SETTINGS = (await api("/api/admin/settings")).settings; return SETTINGS; }

  async function viewContact() {
    const s = await loadSettings(); const c = s.contact;
    $("view").innerHTML = `<div class="panel"><h2>Contact settings</h2>
      <div class="grid2">
        <div class="fld"><label>Company</label><input id="c_company" value="${esc(c.company)}"></div>
        <div class="fld"><label>Contact person</label><input id="c_person" value="${esc(c.person)}"></div>
        <div class="fld"><label>Phone (display)</label><input id="c_phone" value="${esc(c.phone)}"></div>
        <div class="fld"><label>WhatsApp number (digits only)</label><input id="c_raw" value="${esc(c.phoneRaw)}"></div>
        <div class="fld"><label>Email 1</label><input id="c_e1" value="${esc(c.email1)}"></div>
        <div class="fld"><label>Email 2</label><input id="c_e2" value="${esc(c.email2)}"></div>
        <div class="fld"><label>Website</label><input id="c_web" value="${esc(c.website)}"></div>
        <div class="fld"><label>Business hours</label><input id="c_hours" value="${esc(c.hours)}"></div>
      </div>
      <div class="fld"><label>Address</label><input id="c_addr" value="${esc(c.address)}"></div>
      <button class="btn btn-primary" id="saveC">Save contact settings</button>
      <p class="hint" style="margin-top:8px">Note: the public site's contact details are also defined in <code>public/js/app.js</code> for instant loading; update both for a permanent change.</p>
    </div>`;
    $("saveC").onclick = async () => {
      const contact = { company: $("c_company").value, person: $("c_person").value, phone: $("c_phone").value, phoneRaw: $("c_raw").value, email1: $("c_e1").value, email2: $("c_e2").value, website: $("c_web").value, hours: $("c_hours").value, address: $("c_addr").value };
      try { SETTINGS = (await api("/api/admin/settings", { method: "PUT", body: JSON.stringify({ contact }) })).settings; toast("Saved"); } catch (e) { toast(e.message, true); }
    };
  }

  async function viewSeo() {
    const s = await loadSettings(); const o = s.seo;
    $("view").innerHTML = `<div class="panel"><h2>SEO settings</h2>
      <div class="fld"><label>Site title</label><input id="s_title" value="${esc(o.siteTitle)}"></div>
      <div class="fld"><label>Meta description</label><textarea id="s_desc">${esc(o.siteDescription)}</textarea></div>
      <div class="fld"><label>Keywords</label><textarea id="s_keys">${esc(o.keywords)}</textarea></div>
      <div class="grid2">
        <div class="fld"><label>OG image path</label><input id="s_og" value="${esc(o.ogImage)}"></div>
        <div class="fld"><label>Base URL (for sitemap)</label><input id="s_base" value="${esc(o.baseUrl)}"></div>
      </div>
      <button class="btn btn-primary" id="saveS">Save SEO settings</button>
      <p class="hint" style="margin-top:8px">sitemap.xml &amp; robots.txt are generated automatically from products and the base URL.</p>
    </div>`;
    $("saveS").onclick = async () => {
      const seo = { siteTitle: $("s_title").value, siteDescription: $("s_desc").value, keywords: $("s_keys").value, ogImage: $("s_og").value, baseUrl: $("s_base").value };
      try { SETTINGS = (await api("/api/admin/settings", { method: "PUT", body: JSON.stringify({ seo }) })).settings; toast("Saved"); } catch (e) { toast(e.message, true); }
    };
  }

  async function viewLegal() {
    const s = await loadSettings(); const L = s.legal;
    const keys = ["terms", "privacy", "disclaimer", "warranty", "usage"];
    $("view").innerHTML = `<div class="panel"><h2>Legal pages</h2>
      <div class="fld"><label>Document</label><select id="l_pick">${keys.map((k) => `<option value="${k}">${esc(L[k].title)}</option>`).join("")}</select></div>
      <div class="fld"><label>Title</label><input id="l_title" value="${esc(L.terms.title)}"></div>
      <div class="fld"><label>Body (HTML allowed)</label><textarea id="l_body" style="min-height:280px;font-family:monospace;font-size:.85rem">${esc(L.terms.body)}</textarea></div>
      <button class="btn btn-primary" id="saveL">Save document</button>
      <a class="btn btn-outline" id="viewL" target="_blank" style="margin-left:8px">Preview ↗</a>
    </div>`;
    const fill = (k) => { $("l_title").value = L[k].title; $("l_body").value = L[k].body; $("viewL").href = "/" + k; };
    $("l_pick").onchange = () => fill($("l_pick").value);
    fill("terms");
    $("saveL").onclick = async () => {
      const k = $("l_pick").value;
      L[k] = { title: $("l_title").value, body: $("l_body").value };
      try { SETTINGS = (await api("/api/admin/settings", { method: "PUT", body: JSON.stringify({ legal: { [k]: L[k] } }) })).settings; toast("Saved"); } catch (e) { toast(e.message, true); }
    };
  }

  // ---------- boot ----------
  async function boot() {
    try {
      const me = await api("/api/admin/me"); ADMIN = me.admin;
      $("who").textContent = "Signed in as " + (ADMIN.email || "admin");
      try { CATS = (await (await fetch("/api/categories")).json()).categories || CATS; } catch {}
      showApp();
      SETTINGS = null;
      setView("dashboard");
    } catch { logout(); }
  }

  document.addEventListener("DOMContentLoaded", () => {
    $("loginBtn").onclick = login;
    $("password").addEventListener("keydown", (e) => { if (e.key === "Enter") login(); });
    $("logoutBtn").onclick = logout;
    document.querySelectorAll("#nav button").forEach((b) => b.onclick = () => setView(b.dataset.view));
    $("modalClose").onclick = $("modalCancel").onclick = () => $("modalBg").classList.remove("open");
    $("modalSave").onclick = saveProduct;
    if (TOKEN) boot(); else showLogin();
  });
})();
