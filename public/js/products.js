/* Products listing — fetch, filter by category, render cards */
(function () {
  const wa = document.getElementById("waBtn");
  if (wa) wa.href = MOC.waLink;

  let all = [];
  const params = new URLSearchParams(location.search);
  let active = params.get("category") || "All";
  let query = (params.get("q") || "").toLowerCase();

  function card(p) {
    const priceHtml = p.priceValue
      ? `<span class="price">${p.price}</span>`
      : `<span class="price" style="font-size:.95rem;color:var(--muted)">${p.price}</span>`;
    return `
      <a class="pcard reveal" href="/product?id=${p.id}">
        <div class="media">
          ${p.featured ? '<span class="tag feat">Flagship</span>' : `<span class="tag">${p.category.split(" ")[0]}</span>`}
          <img src="${p.thumb || p.image}" alt="${p.name}" loading="lazy" />
        </div>
        <div class="body">
          <span class="cat">${p.category}</span>
          <h3>${p.name}</h3>
          <div class="model">${p.model} · <span class="status-dot">In stock</span></div>
          <p>${p.shortDescription}</p>
          <div class="foot">
            ${priceHtml}
            <span class="btn btn-outline" style="padding:8px 16px">Details ${icon("arrow")}</span>
          </div>
        </div>
      </a>`;
  }

  function render() {
    const grid = document.getElementById("productGrid");
    let list = active === "All" ? all : all.filter((p) => p.category === active);
    if (query) {
      list = list.filter((p) =>
        [p.name, p.model, p.category, p.shortDescription, p.tagline].join(" ").toLowerCase().includes(query));
    }
    grid.innerHTML = list.map(card).join("") ||
      `<p class="text-center" style="grid-column:1/-1;padding:40px;color:var(--muted)">No products match your search. <a href="/products">Clear</a></p>`;
    injectIcons(grid);
    initReveals();
    // filter chips active state
    document.querySelectorAll("#filters .chip").forEach((c) => {
      c.style.background = c.dataset.cat === active ? "var(--teal-d)" : "";
      c.style.color = c.dataset.cat === active ? "#fff" : "";
      c.style.borderColor = c.dataset.cat === active ? "var(--teal-d)" : "";
    });
  }

  function buildFilters() {
    const cats = ["All", ...new Set(all.map((p) => p.category))];
    const box = document.getElementById("filters");
    box.innerHTML = cats
      .map((c) => `<button class="chip" data-cat="${c}" style="cursor:pointer;border:1px solid #cdeef4">${c}</button>`)
      .join("");
    box.querySelectorAll(".chip").forEach((c) =>
      c.addEventListener("click", () => {
        active = c.dataset.cat;
        const u = new URL(location);
        if (active === "All") u.searchParams.delete("category");
        else u.searchParams.set("category", active);
        history.replaceState({}, "", u);
        render();
      })
    );
  }

  async function load() {
    try {
      const r = await fetch("/api/products");
      const j = await r.json();
      all = j.products;
      buildFilters();
      render();
    } catch {
      document.getElementById("productGrid").innerHTML =
        '<p class="text-center">Unable to load products. Please refresh or contact us.</p>';
    }
  }

  function wireSearch() {
    const input = document.getElementById("searchInput");
    if (!input) return;
    input.value = query;
    let t;
    input.addEventListener("input", () => {
      clearTimeout(t);
      t = setTimeout(() => {
        query = input.value.trim().toLowerCase();
        const u = new URL(location);
        if (query) u.searchParams.set("q", query); else u.searchParams.delete("q");
        history.replaceState({}, "", u);
        render();
      }, 180);
    });
  }

  document.addEventListener("DOMContentLoaded", () => { load(); wireSearch(); });
})();
