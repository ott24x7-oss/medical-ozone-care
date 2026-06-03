/* Instructions page — concentration chart + scrollspy side nav */
(function () {
  async function loadChart() {
    const mount = document.getElementById("chartMount");
    if (!mount) return;
    try {
      const { concentrationTable: t } = await (await fetch("/api/concentration-table")).json();
      const head = t.levels.map((l, i) => `<th class="${i < 4 ? "m1" : "m2"}">${l}</th>`).join("");
      const rows = t.rows
        .map((row) => `<tr><th>${row.flow}</th>${row.values.map((v) => `<td>${v == null ? "—" : v}</td>`).join("")}</tr>`)
        .join("");
      mount.innerHTML = `<div class="ctable-scroll"><table class="ctable">
        <thead><tr><th rowspan="2">O₂ Flow</th><th class="m1" colspan="4">Mode M1</th><th class="m2" colspan="2">Mode M2</th></tr><tr>${head}</tr></thead>
        <tbody>${rows}</tbody></table></div>
        <p class="form-note" style="margin-top:12px">All values in µg/ml (mg/L). "—" = not available at that flow.</p>`;
    } catch {
      mount.innerHTML = '<p class="text-center">Chart unavailable — please contact us for a copy.</p>';
    }
  }

  function scrollSpy() {
    const links = [...document.querySelectorAll("#docNav a")];
    const sections = links.map((a) => document.querySelector(a.getAttribute("href"))).filter(Boolean);
    if (!sections.length) return;
    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((en) => {
          if (en.isIntersecting) {
            links.forEach((l) => l.classList.remove("active"));
            const link = links.find((l) => l.getAttribute("href") === "#" + en.target.id);
            if (link) link.classList.add("active");
          }
        });
      },
      { rootMargin: "-20% 0px -70% 0px" }
    );
    sections.forEach((s) => io.observe(s));
  }

  document.addEventListener("DOMContentLoaded", () => {
    loadChart();
    scrollSpy();
  });
})();
