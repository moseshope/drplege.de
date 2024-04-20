<?php

session_start();
if (isset($_SESSION['staff_id'])) {
    header("Location: index");
    }

include ('./config/database.php');

$emailValue = '';
$passwordValue = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailValue = isset($_POST['email']) ? $_POST['email'] : '';
    $passwordValue = isset($_POST['password']) ? $_POST['password'] : '';
    $error = "";

    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $email = mysqli_real_escape_string($connect, $email);
    $password = mysqli_real_escape_string($connect, $password);

    $sql = "select * from user where email='$email' and deleted_at IS NULL";
    $result = $connect->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // if($row['role'] == 3){
        //     header("Location: ./employees");
        // }else{
        if ($password == $row['password']) {
            $_SESSION['staff_id'] = $row['id'];
            if ($row['role'] == 3) {
                header("Location: ./doctors");
                } else {
                header("Location: ./index");
                }
            } else {
            $error = "Ungültige Anmeldedaten.";
            }
        // }
        } else {
        $error = "Ungültige Anmeldedaten.";
        }
    }
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

    <!-- Custom Css -->
    <link rel="stylesheet" href="asset/css/index.css">
    <link rel="stylesheet" href="asset/css/backend.css">

    <title>Dr Pleger</title>
</head>

<body>
    <div class="bg-light" style="height: 100vh;width: 100vw;">
        <div class="w-100 h-100 d-flex justify-content-center align-items-center">
            <div class="login-body">
                <div class="login-content mt-5">
                    <div class="d-flex justify-content-center align-items-center">
                        <h3 style="font-weight: 700; font-family: Cambridge-Round-Bold;">Login</h3>
                    </div>
                    <form class="px-2" method="post" id="loginForm">
    <div class="form-group p-2 my-2">
        <label class="my-1" for="email" style="font-family: Cambridge-Round-Regular;">E-Mail</label>
        <input type="text" class="form-control custom-input" name="email" id="email" value="<?php echo $emailValue ?>" placeholder="E-Mail eingeben">
        <p class="error mb-0" id="email-error"></p>
    </div>
    <div class="form-group p-2 my-2">
    <label class="my-1" for="password" style="font-family: Cambridge-Round-Regular;">Passwort</label>
    <div class="d-flex align-items-center input-with-icon hideinputFocus" style="background-color: var(--input-bg); border-radius: 4px;">
        <input type="password" class="form-control custom-input" name="password" id="password" placeholder="Passwort eingeben">
        <i class="bi bi-eye-fill mx-2 mr-4 cursor-pointer" id="toggle-password-icon" onclick="togglePasswordVisibility()"></i>
    </div>
    <p class="error mb-0" id="password-error"></p>
</div>

<script>
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('toggle-password-icon');

    // Toggle password visibility
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        // Change icon to eye-slash when password is visible
        eyeIcon.classList.remove('bi-eye-fill');
        eyeIcon.classList.add('bi-eye-slash-fill');
    } else {
        passwordInput.type = 'password';
        // Change icon back to eye-fill when password is hidden
        eyeIcon.classList.remove('bi-eye-slash-fill');
        eyeIcon.classList.add('bi-eye-fill');
    }
}
</script>

    <div class="d-flex justify-content-end mx-3">
        <p class="mb-0  cursor-pointer"
            onclick="window.location = './forgot_password'" style="font-family: Cambridge-Round-Regular;">Passwort vergessen</p>
    </div>
    <div class="d-flex justify-content-center align-items-center py-2 my-3">
        <button type="submit" class="success-button cursor-pointer">Login</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const togglePasswordButton = document.getElementById('toggle-password');
    const eyeIcon = document.getElementById('eye-icon');

    // Function to toggle the password visibility
    function togglePasswordVisibility() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }

    // Attach the function to the button's click event
    togglePasswordButton.addEventListener('click', togglePasswordVisibility);
});
</script>

                </div>
                <div class="login-logo">
                    <img class="w-100 h-100" style="object-fit: contain;" src="asset/images/logo-2.png" alt="logo" />
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>
    <script src="asset/js/index.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="asset/js/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <?php
    if (!empty($error)) {
        echo "<script>showToast('$error', 'error')</script>";
        }
    ?>

    <?php
    if (isset($_SESSION['forgot_success_message'])) {
        echo "<script>showToast('" . $_SESSION['forgot_success_message'] . "', 'success' );</script>";
        unset($_SESSION['forgot_success_message']);
        unset($_SESSION['admin_email']);
        }
    if (isset($_SESSION['reset_password_message'])) {
        echo "<script>showToast('" . $_SESSION['reset_password_message'] . "', 'success' );</script>";
        unset($_SESSION['reset_password_message']);
        // unset($_SESSION['admin_email']);
        }

    ?>

</body>
</html>