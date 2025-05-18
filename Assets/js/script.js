// Confirmation avant action sensible (suspension / réactivation)
document.addEventListener("DOMContentLoaded", () => {
  const links = document.querySelectorAll("a.confirm-action");

  links.forEach(link => {
    link.addEventListener("click", (e) => {
      const message = link.getAttribute("data-confirm") || "Es-tu sûr(e) ?";
      if (!confirm(message)) {
        e.preventDefault();
      }
    });
  });
});