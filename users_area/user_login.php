<?php
// Assurez-vous que la session est démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../includes/connect.php');
include('../functions/common_functions.php');

// Générer un captcha si nécessaire
if (!isset($_SESSION['captcha_result']) || !isset($_SESSION['captcha_question'])) {
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $_SESSION['captcha_result'] = $num1 + $num2;
    $_SESSION['captcha_question'] = "$num1 + $num2";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce User Login Page</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css" />
    <link rel="stylesheet" href="../assets/css/main.css" />
</head>

<body>

    <div class="register">
        <div class="container py-3">
            <h2 class="text-center mb-4">User Login</h2>
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <form action="" method="post" class="d-flex flex-column gap-4">
                        <!-- Champ pour le nom d'utilisateur -->
                        <div class="form-outline">
                            <label for="user_username" class="form-label">Username</label>
                            <input type="text" placeholder="Enter your username" autocomplete="off" required="required" name="user_username" id="user_username" class="form-control">
                        </div>
                        
                        <!-- Champ pour le mot de passe -->
                        <div class="form-outline">
                            <label for="user_password" class="form-label">Password</label>
                            <input type="password" placeholder="Enter your password" autocomplete="off" required="required" name="user_password" id="user_password" class="form-control">
                        </div>
                        
                        <!-- Champ pour le captcha -->
                        <div class="form-outline">
                            <label for="captcha" class="form-label">Solve: 
                                <?php 
                                echo $_SESSION['captcha_question']; 
                                ?>
                            </label>
                            <input type="text" placeholder="Enter the result" autocomplete="off" required="required" name="captcha" id="captcha" class="form-control">
                        </div>
                        
                        <!-- Lien et bouton -->
                        <div><a href="" class="text-decoration-underline">Forget your password?</a></div>
                        <div>
                            <input type="submit" value="Login" class="btn btn-primary mb-2" name="user_login">
                            <p>
                                Don't have an account? <a href="user_registration.php" class="text-primary text-decoration-underline"><strong>Register</strong></a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.js"></script>
</body>

</html>

<?php
// Vérifiez si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_login'])) {
    $user_username = trim($_POST['user_username']);
    $user_password = trim($_POST['user_password']);
    $captcha = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';

    // Vérifiez si le captcha est correct
    if (!isset($_SESSION['captcha_result']) || $captcha != $_SESSION['captcha_result']) {
        echo "<script>alert('Captcha incorrect. Please try again.')</script>";
        exit;
    }

    // Requête pour vérifier l'utilisateur dans la base de données
    $select_query = "SELECT * FROM `user_table` WHERE username='$user_username'";
    $select_result = mysqli_query($con, $select_query);
    $row_data = mysqli_fetch_assoc($select_result);
    $row_count = mysqli_num_rows($select_result);
    $user_ip = getIPAddress();

    // Vérifiez si l'utilisateur existe
    if ($row_count > 0) {
        if (password_verify($user_password, $row_data['user_password'])) {
            $_SESSION['username'] = $user_username;

            // Vérifiez si l'utilisateur a des articles dans son panier
            $select_cart_query = "SELECT * FROM `card_details` WHERE ip_address='$user_ip'";
            $select_cart_result = mysqli_query($con, $select_cart_query);
            $row_cart_count = mysqli_num_rows($select_cart_result);

            if ($row_cart_count == 0) {
                echo "<script>alert('Login Successfully');</script>";
                echo "<script>window.open('profile.php','_self');</script>";
            } else {
                echo "<script>alert('Login Successfully');</script>";
                echo "<script>window.open('payment.php','_self');</script>";
            }
        } else {
            echo "<script>alert('Invalid Credentials')</script>";
        }
    } else {
        echo "<script>alert('Invalid Credentials')</script>";
    }

    // Réinitialisez le captcha après une tentative
    unset($_SESSION['captcha_result']);
    unset($_SESSION['captcha_question']);
}
?>
