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
  <title>Dr Pleger</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="asset/css/index.css">
  <link rel="stylesheet" href="asset/css/backend.css">
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
            <div class="form-group p-2 mt-2">
              <label class="my-1" for="email" style="font-family: Cambridge-Round-Regular;">E-Mail</label>
              <input type="email" class="form-control custom-input" name="email" id="email"
                placeholder="E-Mail eingeben">
              <p class="error mb-0" id="email-error"></p>
            </div>
            <div class="form-group p-2 mt-2">
              <label class="my-1" for="password" style="font-family: Cambridge-Round-Regular;">Passwort</label>
              <div class="d-flex align-items-center input-with-icon hideinputFocus"
                style="background-color: var(--input-bg); border-radius: 4px;">
                <input type="password" class="form-control custom-input" name="password" id="password"
                  placeholder="Passwort eingeben">
                <i class="bi bi-eye-fill mx-2 mr-4 cursor-pointer" id="toggle-password-icon"
                  onclick="togglePasswordVisibility()"></i>
              </div>
              <p class="error mb-0" id="password-error"></p>
            </div>
            <div class="d-flex justify-content-end mx-3">
              <p class="mb-0  cursor-pointer" onclick="window.location = './forgot_password'"
                style="font-family: Cambridge-Round-Regular;">Passwort vergessen</p>
            </div>
            <div class="d-flex justify-content-center align-items-center py-2 my-3">
              <button type="submit" class="success-button cursor-pointer">Login</button>
            </div>
          </form>
        </div>
        <div class="login-logo">
          <img class="w-100 h-100" style="object-fit: contain;" src="asset/images/logo-2.png" alt="logo" />
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <!-- jQuery Validate -->
  <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
  <!-- Custom JavaScript -->
  <script src="asset/js/index.js"></script>
  <script>
    $(document).ready(function() {
      $('#loginForm').validate({
        rules: {
          email: {
            required: true,
            email: true
          },
          password: {
            required: true
          }
        },
        messages: {
          email: {
            required: "Bitte geben Sie Ihre E-Mail Adresse ein",
            email: "Bitte geben Sie eine gültige E-Mail Adresse ein"
          },
          password: {
            required: "Bitte geben Sie Ihr Passwort ein"
          }
        },
        errorPlacement: function(error, element) {
          if (element.attr("name") == "email") {
            error.insertAfter(element);
          } else {
            error.insertAfter(element.parent());
          }
        },
        highlight: function(element) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
          $(element).removeClass('is-invalid');
        },
        errorClass: 'text-danger'
      });
    });
  </script>
</body>

</html>