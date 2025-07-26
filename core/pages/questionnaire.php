<section class="container align-content-center text-center h-100 w-100 m-auto">
    <div class="form-signin overlay-box py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4">
        <form id="surveyForm">
            <h1 class="mb-3 fw-normal text-white">Intern Satisfaction Survey</h1>

            <div class="form-floating mb-3">
                <select class="form-control" id="q1" name="q1" required>
                    <option value="" selected disabled>Select your answer</option>
                    <option value="Excellent">Excellent</option>
                    <option value="Good">Good</option>
                    <option value="Average">Average</option>
                    <option value="Poor">Poor</option>
                </select>
                <label for="q1">Was the website interface clear and easy to use?</label>
            </div>

            <div class="form-floating mb-3">
                <select class="form-control" id="q2" name="q2" required>
                    <option value="" selected disabled>Select your answer</option>
                    <option value="Yes">Yes</option>
                    <option value="Somewhat">Somewhat</option>
                    <option value="No">No</option>
                </select>
                <label for="q2">Were the application steps clear and straightforward?</label>
            </div>

            <div class="form-floating mb-3">
                <select class="form-control" id="q3" name="q3" required>
                    <option value="" selected disabled>Select your answer</option>
                    <option value="Very Fast">Very Fast</option>
                    <option value="Average">Average</option>
                    <option value="Slow">Slow</option>
                </select>
                <label for="q3">How was the website performance during application?</label>
            </div>

            <div class="form-floating mb-3">
                <textarea class="form-control" id="problems" name="problems" style="height: 100px"
                    placeholder="Please describe any problems if any..."></textarea>
                <label for="problems">Did you face any issues during the application process?</label>
            </div>

            <div class="form-floating mb-3">
                <textarea class="form-control" id="suggestions" name="suggestions" style="height: 100px"
                    placeholder="Write your suggestions here..."></textarea>
                <label for="suggestions">Do you have any suggestions to improve the website?</label>
            </div>

            <button type="submit" class="w-100 btn btn-lg btn-primary mb-3">Submit Survey</button>

            <p class="mt-5 mb-3 text-muted">© 1983 - 2025</p>
        </form>
    </div>
</section>