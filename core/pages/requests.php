<section class="py-4">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="overlay-box p-3 p-sm-4 shadow rounded-4">
                    <!-- Header with Controls -->
                    <div class="row align-items-center mb-4 gy-3">
                        <div class="col-12 col-sm-6 col-md-3">
                            <h2 class="h3 mb-0 text-white" data-i18n="requests.title">Co-op Training Requests</h2>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary">
                                    <i class="bi bi-search text-white"></i>
                                </span>
                                <input type="text" class="form-control bg-dark text-white border-secondary"
                                    id="searchRequests" placeholder="Search requests..."
                                    data-i18n="requests.search-placeholder" aria-label="Search requests">
                            </div>
                        </div>
                        <div class="col-12 col-md-5">
                            <div class="d-flex gap-2 justify-content-end flex-wrap">
                                <button class="btn btn-success d-inline-flex align-items-center gap-2" id="bulkAccept">
                                    <i class="bi bi-check-circle"></i>
                                    <span data-i18n="requests.bulk-accept">Accept Selected</span>
                                </button>
                                <button class="btn btn-danger d-inline-flex align-items-center gap-2" id="bulkReject">
                                    <i class="bi bi-x-circle"></i>
                                    <span data-i18n="requests.bulk-reject">Reject Selected</span>
                                </button>
                                <button class="btn btn-light d-inline-flex align-items-center gap-2"
                                    onclick="window.print()">
                                    <i class="bi bi-printer"></i>
                                    <span data-i18n="requests.print">Print</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive" style="max-height: 70vh;">
                        <table class="table table-hover align-middle mb-0 text-white requests-table">
                            <thead class="sticky-top">
                                <tr class="align-middle">
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.name-ar">
                                        Name (Arabic)
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="text"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.name-en">
                                        Name (English)
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="text"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.id">ID Number</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.age">
                                        Age
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="number"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.mobile">Mobile</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.email">Email</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.country">Country</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.gender">Gender</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.university">
                                        University
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="text"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.student-no">Student Number</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.major">
                                        Major
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="text"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.degree">Degree</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.start-date">
                                        Start Date
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="date"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.end-date">End Date</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.supervisor">Supervisor</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.supervisor-email">Supervisor Email</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.letter">Training Letter</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.cv">CV</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="requests.actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="studentsTableBody">
                                <tr>
                                    <td class="border-bottom border-secondary">
                                        <div class="form-check">
                                            <input class="form-check-input studentCheckbox" type="checkbox">
                                        </div>
                                    </td>
                                    <td class="border-bottom border-secondary">اماني الحارثي</td>
                                    <td class="border-bottom border-secondary">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center"
                                                style="width: 35px; height: 35px;">
                                                <span class="text-white fw-bold">A</span>
                                            </div>
                                            <span>Amani Alharthi</span>
                                        </div>
                                    </td>
                                    <td class="border-bottom border-secondary">
                                        <span class="badge bg-secondary">443960689</span>
                                    </td>
                                    <td class="border-bottom border-secondary">22</td>
                                    <td class="border-bottom border-secondary">
                                        <a href="tel:0553817599" class="text-decoration-none text-white">
                                            <i class="bi bi-telephone-fill text-muted me-1"></i>
                                            0553817599
                                        </a>
                                    </td>
                                    <td class="border-bottom border-secondary">
                                        <a href="mailto:amani.alharthi443@gmail.com"
                                            class="text-decoration-none text-white">
                                            <i class="bi bi-envelope-fill text-muted me-1"></i>
                                            amani.alharthi443@gmail.com
                                        </a>
                                    </td>
                                    <td class="border-bottom border-secondary">السعودية</td>
                                    <td class="border-bottom border-secondary">
                                        <i class="bi bi-gender-female text-pink me-1"></i>
                                        أنثى
                                    </td>
                                    <td class="border-bottom border-secondary">Prince Sattam University</td>
                                    <td class="border-bottom border-secondary">
                                        <span class="badge bg-secondary">443960689</span>
                                    </td>
                                    <td class="border-bottom border-secondary">
                                        <span class="badge bg-info text-dark">Information Systems</span>
                                    </td>
                                    <td class="border-bottom border-secondary">
                                        <span class="badge bg-primary">Bachelor</span>
                                    </td>
                                    <td class="border-bottom border-secondary">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-calendar text-info"></i>
                                            <small>2025-06-22</small>
                                        </div>
                                    </td>
                                    <td class="border-bottom border-secondary">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-calendar text-warning"></i>
                                            <small>2025-08-22</small>
                                        </div>
                                    </td>
                                    <td class="border-bottom border-secondary">د. ساره محمد</td>
                                    <td class="border-bottom border-secondary">
                                        <a href="mailto:supervisor@psau.edu.sa" class="text-decoration-none text-white">
                                            <i class="bi bi-envelope-fill text-muted me-1"></i>
                                            supervisor@psau.edu.sa
                                        </a>
                                    </td>
                                    <td class="border-bottom border-secondary">
                                        <button class="btn btn-sm btn-outline-info" onclick="viewDocument('letter')">
                                            <i class="bi bi-file-text me-1"></i>
                                            View
                                        </button>
                                    </td>
                                    <td class="border-bottom border-secondary">
                                        <button class="btn btn-sm btn-outline-info" onclick="viewDocument('cv')">
                                            <i class="bi bi-file-person me-1"></i>
                                            View
                                        </button>
                                    </td>
                                    <td class="border-bottom border-secondary">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-warning"
                                                title="Schedule Interview">
                                                <i class="bi bi-camera-video"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-success" title="Accept">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" title="Reject">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile View Helper -->
                    <div class="d-block d-sm-none mt-3">
                        <div class="alert alert-info d-flex align-items-center gap-2" role="alert">
                            <i class="bi bi-info-circle"></i>
                            <span data-i18n="requests.scroll-hint">Scroll horizontally to view all data</span>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="emptyRequestsState" class="d-none text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-inbox-fill fs-1 mb-3 d-block"></i>
                            <h5 data-i18n="requests.no-data">No training requests found</h5>
                            <p class="small" data-i18n="requests.no-data-hint">New requests will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>