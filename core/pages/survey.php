    <section class="py-4">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="overlay-box p-3 p-sm-4 shadow rounded-4">
                    <!-- Header -->
                    <div class="row align-items-center mb-4 gy-3">
                        <div class="col-12 col-sm">
                            <h1 class="h2 mb-0 text-white" data-i18n="survey.title">Trainee Satisfaction Survey</h1>
                        </div>
                        <div class="col-12 col-sm-auto">
                            <button onclick="window.print()"
                                class="btn btn-light d-inline-flex align-items-center gap-2" id="print-btn">
                                <i class="bi bi-printer"></i>
                                <span data-i18n="survey.print">Print</span>
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive" style="max-height: 70vh;">
                        <table class="table table-hover align-middle mb-0 text-white">
                            <thead class="sticky-top">
                                <tr class="align-middle">
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="survey.interface">Website Interface</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="survey.steps">Application Steps</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="survey.speed">Speed</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="survey.problems">Problems</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="survey.suggestions">Suggestions</th>
                                    <th class="border-bottom border-secondary bg-dark bg-opacity-50"
                                        data-i18n="survey.time">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border-bottom border-secondary">
                                        <div class="d-flex align-items-center gap-2">
                                            <span>ممتاز</span>
                                        </div>
                                    </td>
                                    <td class="border-bottom border-secondary">نعم</td>
                                    <td class="border-bottom border-secondary">
                                        <span class="badge bg-success">سريعة جداً</span>
                                    </td>
                                    <td class="border-bottom border-secondary">-</td>
                                    <td class="border-bottom border-secondary">لا يوجد</td>
                                    <td class="border-bottom border-secondary">
                                        <small>2025-07-09 09:00 ص</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border-bottom border-secondary">
                                        <div class="d-flex align-items-center gap-2">
                                            <span>جيد</span>
                                        </div>
                                    </td>
                                    <td class="border-bottom border-secondary">إلى حد ما</td>
                                    <td class="border-bottom border-secondary">
                                        <span class="badge bg-warning text-dark">متوسطة</span>
                                    </td>
                                    <td class="border-bottom border-secondary">
                                        <span class="text-danger">واجهت مشكلة في التسجيل</span>
                                    </td>
                                    <td class="border-bottom border-secondary">إضافة شروحات فيديو</td>
                                    <td class="border-bottom border-secondary">
                                        <small>2025-07-09 09:15 ص</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile View (Visible only on xs screens) -->
                    <div class="d-block d-sm-none mt-3">
                        <div class="alert alert-info" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            <span data-i18n="survey.scroll-hint">Scroll horizontally to view more data</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>