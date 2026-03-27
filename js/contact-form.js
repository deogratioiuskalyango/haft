/**
 * Sends the contact form to HAFT_CONTACT_API (default /api/contact) via JSON.
 * Expects Express + Resend on POST /api/contact (server.js), or any compatible handler.
 */
(function () {
  var form = document.getElementById("wf-form-Contact-Email-form");
  if (!form || form.getAttribute("data-haft-contact") !== "1") return;

  var block = form.closest(".w-form");
  var done = block && block.querySelector(".w-form-done");
  var fail = block && block.querySelector(".w-form-fail");
  var submitBtn = form.querySelector('input[type="submit"], button[type="submit"]');

  var apiBase =
    (typeof window.HAFT_CONTACT_API === "string" && window.HAFT_CONTACT_API) || "/api/contact";

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    if (done) done.style.display = "none";
    if (fail) fail.style.display = "none";

    var hp = form.querySelector('input[name="Contact-Company"]');
    if (hp && hp.value && String(hp.value).trim() !== "") {
      if (done) done.style.display = "block";
      form.reset();
      return;
    }

    var fd = new FormData(form);
    var payload = {
      name: String(fd.get("Contact-Name") || "").trim(),
      email: String(fd.get("Contact-Email") || "").trim(),
      phone: String(fd.get("Contact-Phone") || "").trim(),
      subject: String(fd.get("Contact-Subject") || "").trim(),
      message: String(fd.get("Contact-Field") || "").trim(),
    };

    if (submitBtn) {
      submitBtn.disabled = true;
      var wait = submitBtn.getAttribute("data-wait") || "Please wait...";
      if (submitBtn.tagName === "INPUT") {
        submitBtn.dataset.prevValue = submitBtn.value;
        submitBtn.value = wait;
      } else {
        submitBtn.dataset.prevText = submitBtn.textContent;
        submitBtn.textContent = wait;
      }
    }

    fetch(apiBase.replace(/\/$/, ""), {
      method: "POST",
      headers: { "Content-Type": "application/json", Accept: "application/json" },
      body: JSON.stringify(payload),
    })
      .then(function (r) {
        return r.json().then(function (body) {
          return { ok: r.ok, status: r.status, body: body || {} };
        });
      })
      .then(function (res) {
        if (res.ok && res.body.ok) {
          form.reset();
          if (done) done.style.display = "block";
        } else {
          if (fail) {
            var msg = res.body.error || "Something went wrong. Please try again or email us directly.";
            var div = fail.querySelector("div");
            if (div) div.textContent = msg;
            fail.style.display = "block";
          }
        }
      })
      .catch(function () {
        if (fail) {
          var div = fail.querySelector("div");
          if (div)
            div.textContent =
              "Could not reach the server. If this persists, email info@haft-ug.com directly.";
          fail.style.display = "block";
        }
      })
      .finally(function () {
        if (submitBtn) {
          submitBtn.disabled = false;
          if (submitBtn.tagName === "INPUT" && submitBtn.dataset.prevValue != null) {
            submitBtn.value = submitBtn.dataset.prevValue;
          } else if (submitBtn.dataset.prevText != null) {
            submitBtn.textContent = submitBtn.dataset.prevText;
          }
        }
      });
  });
})();
