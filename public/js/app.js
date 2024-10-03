/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

document.addEventListener("DOMContentLoaded", () => {
  // MENU
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

  // VOIR PLUS (VOITURES)
  const btn_loadmore = document.getElementById("load-more");
  if (btn_loadmore) {
    btn_loadmore.addEventListener("click", function () {
      const offset = document.querySelectorAll(".card-voiture").length;

      fetch("/voitures/load-more?offset=" + offset)
        .then((response) => response.json())
        .then((data) => {
          if (!data.hasMore) {
            document.getElementById("load-more").style.display = "none";
          }

          const voitureList = document.getElementById("voiture-list");

          data.voitures.forEach((voiture) => {
            const voitureCard = `
            <a href="/reservation/resa/${voiture.id}" class="col-12 col-md-4 card-voiture">
                <div class="card-title">
                    <p>${voiture.marque}</p>
                    <p>${voiture.modele}</p>
                </div>
                <div class="card-img">
                    <img src="${voiture.image}" alt="Voiture image" class="img-fluid">
                </div>
                <div class="card-infos">
                    <p>${voiture.annee}</p>
                    <p>${voiture.couleur}</p>
                    <p>${voiture.boite}</p>
                    <p>${voiture.carburant}</p>
                </div>
                <div class="card-price">
                    <p>${voiture.prix}€ / Jours</p>
                </div>
            </a>`;
            voitureList.insertAdjacentHTML("beforeend", voitureCard);
          });
        })
        .catch((error) => console.log("Error:", error));
    });
  }

  // AJAX VALIDER RESERVATION
});
