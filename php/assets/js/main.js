/* Medical Ozone Care — front-end interactions (server renders content & icons) */
(function () {
  var API = window.MOC_API || "api/enquiry.php";

  // ---- sticky nav shadow + mobile drawer ----
  var nav = document.getElementById("nav");
  if (nav) {
    var onScroll = function () { nav.classList.toggle("stuck", window.scrollY > 4); };
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
  }
  var toggle = document.getElementById("navToggle");
  var drawer = document.getElementById("mdrawer");
  function openDrawer() { if (!drawer) return; drawer.classList.add("open"); document.body.style.overflow = "hidden"; if (toggle) toggle.setAttribute("aria-expanded", "true"); }
  function closeDrawer() { if (!drawer) return; drawer.classList.remove("open"); document.body.style.overflow = ""; if (toggle) toggle.setAttribute("aria-expanded", "false"); }
  if (toggle && drawer) toggle.addEventListener("click", openDrawer);
  if (drawer) {
    drawer.querySelectorAll("[data-mclose]").forEach(function (el) { el.addEventListener("click", closeDrawer); });
    drawer.querySelectorAll(".mdrawer-links a").forEach(function (a) { a.addEventListener("click", closeDrawer); });
    document.addEventListener("keydown", function (e) { if (e.key === "Escape") closeDrawer(); });
  }

  // ---- reveal on scroll ----
  var reveals = document.querySelectorAll(".reveal");
  if ("IntersectionObserver" in window) {
    var io = new IntersectionObserver(function (es) {
      es.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add("in"); io.unobserve(en.target); } });
    }, { threshold: 0.12 });
    reveals.forEach(function (el) { io.observe(el); });
  } else { reveals.forEach(function (el) { el.classList.add("in"); }); }

  // ---- count up ----
  var nums = document.querySelectorAll("[data-count]");
  if (nums.length && "IntersectionObserver" in window) {
    var io2 = new IntersectionObserver(function (es) {
      es.forEach(function (en) {
        if (!en.isIntersecting) return;
        var el = en.target, target = parseFloat(el.dataset.count), suf = el.dataset.suffix || "";
        var cur = 0, step = target / 46;
        var t = setInterval(function () {
          cur += step;
          if (cur >= target) { cur = target; clearInterval(t); }
          el.textContent = (Number.isInteger(target) ? Math.floor(cur) : cur.toFixed(1)) + suf;
        }, 22);
        io2.unobserve(el);
      });
    }, { threshold: 0.5 });
    nums.forEach(function (n) { io2.observe(n); });
  }

  // ---- quote modal ----
  var modal = document.getElementById("quoteModal");
  function openModal(product) {
    if (!modal) return;
    if (product) {
      var sel = document.getElementById("qmProduct");
      if (sel) {
        var p = product.toLowerCase();
        var opt = Array.prototype.find.call(sel.options, function (o) {
          var v = o.value.toLowerCase(); return v && (v.indexOf(p) > -1 || p.indexOf(v) > -1);
        });
        if (opt) sel.value = opt.value; else { var o = new Option(product, product, true, true); sel.add(o); }
      }
    }
    modal.style.display = "flex"; document.body.style.overflow = "hidden";
    var nm = modal.querySelector('input[name="name"]'); if (nm) setTimeout(function () { nm.focus(); }, 50);
  }
  function closeModal() { if (modal) { modal.style.display = "none"; document.body.style.overflow = ""; } }
  if (modal) {
    modal.querySelector(".qm-backdrop").addEventListener("click", closeModal);
    document.getElementById("qmClose").addEventListener("click", closeModal);
    document.addEventListener("keydown", function (e) { if (e.key === "Escape") closeModal(); });
  }
  document.addEventListener("click", function (e) {
    var t = e.target.closest("[data-quote]");
    if (t) { e.preventDefault(); openModal(t.getAttribute("data-quote") || ""); }
  });

  // ---- AJAX enquiry submit (modal + any .ajax-enquiry form) ----
  function wireForm(form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var msg = form.querySelector(".form-msg") || document.getElementById("qmMsg");
      var btn = form.querySelector('button[type="submit"]');
      var data = new URLSearchParams(new FormData(form));
      if (!form.name.value || !form.phone.value) {
        if (msg) { msg.className = "form-msg err"; msg.textContent = "Please enter your name and phone number."; }
        return;
      }
      var orig = btn ? btn.innerHTML : "";
      if (btn) { btn.disabled = true; btn.style.opacity = .7; btn.innerHTML = "Sending…"; }
      fetch(API, { method: "POST", headers: { "X-Requested-With": "fetch", "Content-Type": "application/x-www-form-urlencoded" }, body: data.toString() })
        .then(function (r) { return r.json(); })
        .then(function (j) {
          if (msg) {
            msg.className = "form-msg " + (j.ok ? "ok" : "err");
            msg.textContent = j.ok ? j.message : (j.error || "Something went wrong.");
          }
          if (j.ok) { form.reset(); if (form.closest("#quoteModal")) setTimeout(closeModal, 2400); }
        })
        .catch(function () { if (msg) { msg.className = "form-msg err"; msg.textContent = "Network error — please call or WhatsApp us."; } })
        .finally(function () { if (btn) { btn.disabled = false; btn.style.opacity = 1; btn.innerHTML = orig; } });
    });
  }
  var qm = document.getElementById("qmForm"); if (qm) wireForm(qm);
  document.querySelectorAll("form.ajax-enquiry").forEach(wireForm);
})();
