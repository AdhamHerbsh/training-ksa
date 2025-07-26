<section class="container align-content-center text-center h-100 w-100 m-auto">
    <div class="form-signin overlay-box py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4">
        <form id="trainee-form">
            <h1 class="mb-3 fw-normal text-white">Personal Information</h1>

            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="file-number" name="file-number" placeholder="File Number"
                    disabled>
                <label for="file-number">File Number</label>
            </div>

            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="national-id" name="national-id" maxlength="10"
                    pattern="\d{10}" placeholder="National ID / Iqama" disabled>
                <label for="national-id">National ID / Iqama</label>
            </div>

            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="full-name-ar" name="full-name-ar"
                    placeholder="Full Name (Arabic)" disabled>
                <label for="full-name-ar">Full Name (Arabic)</label>
            </div>

            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="full-name-en" name="full-name-en"
                    placeholder="Full Name (English)" disabled>
                <label for="full-name-en">Full Name (English)</label>
            </div>

            <div class="form-floating mb-3">
                <select class="form-control" id="gender" name="gender" disabled>
                    <option value="" selected disabled>Select Gender</option>
                    <option value="female">Female</option>
                    <option value="male">Male</option>
                </select>
                <label for="gender">Gender</label>
            </div>

            <div class="row mb-3">
                <div class="col-4">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="country-code" name="country-code" value="966"
                            maxlength="4" placeholder="Country Code" disabled>
                        <label for="country-code">Code</label>
                    </div>
                </div>
                <div class="col-8">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="mobile" name="mobile" pattern="\d{9}"
                            placeholder="Mobile Number" disabled>
                        <label for="mobile">Mobile Number</label>
                    </div>
                </div>
                <div class="error-message text-danger" id="mobile-error">Invalid mobile number. It must be 9 digits.
                </div>
            </div>

            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" disabled>
                <label for="email">Email</label>
                <div class="error-message text-danger" id="email-error">Please enter a valid email address.</div>
            </div>

            <button type="button" id="edit-btn" class="w-100 btn btn-lg btn-primary mb-3">Edit</button>
            <button type="submit" id="save-btn" class="w-100 btn btn-lg btn-success mb-3"
                style="display: none;">Save</button>

            <p class="mt-5 mb-3 text-muted">© 1983 - 2025</p>
        </form>
    </div>
</section>
<script>

</script>