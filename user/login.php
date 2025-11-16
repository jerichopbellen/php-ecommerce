<?php
session_start();
include("../includes/header.php"); 
include("../includes/config.php");

if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_POST['submit'])) {
    // Input sanitization
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    $pass = sha1(trim($_POST['password']));

    if (!$email) {
        $_SESSION['flash'] = 'Invalid email format';
        header("Location: login.php");
        exit();
    }

    // Begin transaction
    mysqli_begin_transaction($conn);

    try {
        $sql = "SELECT user_id, email, role, is_active
                FROM users 
                WHERE email=? AND password_hash=? 
                LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, 'ss', $email, $pass);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        mysqli_stmt_bind_result($stmt, $user_id, $email, $role, $is_active);

        if (mysqli_stmt_num_rows($stmt) === 1) {
            mysqli_stmt_fetch($stmt);

            // Restrict login if account is inactive
            if ((int)$is_active === 0) {
                mysqli_stmt_close($stmt);
                mysqli_commit($conn);
                $_SESSION['flash'] = 'Your account is deactivated. Please contact support to reactivate.';
                header("Location: login.php");
                exit();
            }

            // Allow login
            $_SESSION['email'] = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
            $_SESSION['user_id'] = (int)$user_id;
            $_SESSION['role'] = $role;
            
            mysqli_stmt_close($stmt);
            mysqli_commit($conn);
            
            header("Location: ../index.php");
            exit();
        } else {
            mysqli_stmt_close($stmt);
            mysqli_commit($conn);
            
            $_SESSION['flash'] = 'Wrong email or password';
            header("Location: login.php");
            exit();
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        error_log($e->getMessage());
        $_SESSION['flash'] = 'An error occurred. Please try again.';
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
                    <form action="<?=htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
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