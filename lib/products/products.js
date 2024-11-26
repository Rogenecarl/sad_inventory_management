const tableBody = document.querySelector("table tbody");
const rows = Array.from(tableBody.querySelectorAll("tr"));
const rowsPerPage = 10;
let currentPage = 1;
let totalPages = Math.ceil(rows.length / rowsPerPage);

function showPage(page) {
  const start = (page - 1) * rowsPerPage;
  const end = start + rowsPerPage;
  rows.forEach((row, index) => {
    if (index >= start && index < end) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });

  const prevPage = page - 1;
  const nextPage = page + 1;

  const prevButton = document.querySelector(".prev-page");
  const nextButton = document.querySelector(".next-page");

  if (prevPage === 0) {
    prevButton.disabled = true;
  } else {
    prevButton.disabled = false;
    prevButton.textContent = `Previous ${prevPage}`;
  }

  if (nextPage > totalPages) {
    nextButton.disabled = true;
  } else {
    nextButton.disabled = false;
    nextButton.textContent = `Next ${nextPage}`;
  }

  const totalUsers = rows.length;
  const currentPageUsers = rows.slice(start, end).length;
  const totalUsersText = `Showing ${currentPageUsers} out of ${totalUsers} Products`;
  document.querySelector(".total-users").textContent = totalUsersText;

  const paginationLinks = document.querySelectorAll(".page-link");
  paginationLinks.forEach((link) => {
    if (link.getAttribute("data-page") == page) {
      link.classList.add("active");
    } else {
      link.classList.remove("active");
    }
  });
}

showPage(currentPage);

document.querySelector(".prev-page").addEventListener("click", () => {
  if (currentPage > 1) {
    currentPage--;
    showPage(currentPage);
  }
});

document.querySelector(".next-page").addEventListener("click", () => {
  if (currentPage < totalPages) {
    currentPage++;
    showPage(currentPage);
  }
});

document.querySelectorAll(".page-link").forEach((link) => {
  link.addEventListener("click", (e) => {
    e.preventDefault();
    const page = link.getAttribute("data-page");
    currentPage = parseInt(page);
    showPage(currentPage);
  });
});
