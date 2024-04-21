<?php 
session_start();
if (!isset($_SESSION['staff_id'])) {
    header("Location: login");
}
include('config/database.php');
include('layout/header.php');
include('layout/sidebar.php');

$id = $_SESSION['staff_id'];
$sql = "select * from user where id='$id' and deleted_at IS NULL";
$result = $connect->query($sql);
$row = $result->fetch_assoc();
$role = $row['role'];

if($role == 1){

    $adminData = "select * from user where id='$id'";
    $result = $connect->query($adminData);
    $row = $result->fetch_assoc();
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") 
    {
        $current_password = mysqli_real_escape_string($connect,md5($_POST['current_password']));
    
        if($current_password == $row['password']){
            $password = mysqli_real_escape_string($connect,md5($_POST['password']));
            $confirm_password = mysqli_real_escape_string($connect,$_POST['confirm_password']);
        
            $sql = "update user set password='$password' where id='$id'";
            if ($connect->query($sql) === TRUE) {
            }
        }else{
            $error = "Invalid current password.";
        }
    }
}else{
    $doctorData = "select * from user where id='$id'";
    $result = $connect->query($doctorData);
    $row = $result->fetch_assoc();
    $profile = $row['profile'];
    if(!empty($profile)){

        $filePath = 'https://drpleger.de/termin-buchen/images/'.$profile;
    }else{
        $filePath = 'https://drpleger.de/termin-buchen/images/logo.png';
    }


    if ($_SERVER["REQUEST_METHOD"] == "POST") 
    {
        $password = $_POST['password'];
        if($password){
            
            $password = mysqli_real_escape_string($connect,md5($_POST['password']));
            $confirm_password = mysqli_real_escape_string($connect,$_POST['confirm_password']);
            if($_FILES["profile"]["name"]){
                    
                    
                    $originalFileName = $_FILES["profile"]["name"];
                    $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
                    $profileName = rand(11111111, 99999999). "." . $extension;
                    $path = move_uploaded_file($_FILES["profile"]["tmp_name"], "https://drpleger.de/termin-buchen/images/" . $profileName);
                }
                else{
                        $profileName = $profile;
                }
            
                $sql = "update user set password='$password',profile='$profileName' where id='$id'";
            }else{
                if($_FILES["profile"]["name"]){
                    
                    
                    $originalFileName = $_FILES["profile"]["name"];
                    $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
                    $profileName = rand(11111111, 99999999). "." . $extension;
                    $path = move_uploaded_file($_FILES["profile"]["tmp_name"], "https://drpleger.de/termin-buchen/images/" . $profileName);
                }
                else{
                        $profileName = $profile;
                }
            
                $sql = "update user set profile='$profileName' where id='$id'";
                if($sql == true)
                { ?>
                   <script>
                    window.location="profile";
                   </script>
          <?php } 
            }
        
            // echo $sql;
            $result = $connect->query($sql);
            if ($result) {
                $doctorData1 = "select * from user where id='$id'";
                $result = $connect->query($doctorData1);
                $row = $result->fetch_assoc();
                $profile = $row['profile'];
                if(!empty($profile)){
        
                    $filePath = 'https://drpleger.de/termin-buchen/images/'.$profile;
                }
            }
        
    }
}
?>

    <!-- Main -->
    <div id="main-content">
        <div class="p-2 w-100">
            <div class="d-flex justify-content-center align-items-center">
                <h1 class="page-heading">Profil</h1>
            </div>
            <div class="px-2 profile-form ">
                <?php if($role == 1) {?>
                    
                    <form method="post" id="profileForm">
                        <div class="row">
                            <div class="col-lg-5 col-12">
                        <div class="form-group p-2 my-2">
                                    <label class="my-1" for="name">Name</label>
                                    <input type="text" class="form-control custom-input" value="<?php echo $row['name']?>" disabled>
                                </div>
                                <div class="form-group p-2 my-2">
                                    <label class="my-1" for="name">E-Mail</label>
                                    <input type="text" class="form-control custom-input" value="<?php echo $row['email']?>" disabled>
                                </div>
                                <!-- <div class="form-group p-2 my-2">
                                    <label class="my-1" for="name">Current password</label>
                                    <input type="password" name="current_password" class="form-control custom-input" id="current_password"
                                        placeholder="Enter current password" value="">
                                    <span class="error" id="current_password-error"></span>
                                </div> -->
                                <div class="form-group p-2 my-2">
                                <label class="my-1" for="password">Aktuelles Passwort</label>
                                    <div class="password-input-container position-relative d-flex align-items-center">
                                    <!-- Password input -->
                                    <input type="password" name="current_password" class="form-control custom-input pe-5" id="current_password"
                                        placeholder="Aktuelles Passwort eingeben" required>
                                    <!-- Eye button to toggle visibility -->
                                    <span class="toggle-current_password position-absolute end-0" style="margin-right:16px;" role="button" id="toggle-current_password">
                                        <i class="fas fa-eye" id="eye-icon-current"></i>
                                    </span>
                                    </div>
                                    <span class="error" id="current_password-error"></span>
                                </div>

                                <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const passwordInputCurrent = document.getElementById('current_password');
                                    const togglePasswordButtonCurrent = document.getElementById('toggle-current_password');
                                    const eyeIconCurrent = document.getElementById('eye-icon-current');

                                    // Function to toggle the password visibility
                                    function togglePasswordVisibilityCurrent() {
                                        if (passwordInputCurrent.type === 'password') {
                                            passwordInputCurrent.type = 'text';
                                            eyeIconCurrent.classList.remove('fa-eye');
                                            eyeIconCurrent.classList.add('fa-eye-slash');
                                        } else {
                                            passwordInputCurrent.type = 'password';
                                            eyeIconCurrent.classList.remove('fa-eye-slash');
                                            eyeIconCurrent.classList.add('fa-eye');
                                        }
                                    }

                                    // Attach the function to the button's click event
                                    togglePasswordButtonCurrent.addEventListener('click', togglePasswordVisibilityCurrent);
                                });
                            </script>

                                <div class="form-group p-2 my-2">
                                <label class="my-1" for="password">Neues Passwort</label>
                                    <div class="password-input-container position-relative d-flex align-items-center">
                                    <!-- Password input -->
                                    <input type="password" name="password" class="form-control custom-input pe-5" id="password"
                                        placeholder="Passwort eingeben" required>
                                    <!-- Eye button to toggle visibility -->
                                    <span class="toggle-password position-absolute end-0" style="margin-right:16px;" role="button" id="toggle-password">
                                        <i class="fas fa-eye" id="eye-icon"></i>
                                    </span>
                                    </div>
                                    <span class="error" id="password-error"></span>
                                </div>
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

                                <div class="form-group p-2 my-2">
                                <label class="my-1" for="password">Passwort bestätigen</label>
                                    <div class="password-input-container position-relative d-flex align-items-center">
                                    <!-- Password input -->
                                    <input type="password" name="confirm_password" class="form-control custom-input pe-5" id="confirm_password"
                                        placeholder="Passwort bestätigen" required>
                                    <!-- Eye button to toggle visibility -->
                                    <span class="toggle-password position-absolute end-0" style="margin-right:16px;" role="button" id="toggle-confirm_password">
                                        <i class="fas fa-eye" id="eye-icon-confirm"></i>
                                    </span>
                                    </div>
                                    <span class="error" id="confirm_password-error"></span>
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        const passwordInputConfirm = document.getElementById('confirm_password');
                                        const togglePasswordButtonConfirm = document.getElementById('toggle-confirm_password');
                                        const eyeIconConfirm = document.getElementById('eye-icon-confirm');

                                        // Function to toggle the password visibility
                                        function togglePasswordConfirmVisibility() {
                                            if (passwordInputConfirm.type === 'password') {
                                                passwordInputConfirm.type = 'text';
                                                eyeIconConfirm.classList.remove('fa-eye');
                                                eyeIconConfirm.classList.add('fa-eye-slash');
                                            } else {
                                                passwordInputConfirm.type = 'password';
                                                eyeIconConfirm.classList.remove('fa-eye-slash');
                                                eyeIconConfirm.classList.add('fa-eye');
                                            }
                                        }
                                        // Attach the function to the button's click event
                                        togglePasswordButtonConfirm.addEventListener('click', togglePasswordConfirmVisibility);
                                    });
                                </script>

                                <div class="p-2 my-3">
                                    <button type="button" id="profileSubmit" class="success-button cursor-pointer" data-bs-toggle="modal">Speichern</button>
                                </div>
                            </div>
                        </div>
    
                        <!-- Confirmation -->
                        <div class="modal fade " id="Confirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                                    <div class="d-flex justify-content-center align-items-center flex-column">
                                        <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
                                        <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal" style="margin-right: 3px;">Nein</button>
                                        <button type="button" class="success-button cursor-pointer" data-bs-target="#show-info"
                                            data-bs-toggle="modal" data-bs-dismiss="modal" style="margin-left: 3px;">Ja</button>
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        <div class="modal fade " id="show-info" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered " role="document">
                                <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                                    <div class="d-flex justify-content-center align-items-center flex-column py-4">
                                        <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Profil erfolgreich aktualisiert.</h1>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <button type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal">Okay</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php }else{?>
                    <form method="post" id="profileForm" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-lg-5 col-12">
			        <div class="form-group p-2 my-2">
                                <label class="my-1" for="name">Name</label>
                                <input type="text" class="form-control custom-input" value="<?php echo $row['name']?>" disabled>
                            </div>
                            <div class="form-group p-2 my-2">
                                <label class="my-1" for="name">E-Mail</label>
                                <input type="text" class="form-control custom-input" value="<?php echo $row['email']?>" disabled>
                            </div>
                            
                            <div class="form-group p-2 my-2">
                                <label class="my-1" for="password">Aktuelles Passwort</label>
                                <div class="d-flex align-items-center input-with-icon hideinputFocus" style="background-color: var(--input-bg); border-radius: 4px;">
                                    <input type="password" name="current_password" class="form-control custom-input" id="current_password"
                                        placeholder="Aktuelles Passwort eingeben">
                                    <i class="bi bi-eye-fill mx-2 mr-4 cursor-pointer"
                                        onclick="showPassword('current_password')"></i>
                                </div>
                                <span  class="error" id="currentpassword-error"></span>

                            </div>
                            <div class="form-group p-2 my-2">
                                <label class="my-1" for="password">Neues Passwort</label>
                                <div class="d-flex align-items-center input-with-icon hideinputFocus" style="background-color: var(--input-bg); border-radius: 4px;">
                                    <input type="password" name="password" class="form-control custom-input" id="password"
                                        placeholder="Passwort eingeben">
                                    <i class="bi bi-eye-fill mx-2 mr-4 cursor-pointer"
                                        onclick="showPassword('password')"></i>
                                </div>
                                <span  class="error" id="password-error"></span>

                            </div>
                            <div class="form-group p-2 my-2">
                                <label class="my-1" for="confirm_password">Passwort bestätigen</label>
                                <div class="d-flex align-items-center input-with-icon hideinputFocus" style="background-color: var(--input-bg);  border-radius: 4px">
                                    <input type="password" name="confirm_password" class="form-control custom-input" id="confirm_password"
                                        placeholder="Passwort bestätigen">
                                    <i class="bi bi-eye-fill mx-2 mr-4 cursor-pointer"
                                        onclick="showPassword('confirm_password')"></i>
                                </div>
                                <span  class="error" id="confirm_password-error"></span>

                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group p-2 my-2">
                                    <label class="my-1" for="Status">Profil</label>
                                    <input hidden type="file" name="profile" class="form-control custom-input d-none" id="profile-image" aria-invalid="false">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-2">
                                            <button class="btn custom-main-btn" type="button" id="open-image-picker">
                                                <i class="bi bi-upload mr-1"></i> Bild hochladen
                                            </button>
                                        </div>
                                        <div>
                                            <?php 
                                                echo '<img src="'.$filePath.'" class="mx-5" height="100" width="100" id="image-pre">' ;
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2 my-3">
                                <button type="button" id="profileSubmitDoctor" class="success-button cursor-pointer" data-bs-toggle="modal">Speichern</button>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmation -->
                    <div class="modal fade " id="Confirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                                <div class="d-flex justify-content-center align-items-center flex-column">
                                    <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
                                    <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
                                </div>
                                <div class="d-flex justify-content-center align-items-center">
                                    <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal" style="margin-right: 3px;">Nein</button>
                                    <button type="button" class="success-button cursor-pointer" data-bs-target="#show-info"
                                        data-bs-toggle="modal" data-bs-dismiss="modal" style="margin-left: 3px;">Ja</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade " id="show-info" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered " role="document">
                            <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                                <div class="d-flex justify-content-center align-items-center flex-column py-4">
                                    <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Profil erfolgreich aktualisiert.</h1>
                                </div>
                                <div class="d-flex justify-content-center align-items-center">
                                    <button type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal">Okay</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <?php }?>
            </div>
        </div>
    </div>

    <!-- Modals -->


    

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <script src="asset/js/index.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <?php if($role == 1){?>
        <script src="asset/js/script.js"></script>
    <?php }else{?>
        <script src="asset/js/script2.js"></script>
        <script>
        document.getElementById("open-image-picker").addEventListener('click',()=>{document.getElementById('profile-image').click()});
            document.getElementById('profile-image').addEventListener('change',(event)=>{
                document.getElementById('image-pre').src = URL.createObjectURL(event.target.files[0]);
            })
        </script>
    <?php }?>
    <!-- <script type="text/javascript">
        $(document).ready(function() {
    // $('#profileForm').validate({
    //     rules: {
    //         email: {
    //             required: true,
    //             email: true 
    //         },
    //         password: {
    //             required: true,
    //             minlength: 6
    //         },
    //         confirm_password: {
    //             required: true,
    //             minlength: 6,
    //             equalTo: "#password" // Validation to ensure it matches the password field
    //         }
    //     },
    //     messages: {
    //         email: {
    //             required: "Please enter your email address",
    //             email: "Please enter a valid email address"
    //         },
    //         password: {
    //             required: "Please enter your password",
    //             minlength: "Your password must be at least 6 characters long"
    //         },
    //         confirm_password: {
    //             required: "Please enter your password",
    //             minlength: "Your password must be at least 6 characters long",
    //             equalTo: "Passwords do not match"
    //         }
    //     },
    //     errorPlacement: function(error, element) {
    //         if (element.attr("name") == "email") {
    //             error.insertAfter("#email-error");
    //         } else if (element.attr("name") == "password") {
    //             error.insertAfter("#password-error");
    //         } else if (element.attr("name") == "confirm_password") {
    //             error.insertAfter("#confirm_password-error");
    //         }
    //         error.addClass('text-danger');
    //     },
    //     highlight: function(element) {
    //         $(element).siblings('.error').addClass('text-danger');
    //     },
    //     unhighlight: function(element) {
    //         $(element).siblings('.error').removeClass('text-danger');
    //     },
    //     submitHandler: function(form) {
    //         form.submit();
    //     }
    // });

    // $('#profileSubmit').on('click', function() {
    //     if ($('#profileForm').valid()) {
    //         $('#Confirmation').modal('show');
    //     }
    // });
});
    </script> -->
    <!-- logout script  -->
    <?php include('layout/script.php')?>
</body>

</html>