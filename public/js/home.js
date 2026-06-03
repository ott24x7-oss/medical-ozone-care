/* Landing page dynamic content — features, product range, concentration chart */
(function () {
  // WhatsApp CTA
  const wa = document.getElementById("ctaWa");
  if (wa) wa.href = MOC.waLink;

  // ---- Features (from flagship product) ----
  async function loadFeatures() {
    const grid = document.getElementById("featureGrid");
    if (!grid) return;
    try {
      const r = await fetch("/api/products/aot-md-520");
      const { product } = await r.json();
      grid.innerHTML = product.features
        .map(
          (f, i) => `
        <div class="feature reveal ${i % 3 ? "d" + (i % 3) : ""}">
          <div class="ic">${icon(f.icon)}</div>
          <h3>${f.title}</h3>
          <p>${f.text}</p>
        </div>`
        )
        .join("");
      injectIcons(grid);
      initReveals();
    } catch {
      grid.innerHTML = "";
    }
  }

  // ---- Product range cards ----
  function card(p) {
    return `
      <a class="pcard reveal" href="/product?id=${p.id}">
        <div class="media">
          ${p.featured ? '<span class="tag feat">Flagship</span>' : `<span class="tag">${p.category}</span>`}
          <img src="${p.thumb || p.image}" alt="${p.name}" loading="lazy" />
        </div>
        <div class="body">
          <span class="cat">${p.category}</span>
          <h3>${p.name}</h3>
          <div class="model">${p.model}</div>
          <p>${p.shortDescription}</p>
          <div class="foot">
            <span class="price">${p.price}</span>
            <span class="btn btn-outline" style="padding:8px 16px">Details ${icon("arrow")}</span>
          </div>
        </div>
      </a>`;
  }
  async function loadProducts() {
    const grid = document.getElementById("productGrid");
    if (!grid) return;
    try {
      const r = await fetch("/api/products");
      const { products } = await r.json();
      // Feature the three priced generators
      const order = ["aot-md-520", "ozone-generator-digital", "ozone-water-oil-system"];
      const pick = order.map((id) => products.find((p) => p.id === id)).filter(Boolean);
      grid.innerHTML = pick.map(card).join("");
      injectIcons(grid);
      initReveals();
    } catch {
      grid.innerHTML = '<p class="text-center">Unable to load products right now.</p>';
    }
  }

  // ---- Concentration chart ----
  async function loadChart() {
    const mount = document.getElementById("chartMount");
    if (!mount) return;
    try {
      const r = await fetch("/api/concentration-table");
      const { concentrationTable: t } = await r.json();
      const head = t.levels
        .map((l, i) => `<th class="${i < 4 ? "m1" : "m2"}">${l}</th>`)
        .join("");
      const rows = t.rows
        .map(
          (row) =>
            `<tr><th>${row.flow}</th>${row.values
              .map((v) => `<td>${v == null ? "—" : v}</td>`)
              .join("")}</tr>`
        )
        .join("");
      mount.innerHTML = `
        <div class="ctable-scroll">
          <table class="ctable">
            <thead>
              <tr><th rowspan="2">O₂ Flow</th><th class="m1" colspan="4">Mode M1</th><th class="m2" colspan="2">Mode M2</th></tr>
              <tr>${head}</tr>
            </thead>
            <tbody>${rows}</tbody>
          </table>
        </div>
        <p class="form-note text-center" style="margin-top:14px">All values in µg/ml (mg/L). "—" indicates a combination not supported at that flow.</p>`;
    } catch {
      mount.innerHTML = '<p class="text-center">Chart unavailable.</p>';
    }
  }

  // ---- Home CTA form ----
  function wireForm() {
    const form = document.getElementById("homeForm");
    if (!form) return;
    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      const msg = document.getElementById("homeMsg");
      const btn = form.querySelector('button[type="submit"]');
      const data = Object.fromEntries(new FormData(form).entries());
      msg.className = "form-msg";
      if (!data.name || !data.phone) {
        msg.className = "form-msg err";
        msg.textContent = "Please enter your name and phone number.";
        return;
      }
      btn.disabled = true; btn.style.opacity = .7;
      try {
        const res = await fetch("/api/enquiries", {
          method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(data),
        });
        const json = await res.json();
        if (json.ok) {
          msg.className = "form-msg ok";
          msg.textContent = json.message || "Thank you! We'll be in touch shortly.";
          form.reset();
        } else {
          msg.className = "form-msg err";
          msg.textContent = json.error || "Something went wrong.";
        }
      } catch {
        msg.className = "form-msg err";
        msg.textContent = "Network error — please call or WhatsApp us.";
      } finally {
        btn.disabled = false; btn.style.opacity = 1;
      }
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
    loadFeatures();
    loadProducts();
    loadChart();
    wireForm();
  });
})();
