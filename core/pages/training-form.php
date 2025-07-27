<section class="container align-content-center h-100 w-100 m-auto">
    <div class="overlay-box py-4 px-4 m-auto shadow border border-1 border-secondary rounded-4">
        <h1 class="h3 mb-4 fw-normal text-white text-center">Trainee Registration Form</h1>

        <!-- Personal Information Section -->
        <div class="mb-4">
            <h5 class="text-white mb-3">Personal Information</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="ar-name" name="ar_name" placeholder="Name in Arabic"
                            required>
                        <label for="ar-name">Name in Arabic</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="en-name" name="en_name"
                            placeholder="Name in English" required>
                        <label for="en-name">Name in English</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="national-id" name="national_id"
                            placeholder="National ID" required>
                        <label for="national-id">National ID</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="number" class="form-control" id="age" name="age" placeholder="Age" min="15"
                            max="100" required>
                        <label for="age">Age</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information Section -->
        <div class="mb-4">
            <h5 class="text-white mb-3">Contact Information</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="tel" class="form-control" id="mobile" name="mobile" placeholder="Mobile Number"
                            required>
                        <label for="mobile">Mobile Number</label>
                        <span class="note">* Starts with +966 and must be 9 digits</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email Address"
                            required>
                        <label for="email">Email Address</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="country" name="country" placeholder="Country"
                            required>
                        <label for="country">Country</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <select class="form-select" id="gender-select" name="gender" required>
                            <option value="">-- Select Gender --</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        <label for="gender-select">Gender</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Information Section -->
        <div class="mb-4">
            <h5 class="text-white mb-3">Academic Information</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="university" name="university"
                            placeholder="University Name" required>
                        <label for="university">University Name</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="uni-id" name="uni_id"
                            placeholder="University ID Number" required>
                        <label for="uni-id">University ID Number</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="major" name="major" placeholder="Major" required>
                        <label for="major">Major</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="degree" name="degree" placeholder="Degree" required>
                        <label for="degree">Degree</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Training Information Section -->
        <div class="mb-4">
            <h5 class="text-white mb-3">Training Information</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="date" class="form-control" id="start-date" name="start_date" required>
                        <label for="start-date">Training Start Date</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="date" class="form-control" id="end-date" name="end_date" required>
                        <label for="end-date">Training End Date</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="supervisor-name" name="supervisor_name"
                            placeholder="Academic Supervisor Name" required>
                        <label for="supervisor-name">Academic Supervisor Name</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="supervisor-email" name="supervisor_email"
                            placeholder="Academic Supervisor Email" required>
                        <label for="supervisor-email">Academic Supervisor Email</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Center Selection Section -->
        <div class="mb-4">
            <h5 class="text-white mb-3">Center Selection</h5>
            <div class="form-floating">
                <input type="text" class="form-control" id="center-search"
                    placeholder="Search for health centers and hospitals...">
                <label for="center-search">Available Facilities</label>
            </div>
            <div class="mt-2">
                <select class="form-select" id="center-select" name="center" required>
                    <option value="">-- Select Center --</option>
                    <option value="Al-Ghadeer">Al-Ghadeer</option>
                    <option value="Al-Narjis">Al-Narjis</option>
                    <option value="Al-Rabie">Al-Rabie</option>
                    <option value="Al-Sahafa">Al-Sahafa</option>
                    <option value="Al-Falah">Al-Falah</option>
                    <option value="Al-Yasmeen">Al-Yasmeen</option>
                    <option value="Al-Wadi">Al-Wadi</option>
                    <option value="Al-Ezdihar">Al-Ezdihar</option>
                    <option value="Salah Aldeen">Salah Aldeen</option>
                    <option value="Al-Maseef">Al-Maseef</option>
                    <option value="Al-Mursalat">Al-Mursalat</option>
                    <option value="King Fahad District">King Fahad District</option>
                    <option value="Al-Murooj">Al-Murooj</option>
                    <option value="Al-Sulaimania">Al-Sulaimania</option>
                    <option value="Al-Nuzha">Al-Nuzha</option>
                    <option value="Al-Wurood">Al-Wurood</option>
                    <option value="Ishbiliya">Ishbiliya</option>
                    <option value="Khaleej 2">Khaleej 2</option>
                    <option value="Al-Hamra">Al-Hamra</option>
                    <option value="Khaleej 1">Khaleej 1</option>
                    <option value="Qurtoba">Qurtoba</option>
                    <option value="Al-Yarmouk (West)">Al-Yarmouk (West)</option>
                    <option value="King Faisal District">King Faisal District</option>
                    <option value="Al-Munsiyah">Al-Munsiyah</option>
                    <option value="Ghornata">Ghornata</option>
                    <option value="Al-Rawdah 1">Al-Rawdah 1</option>
                    <option value="Al-Rawdah 2">Al-Rawdah 2</option>
                    <option value="Al-Nahda (West)">Al-Nahda (West)</option>
                    <option value="Janadriyah (West)">Janadriyah (West)</option>
                    <option value="Janadriyah (East)">Janadriyah (East)</option>
                    <option value="North Nadheem">North Nadheem</option>
                    <option value="South Nadheem">South Nadheem</option>
                    <option value="Al-Nadwa">Al-Nadwa</option>
                    <option value="Hijrat Saad">Hijrat Saad</option>
                    <option value="Airport Health Control Center">Airport Health Control Center</option>
                    <option value="Royal Protocol Clinic">Royal Protocol Clinic</option>
                    <option value="Royal Diwan Clinic">Royal Diwan Clinic</option>
                    <option value="Women’s Health Clinic (Hayat Mall)">Women’s Health Clinic (Hayat Mall)</option>
                    <option value="Al-Manar">Al-Manar</option>
                    <option value="Al-Salam">Al-Salam</option>
                    <option value="Middle Naseem">Middle Naseem</option>
                    <option value="South Naseem">South Naseem</option>
                    <option value="East Naseem">East Naseem</option>
                    <option value="West Naseem">West Naseem</option>
                    <option value="Al-Saada">Al-Saada</option>
                    <option value="Al-Jazeera">Al-Jazeera</option>
                    <option value="Artawiyah">Artawiyah</option>
                    <option value="Umm Al-Jamajim">Umm Al-Jamajim</option>
                    <option value="Mushrifah">Mushrifah</option>
                    <option value="Jurab">Jurab</option>
                    <option value="Al-Barzah">Al-Barzah</option>
                    <option value="Mishdhuba">Mishdhuba</option>
                    <option value="Masadat Sudair">Masadat Sudair</option>
                    <option value="Qaa’yat Sudair">Qaa’yat Sudair</option>
                    <option value="Mishlah">Mishlah</option>
                    <option value="Mishash Awadh">Mishash Awadh</option>
                    <option value="Howaimidah">Howaimidah</option>
                    <option value="Umm Sudairah">Umm Sudairah</option>
                    <option value="Barzan">Barzan</option>
                    <option value="Al-Quds">Al-Quds</option>
                    <option value="Al-Khalidiyah">Al-Khalidiyah</option>
                    <option value="Al-Siddiq">Al-Siddiq</option>
                    <option value="Al-Farouq">Al-Farouq</option>
                    <option value="Al-Yamamah">Al-Yamamah</option>
                    <option value="Ulaqqah">Ulaqqah</option>
                    <option value="Al-Thuwair">Al-Thuwair</option>
                    <option value="Ghat PHC">Ghat PHC</option>
                    <option value="Meleeh PHC">Meleeh PHC</option>
                    <option value="Al-Abdaliyah PHC">Al-Abdaliyah PHC</option>
                    <option value="Airport PHC">Airport PHC</option>
                    <option value="Al-Fayhaa PHC">Al-Fayhaa PHC</option>
                    <option value="Al-Yarmouk PHC">Al-Yarmouk PHC</option>
                    <option value="Al-Baseerah PHC">Al-Baseerah PHC</option>
                    <option value="Abdulaziz Al-Shuwai'er PHC">Abdulaziz Al-Shuwai'er PHC</option>
                    <option value="Harmah PHC">Harmah PHC</option>
                    <option value="Al-Faisaliah PHC">Al-Faisaliah PHC</option>
                    <option value="Majmaah PHC">Majmaah PHC</option>
                    <option value="Tumair PHC">Tumair PHC</option>
                    <option value="Umm Rujoum Center">Umm Rujoum Center</option>
                    <option value="Hotat Sudair Center">Hotat Sudair Center</option>
                    <option value="Al-Nahda PHC (Sudair)">Al-Nahda PHC (Sudair)</option>
                    <option value="Al-Shifa PHC (Hotat Sudair)">Al-Shifa PHC (Hotat Sudair)</option>
                    <option value="Tuwaiem Center">Tuwaiem Center</option>
                    <option value="Rawdat Sudair Center">Rawdat Sudair Center</option>
                    <option value="Al-Atar PHC">Al-Atar PHC</option>
                    <option value="Al-Khatamah PHC">Al-Khatamah PHC</option>
                    <option value="Awdat Sudair PHC">Awdat Sudair PHC</option>
                    <option value="Ashirat Sudair PHC">Ashirat Sudair PHC</option>
                    <option value="Mubayidh PHC">Mubayidh PHC</option>
                    <option value="Hafr Al-Atash">Hafr Al-Atash</option>
                    <option value="Rumhiya">Rumhiya</option>
                    <option value="Aytliyah">Aytliyah</option>
                    <option value="Ghaylanah">Ghaylanah</option>
                    <option value="Al-Muzayri’">Al-Muzayri’</option>
                    <option value="Hafnat Al-Tairi">Hafnat Al-Tairi</option>
                    <option value="Rumah">Rumah</option>
                    <option value="Shuweyah">Shuweyah</option>
                </select>
                <div id="center-no-results" class="text-danger small mt-2" style="display:none;">No results found.</div>
            </div>
        </div>

        <!-- Documents Section -->
        <div class="mb-4">
            <h4 class="text-white mb-3">Required Documents</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="training-letter" class="form-label text-white small">Training Letter (PDF)</label>
                    <input type="file" class="form-control" id="training-letter" name="training_letter" accept=".pdf"
                        multiple required>
                    <div id="letter-preview" class="file-preview"></div>
                </div>
                <div class="col-md-6">
                    <label for="cv" class="form-label text-white small">Curriculum Vitae (CV)</label>
                    <input type="file" class="form-control" id="cv" name="cv" accept=".pdf,.doc,.docx" multiple
                        required>
                    <div id="cv-preview" class="file-preview"></div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="row g-3 mt-4">
            <div class="col-12 col-md-6">
                <button type="submit" id="submit-btn" class="w-100 btn btn-lg btn-primary">Submit</button>
            </div>
            <div class="col-12 col-md-6">
                <a href="?page=home" class="w-100 btn btn-lg btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </div>