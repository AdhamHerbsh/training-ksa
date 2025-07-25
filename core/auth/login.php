<div class="vh-100">
    <main class="container align-content-center text-center form-signin h-100 w-100 m-auto">
        <form>
            <h1 class="h3 mb-3 fw-normal">Please Login</h1>

            <div class="form-floating">
                <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com">
                <label for="floatingInput">Email address</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="floatingPassword" placeholder="Password">
                <label for="floatingPassword">Password</label>
            </div>

            <div class="checkbox mb-3">
                <label>
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
    </main>
</div>