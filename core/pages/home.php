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
                            <strong data-i18n="home.registration-success.title"></strong> <span data-i18n="home.registration-success.message"></span>
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
                                        <h2 data-i18n="home.personal-info.title"></h2>
                                        <p class="lead" data-i18n="home.personal-info.description"></p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div
                                class="overlay-box overlay-hover py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4 text-white">
                                <div class="card-body">
                                    <a href="?page=training-form" class="text-decoration-none text-white card-hover">
                                        <h2 data-i18n="home.cooperative-training.title"></h2>
                                        <p class="lead" data-i18n="home.cooperative-training.description"></p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div
                                class="overlay-box overlay-hover py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4 text-white">
                                <div class="card-body">
                                    <a href="?page=tracking-request" class="text-decoration-none text-white card-hover">
                                        <h2 data-i18n="home.track-request.title"></h2>
                                        <p class="lead" data-i18n="home.track-request.description"></p>
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
                                        <h2 data-i18n="home.clearance.title"></h2>
                                        <p class="lead" data-i18n="home.clearance.description"></p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div
                                class="overlay-box overlay-hover py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4 text-white">
                                <div class="card-body">
                                    <a href="?page=survey-form" class="text-decoration-none text-white card-hover">
                                        <h2 data-i18n="home.survey.title"></h2>
                                        <p class="lead" data-i18n="home.survey.description"></p>
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
                                        <h2 data-i18n="home.personal-info.title"></h2>
                                        <p class="lead" data-i18n="home.personal-info.description"></p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div
                                class="overlay-box overlay-hover py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4 text-white">
                                <div class="card-body">
                                    <a href="?page=requests" class="text-decoration-none text-white card-hover">
                                        <h2 data-i18n="home.trainee-requests.title"></h2>
                                        <p class="lead" data-i18n="home.trainee-requests.description"></p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div
                                class="overlay-box overlay-hover py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4 text-white">
                                <div class="card-body">
                                    <a href="?page=clearance" class="text-decoration-none text-white card-hover">
                                        <h2 data-i18n="home.clearance-admin.title"></h2>
                                        <p class="lead" data-i18n="home.clearance-admin.description"></p>
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
                                        <h2 data-i18n="home.survey-admin.title"></h2>
                                        <p class="lead" data-i18n="home.survey-admin.description"></p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div
                                class="overlay-box overlay-hover py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4 text-white">
                                <div class="card-body">
                                    <a href="?page=accepted" class="text-decoration-none text-white card-hover">
                                        <h2 data-i18n="home.accept-trainee.title"></h2>
                                        <p class="lead" data-i18n="home.accept-trainee.description"></p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div
                                class="overlay-box overlay-hover py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4 text-white">
                                <div class="card-body">
                                    <a href="?page=rejected" class="text-decoration-none text-white card-hover">
                                        <h2 data-i18n="home.reject-trainee.title"></h2>
                                        <p class="lead" data-i18n="home.reject-trainee.description"></p>
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