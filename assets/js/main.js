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
   * Preloader
   */
  window.addEventListener("load", function () {
    var preloader = document.getElementById("preloader");
    if (preloader) {
      preloader.style.display = "none";
    }
  });
})();
