<?php
session_start();
include('./config/database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $token = $_GET['token'];
    // $email = $_SESSION['admin_email'];
    $password = mysqli_real_escape_string($connect,md5($_POST['password']));
    
    $sql = "update user set password='$password',token = NULL where token='$token'";
    if ($connect->query($sql) === TRUE) {
        $_SESSION['forgot_success_message'] = "Passwort erfolgreich zurückgesetzt.";
        header("Location: ./login");
    }
}
?>
<!DOCTYPE html>
<html lang="de">

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
            <h3 style="font-weight: 700;">Passwort zurücksetzen</h3>
          </div>
          <form class="px-2" id="reset-password" method="post">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="password">Neues Passwort</label>
              <div class="d-flex align-items-center input-with-icon hideinputFocus"
                style="background-color: var(--input-bg); border-radius: 4px;">
                <input type="password" name="password" class="form-control custom-input" id="password"
                  placeholder="Passwort eingeben">
                <i class="bi bi-eye-fill mx-2 mr-4 cursor-pointer" onclick="showPassword('password')"></i>
              </div>
              <p class="error" id="reset-password-error"></p>
            </div>
            <div class="form-group p-2 my-2">
              <label class="my-1" for="confirm_password">Passwort bestätigen</label>
              <div class="d-flex align-items-center input-with-icon hideinputFocus"
                style="background-color: var(--input-bg); border-radius: 4px;">
                <input type="password" name="confirm_password" class="form-control custom-input" id="confirm_password"
                  placeholder="Passwort bestätigen">
                <i class="bi bi-eye-fill mx-2 mr-4 cursor-pointer" onclick="showPassword('confirm_password')"></i>
              </div>
              <p class="error" id="reset-confirm-password-error"></p>
            </div>
            <div class="d-flex justify-content-center align-items-center py-2 my-3">
              <button type="submit" class="success-button cursor-pointer p-1"
                style="font-size: var(--sm-text) !important;">Passwort zurücksetzen</button>
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
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
  </script>
  <script src="./asset/js/index.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
  <script>
  $('#reset-password').validate({
    rules: {
      password: {
        required: true,
        minlength: 6
      },
      confirm_password: {
        required: true,
        minlength: 6,
        equalTo: "#password" // Validation to ensure it matches the password field
      }
    },
    messages: {
      password: {
        required: "Bitte geben Sie Ihr neues Passwort ein.",
        minlength: "Ihr Passwort muss mindestens 6 Zeichen lang sein."
      },
      confirm_password: {
        required: "Bitte geben Sie Ihr neues Passwort ein.",
        minlength: "Ihr Passwort muss mindestens 6 Zeichen lang sein.",
        equalTo: "Passwörter stimmen nicht überein."
      }
    },
    errorPlacement: function(error, element) {
      if (element.attr("name") == "password") {
        error.insertAfter("#reset-password-error");
      } else if (element.attr("name") == "confirm_password") {
        error.insertAfter("#reset-confirm-password-error");
      }
      error.addClass('text-danger');
    },
    highlight: function(element) {
      $(element).siblings('.error').addClass('text-danger');
    },
    unhighlight: function(element) {
      $(element).siblings('.error').removeClass('text-danger');
    },
    submitHandler: function(form) {
      form.submit();
    }
  });
  </script>
  <?php 
    ?>
</body>

</html>