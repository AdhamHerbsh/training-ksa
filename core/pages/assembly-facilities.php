<section>
    <div class="container">
        <!-- Filter Buttons -->
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-center gap-3">
                <button class="btn btn-primary filter-btn active" data-filter="all">All</button>
                <button class="btn btn-primary filter-btn" data-filter="health-center">Health Centers</button>
                <button class="btn btn-primary filter-btn" data-filter="hospital">Hospitals</button>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="row mb-4">
            <div class="col-12 col-md-6 mx-auto">
                <div class="input-group">
                    <span class="input-group-text rounded-4 m-auto w-100"><i class="bi bi-search"></i><input type="text"
                            class="form-control rounded-4 ms-2" id="searchFacilities"
                            placeholder="Search facilities..."></span>

                </div>
            </div>
        </div>

        <!-- Facilities Cards -->
        <div class="row g-4" id="facilitiesContainer">
            <!-- Health Centers -->
            <div class="col-12 col-md-6 col-lg-4 facility-item" data-type="health-center">
                <div
                    class="overlay-box h-100 text-center py-3 px-4 shadow border border-1 border-secondary rounded-4 text-white">
                    <h3>Al-Yarmouk Health Center</h3>
                    <p>Specialized in primary healthcare services</p>
                    <div class="mt-3">
                        <a href="#" class="btn btn-light">Learn More</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 facility-item" data-type="health-center">
                <div
                    class="overlay-box h-100 text-center py-3 px-4 shadow border border-1 border-secondary rounded-4 text-white">
                    <h3>Al-Naseem Health Center</h3>
                    <p>Community healthcare and preventive services</p>
                    <div class="mt-3">
                        <a href="#" class="btn btn-light">Learn More</a>
                    </div>
                </div>
            </div>

            <!-- Hospitals -->
            <div class="col-12 col-md-6 col-lg-4 facility-item" data-type="hospital">
                <div
                    class="overlay-box h-100 text-center py-3 px-4 shadow border border-1 border-secondary rounded-4 text-white">
                    <h3>King Fahad Medical City</h3>
                    <p>Tertiary care and specialized medical services</p>
                    <div class="mt-3">
                        <a href="#" class="btn btn-light">Learn More</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 facility-item" data-type="hospital">
                <div
                    class="overlay-box h-100 text-center py-3 px-4 shadow border border-1 border-secondary rounded-4 text-white">
                    <h3>Children's Hospital</h3>
                    <p>Specialized pediatric care and services</p>
                    <div class="mt-3">
                        <a href="#" class="btn btn-light">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>