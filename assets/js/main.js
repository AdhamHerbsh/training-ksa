(function () {
  "use strict";

  /**
   * Animation on scroll
   */
  window.addEventListener("load", () => {
    AOS.init({
      duration: 1200,
      easing: "ease-in-out",
      once: true,
      mirror: false,
    });
  });
  /**
   * Preloader
   */
  window.addEventListener("load", function () {
    var preloader = document.getElementById("preloader");
    if (preloader) {
      preloader.style.display = "none";
    }
  });

  /**
   * Navigation Menu
   */
  document.addEventListener("DOMContentLoaded", function () {
    var navmenu = document.getElementById("navmenu");
    var openBtn = document.getElementById("openNavmenu");
    var closeBtn = document.getElementById("closeNavmenu");

    if (openBtn && navmenu) {
      openBtn.addEventListener("click", function () {
        navmenu.style.display = "block";
      });
    }
    if (closeBtn && navmenu) {
      closeBtn.addEventListener("click", function () {
        navmenu.style.display = "none";
      });
    }
  });
  /**
   * Translation
   */
  // Language and translation logic
  function detectBrowserLang() {
    const lang = navigator.language || navigator.userLanguage || "en";
    return lang.toLowerCase().startsWith("ar") ? "ar" : "en";
  }

  function getSavedLang() {
    return localStorage.getItem("siteLang") || detectBrowserLang();
  }

  function getSavedDir() {
    return (
      localStorage.getItem("siteDir") ||
      (getSavedLang() === "ar" ? "rtl" : "ltr")
    );
  }

  function setLanguage(lang) {
    fetch(`core/lang/${lang}.json`)
      .then((res) => {
        if (!res.ok) throw new Error("Translation file not found");
        return res.json();
      })
      .then((data) => {
        applyTranslations(data);
        document.documentElement.lang = lang;
        document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";
        localStorage.setItem("siteLang", lang);
        localStorage.setItem("siteDir", lang === "ar" ? "rtl" : "ltr");
      })
      .catch((err) => {
        console.warn("Translation error:", err);
      });
  }

  function applyTranslations(data) {
    document.querySelectorAll("[data-i18n]").forEach((el) => {
      const key = el.getAttribute("data-i18n");
      const value = getNested(data, key);
      if (value !== undefined && value !== null) {
        el.textContent = value;
      }
    });
  }

  function getNested(obj, path) {
    return path
      .split(".")
      .reduce((o, i) => (o && o[i] !== undefined ? o[i] : undefined), obj);
  }

  // Restore dir and lang from localStorage on page load
  var html = document.documentElement;
  html.lang = getSavedLang();
  html.dir = getSavedDir();
  setLanguage(html.lang);

  var globeBtn = document.getElementById("globeBtn");
  if (globeBtn) {
    globeBtn.addEventListener("click", function () {
      var newLang = html.lang === "ar" ? "en" : "ar";
      var newDir = newLang === "ar" ? "rtl" : "ltr";
      html.lang = newLang;
      html.dir = newDir;
      localStorage.setItem("siteLang", newLang);
      localStorage.setItem("siteDir", newDir);
      setLanguage(newLang);
    });
  }

  /**
   * Filter and Search Facilities
   * This script handles filtering and searching of facilities based on type and search input.
   */
  document.addEventListener("DOMContentLoaded", function () {
    const filterBtns = document.querySelectorAll(".filter-btn");
    const searchInput = document.getElementById("searchFacilities");
    const facilities = document.querySelectorAll(".facility-item");

    // Filter button click handler
    filterBtns.forEach((btn) => {
      btn.addEventListener("click", function () {
        // Remove active class from all buttons
        filterBtns.forEach((b) => b.classList.remove("active"));
        // Add active class to clicked button
        this.classList.add("active");

        const filterValue = this.getAttribute("data-filter");

        facilities.forEach((facility) => {
          if (
            filterValue === "all" ||
            facility.getAttribute("data-type") === filterValue
          ) {
            facility.style.display = "";
          } else {
            facility.style.display = "none";
          }
        });

        // Reapply search filter
        applySearch();
      });
    });

    // Search input handler
    searchInput.addEventListener("input", applySearch);

    function applySearch() {
      const searchText = searchInput.value.toLowerCase();
      const activeFilter = document
        .querySelector(".filter-btn.active")
        .getAttribute("data-filter");

      facilities.forEach((facility) => {
        const facilityType = facility.getAttribute("data-type");
        const facilityText = facility.textContent.toLowerCase();
        const matchesSearch = facilityText.includes(searchText);
        const matchesFilter =
          activeFilter === "all" || facilityType === activeFilter;

        facility.style.display = matchesSearch && matchesFilter ? "" : "none";
      });
    }
  });

  /**
   * Toggle Password Visibility
   * This script toggles the visibility of the password input field when the eye icon is clicked.
   */
  const togglePassword = document.getElementById("togglePassword");
  if (togglePassword) {
    togglePassword.addEventListener("click", function (e) {
      e.preventDefault();
      const passwordInput = document.getElementById("floatingPassword");
      const icon = this.querySelector("i");

      // Toggle the password visibility
      const type =
        passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);

      // Toggle the icon with a smooth transition
      icon.style.transition = "opacity 0.2s ease-in-out";
      icon.style.opacity = "0";

      setTimeout(() => {
        icon.classList.toggle("bi-eye");
        icon.classList.toggle("bi-eye-slash");
        icon.style.opacity = "1";
      }, 100);

      // Add focus back to password input
      passwordInput.focus();
    });

    // Add hover effect
    togglePassword.addEventListener("mouseover", function () {
      this.querySelector("i").style.opacity = "0.8";
    });

    togglePassword.addEventListener("mouseout", function () {
      this.querySelector("i").style.opacity = "1";
    });
  }

  /**
   * Trainee Tables Functionality
   * This script handles searching, sorting, and other interactions for trainee tables
   */
  document.addEventListener("DOMContentLoaded", function () {
    // Search functionality for trainee tables
    function initializeTraineeTable(searchInputId, tableId, emptyStateId) {
      const searchInput = document.getElementById(searchInputId);
      const tableBody = document.getElementById(tableId);
      const emptyState = document.getElementById(emptyStateId);

      if (searchInput && tableBody) {
        searchInput.addEventListener("input", function () {
          const searchTerm = this.value.toLowerCase();
          const rows = tableBody.getElementsByTagName("tr");
          let hasVisibleRows = false;

          Array.from(rows).forEach((row) => {
            const text = row.textContent.toLowerCase();
            const isVisible = text.includes(searchTerm);
            row.style.display = isVisible ? "" : "none";
            if (isVisible) hasVisibleRows = true;
          });

          // Toggle empty state
          if (emptyState) {
            emptyState.classList.toggle("d-none", hasVisibleRows);
          }
        });
      }
    }

    // Initialize sorting functionality for trainee tables
    function initializeTableSorting(table) {
      if (!table) return;

      const getCellValue = (tr, idx) =>
        tr.children[idx].innerText || tr.children[idx].textContent;

      const comparer = (idx, asc) => (a, b) =>
        ((v1, v2) =>
          v1 !== "" && v2 !== "" && !isNaN(v1) && !isNaN(v2)
            ? v1 - v2
            : v1.toString().localeCompare(v2))(
          getCellValue(asc ? a : b, idx),
          getCellValue(asc ? b : a, idx)
        );

      table.querySelectorAll("th i[data-sort]").forEach((th) => {
        th.addEventListener("click", () => {
          const tbody = table.querySelector("tbody");
          const rows = Array.from(tbody.querySelectorAll("tr"));
          const columnIndex = th.closest("th").cellIndex;

          // Toggle sort direction
          th.classList.toggle("bi-arrow-down");
          th.classList.toggle("bi-arrow-up");
          const isAscending = th.classList.contains("bi-arrow-up");

          // Sort the rows
          rows.sort(comparer(columnIndex, isAscending));

          // Remove rows and re-add in sorted order
          rows.forEach((row) => tbody.removeChild(row));
          rows.forEach((row) => tbody.appendChild(row));
        });
      });
    }

    // Initialize for Accepted Trainees table
    initializeTraineeTable("searchTrainees", "confirmedTable", "emptyState");

    // Initialize for Rejected Trainees table
    initializeTraineeTable(
      "searchRejectedTrainees",
      "rejectedTable",
      "emptyRejectedState"
    );

    // Initialize sorting for all trainee tables
    document.querySelectorAll(".trainee-table").forEach((table) => {
      initializeTableSorting(table);
    });

    // Add hover effect to action buttons
    document.querySelectorAll(".btn-group .btn").forEach((btn) => {
      btn.addEventListener("mouseover", function () {
        this.querySelector("i")?.classList.add("pulse");
      });

      btn.addEventListener("mouseout", function () {
        this.querySelector("i")?.classList.remove("pulse");
      });
    });
  });

  /**
   * Training Requests Functionality
   * This script handles the training requests table functionality
   */
  document.addEventListener("DOMContentLoaded", function () {
    // Training Requests Table Functionality
    const selectAllCheckbox = document.getElementById("selectAll");
    const studentCheckboxes = document.querySelectorAll(".studentCheckbox");
    const searchInput = document.getElementById("searchRequests");
    const bulkAcceptBtn = document.getElementById("bulkAccept");
    const bulkRejectBtn = document.getElementById("bulkReject");
    const table = document.querySelector(".requests-table");
    const emptyState = document.getElementById("emptyRequestsState");

    // Select All functionality
    if (selectAllCheckbox) {
      selectAllCheckbox.addEventListener("change", function () {
        const isChecked = this.checked;
        studentCheckboxes.forEach((checkbox) => {
          const row = checkbox.closest("tr");
          if (row.style.display !== "none") {
            // Only check visible rows
            checkbox.checked = isChecked;
          }
        });
        updateBulkActionButtons();
      });
    }

    // Individual checkbox functionality
    studentCheckboxes.forEach((checkbox) => {
      checkbox.addEventListener("change", updateBulkActionButtons);
    });

    // Search functionality
    if (searchInput) {
      searchInput.addEventListener("input", function () {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll("#studentsTableBody tr");
        let hasVisibleRows = false;

        rows.forEach((row) => {
          const text = row.textContent.toLowerCase();
          const isVisible = text.includes(searchTerm);
          row.style.display = isVisible ? "" : "none";
          if (isVisible) hasVisibleRows = true;
        });

        // Update select all checkbox
        if (selectAllCheckbox) {
          selectAllCheckbox.checked = false;
        }
        updateBulkActionButtons();

        // Toggle empty state
        if (emptyState) {
          emptyState.classList.toggle("d-none", hasVisibleRows);
        }
      });
    }

    // Bulk action buttons functionality
    function updateBulkActionButtons() {
      const checkedBoxes = document.querySelectorAll(
        ".studentCheckbox:checked"
      );
      const hasChecked = checkedBoxes.length > 0;

      if (bulkAcceptBtn) {
        bulkAcceptBtn.disabled = !hasChecked;
        bulkAcceptBtn.classList.toggle("btn-success", hasChecked);
        bulkAcceptBtn.classList.toggle("btn-secondary", !hasChecked);
      }

      if (bulkRejectBtn) {
        bulkRejectBtn.disabled = !hasChecked;
        bulkRejectBtn.classList.toggle("btn-danger", hasChecked);
        bulkRejectBtn.classList.toggle("btn-secondary", !hasChecked);
      }
    }

    // Initialize bulk action buttons state
    updateBulkActionButtons();

    // View Document function
    window.viewDocument = function (type) {
      // Implement document viewer functionality
      console.log(`Viewing ${type} document`);
    };

    // Initialize sorting for the table
    if (table) {
      initializeTableSorting(table);
    }

    // Add hover effects to action buttons
    document.querySelectorAll(".btn-group .btn").forEach((btn) => {
      btn.addEventListener("mouseover", function () {
        this.querySelector("i")?.classList.add("pulse");
      });

      btn.addEventListener("mouseout", function () {
        this.querySelector("i")?.classList.remove("pulse");
      });
    });
  });

  /**
   * Clearance Form Functionality
   * This script handles the clearance form responses table functionality
   */
  document.addEventListener("DOMContentLoaded", function () {
    const adminFilter = document.getElementById("adminFilter");
    const searchInput = document.getElementById("searchClearance");
    const table = document.getElementById("responsesTable");
    const emptyState = document.getElementById("emptyClearanceState");

    if (adminFilter && table) {
      // Department filter functionality
      adminFilter.addEventListener("change", function () {
        applyFilters();
      });

      // Search functionality
      if (searchInput) {
        searchInput.addEventListener("input", function () {
          applyFilters();
        });
      }

      // Initialize sorting
      initializeTableSorting(table);

      function applyFilters() {
        const searchTerm = (searchInput?.value || "").toLowerCase();
        const selectedDepartment = adminFilter.value;
        const rows = table.querySelectorAll("tbody tr");
        let hasVisibleRows = false;

        rows.forEach((row) => {
          const text = row.textContent.toLowerCase();
          const department = row.querySelector("td:last-child").textContent;
          const matchesSearch = text.includes(searchTerm);
          const matchesDepartment =
            selectedDepartment === "all" || department === selectedDepartment;

          const isVisible = matchesSearch && matchesDepartment;
          row.style.display = isVisible ? "" : "none";
          if (isVisible) hasVisibleRows = true;
        });

        // Toggle empty state
        if (emptyState) {
          emptyState.classList.toggle("d-none", hasVisibleRows);
        }
      }
    }
  });
})();
