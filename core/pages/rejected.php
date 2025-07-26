<section class="py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="overlay-box p-3 p-sm-4 shadow rounded-4">
                <!-- Header with Search -->
                <div class="row align-items-center mb-4 gy-3">
                    <div class="col-12 col-sm-6">
                        <h2 class="mb-0 text-white" data-i18n="rejected.title">Rejected Trainees</h2>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary">
                                <i class="bi bi-search text-white"></i>
                            </span>
                            <input type="text" class="form-control bg-dark text-white border-secondary"
                                id="searchRejectedTrainees" placeholder="Search..."
                                data-i18n="rejected.search-placeholder" aria-label="Search trainees">
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive" style="max-height: 70vh;">
                    <table class="table table-hover align-middle mb-0 text-white trainee-table">
                        <thead class="sticky-top">
                            <tr class="align-middle">
                                <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                    data-i18n="rejected.name">
                                    Name
                                    <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="name"></i>
                                </th>
                                <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                    data-i18n="rejected.student-id">
                                    University ID
                                </th>
                                <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                    data-i18n="rejected.major">
                                    Major
                                    <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="major"></i>
                                </th>
                                <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                    data-i18n="rejected.email">
                                    Email
                                </th>
                                <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                    data-i18n="rejected.reject-date">
                                    Rejection Date
                                    <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="date"></i>
                                </th>
                                <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                    data-i18n="rejected.actions">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody id="rejectedTable">
                            <tr>
                                <td class="border-bottom border-secondary">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-danger rounded-circle p-2 d-flex align-items-center justify-content-center"
                                            style="width: 35px; height: 35px;">
                                            <span class="text-white fw-bold">R</span>
                                        </div>
                                        <span>Reem Alabdullah</span>
                                    </div>
                                </td>
                                <td class="border-bottom border-secondary">
                                    <span class="badge bg-secondary">440998877</span>
                                </td>
                                <td class="border-bottom border-secondary">
                                    <span class="badge bg-info text-dark">Computer Science</span>
                                </td>
                                <td class="border-bottom border-secondary">
                                    <a href="mailto:reem@example.com"
                                        class="text-decoration-none text-white d-flex align-items-center gap-2">
                                        <i class="bi bi-envelope-fill text-muted"></i>
                                        reem@example.com
                                    </a>
                                </td>
                                <td class="border-bottom border-secondary">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-calendar-x text-danger"></i>
                                        <small>2025-07-19</small>
                                    </div>
                                </td>
                                <td class="border-bottom border-secondary">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-light" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-success" title="Reconsider">
                                            <i class="bi bi-arrow-counterclockwise"></i>
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
                        <span data-i18n="rejected.scroll-hint">Scroll horizontally to view all data</span>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="emptyRejectedState" class="d-none text-center py-5">
                    <div class="text-muted">
                        <i class="bi bi-inbox-fill fs-1 mb-3 d-block"></i>
                        <h5 data-i18n="rejected.no-data">No rejected trainees found</h5>
                        <p class="small" data-i18n="rejected.no-data-hint">Rejected trainees will appear here</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>