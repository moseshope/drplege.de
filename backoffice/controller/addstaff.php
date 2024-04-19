<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include('../config/mail.php');
require '../vendor/autoload.php';
include('../config/database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{

        $currentURL = "http" . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 's' : '') . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $name = mysqli_real_escape_string($connect,$_POST['name']);
        $qualification = mysqli_real_escape_string($connect,$_POST['qualification']);
        $email = mysqli_real_escape_string($connect,$_POST['email']);
        $originalPassword = $_POST['password'];
        $password = mysqli_real_escape_string($connect,md5($_POST['password']));
        $telephone = mysqli_real_escape_string($connect,$_POST['telephone']);
        $status = mysqli_real_escape_string($connect,$_POST['status']);
        $role = 2;

        if($_FILES["profile"]["name"]){

                $originalFileName = $_FILES["profile"]["name"];
                $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
                $profileName = rand(11111111, 99999999). "." . $extension;
                $path = move_uploaded_file($_FILES["profile"]["tmp_name"], "../../images/" . $profileName);
        }
        else{
                $profileName = NULL;
        }
        $staff_services = isset($_POST['staff_services']) ? $_POST['staff_services'] : array();
        
        if (is_array($staff_services)) {
                $staff_services = implode('__', $staff_services);
        }
        
        $created_at = date("Y-m-d");
        
        $sql = "insert into user(name,qualification,email,password,profile,telephone,status,role,services,created_at) values('$name','$qualification','$email','$password','$profileName','$telephone','$status','$role','$staff_services','$created_at')";
        $query = $connect->query($sql);

        if ($query) {
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
                        $mail->Subject = 'Bestätigung der Registrierung';
                        $mail->isHTML(true);

                        $body = file_get_contents('../mail/add-doctor_mail.php');
                        $body = str_replace('$doctorName', $name, $body);
                        $body = str_replace('$qualification', $qualification, $body);
                        $body = str_replace('$email', $email, $body);
                        $body = str_replace('$password', $originalPassword, $body);
                        $body = str_replace('$telephone', $telephone, $body);
                        $body = str_replace('$serviceName', $staff_services, $body);
                        $mail->Body = $body;    
                        $mail->send();

                $redirectURL = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'default_page';
                header("Location: $redirectURL");
        }

}
?>
