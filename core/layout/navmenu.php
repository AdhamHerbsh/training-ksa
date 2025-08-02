<div id="navmenu" class="position-fixed flex-column flex-shrink-0 p-3 h-100" data-aos="slide-right">
    <div class="d-flex flex-row">
        <button id="closeNavmenu" class="btn text-white px-2 py-1"><i class="bi bi-x fs-4"></i></button>
    </div>
    <hr>
    <?php $currentPage = $_GET['page'] ?? 'home'; ?>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="?page=home" class="nav-link<?php echo ($currentPage == 'home') ? ' active' : ''; ?>"
                aria-current="page">
                <span data-i18n="navmenu.home">Home</span>
            </a>
        </li>
        <li>
            <a href="?page=about" class="nav-link<?php echo ($currentPage == 'about') ? ' active' : ''; ?>">
                <span data-i18n="navmenu.about">About</span>
            </a>
        </li>
        <li>
            <a href="?page=assembly-facilities"
                class="nav-link<?php echo ($currentPage == 'assembly-facilities') ? ' active' : ''; ?>">
                <span data-i18n="navmenu.assembly-facilities">Assembly Facilities</span>
            </a>
        </li>
    </ul>
    <hr>
    <?php if (isset($_SESSION['loggedin']) != true) : ?>

    <strong><a class="d-flex align-items-center text-decoration-none" href="?auth=login"><i
                class="bi bi-arrow-right-square fs-4 ms-2"></i> <span
                data-i18n="navmenu.login">Login</span></a></strong>
    <?php else : ?>

    <strong><a class="d-flex align-items-center text-decoration-none" href="?auth=logout"><i
                class="bi bi-arrow-left-square fs-4 ms-2"></i> <span
                data-i18n="navmenu.logout">Logout</span></a></strong>
    <?php endif; ?>

</div>