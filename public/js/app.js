/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

document.addEventListener("DOMContentLoaded", () => {
  const menuButton = document.getElementById("menu-button");
  const menuContainer = document.getElementById("menu-container");
  const body = document.body;
  const menuIcon = document.getElementById("menu-icon");
  const links = document.querySelectorAll(".links a");

  if (!menuButton || !menuContainer) {
    console.error(
      "Les éléments #menu-button ou #menu-container n'ont pas été trouvés."
    );
    return;
  }

  const toggleMenu = (state) => {
    const isOpen = state === "open";
    menuContainer.style.display = isOpen ? "block" : "none";
    body.style.overflow = isOpen ? "hidden" : "inherit";
    menuIcon.classList.toggle("open", isOpen);
  };

  menuButton.addEventListener("click", () => {
    const isMenuVisible = menuContainer.style.display === "block";
    toggleMenu(isMenuVisible ? "close" : "open");
  });

  links.forEach((link) =>
    link.addEventListener("click", () => toggleMenu("close"))
  );
});
