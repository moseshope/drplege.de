<?php 
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include('./config/mail.php');
include('./config/database.php');
require './vendor/autoload.php';

$emailValue = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $emailValue = isset($_POST['email']) ? $_POST['email'] : '';
    $email = mysqli_real_escape_string($connect,$_POST['email']);

    $sql = "select * from user where email='$email'";
    $result = $connect->query($sql);
    $row = $result->fetch_assoc();
    if ($result->num_rows == 1) {
        $name = $row['name'];
        $token = '';
        $token = md5(uniqid(rand(), true));
        $sql = "select * from admins where token='$token'";
        $result = $connect->query($sql);
        if ($result->num_rows == 1) {
            $token = md5(uniqid(rand(), true));
        }else{
            $sql = "update user set token='$token' where email='$email'";
            $result = $connect->query($sql);
            $mail = new PHPMailer(true);
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->isSMTP();
            $mail->Host = $host;
            $mail->SMTPAuth = true;
            $mail->Username = $user_name;
            $mail->Password = $user_password;
            $mail->SMTPSecure = $smtpsecure;
            $mail->Port = $port;
            $mail->setFrom($user_name);
            $mail->addAddress($email);
            $mail->Subject = 'Passwort zurücksetzen';
            $mail->isHTML(true);
            $link = 'https://drpleger.de/ct/bo/admin/reset_password?token='.$token.'';
            $body = file_get_contents('./mail/forgot_mail.php');
            $body = str_replace('$Name', $name, $body);
            $body = str_replace('$Link', $link, $body);
            $mail->Body = $body;
            $mail->send();
            $_SESSION['reset_password_message']= 'Passwort-Reset-Link per E-Mail gesendet.';
    
            header("Location: ./login");
        }
    }else{
        $error = "Ungültige Anmeldeinformationen.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

    <!-- Custom Css -->
    <link rel="stylesheet" href="./asset/css/index.css">

    <title>Dr Pleger</title>
</head>

<body>

    <div class="bg-light" style="height: 100vh;width: 100vw;">
        <div class="w-100 h-100 d-flex justify-content-center align-items-center">
            <div class="login-body">
                <div class="login-content mt-5">
                    <div class="d-flex justify-content-center align-items-center">
                        <h3 style="font-weight: 700;">Passwort vergessen</h3>
                    </div>
                    <form class="px-2" method="post" id="forgot-password">
                        <div class="form-group p-2 my-2">
                            <label class="my-1" for="email">E-Mail</label>
                            <input type="text" name="email" value="<?php echo $emailValue?>" class="form-control custom-input" id="email" placeholder="E-Mail eingeben">
                            <p  class="error m-0" id="forgot-email-error"></p>
                        </div>
                        <div class="d-flex justify-content-end mx-3">
                            <p class="mb-0  cursor-pointer"
                                onclick="window.location = './login'">Zurück zum Login</p>
                        </div>
                        <div class="d-flex justify-content-center align-items-center py-2 my-3">
                            <button type="submit" class="success-button cursor-pointer p-1"
                                style="font-size: var(--sm-text) !important;"
                                >Passwort vergessen</button>
                        </div>
                    </form>
                </div>
                <div class="login-logo">
                    <img class="w-100 h-100" style="object-fit: contain;" src="./asset/images/logo-2.png" alt="logo" />
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="./asset/js/index.js"></script>
    <script>
        $(document).ready(function() {

            $('#forgot-password').validate({
                rules: {
                    email: {
                        required: true,
                    },
                },
                messages: {
                    services: {
                        required: "Dienstleistungen ist erforderlich.",
                    },
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") == "email") {
                        error.insertAfter("#forgot-email-error");
                    }
                    error.addClass('text-danger');
                },
                highlight: function(element) {
                    $(element).siblings('.error').addClass('text-danger'); 
                },
                unhighlight: function(element) {
                    $(element).siblings('.error').removeClass('text-danger'); 
                },
            });
        });

    </script>
    <?php
        if (!empty($error)) {
            echo "<script>showToast('$error', 'error')</script>";
        }
    ?>
</body>

</html>