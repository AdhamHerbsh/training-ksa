(function () {
  "use strict";

  /**
   * Centralized Smart Search Utility
   * Supports case-insensitive, normalized, fuzzy, and multilingual search
   */
  function normalizeText(text) {
    if (!text) return "";
    return text
      .toString()
      .normalize("NFD")
      .replace(/\p{Diacritic}/gu, "")
      .replace(/[\u064B-\u0652]/g, "") // Arabic diacritics
      .replace(/[إأآ]/g, "ا") // Normalize Arabic Alef
      .replace(/[ة]/g, "ه") // Normalize Ta Marbuta
      .replace(/[يى]/g, "ي") // Normalize Ya
      .replace(/[ؤئ]/g, "ء") // Normalize Hamza
      .replace(/\s+/g, " ")
      .trim()
      .toLowerCase();
  }

  function smartSearch({
    data,
    keys,
    query,
    limit = 50,
    fuzzy = true,
    nested = false,
  }) {
    if (!query || !data || !keys) return data;
    const normQuery = normalizeText(query);
    const tokens = normQuery.split(" ").filter(Boolean);
    let results = [];
    for (const item of data) {
      let text = keys
        .map((key) => {
          if (nested && key.includes(".")) {
            return key.split(".").reduce((o, k) => (o ? o[k] : ""), item);
          }
          return item[key];
        })
        .join(" ");
      const normText = normalizeText(text);
      let score = 0;
      if (normText.includes(normQuery)) score += 10;
      if (fuzzy) {
        for (const token of tokens) {
          if (normText.includes(token)) score += 2;
        }
      }
      if (score > 0) results.push({ item, score });
    }
    results.sort((a, b) => b.score - a.score);
    return results.slice(0, limit).map((r) => r.item);
  }

  function debounce(fn, delay) {
    let timer;
    return function (...args) {
      clearTimeout(timer);
      timer = setTimeout(() => fn.apply(this, args), delay);
    };
  }
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
    const facilities = Array.from(document.querySelectorAll(".facility-item"));

    function getFacilityData() {
      return facilities.map((el) => ({
        el,
        name: el.querySelector("h3")?.textContent || "",
        region: el.querySelector(".lead")?.textContent || "",
        governorate: el.querySelector(".badge")?.textContent || "",
        email: el.querySelector("a[href^='mailto']")?.textContent || "",
      }));
    }

    function renderFacilities(filtered) {
      facilities.forEach((el) => (el.style.display = "none"));
      filtered.forEach((f) => (f.el.style.display = ""));
    }

    function applyFacilitySearch() {
      const searchText = searchInput.value;
      const activeFilter =
        document
          .querySelector(".filter-btn.active")
          ?.getAttribute("data-filter") || "all";
      let data = getFacilityData();
      if (activeFilter !== "all") {
        data = data.filter((f) => f.el.dataset.type === activeFilter);
      }
      const results = smartSearch({
        data,
        keys: ["name", "region", "governorate", "email"],
        query: searchText,
        limit: 100,
      });
      renderFacilities(results);
    }

    filterBtns.forEach((btn) => {
      btn.addEventListener("click", function () {
        filterBtns.forEach((b) => b.classList.remove("active"));
        btn.classList.add("active");
        applyFacilitySearch();
      });
    });

    searchInput.addEventListener("input", debounce(applyFacilitySearch, 200));
    applyFacilitySearch();
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
    function getTableData(tableBody) {
      return Array.from(tableBody.querySelectorAll("tr")).map((row) => {
        const cells = row.querySelectorAll("td, th");
        return {
          row,
          name: cells[1]?.textContent || cells[0]?.textContent || "",
          studentId: cells[2]?.textContent || "",
          major: cells[3]?.textContent || "",
          email: cells[4]?.textContent || "",
        };
      });
    }

    function renderTableRows(tableBody, filtered) {
      Array.from(tableBody.querySelectorAll("tr")).forEach(
        (row) => (row.style.display = "none")
      );
      filtered.forEach((f) => (f.row.style.display = ""));
    }

    function initializeTraineeTable(searchInputId, tableId, emptyStateId) {
      const searchInput = document.getElementById(searchInputId);
      const tableBody = document.getElementById(tableId);
      const emptyState = document.getElementById(emptyStateId);
      if (!searchInput || !tableBody) return;
      function applyTraineeSearch() {
        const query = searchInput.value;
        const data = getTableData(tableBody);
        const results = smartSearch({
          data,
          keys: ["name", "studentId", "major", "email"],
          query,
          limit: 100,
        });
        renderTableRows(tableBody, results);
        if (emptyState) {
          emptyState.classList.toggle("d-none", results.length > 0);
        }
      }
      searchInput.addEventListener("input", debounce(applyTraineeSearch, 200));
      applyTraineeSearch();
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
    const tableBody = document.getElementById("studentsTableBody");
    function getRequestTableData() {
      return Array.from(tableBody.querySelectorAll("tr")).map((row) => {
        const cells = row.querySelectorAll("td, th");
        return {
          row,
          name: cells[1]?.textContent || "",
          id: cells[3]?.textContent || "",
          mobile: cells[5]?.textContent || "",
          email: cells[6]?.textContent || "",
          country: cells[7]?.textContent || "",
          gender: cells[8]?.textContent || "",
          studentNo: cells[10]?.textContent || "",
          degree: cells[12]?.textContent || "",
        };
      });
    }

    function renderRequestRows(filtered) {
      Array.from(tableBody.querySelectorAll("tr")).forEach(
        (row) => (row.style.display = "none")
      );
      filtered.forEach((f) => (f.row.style.display = ""));
    }

    function applyRequestSearch() {
      const query = searchInput.value;
      const data = getRequestTableData();
      const results = smartSearch({
        data,
        keys: [
          "name",
          "id",
          "mobile",
          "email",
          "country",
          "gender",
          "studentNo",
          "degree",
        ],
        query,
        limit: 100,
      });
      renderRequestRows(results);
      if (emptyState) {
        emptyState.classList.toggle("d-none", results.length > 0);
      }
      if (selectAllCheckbox) {
        selectAllCheckbox.checked = false;
      }
      updateBulkActionButtons();
    }

    if (searchInput) {
      searchInput.addEventListener("input", debounce(applyRequestSearch, 200));
      applyRequestSearch();
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
      const tableBody = table.querySelector("tbody");
      function getClearanceTableData() {
        return Array.from(tableBody.querySelectorAll("tr")).map((row) => {
          const cells = row.querySelectorAll("td, th");
          return {
            row,
            name: cells[5]?.textContent || "",
            id: cells[6]?.textContent || "",
            mobile: cells[7]?.textContent || "",
            email: cells[8]?.textContent || "",
            department: cells[9]?.textContent || "",
          };
        });
      }

      function renderClearanceRows(filtered) {
        Array.from(tableBody.querySelectorAll("tr")).forEach(
          (row) => (row.style.display = "none")
        );
        filtered.forEach((f) => (f.row.style.display = ""));
      }

      function applyClearanceSearch() {
        const query = searchInput.value;
        let data = getClearanceTableData();
        if (adminFilter && adminFilter.value !== "all") {
          data = data.filter((d) => d.department === adminFilter.value);
        }
        const results = smartSearch({
          data,
          keys: ["name", "id", "mobile", "email", "department"],
          query,
          limit: 100,
        });
        renderClearanceRows(results);
        if (emptyState) {
          emptyState.classList.toggle("d-none", results.length > 0);
        }
      }

      if (searchInput) {
        searchInput.addEventListener(
          "input",
          debounce(applyClearanceSearch, 200)
        );
        applyClearanceSearch();
      }
      if (adminFilter) {
        adminFilter.addEventListener("change", applyClearanceSearch);
      }
      // Initialize sorting
      initializeTableSorting(table);
    }
  });

  /**
   * Trainee Form Functionality
   * This script handles the trainee form edit/save functionality
   **/
  document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("trainee-form");
    const editBtn = document.getElementById("edit-btn");
    const saveBtn = document.getElementById("save-btn");
    const cancelBtn = document.getElementById("cancel-btn");
    const editableInputs = form.querySelectorAll(".editable"); // Only target editable inputs
    const allInputs = form.querySelectorAll("input, select");

    let initialValues = {};

    function setEditable(editable) {
      // Only modify editable inputs, leave readonly fields as readonly
      editableInputs.forEach((input) => {
        if (editable) {
          input.removeAttribute("readonly");
          input.removeAttribute("disabled");
        } else {
          // Handle select elements differently - they need disabled, not readonly
          if (input.tagName.toLowerCase() === "select") {
            input.setAttribute("disabled", true);
          } else {
            input.setAttribute("readonly", true);
          }
        }
      });

      // Show/hide buttons
      editBtn.style.display = editable ? "none" : "";
      saveBtn.style.display = editable ? "" : "none";
      cancelBtn.style.display = editable ? "" : "none";
    }

    function storeInitialValues() {
      initialValues = {};
      allInputs.forEach((input) => {
        if (input.name) initialValues[input.name] = input.value;
      });
    }

    function restoreInitialValues() {
      allInputs.forEach((input) => {
        if (input.name && initialValues.hasOwnProperty(input.name)) {
          input.value = initialValues[input.name];
        }
      });
    }

    editBtn.addEventListener("click", function () {
      storeInitialValues();
      setEditable(true);
      // Focus on the first editable input
      if (editableInputs.length > 0) {
        editableInputs[0].focus();
      }
    });

    cancelBtn.addEventListener("click", function () {
      restoreInitialValues();
      setEditable(false);
    });

    // Temporarily enable disabled selects before form submission
    form.addEventListener("submit", function (e) {
      // Temporarily enable any disabled select elements so their values are submitted
      const disabledSelects = form.querySelectorAll("select[disabled]");
      disabledSelects.forEach((select) => {
        select.removeAttribute("disabled");
      });

      // Hide buttons during submission
      editBtn.style.display = "none";
      saveBtn.style.display = "none";
      cancelBtn.style.display = "none";
    });

    // Initialize form in readonly state
    setEditable(false);
  });
  /**
   * Timeline Steps Functionality
   * This script handles the timeline steps display and interaction
   **/
  function showStep(stepNumber) {
    const totalSteps = 3; // Define the total number of steps

    // Update step indicators (circles and labels)
    for (let i = 1; i <= totalSteps; i++) {
      const stepCircle = document.getElementById(`step${i}`);
      const stepLabel = document.getElementById(`label${i}`);

      // Reset classes for all steps
      stepCircle.classList.remove(
        "active",
        "done",
        "bg-primary",
        "text-white",
        "bg-success",
        "text-white"
      );
      stepLabel.classList.remove("active-label");

      if (i < stepNumber) {
        // Done steps
        stepCircle.classList.add("done");
        stepCircle.classList.add("bg-success", "text-white"); // Use Bootstrap success for done
      } else if (i === stepNumber) {
        // Active step
        stepCircle.classList.add("active");
        stepCircle.classList.add("bg-primary", "text-white"); // Use Bootstrap primary for active
        stepLabel.classList.add("active-label"); // Apply active label color
      } else {
        // Future steps (default state)
        stepCircle.classList.add("bg-light", "text-dark"); // Use Bootstrap light background
      }
    }

    // Update progress line fill
    const progressFillLine = document.getElementById("progress-fill-line");
    let fillWidth = 0;
    if (stepNumber === 1) {
      fillWidth = 0;
    } else if (stepNumber === 2) {
      fillWidth = 50;
    } else if (stepNumber === 3) {
      fillWidth = 100;
    }
    progressFillLine.style.width = `${fillWidth}%`;

    // Show/hide details sections
    const detailsSections = document.querySelectorAll(".details-section");
    detailsSections.forEach((section) => section.classList.remove("active")); // Hide all
    document
      .getElementById(`details-step${stepNumber}`)
      .classList.add("active"); // Show current
  }

  // Initialize the progress bar to the first step on page load
  document.addEventListener("DOMContentLoaded", () => {
    showStep(1);
  });

  /**
   * Center Selection Search
   * This script handles the search functionality for center selection dropdown
   * It allows users to search for centers by name or ID, with fuzzy matching and normalization.
   **/
  // Center Selection Search
  document.addEventListener("DOMContentLoaded", function () {
    const centerSearch = document.getElementById("center-search");
    const centerSelect = document.getElementById("center-select");
    const noResults = document.getElementById("center-no-results");

    if (!centerSearch || !centerSelect) return;

    // Store all options for reset
    const allOptions = Array.from(centerSelect.options).map((opt) => ({
      value: opt.value,
      text: opt.text,
      searchText: `${opt.text.toLowerCase()} ${opt.value.toLowerCase()}`, // Combined search text
      isDefault: opt.value === "",
    }));

    function filterCenters() {
      const query = centerSearch.value.trim().toLowerCase();

      // Always show default option
      centerSelect.innerHTML = "";

      const filtered = allOptions.filter((opt) => {
        if (opt.isDefault) return true;

        // Create variations of the search query and the option text
        const searchVariations = [
          opt.searchText,
          opt.searchText.replace(/\s+/g, ""), // No spaces
          opt.searchText.replace(/-/g, ""), // No hyphens
          opt.searchText.replace(/phc/g, "primary health center"), // Expand abbreviation
          opt.searchText.replace(/\(|\)/g, ""), // No parentheses
        ];

        // Try all variations against the query
        return searchVariations.some((variation) => variation.includes(query));
      });

      // Add filtered options back to select
      filtered.forEach((opt) => {
        const option = document.createElement("option");
        option.value = opt.value;
        option.text = opt.text;
        centerSelect.appendChild(option);
      });

      // Show/hide 'No results found' message
      if (filtered.length === 1 && filtered[0].isDefault) {
        noResults.style.display = "block";
      } else {
        noResults.style.display = "none";
      }
    }

    // Debounce the search to improve performance
    const debounce = (fn, delay) => {
      let timer;
      return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
      };
    };

    // Add event listener with debounce
    centerSearch.addEventListener("input", debounce(filterCenters, 200));

    // Initialize
    filterCenters();
  });

  /**
   * Form Validation
   * This script handles form validation for various fields like ID/Iqama Number,
   * Mobile Number, and Email.
   **/
  function validateForm(event) {
    let isValid = true;
    const errors = [];

    // Example: ID/Iqama Number validation
    const idIqama = document.getElementById("id_iqama_number");
    if (idIqama.value.length !== 10 || isNaN(idIqama.value)) {
      errors.push("ID/Iqama Number must be 10 digits.");
      isValid = false;
    }

    // Example: Mobile Number validation
    const mobileNumber = document.getElementById("mobile_number");
    if (mobileNumber.value.length !== 9 || isNaN(mobileNumber.value)) {
      errors.push("Mobile Number must be 9 digits after the country code.");
      isValid = false;
    }

    // Example: Email validation
    const email = document.getElementById("email");
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email.value)) {
      errors.push("Please enter a valid email address.");
      isValid = false;
    }

    // Display errors if any (you might want to integrate this with your PHP error display)
    if (!isValid) {
      alert(errors.join("\n")); // Simple alert for demonstration
      event.preventDefault(); // Stop form submission
    }
    return isValid;
  }
})();
