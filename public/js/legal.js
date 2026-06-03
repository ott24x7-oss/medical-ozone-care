/* Legal pages — load the doc for the current slug from editable settings */
(function () {
  const slug = (location.pathname.replace(/\//g, "") || "terms").toLowerCase();
  const valid = ["terms", "privacy", "disclaimer", "warranty", "usage"];
  const key = valid.includes(slug) ? slug : "terms";

  async function load() {
    try {
      const { settings } = await (await fetch("/api/settings/public")).json();
      const doc = settings.legal[key] || { title: "Legal", body: "<p>Not found.</p>" };
      document.getElementById("lTitle").textContent = `${doc.title} — Medical Ozone Care`;
      document.getElementById("hTitle").textContent = doc.title;
      document.getElementById("bc").textContent = doc.title;
      document.getElementById("doc").innerHTML = doc.body;
      document.querySelectorAll(".legal-nav a").forEach((a) =>
        a.classList.toggle("active", a.dataset.slug === key));
    } catch {
      document.getElementById("doc").innerHTML = "<p>Unable to load this document. Please try again later.</p>";
    }
  }
  document.addEventListener("DOMContentLoaded", load);
})();
