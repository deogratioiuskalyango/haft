(function () {
  var PESAPAL_STORE_EMBED_SRC =
    "https://store.pesapal.com/embed-code?pageUrl=https://store.pesapal.com/haftdonations";

  var form = document.getElementById("wf-form-Donation-Payment-Form");
  var wrap = document.getElementById("haft-pesapal-frame-wrap");
  var iframe = document.getElementById("haft-pesapal-embed-iframe");
  if (!form || !wrap) return;

  function readField(id) {
    var el = document.getElementById(id);
    return el ? String(el.value || "").trim() : "";
  }

  function openPesapalStoreEmbed() {
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

    if (iframe && !iframe.getAttribute("src")) {
      iframe.setAttribute("src", PESAPAL_STORE_EMBED_SRC);
    }
    wrap.style.display = "block";
    wrap.scrollIntoView({ behavior: "smooth", block: "start" });
  }

  form.addEventListener(
    "submit",
    function (e) {
      e.preventDefault();
      e.stopImmediatePropagation();
      openPesapalStoreEmbed();
    },
    true,
  );
})();
