window.addEventListener("load", function () {
  // Ensure scroll starts at top
  window.history.scrollRestoration = "manual";
  window.scrollTo(0, 0);
});


function setupToggle(toggleId, inputId) {
  const toggle = document.getElementById(toggleId);
  const input = document.getElementById(inputId);

  if (toggle && input) {
    toggle.addEventListener("click", function () {
      const type =
        input.getAttribute("type") === "password" ? "text" : "password";
      input.setAttribute("type", type);
      toggle.classList.toggle("fa-eye");
      toggle.classList.toggle("fa-eye-slash");
    });
  }
}

setupToggle("togglePass", "pass");
setupToggle("toggleConfirmPass", "confirmPass");

