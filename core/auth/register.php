<div class="vh-100">
    <main class="container align-content-center text-center h-100 w-100 m-auto">
        <div class="form-signup overlay-box py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4">
            <form>
                <h1 class="h3 mb-3 fw-normal text-white">Register New Account</h1>

                <div class="form-floating">
                    <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com">
                    <label for="floatingInput">Email address</label>
                </div>
                <div class="mb-3"></div>
                <div class="form-floating position-relative">
                    <input type="password" class="form-control" id="floatingPassword" placeholder="Password">
                    <label for="floatingPassword">Password</label>
                    <button type="button" id="togglePassword"
                        class="btn position-absolute top-50 translate-middle-y pe-3"
                        style="background: none; border: none; cursor: pointer;">
                        <i class="bi bi-eye-slash text-primary fs-5"></i>
                    </button>
                </div>
                <div class="mb-3"></div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">Register</button>
                <hr>
                <a href="?auth=login" class="w-100 btn btn-lg btn-outline-secondary">Login</a>
                <p class="mt-5 mb-3 text-muted">© 1983 - 2025</p>
            </form>
        </div>
    </main>
</div>