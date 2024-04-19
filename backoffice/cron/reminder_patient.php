<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include('../config/database.php');
include('../config/mail.php');
require '../vendor/autoload.php';
$currentDate = date("Y-m-d");
$date = new DateTime($currentDate);
$date->modify('+1 day');
$formattedDate = $date->format('d.m.Y');

$sql = "select * from patients where selected_date = '$formattedDate' AND deleted_at IS NULL";
$result = $connect->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $emails = $row['email'];
        $bookingId = $row['id'];
        $patientName = $row['name'];
        $doctorId = $row['doctor'];
        $serviceId = $row['services'];
        $time = $row['visits'];
        $date = $row['selected_date'];

        $getDoctor = "select name from staffs where id = '$doctorId'";
        $doctorResult = $connect->query($getDoctor);
        $docotrRow = $doctorResult->fetch_assoc();
        $doctorName = $docotrRow['name'];

        $getService = "select services from services where id = '$serviceId'";
        $serviceResult = $connect->query($getService);
        $serviceRow = $serviceResult->fetch_assoc();
        $serviceName = $serviceRow['services'];

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
        $mail->addAddress($emails);
        $mail->Subject = 'Terminerinnerung bei Dr. Plegers';
        $mail->isHTML(true);

        $body = file_get_contents('../mail/Reminder_patient_mail.php');
        $body = str_replace('$LastInsertId', $bookingId, $body);
        $body = str_replace('$doctor_name', $doctorName, $body);
        $body = str_replace('$PatientName', $patientName, $body);
        $body = str_replace('$appointmentTime', $time, $body);
        $body = str_replace('$appointmentDate', $date, $body);
        $body = str_replace('$serviceName', $serviceName, $body);
        $body = str_replace('$patientEmail', $emails, $body);

        $mail->Body = $body;
       
        if($mail->send()) {
            $response =  'success';
        } else {
            $response = 'error';
        }
        $updateData = "update patients set mail_status = 1, response = '$response' where email='$emails'";
        $updateResult = $connect->query($updateData);
        echo $response;
    }
}               
?>
