(function () {
  var form = document.getElementById("wf-form-Donation-Payment-Form");
  var wrap = document.getElementById("haft-pesapal-frame-wrap");
  var iframe = document.getElementById("haft-pesapal-iframe");
  if (!form || !wrap || !iframe) return;

  var submitBtn =
    document.getElementById("haft-donation-submit") ||
    form.querySelector('input[type="submit"]');

  function readField(id) {
    var el = document.getElementById(id);
    return el ? String(el.value || "").trim() : "";
  }

  function runPesapalCheckout() {
    var amount = readField("Donation-Amount");
    var first = readField("Donation-First-Name");
    var last = readField("Donation-Last-Name");
    var email = readField("Donation-Email");
    if (!amount || !first || !last || !email) {
      window.alert(
        "Please fill in amount, first name, last name, and email to continue to secure payment.",
      );
      return;
    }
    var chk = document.getElementById("Donation-Form-Checkbox");
    if (chk && !chk.checked) {
      window.alert("Please confirm the donation checkbox to continue.");
      return;
    }

    var donationType = "";
    var radio = document.querySelector('input[name="Donation-Radio"]:checked');
    if (radio) donationType = radio.value || "";

    if (submitBtn) submitBtn.disabled = true;
    fetch("/api/pesapal/init", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        amount: amount,
        firstName: first,
        lastName: last,
        email: email,
        phone: readField("Donation-Phone-Pesapal") || undefined,
        donationType: donationType,
      }),
    })
      .then(function (r) {
        return r.json().then(function (data) {
          if (!r.ok) throw new Error(data.error || r.statusText || "Request failed");
          return data;
        });
      })
      .then(function (data) {
        iframe.src = data.iframeUrl;
        wrap.style.display = "block";
        wrap.scrollIntoView({ behavior: "smooth", block: "start" });
      })
      .catch(function (e) {
        window.alert(e.message || "Could not start payment. Try again later.");
      })
      .finally(function () {
        if (submitBtn) submitBtn.disabled = false;
      });
  }

  form.addEventListener(
    "submit",
    function (e) {
      e.preventDefault();
      e.stopImmediatePropagation();
      runPesapalCheckout();
    },
    true,
  );
})();
