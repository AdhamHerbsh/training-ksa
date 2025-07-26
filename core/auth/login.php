<div class="vh-100">
    <main class="container align-content-center text-center h-100 w-100 m-auto">
        <div class="form-signin overlay-box py-2 px-4 m-auto shadow border border-1 border-secondary rounded-4">
            <form>
                <h1 class="mb-3 fw-normal text-white">Please Login</h1>

                <div class="form-floating">
                    <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com">
                    <label for="floatingInput">Email address</label>
                </div>
                <div class="mb-3">
                </div>
                <div class="form-floating position-relative">
                    <input type="password" class="form-control" id="floatingPassword" placeholder="Password">
                    <label for="floatingPassword">Password</label>
                    <button type="button" id="togglePassword"
                        class="btn position-absolute top-50 translate-middle-y pe-3"
                        style="background: none; border: none; cursor: pointer;">
                        <i class="bi bi-eye-slash text-primary fs-5"></i>
                    </button>
                </div>
                <div class="mb-3">
                </div>
                <div class="checkbox mb-3">
                    <label class="text-white">
                        <input type="checkbox" value="remember-me"> Remember me
                    </label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">Login</button>
                <hr>
                <a href="?auth=register" class="w-100 btn btn-lg btn-outline-secondary">Register</a>
                <hr>
                <div class="mb-3">
                    <label>
                        <a href="?auth=reset-password">Forget Password</a>
                    </label>
                </div>
                <p class="mt-5 mb-3 text-muted">© 1983 - 2025</p>
            </form>
        </div>
    </main>
</div>