<?php
require_once __DIR__ . '/../db/connection.php';
?>

<section class="d-flex flex-column h-100">
    <div class="container flex-grow-1">

        <div class="row sticky-top mb-3 bg-dark text-white rounded-4">
            <!-- Filter Buttons -->
            <div class="row mb-3 py-3">
                <div class="col-12 d-flex justify-content-center gap-3">
                    <button class="btn btn-primary filter-btn active" data-filter="all">All</button>
                    <button class="btn btn-outline-light filter-btn" data-filter="health-center">Health Centers</button>
                    <button class="btn btn-outline-light filter-btn" data-filter="hospital">Hospitals</button>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="row mb-3 pt-0 pb-3" style="top: 70px;">
                <div class="col-12 col-md-6 mx-auto">
                    <div class="input-group">
                        <span class="input-group-text rounded-4 m-auto w-100">
                            <i class="bi bi-search"></i>
                            <input type="text" class="form-control rounded-4 ms-2" id="searchFacilities"
                                placeholder="Search facilities...">
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Facilities Cards -->
        <div class="row g-3 overflow-auto" id="facilitiesContainer" style="max-height: calc(100vh - 250px);">

            <?php

            try {
                // Query to fetch all health centers
                $result = $conn->query("SELECT center_name, governorate, region_cluster, email, location_url FROM health_centers");
                $health_centers = [];
                if ($result) {
                    while ($center = $result->fetch_assoc()) {
                        $health_centers[] = $center;
                    }
                }

                // Loop through health centers and generate HTML
                foreach ($health_centers as $center) {
            ?>
                    <!-- Health Centers -->
                    <div class="col-12 col-md-6 col-lg-3 facility-item" data-type="health-center">
                        <div class="overlay-box h-100 py-3 px-4 shadow border border-1 border-secondary rounded-4 text-white">
                            <h3><?php echo htmlspecialchars($center['center_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <a href="mailto:<?php echo $center['email'] ?>"> <i class="bi bi-envelope"></i>
                                <small><?php echo $center['email'] ?></small></a>
                            <p class="lead"><?php echo $center['region_cluster'] ?></p>
                            <hr>
                            <p class="lead"><?php echo $center['governorate'] ?></p>
                            <div class="mt-3">
                                <a href="<?php echo $center['location_url'] ?>" class="btn btn-outline-primary"> <i
                                        class="bi bi-pin-map"></i> Location on Map</a>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } catch (PDOException $e) {
                echo "Query failed: " . $e->getMessage();
            }

            ?>

            <?php
            try {
                // Query to fetch all hospitals
                $result = $conn->query("SELECT hospital_name, governorate, region_cluster, location_url, phone_number FROM hospitals");
                $hospitals = [];
                if ($result) {
                    while ($hospital = $result->fetch_assoc()) {
                        $hospitals[] = $hospital;
                    }
                }

                // Loop through hospitals and generate HTML
                foreach ($hospitals as $hospital) {
            ?>
                    <!-- Hospitals -->
                    <div class="col-12 col-md-6 col-lg-3 facility-item" data-type="hospital">
                        <div class="overlay-box h-100 py-3 px-4 shadow border border-1 border-secondary rounded-4 text-white">
                            <h3><?php echo htmlspecialchars($hospital['hospital_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="lead"><?php echo $hospital['region_cluster'] ?></p>
                            <hr>
                            <span class="badge bg-primary fs-5"><?php echo $hospital['governorate'] ?></span>
                            <span class="badge bg-secondary fs-5"> <i class="bi bi-telephone"></i>

                                <?php echo $hospital['phone_number'] ?></span>
                            <div class="mt-3">
                                <a href="<?php echo $center['location_url'] ?>" class="btn btn-outline-primary"> <i
                                        class="bi bi-pin-map"></i> Location on Map</a>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } catch (PDOException $e) {
                echo "Query failed: " . $e->getMessage();
            }

            ?>

        </div>
    </div>
</section>