(function () {
  document.querySelectorAll("[data-donation-amount]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var amt = btn.getAttribute("data-donation-amount");
      var input = document.getElementById("Donation-Amount");
      if (input && amt) {
        input.value = amt;
        input.focus();
      }
      var anchor = document.getElementById("donation-form-anchor");
      if (anchor) {
        anchor.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    });
  });
})();
