<!-- Schedule Interview Modal -->
<div class="modal fade" id="scheduleInterviewModal" tabindex="-1" aria-labelledby="scheduleInterviewModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleInterviewModalLabel" data-i18n="schedule-interview.title">
                    <?php echo $lang['schedule-interview']['title'] ?? 'Schedule Interview'; ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="scheduleInterviewForm">
                    <input type="hidden" id="requestId" name="requestId">
                    <div class="mb-3">
                        <label for="interviewDay" class="form-label"
                            data-i18n="schedule-interview.day"><?php echo $lang['schedule-interview']['day'] ?? 'Day'; ?></label>
                        <select class="form-select" id="interviewDay" name="day" required>
                            <option value="" data-i18n="schedule-interview.select-day">
                                <?php echo $lang['schedule-interview']['select-day'] ?? 'Select a day'; ?></option>
                            <option value="Sunday" data-i18n="schedule-interview.sunday">
                                <?php echo $lang['schedule-interview']['sunday'] ?? 'Sunday'; ?></option>
                            <option value="Monday" data-i18n="schedule-interview.monday">
                                <?php echo $lang['schedule-interview']['monday'] ?? 'Monday'; ?></option>
                            <option value="Tuesday" data-i18n="schedule-interview.tuesday">
                                <?php echo $lang['schedule-interview']['tuesday'] ?? 'Tuesday'; ?></option>
                            <option value="Wednesday" data-i18n="schedule-interview.wednesday">
                                <?php echo $lang['schedule-interview']['wednesday'] ?? 'Wednesday'; ?></option>
                            <option value="Thursday" data-i18n="schedule-interview.thursday">
                                <?php echo $lang['schedule-interview']['thursday'] ?? 'Thursday'; ?></option>
                            <option value="Friday" data-i18n="schedule-interview.friday">
                                <?php echo $lang['schedule-interview']['friday'] ?? 'Friday'; ?></option>
                            <option value="Saturday" data-i18n="schedule-interview.saturday">
                                <?php echo $lang['schedule-interview']['saturday'] ?? 'Saturday'; ?></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="interviewDate" class="form-label"
                            data-i18n="schedule-interview.date"><?php echo $lang['schedule-interview']['date'] ?? 'Date'; ?></label>
                        <input type="date" class="form-control" id="interviewDate" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="interviewTime" class="form-label"
                            data-i18n="schedule-interview.time"><?php echo $lang['schedule-interview']['time'] ?? 'Time'; ?></label>
                        <input type="time" class="form-control" id="interviewTime" name="time" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    data-i18n="schedule-interview.close"><?php echo $lang['schedule-interview']['close'] ?? 'Close'; ?></button>
                <button type="button" class="btn btn-primary" onclick="saveInterview()"
                    data-i18n="schedule-interview.schedule"><?php echo $lang['schedule-interview']['schedule'] ?? 'Schedule'; ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    var scheduleModal;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the modal
        scheduleModal = new bootstrap.Modal(document.getElementById('scheduleInterviewModal'));

        // Add form reset handler
        document.getElementById('scheduleInterviewModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('scheduleInterviewForm').reset();
        });
    });

    function showScheduleModal(requestId, email) {
        // Store email in a hidden field for later use
        if (!document.getElementById('traineeEmail')) {
            const emailInput = document.createElement('input');
            emailInput.type = 'hidden';
            emailInput.id = 'traineeEmail';
            emailInput.name = 'traineeEmail';
            document.getElementById('scheduleInterviewForm').appendChild(emailInput);
        }

        // Set form values
        document.getElementById('requestId').value = requestId;
        document.getElementById('traineeEmail').value = email;

        // Show the modal
        if (scheduleModal) {
            scheduleModal.show();
        } else {
            scheduleModal = new bootstrap.Modal(document.getElementById('scheduleInterviewModal'));
            scheduleModal.show();
        }
    }

    function saveInterview() {
        const form = document.getElementById('scheduleInterviewForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const day = document.getElementById('interviewDay').value;
        const date = document.getElementById('interviewDate').value;
        const time = document.getElementById('interviewTime').value;
        const email = document.getElementById('traineeEmail').value;

        // Format the date to Arabic format
        const formattedDate = new Date(date).toLocaleDateString('ar-SA', {
            year: 'numeric',
            month: 'numeric',
            day: 'numeric'
        });

        // Format the time to 12-hour format
        const formattedTime = new Date(`2000-01-01T${time}`).toLocaleTimeString('ar-SA', {
            hour: 'numeric',
            minute: 'numeric',
            hour12: true
        });

        // Construct the email body
        const emailBody = `<?php echo $lang['schedule-interview']['email-greeting'] ?? 'بعد التحية ,,,'; ?>

<?php echo $lang['schedule-interview']['email-interview-notice'] ?? 'ننوه بموعد مقابلة شخصية'; ?>
<?php echo $lang['schedule-interview']['email-day'] ?? 'اليوم'; ?>: ${day}
<?php echo $lang['schedule-interview']['email-date'] ?? 'الموافق'; ?>: ${formattedDate}
<?php echo $lang['schedule-interview']['email-time'] ?? 'الساعة'; ?>: ${formattedTime}

<?php echo $lang['schedule-interview']['email-instructions'] ?? 'الرجاء احضار السيرة الذاتية , ونسخة من خطاب التدريب'; ?>

<?php echo $lang['schedule-interview']['email-location'] ?? 'مدينة الملك فهد الطبية، كلية الطب، الدور الثاني'; ?>

https://maps.app.goo.gl/dt8qNJUY6FFCFnAS7`;

        // Encode the email body for the mailto link
        const encodedBody = encodeURIComponent(emailBody);
        const mailtoLink =
            `mailto:${email}?subject=${encodeURIComponent('<?php echo $lang['schedule-interview']['email-subject'] ?? 'موعد المقابلة الشخصية - مدينة الملك فهد الطبية'; ?>')}&body=${encodedBody}`;

        // Open email client
        window.location.href = mailtoLink;

        // Submit the form data to the server
        const formData = new FormData(form);
        fetch('api/schedule_interview.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(formData.entries()))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('scheduleInterviewModal')).hide();
                    // Optionally refresh the page
                    // location.reload();
                } else {
                    alert('<?php echo $lang['schedule-interview']['errors']['schedule-failed'] ?? 'Error scheduling interview: '; ?>' +
                        data.message);
                }
            })
            .catch(error => {
                alert('<?php echo $lang['schedule-interview']['errors']['schedule-error'] ?? 'Error scheduling interview: '; ?>' +
                    error);
            });
    }
</script>