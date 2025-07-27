<section>
    <div class="overlay-box py-4 px-4 m-auto shadow border border-1 border-secondary rounded-4">
        <h1 class="h3 mb-4 fw-normal text-white text-center">Application Tracking</h1>

        <div class="mb-5 position-relative" style="height: 60px;">
            <div class="position-absolute top-50 start-0 w-100 bg-secondary"
                style="height: 4px; transform: translateY(-50%); z-index: 1;"></div>
            <div id="progress-fill-line" class="position-absolute top-50 start-0 bg-primary"
                style="height: 4px; width: 0%; transform: translateY(-50%); z-index: 1; transition: width 0.3s ease-in-out;">
            </div>

            <div class="d-flex justify-content-between w-100 position-absolute top-0 start-0 h-100 align-items-center">
                <div class="text-center position-relative z-2 cursor-pointer flex-grow-1" onclick="showStep(1)">
                    <div id="step1"
                        class="step-circle rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 text-dark bg-light border border-light"
                        style="width: 35px; height: 35px; font-weight: bold; border-width: 3px !important;">1</div>
                    <div id="label1" class="step-label text-white fw-bold" style="font-size: 0.9rem;">Received</div>
                </div>
                <div class="text-center position-relative z-2 cursor-pointer flex-grow-1" onclick="showStep(2)">
                    <div id="step2"
                        class="step-circle rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 text-dark bg-light border border-light"
                        style="width: 35px; height: 35px; font-weight: bold; border-width: 3px !important;">2</div>
                    <div id="label2" class="step-label text-white fw-bold" style="font-size: 0.9rem;">Under Review</div>
                </div>
                <div class="text-center position-relative z-2 cursor-pointer flex-grow-1" onclick="showStep(3)">
                    <div id="step3"
                        class="step-circle rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 text-dark bg-light border border-light"
                        style="width: 35px; height: 35px; font-weight: bold; border-width: 3px !important;">3</div>
                    <div id="label3" class="step-label text-white fw-bold" style="font-size: 0.9rem;">Status</div>
                </div>
            </div>
        </div>

        <div class="details-section-container">
            <div id="details-step1" class="details-section p-4 rounded text-white active"
                style="background: rgba(0, 0, 0, 0.5); border: 1px solid #ddd;">
                <h5 class="text-white-50 text-start mb-3">Student Details</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" value="Sarah" readonly>
                            <label>Name</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" value="12345678" readonly>
                            <label>Student Number</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" value="Computer Science" readonly>
                            <label>Major</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" value="Prince Sattam University" readonly>
                            <label>University</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="tel" class="form-control" id="phone" value="0560772233" readonly>
                            <label>Mobile Number</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="email" class="form-control" value="student@example.com" readonly>
                            <label>Email</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" value="22 July 2025" readonly>
                            <label>Application Date</label>
                        </div>
                    </div>
                </div>
            </div>

            <div id="details-step2" class="details-section p-4 rounded text-white"
                style="background: rgba(0, 0, 0, 0.5); border: 1px solid #ddd;">
                <h5 class="text-white-50 text-start mb-3">Review Status</h5>
                <div class="alert alert-info">
                    <p class="mb-0">Your application is under review by the training committee.</p>
                    <p class="mb-0">You will be notified once the evaluation is complete.</p>
                </div>
            </div>

            <div id="details-step3" class="details-section p-4 rounded text-white"
                style="background: rgba(0, 0, 0, 0.5); border: 1px solid #ddd;">
                <h5 class="text-white-50 text-start mb-3">Application Result</h5>
                <div class="text-center">
                    <div style="font-size: 2rem; color: #28a745;"><i class="bi bi-check-circle-fill"></i></div>
                    <h2 class="text-primary">Your application has been accepted!</h2>
                    <p>Congratulations, you have been accepted into the training program.</p>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" value="TR-2023-1542" readonly>
                            <label>Application Number</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" value="15 May 2023" readonly>
                            <label>Application Date</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" value="20 May 2023" readonly>
                            <label>Acceptance Date</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" value="Accepted" readonly>
                            <label>Status</label>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mt-4">
                    <div class="col-md-6">
                        <button class="w-100 btn btn-lg btn-primary accept-training">Accept Training</button>
                    </div>
                    <div class="col-md-6">
                        <button class="w-100 btn btn-lg btn-outline-danger reject-training">Reject Training</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>