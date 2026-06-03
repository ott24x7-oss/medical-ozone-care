/* Product detail page — render gallery, info, tabs, related */
(function () {
  const id = new URLSearchParams(location.search).get("id") || "aot-md-520";

  function specRows(specs) {
    return Object.entries(specs)
      .map(([k, v]) => `<tr><th>${k}</th><td>${v}</td></tr>`)
      .join("");
  }

  function ctableHtml(t) {
    const head = t.levels.map((l, i) => `<th class="${i < 4 ? "m1" : "m2"}">${l}</th>`).join("");
    const rows = t.rows
      .map((row) => `<tr><th>${row.flow}</th>${row.values.map((v) => `<td>${v == null ? "—" : v}</td>`).join("")}</tr>`)
      .join("");
    return `<div class="ctable-scroll"><table class="ctable">
      <thead><tr><th rowspan="2">O₂ Flow</th><th class="m1" colspan="4">Mode M1</th><th class="m2" colspan="2">Mode M2</th></tr><tr>${head}</tr></thead>
      <tbody>${rows}</tbody></table></div>
      <p class="form-note" style="margin-top:12px">Values in µg/ml (mg/L). Press the M1 or M2 switch, set oxygen flow on the regulator, then select the level.</p>`;
  }

  async function render(p) {
    document.getElementById("pTitle").textContent = `${p.name} (${p.model}) — Medical Ozone Care`;
    document.getElementById("pDesc").setAttribute("content", p.shortDescription);
    document.getElementById("bcName").textContent = p.name;

    // JSON-LD product schema (SEO / rich results)
    try {
      const ld = {
        "@context": "https://schema.org/", "@type": "Product",
        name: p.name, sku: p.model, mpn: p.model,
        description: p.shortDescription,
        brand: { "@type": "Brand", name: "Medical Ozone Care" },
        image: [location.origin + (p.image || "")],
        offers: {
          "@type": "Offer",
          priceCurrency: "INR",
          price: p.priceValue || undefined,
          availability: "https://schema.org/InStock",
          seller: { "@type": "Organization", name: "Medical Ozone Care" },
          url: location.href,
        },
      };
      const s = document.createElement("script");
      s.type = "application/ld+json";
      s.textContent = JSON.stringify(ld);
      document.head.appendChild(s);
    } catch {}

    // gallery
    const gallery = (p.gallery && p.gallery.length ? p.gallery : [p.image]);
    const thumbs = gallery
      .map((g, i) => `<button class="${i === 0 ? "active" : ""}" data-img="${g}"><img src="${g}" alt="view ${i + 1}"></button>`)
      .join("");

    // tabs
    let chart = null;
    if (p.hasConcentrationTable) {
      try { chart = (await (await fetch("/api/concentration-table")).json()).concentrationTable; } catch {}
    }
    const boxItems =
      (p.includedAccessories || []).map((a) => `<li><span data-icon="check"></span> ${a.name} <strong>× ${a.qty.replace(/^\d+\s*/, (m)=>m)}</strong></li>`).join("") ||
      (p.items || []).map((a) => `<li><span data-icon="check"></span> ${a.name} <strong>(${a.qty})</strong></li>`).join("");

    const accGrid = (p.items || [])
      .map((a) => `<div class="acc-item"><img src="${a.img}" alt="${a.name}"><div class="nm">${a.name}</div><div class="qty">${a.qty}</div></div>`)
      .join("");

    const tabs = [];
    tabs.push({ id: "overview", label: "Overview", html: `
      <p class="lead">${p.longDescription}</p>
      ${p.features ? `<div class="features" style="margin-top:24px">${p.features.map((f) => `
        <div class="feature"><div class="ic">${icon(f.icon)}</div><h3>${f.title}</h3><p>${f.text}</p></div>`).join("")}</div>` : ""}
    `});
    tabs.push({ id: "specs", label: "Specifications", html: `<table class="spec-table"><tbody>${specRows(p.specs)}</tbody></table>` });
    if (boxItems) tabs.push({ id: "box", label: "In the Box", html: `
      <ul class="pd-highlights" style="margin-bottom:24px">${boxItems}</ul>
      ${accGrid ? `<div class="acc-items">${accGrid}</div>` : ""}` });
    if (chart) tabs.push({ id: "chart", label: "Concentration Chart", html: ctableHtml(chart) });

    const tabBtns = tabs.map((t, i) => `<button class="${i === 0 ? "active" : ""}" data-tab="${t.id}">${t.label}</button>`).join("");
    const tabPanes = tabs.map((t, i) => `<div class="tabpane ${i === 0 ? "active" : ""}" id="tab-${t.id}">${t.html}</div>`).join("");

    document.getElementById("pdMount").innerHTML = `
      <div class="pd-grid">
        <div class="pd-gallery">
          <div class="main"><img id="mainImg" src="${gallery[0]}" alt="${p.name}"></div>
          ${gallery.length > 1 ? `<div class="pd-thumbs">${thumbs}</div>` : ""}
        </div>
        <div class="pd-info">
          <span class="cat">${p.category}</span>
          <h1>${p.name}</h1>
          <div class="model">Model: ${p.model}</div>
          <div class="chips">${(p.highlights || []).map((h) => `<span class="chip">${h}</span>`).join("")}</div>
          <ul class="pd-highlights">${(p.highlights || []).slice(0, 5).map((h) => `<li><span data-icon="checkc"></span> ${h}</li>`).join("")}</ul>
          <div class="price-row">
            ${p.priceValue ? `<span class="amt">${p.price}</span><span class="cur">incl. unit · taxes/delivery extra</span>`
                           : `<span class="amt" style="font-size:1.2rem">${p.price}</span><span class="cur">share requirement for a quote</span>`}
          </div>
          <div class="chips" style="margin:0 0 18px"><span class="status-dot">In stock</span> ${p.warranty && p.warranty !== "—" ? `<span class="chip">${p.warranty} warranty</span>` : ""}</div>
          <div class="pd-actions">
            <a class="btn btn-primary btn-lg" data-quote="${p.name}"><span data-icon="send"></span> Request a Quote</a>
            <a class="btn btn-wa btn-lg" href="${MOC.waFor(p.name)}" target="_blank" rel="noopener"><span data-icon="wa" data-fill></span> WhatsApp</a>
            <a class="btn btn-outline btn-lg" href="${MOC.telLink}"><span data-icon="phone"></span> Call</a>
            ${p.brochure ? `<a class="btn btn-outline btn-lg" href="/api/brochure/${p.id}" target="_blank" rel="noopener"><span data-icon="doc"></span> Download Brochure</a>` : ""}
          </div>
        </div>
      </div>
      <div class="tabs">${tabBtns}</div>
      <div>${tabPanes}</div>`;

    injectIcons(document.getElementById("pdMount"));

    // gallery thumbs
    document.querySelectorAll(".pd-thumbs button").forEach((b) =>
      b.addEventListener("click", () => {
        document.getElementById("mainImg").src = b.dataset.img;
        document.querySelectorAll(".pd-thumbs button").forEach((x) => x.classList.remove("active"));
        b.classList.add("active");
      })
    );
    // tabs
    document.querySelectorAll(".tabs button").forEach((b) =>
      b.addEventListener("click", () => {
        document.querySelectorAll(".tabs button").forEach((x) => x.classList.remove("active"));
        document.querySelectorAll(".tabpane").forEach((x) => x.classList.remove("active"));
        b.classList.add("active");
        document.getElementById("tab-" + b.dataset.tab).classList.add("active");
      })
    );
  }

  async function loadRelated(currentId) {
    try {
      const { products } = await (await fetch("/api/products")).json();
      const rel = products.filter((p) => p.id !== currentId).slice(0, 3);
      if (!rel.length) return;
      document.getElementById("relatedWrap").style.display = "";
      document.getElementById("related").innerHTML = rel
        .map(
          (p) => `<a class="pcard" href="/product?id=${p.id}">
        <div class="media"><img src="${p.thumb || p.image}" alt="${p.name}"></div>
        <div class="body"><span class="cat">${p.category}</span><h3>${p.name}</h3>
        <div class="model">${p.model}</div><p>${p.shortDescription}</p>
        <div class="foot"><span class="price">${p.price}</span><span class="btn btn-outline" style="padding:8px 16px">Details ${icon("arrow")}</span></div></div></a>`
        )
        .join("");
      injectIcons(document.getElementById("related"));
    } catch {}
  }

  async function load() {
    try {
      const r = await fetch("/api/products/" + encodeURIComponent(id));
      if (!r.ok) throw new Error("not found");
      const { product } = await r.json();
      await render(product);
      loadRelated(product.id);
    } catch {
      document.getElementById("pdMount").innerHTML =
        '<div class="text-center" style="padding:40px"><h2>Product not found</h2><p class="lead">The product you\'re looking for isn\'t available.</p><a class="btn btn-primary" href="/products">Browse all products</a></div>';
    }
  }

  document.addEventListener("DOMContentLoaded", load);
})();
