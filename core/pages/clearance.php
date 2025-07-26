<section class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="overlay-box p-3 p-sm-4 shadow rounded-4">
                    <!-- Header with Controls -->
                    <div class="row align-items-center mb-4 gy-3">
                        <div class="col-12 col-sm-6 col-md-4">
                            <h2 class="h3 mb-0 text-white" data-i18n="clearance.title">Clearance Form Responses</h2>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary">
                                    <i class="bi bi-search text-white"></i>
                                </span>
                                <input type="text" class="form-control bg-dark text-white border-secondary"
                                    id="searchClearance"
                                    placeholder="Search..."
                                    data-i18n="clearance.search-placeholder"
                                    aria-label="Search responses">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="d-flex gap-2">
                                <select id="adminFilter" class="form-select bg-dark text-white border-secondary flex-grow-1">
                                    <option value="all" data-i18n="clearance.all-departments">كل الإدارات</option>
                                    <option value="إدارة التعليم التمريضي">إدارة التعليم التمريضي</option>
                                    <option value="إدارة التدريب الصحي">إدارة التدريب الصحي</option>
                                    <option value="إدارة التدريب الطبي">إدارة التدريب الطبي</option>
                                    <option value="إدارة التدريب الإداري">إدارة التدريب الإداري</option>
                                    <option value="إدارة التطوع">إدارة التطوع</option>
                                    <option value="إدارة التعاقد / التشغيل">إدارة التعاقد / التشغيل</option>
                                    <option value="أكاديمية طب الأسرة">أكاديمية طب الأسرة</option>
                                    <option value="إدارة أمن الرعاية الصحية">إدارة أمن الرعاية الصحية</option>
                                    <option value="إدارة الأشعة AAML">إدارة الأشعة AAML</option>
                                </select>
                                <button onclick="window.print()" class="btn btn-light d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-printer"></i>
                                    <span data-i18n="clearance.print">Print</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive" style="max-height: 70vh;">
                        <table id="responsesTable" class="table table-hover align-middle mb-0 text-white clearance-table">
                            <thead class="sticky-top">
                                <tr class="align-middle">
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50" data-i18n="clearance.date">
                                        التاريخ
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="date"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50" data-i18n="clearance.form-date">
                                        تاريخ النموذج
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="date"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50" data-i18n="clearance.housing">السكن</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50" data-i18n="clearance.nationality">
                                        الجنسية
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="text"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50" data-i18n="clearance.arabic-name">
                                        الاسم العربي
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="text"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50" data-i18n="clearance.english-name">
                                        الاسم الإنجليزي
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="text"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50" data-i18n="clearance.id">رقم الهوية</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50" data-i18n="clearance.mobile">الجوال</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50" data-i18n="clearance.email">البريد الإلكتروني</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50" data-i18n="clearance.section">
                                        القسم
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="text"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50" data-i18n="clearance.title">المسمى</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50" data-i18n="clearance.card-expiry">
                                        انتهاء البطاقة
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="date"></i>
                                    </th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50" data-i18n="clearance.department">
                                        الإدارة
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small" data-sort="text"></i>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border-bottom border-secondary">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-calendar text-info"></i>
                                            <small>2025-07-01</small>
                                        </div>
                                    </td>
                                    <td class="border-bottom border-secondary">
                                        <small>2025-06-30</small>
                                    </td>
                                    <td class="border-bottom border-secondary">
                                        <span class="badge bg-success">نعم</span>
                                    </td>
                                    <td class="border-bottom border-secondary">سعودي</td>
                                    <td class="border-bottom border-secondary">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <span class="text-white fw-bold">أ</span>
                                            </div>
                                            <span>أحمد محمد</span>
                                        </div>
                                    </td>
                                    <td class="border-bottom border-secondary">Ahmed Mohammed</td>
                                    <td class="border-bottom border-secondary">
                                        <span class="badge bg-secondary">1234567890</span>
                                    </td>
                                    <td class="border-bottom border-secondary">
                                        <a href="tel:0500000000" class="text-decoration-none text-white">
                                            <i class="bi bi-telephone-fill text-muted me-1"></i>
                                            0500000000
                                        </a>
                                    </td>
                                    <td class="border-bottom border-secondary">
                                        <a href="mailto:ahmed@example.com" class="text-decoration-none text-white">
                                            <i class="bi bi-envelope-fill text-muted me-1"></i>
                                            ahmed@example.com
                                        </a>
                                    </td>
                                    <td class="border-bottom border-secondary">قسم التمريض</td>
                                    <td class="border-bottom border-secondary">
                                        <span class="badge bg-info text-dark">ممرض</span>
                                    </td>
                                    <td class="border-bottom border-secondary">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-calendar-check text-success"></i>
                                            <small>2026-01-01</small>
                                        </div>
                                    </td>
                                    <td class="border-bottom border-secondary">إدارة التعليم التمريضي</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile View Helper -->
                    <div class="d-block d-sm-none mt-3">
                        <div class="alert alert-info d-flex align-items-center gap-2" role="alert">
                            <i class="bi bi-info-circle"></i>
                            <span data-i18n="clearance.scroll-hint">Scroll horizontally to view all data</span>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="emptyClearanceState" class="d-none text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-inbox-fill fs-1 mb-3 d-block"></i>
                            <h5 data-i18n="clearance.no-data">No clearance responses found</h5>
                            <p class="small" data-i18n="clearance.no-data-hint">Responses will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>