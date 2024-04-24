<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include ('../config/mail.php');
require '../vendor/autoload.php';
include ('../config/database.php');
$doctorId = $_SESSION['staff_id'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $id = $_POST['id'];

        $getData = "select * from patients where id='$id'";
        $getResult = $connect->query($getData);
        $rowData = $getResult->fetch_assoc();
        $service = $rowData['services'];

        $serviceData = "select * from services where id='$service'";
        $serviceQuery = $connect->query($serviceData);
        $serviceRow = $serviceQuery->fetch_assoc();
        $service_Name = $serviceRow['services'];

        $getStaff = "select * from user where id='$doctorId'";
        $result = $connect->query($getStaff);
        $data = $result->fetch_assoc();
        $doctorName = $data['name'];
        $status = isset($_POST['status']) ? $_POST['status'] : $rowData['status'];
        $getDate = isset($_POST['date']) ? $_POST['date'] : $rowData['selected_date'];
        $time = isset($_POST['time']) ? $_POST['time'] : $rowData['visits'];
        $bookingId = $rowData['id'];
        $patientName = $rowData['name'];
        $patientEmail = $rowData['email'];
        $patientTelephone = $rowData['telephone'];
        // $service = $rowData['services'];
        $dateFormate = new DateTime($getDate);
        $date = $dateFormate->format('d.m.Y');

        $recipe = $_POST['recipe'];
        $updated_at = date("Y-m-d");

        // if($status == 'Vollendet'){
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
        $mail->addAddress($patientEmail);
        $mail->Subject = 'Vollständige Terminbestätigung';
        $mail->isHTML(true);
        $body = file_get_contents('../mail/rebooking_patient.php');
        $body = str_replace('$LastInsertId', $bookingId, $body);
        $body = str_replace('$doctor_name', $doctorName, $body);
        $body = str_replace('$PatientName', $patientName, $body);
        $body = str_replace('$appointmentTime', $time, $body);
        $body = str_replace('$appointmentDate', $date, $body);
        $body = str_replace('$serviceName', $service_Name, $body);
        $body = str_replace('$patientEmail', $patientEmail, $body);
        $body = str_replace('$patientTelephone', $patientTelephone, $body);
        $body = str_replace('$recipe', $recipe, $body);
        $mail->Body = $body;
        $mail->send();
        // }

        $sql = "update patients set selected_date='$date', visits='$time', recipe='$recipe', status='$status', updated_at='$updated_at' WHERE id=$id";
        $query = $connect->query($sql);
        if ($query) {
                header("Location: ../patients");
                }
        }
?>