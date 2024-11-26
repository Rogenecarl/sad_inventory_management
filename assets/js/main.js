/*=============== SHOW SIDEBAR ===============*/
const showSidebar = (toggleId, sidebarId, headerId, mainId) => {
  const toggle = document.getElementById(toggleId),
    sidebar = document.getElementById(sidebarId),
    header = document.getElementById(headerId),
    main = document.getElementById(mainId);

  if (toggle && sidebar && header && main) {
    toggle.addEventListener("click", () => {
      /* Show sidebar */
      sidebar.classList.toggle("show-sidebar");
      /* Add padding header */
      header.classList.toggle("left-pd");
      /* Add padding main */
      main.classList.toggle("left-pd");
    });
  }
};
showSidebar("header-toggle", "sidebar", "header", "main");

/*=============== LINK ACTIVE ===============*/
const sidebarLink = document.querySelectorAll(".sidebar__list a");

function linkColor() {
  sidebarLink.forEach((l) => l.classList.remove("active-link"));
  this.classList.add("active-link");
}

sidebarLink.forEach((l) => l.addEventListener("click", linkColor));

/*=============== DARK LIGHT THEME ===============*/
const themeButton = document.getElementById("theme-button");
const darkTheme = "dark-theme";
const iconTheme = "ri-sun-fill";

// Previously selected topic (if user selected)
const selectedTheme = localStorage.getItem("selected-theme");
const selectedIcon = localStorage.getItem("selected-icon");

// We obtain the current theme that the interface has by validating the dark-theme class
const getCurrentTheme = () =>
  document.body.classList.contains(darkTheme) ? "dark" : "light";
const getCurrentIcon = () =>
  themeButton.classList.contains(iconTheme)
    ? "ri-moon-clear-fill"
    : "ri-sun-fill";

// We validate if the user previously chose a topic
if (selectedTheme) {
  // If the validation is fulfilled, we ask what the issue was to know if we activated or deactivated the dark
  document.body.classList[selectedTheme === "dark" ? "add" : "remove"](
    darkTheme
  );
  themeButton.classList[
    selectedIcon === "ri-moon-clear-fill" ? "add" : "remove"
  ](iconTheme);
}

// Activate / deactivate the theme manually with the button
themeButton.addEventListener("click", () => {
  // Add or remove the dark / icon theme
  document.body.classList.toggle(darkTheme);
  themeButton.classList.toggle(iconTheme);
  // We save the theme and the current icon that the user chose
  localStorage.setItem("selected-theme", getCurrentTheme());
  localStorage.setItem("selected-icon", getCurrentIcon());
});

/*=============== TOGGLE DROPDOWN MENU ===============*/
const sidebarLinks = document.querySelectorAll(".sidebar__link");

sidebarLinks.forEach((link) => {
  const submenu = link.nextElementSibling;

  if (submenu && submenu.classList.contains("sidebar__submenu")) {
    link.addEventListener("click", (event) => {
      event.preventDefault();
      if (submenu.classList.contains("show-submenu")) {
        submenu.classList.remove("show-submenu");
        localStorage.removeItem("submenuOpen");
      } else {
        document.querySelectorAll(".sidebar__submenu").forEach((menu) => menu.classList.remove("show-submenu"));
        submenu.classList.add("show-submenu");
        localStorage.setItem("submenuOpen", submenu.id);

        // Add active link to the submenu link
        const submenuLinks = submenu.querySelectorAll(".sidebar__link");
        submenuLinks.forEach((submenuLink) => {
          if (submenuLink.href.includes("<?php echo $currentPage; ?>")) {
            submenuLink.classList.add("active-link");
          }
        });
      }
    });

    // Make href function work
    link.addEventListener("click", (event) => {
      if (link.href !== "#" && !submenu) {
        window.location.href = link.href;
      }
    });

    // Keep submenu open when clicking on a submenu link
    submenu.addEventListener("click", (event) => {
      if (event.target.classList.contains("sidebar__link")) {
        const submenuLinks = submenu.querySelectorAll(".sidebar__link");
        submenuLinks.forEach((submenuLink) => {
          if (submenuLink.href.includes("<?php echo $currentPage; ?>")) {
            submenuLink.classList.add("active-link");
          }
        });
        // Do not close submenu when submenu link is clicked
        event.target.parentNode.classList.add("keep-open");
        // Prevent page from reloading and closing submenu
        event.preventDefault();
      }
    });
  } else {
    link.addEventListener("click", (event) => {
      if (link.href !== "#") {
        // Check if submenu is open before reloading page
        if (!document.querySelector(".sidebar__submenu.show-submenu")) {
          window.location.href = link.href;
        }
      }
    });
  }
});

// Add this code to keep the submenu open when clicking on a submenu page
document.addEventListener("DOMContentLoaded", function() {
  const submenuOpen = localStorage.getItem("submenuOpen");

  if (submenuOpen) {
    const submenu = document.getElementById(submenuOpen);
    submenu.classList.add("show-submenu");

    // Add active link to the submenu link
    const submenuLinks = submenu.querySelectorAll(".sidebar__link");
    submenuLinks.forEach((submenuLink) => {
      if (submenuLink.href.includes("<?php echo $currentPage; ?>")) {
        submenuLink.classList.add("active-link");
      }
    });
  }
});