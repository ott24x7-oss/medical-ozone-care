/* Shared contact form handler (about.html & contact.html) */
(function () {
  const waTile = document.getElementById("waTile");
  if (waTile) waTile.href = MOC.waLink;

  function wire(form) {
    const msg = document.getElementById("msg");
    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      const btn = form.querySelector('button[type="submit"]');
      const data = Object.fromEntries(new FormData(form).entries());
      msg.className = "form-msg";
      if (!data.name || !data.phone) {
        msg.className = "form-msg err";
        msg.textContent = "Please enter your name and phone number.";
        msg.scrollIntoView({ behavior: "smooth", block: "center" });
        return;
      }
      btn.disabled = true; btn.style.opacity = .7; const orig = btn.innerHTML; btn.innerHTML = "Sending…";
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
          msg.textContent = json.error || "Something went wrong. Please try again.";
        }
      } catch {
        msg.className = "form-msg err";
        msg.textContent = "Network error — please call or WhatsApp us instead.";
      } finally {
        btn.disabled = false; btn.style.opacity = 1; btn.innerHTML = orig;
        msg.scrollIntoView({ behavior: "smooth", block: "center" });
      }
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("contactForm");
    if (form) wire(form);
  });
})();
