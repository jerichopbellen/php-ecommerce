<?php
session_start();
include("../includes/header.php"); 
include("../includes/config.php");

if (isset($_SESSION['user_id'])) {
    header(header: "Location: ../index.php");
}

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $pass = sha1(trim($_POST['password']));

    $sql = "SELECT user_id, email, role, is_active
            FROM users 
            WHERE email=? AND password_hash=? 
            LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $email, $pass);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    mysqli_stmt_bind_result($stmt, $user_id, $email, $role, $is_active);

    if (mysqli_stmt_num_rows($stmt) === 1) {
        mysqli_stmt_fetch($stmt);

        // Restrict login if account is inactive

        if ((int)$is_active === 0) {
            $_SESSION['flash'] = 'Your account is deactivated. Please contact support to reactivate.';
            header("Location: login.php");
            exit();
        }

        // Allow login
        $_SESSION['email'] = $email;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $role;
        header("Location: ../index.php");
        exit();
    } else {
        $_SESSION['flash'] = 'Wrong email or password';
        header("Location: login.php");
        exit();
    }
}

include("../includes/alert.php");

?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    
                    <h4 class="text-center mb-4"><i class="bi bi-person-circle me-2"></i>Login to Your Account</h4>
                    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                        <div class="mb-3">
                            <label for="form2Example1" class="form-label">Email address</label>
                            <input type="email" id="form2Example1" class="form-control" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="form2Example2" class="form-label">Password</label>
                            <input type="password" id="form2Example2" class="form-control" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-outline-primary w-100" name="submit">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Sign in
                        </button>

                        <div class="text-center mt-3">
                            <p class="mb-0">Not a member? <a href="register.php">Register</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include("../includes/footer.php");
?>