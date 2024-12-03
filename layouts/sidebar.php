<!--=============== SIDEBAR ===============-->
<nav class="sidebar" id="sidebar">
  <div class="sidebar__container">
    <div class="sidebar__user">
      <div class="sidebar__img">
        <img src="../uploads/users/techno.png" alt="" />
      </div>

      <div class="sidebar__info">
        <h3>Hello, Dev</h3>
        <span>TechNo Core</span>
      </div>
    </div>

    <div class="sidebar__content">
      <!-- Main Management Section -->
      <div>
        <h3 class="sidebar__title">MANAGE</h3>
        <div class="sidebar__list">
          <a href="dev_dashboard.php"
            class="sidebar__link <?= ($currentPage == 'dev_dashboard') ? 'active-link' : '' ?>">
            <i class="ri-dashboard-line"></i>
            <span>Dashboard</span>
          </a>

          <a href="user_management.php"
            class="sidebar__link <?= ($currentPage == 'user_management') ? 'active-link' : '' ?>">
            <i class="ri-user-settings-fill"></i>
            <span>User Management</span>
          </a>

          <a href="category.php" class="sidebar__link <?= ($currentPage == 'category') ? 'active-link' : '' ?>">
            <i class="ri-user-settings-fill"></i>
            <span>Category</span>
          </a>

          <!-- Inventory -->
          <a href="#" class="sidebar__link" id="inventory-link"
            style="display: flex; align-items: start; justify-content: start;">
            <i class="ri-git-repository-fill"></i>
            <span>Inventory</span>
            <i class="ri-arrow-down-s-line" style="margin-left: auto; padding-right: 20px"></i>
          </a>
          <div class="sidebar__submenu" id="inventory-submenu">
            <a href="products.php" class="sidebar__link <?= ($currentPage == 'products') ? 'active-link' : '' ?>">
              <i class="ri-file-list-2-fill"></i>
              <span>Products</span>
            </a>
            <a href="print_products.php"
              class="sidebar__link <?= ($currentPage == 'print_products') ? 'active-link' : '' ?>">
              <i class="ri-folder-2-fill"></i>
              <span>Print Products</span>
            </a>
          </div>

          <!-- Sales -->
          <a href="#" class="sidebar__link" id="sales-link"
            style="display: flex; align-items: start; justify-content: start;">
            <i class="ri-truck-fill"></i>
            <span>Sales</span>
            <i class="ri-arrow-down-s-line" style="margin-left: auto; padding-right: 20px"></i>
          </a>
          <div class="sidebar__submenu" id="sales-submenu">
            <a href="manage_sales.php"
              class="sidebar__link <?= ($currentPage == 'manage_sales') ? 'active-link' : '' ?>">
              <i class="ri-file-list-2-fill"></i>
              <span>Manage Sales</span>
            </a>
            <a href="add_sales.php" class="sidebar__link <?= ($currentPage == 'add_sales') ? 'active-link' : '' ?>">
              <i class="ri-folder-2-fill"></i>
              <span>Add Sales</span>
            </a>
          </div>

          <!-- Sales Report -->
          <a href="#" class="sidebar__link" id="reports-link"
            style="display: flex; align-items: start; justify-content: start;">
            <i class="ri-file-list-2-fill"></i>
            <span>Sales Report</span>
            <i class="ri-arrow-down-s-line" style="margin-left: auto; padding-right: 20px"></i>
          </a>
          <div class="sidebar__submenu" id="reports-submenu">
            <a href="salesby_dates.php"
              class="sidebar__link <?= ($currentPage == 'salesby_dates') ? 'active-link' : '' ?>">
              <i class="ri-file-list-2-fill"></i>
              <span>Sales by Dates</span>
            </a>
            <a href="monthly_sales.php"
              class="sidebar__link <?= ($currentPage == 'monthly_sales') ? 'active-link' : '' ?>">
              <i class="ri-folder-2-fill"></i>
              <span>Monthly Sales</span>
            </a>
            <a href="daily_sales.php" class="sidebar__link <?= ($currentPage == 'daily_sales') ? 'active-link' : '' ?>">
              <i class="ri-folder-2-fill"></i>
              <span>Daily Sales</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Settings and Configuration Section -->
      <div>
        <h3 class="sidebar__title">SETTINGS</h3>
        <div class="sidebar__list">
          <a href="#" class="sidebar__link">
            <i class="ri-key-fill"></i>
            <span>Account Settings</span>
          </a>
        </div>
      </div>
    </div>

    <!-- Theme Toggle and Logout -->
    <div class="sidebar__actions">
      <button>
        <i class="ri-moon-clear-fill sidebar__link sidebar__theme" id="theme-button">
          <span>Theme</span>
        </i>
      </button>

        <a href="../logout.php" class="sidebar__link">
          <i class="ri-logout-box-r-fill"></i>
          <span>Log Out</span>
        </a>

    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>