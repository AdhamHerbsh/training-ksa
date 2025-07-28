<section class="align-content-center">
    <div class="container">
        <?php if (isset($_SESSION['loggedin']) != true) : ?>
            <div class="row">
                <div
                    class="overlay-box col-12 col-md-8 text-center py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4 text-white">
                    <h2 data-i18n="home.our-vision.title">Our Vision</h2>
                    <p class="lead" data-i18n="home.our-vision.p">For the Training Department at the Second Health Cluster
                        in Riyadh to be a leading model
                        in developing competencies according to the highest professional and educational standards.</p>
                    <h2 data-i18n="home.our-mission.title">Our Mission</h2>
                    <p class="lead" data-i18n="home.our-mission.p">To develop and empower human resources through targeted
                        training programs and a safe, innovative learning environment.</p>
                </div>
            </div>
        <?php else : ?>
            <?php if ($_SESSION['user_type'] === 'Trainee') : ?>
                <?php if (isset($_SESSION['success_message'])) : ?>
                    <div class="container text-center">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            <strong>Congratulation ! ❤️</strong> Registration submitted successfully!
                        </div>

                    </div>
                <?php endif; ?>

                <div class="container text-center">
                    <div class="row">
                        <div class="col-sm-4 mb-3">
                            <div
                                class="overlay-box overlay-hover py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4 text-white">
                                <div class="card-body">
                                    <a href="?page=profile" class="text-decoration-none text-white card-hover">
                                        <h2 data-i18n="admin.title">Personal Info</h2>
                                        <p class="lead">View & Update Info</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div
                                class="overlay-box overlay-hover py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4 text-white">
                                <div class="card-body">
                                    <a href="?page=training-form" class="text-decoration-none text-white card-hover">
                                        <h2 data-i18n="admin.title">Cooperavtive Training</h2>
                                        <p class="lead">Register for cooperative training</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div
                                class="overlay-box overlay-hover py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4 text-white">
                                <div class="card-body">
                                    <a href="?page=tracking-request" class="text-decoration-none text-white card-hover">
                                        <h2 data-i18n="admin.title">Track Request</h2>
                                        <p class="lead">Track Training Request Status</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mb-3">
                            <div
                                class="overlay-box overlay-hover py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4 text-white">
                                <div class="card-body">
                                    <a href="?page=clearance-form" class="text-decoration-none text-white card-hover">
                                        <h2 data-i18n="admin.title">Clearance</h2>
                                        <p class="lead">Request Training Clearance</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div
                                class="overlay-box overlay-hover py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4 text-white">
                                <div class="card-body">
                                    <a href="?page=survey-form" class="text-decoration-none text-white card-hover">
                                        <h2 data-i18n="admin.title">Survey</h2>
                                        <p class="lead">Complete Training Survey</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="container text-center">
                    <div class="row">
                        <div class="col-sm-4 mb-3">
                            <div
                                class="overlay-box overlay-hover py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4 text-white">
                                <div class="card-body">
                                    <a href="?page=profile" class="text-decoration-none text-white card-hover">
                                        <h2 data-i18n="admin.title">Personal Info</h2>
                                        <p class="lead">View & Update Info</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div
                                class="overlay-box overlay-hover py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4 text-white">
                                <div class="card-body">
                                    <a href="?page=requests" class="text-decoration-none text-white card-hover">
                                        <h2 data-i18n="admin.title">Trainee Requests</h2>
                                        <p class="lead">Review & Accept/Reject</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div
                                class="overlay-box overlay-hover py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4 text-white">
                                <div class="card-body">
                                    <a href="?page=clearance" class="text-decoration-none text-white card-hover">
                                        <h2 data-i18n="admin.title">Clearance</h2>
                                        <p class="lead">Clearance</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mb-3">
                            <div
                                class="overlay-box overlay-hover py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4 text-white">
                                <div class="card-body">
                                    <a href="?page=survey" class="text-decoration-none text-white card-hover">
                                        <h2 data-i18n="admin.title">Survey</h2>
                                        <p class="lead">ُEvaluation Trainees</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div
                                class="overlay-box overlay-hover py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4 text-white">
                                <div class="card-body">
                                    <a href="?page=accepted" class="text-decoration-none text-white card-hover">
                                        <h2 data-i18n="admin.title">Accept Trainee</h2>
                                        <p class="lead">Approve Trainee Request</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div
                                class="overlay-box overlay-hover py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4 text-white">
                                <div class="card-body">
                                    <a href="?page=rejected" class="text-decoration-none text-white card-hover">
                                        <h2 data-i18n="admin.title">Reject Trainee</h2>
                                        <p class="lead">Training Request Rejected</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>