<?php
session_start();
$lang = $_SESSION['lang'];
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include ('./../backoffice/config/mail.php');
require './../backoffice/vendor/autoload.php';
include ('./../backoffice/config/database.php');
header('Content-Type: application/json');
// $doctorMailContent = include('./../doctor_mail.php');


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST['name'] && $_POST['birthdate'] && $_POST['phone']) {

        $createdAt = date("Y-m-d");

        $serviceName = $_POST['serviceName'];
        if ($lang == 'de') {
            $serviceData = "select id from services where services = '$serviceName'";
            } else {
            $serviceData = "select id from services where services_en = '$serviceName'";
            }
        $serviceResult = $connect->query($serviceData);
        $serviceRow = $serviceResult->fetch_assoc();
        $serviceId = $serviceRow['id'];

        $doctorName = $_POST['doctorName'];
        $doctorId = $_POST['doctorId'];
        $doctorData = "select id, email from user where name = '$doctorName'";
        $doctorResult = $connect->query($doctorData);
        $doctorRow = $doctorResult->fetch_assoc();
        $doctorEmail = $doctorRow['email'];
        $time = str_replace(" Uhr", "", $_POST['time']);
        $selectedDate = $_POST['selectedDate'];
        $date = DateTime::createFromFormat('d.m.Y', $selectedDate);
        $appointment_date = $date->format('Y-m-d');

        $name = $_POST['name'];
        $birthdate = $_POST['birthdate'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $reminder = $_POST['reminder'] == 'true' ? 1 : 0;
        $status = "bevorstehen";

        $sql = "INSERT INTO patients (name,birthdate,telephone,email,services,doctor,selected_date,visits,status,reminder,created_at)
            VALUES ('$name', '$birthdate', '$phone', '$email', '$serviceId', '$doctorId', '$selectedDate', '$time', '$status','$reminder', '$createdAt')";

        $result = $connect->query($sql);

        $lastInsertId = $connect->insert_id;

        if ($lastInsertId > 0) {

            if ($reminder == 1) {

                $mail = new PHPMailer(true);
                $mail->CharSet = PHPMailer::CHARSET_UTF8;

                try {
                    $mail->isSMTP();
                    $mail->Host = $host;
                    $mail->SMTPAuth = true;
                    $mail->Username = $user_name;
                    $mail->Password = $user_password;
                    $mail->SMTPSecure = $smtpsecure;
                    $mail->Port = $port;
                    $mail->setFrom($user_name);
                    $mail->addAddress($email);
                    $mail->Subject = 'Terminbestätigung';
                    $mail->isHTML(true);

                    $body = file_get_contents('./../backoffice/mail/patient_mail.php');
                    $body = str_replace('$LastInsertId', $lastInsertId, $body);
                    $body = str_replace('$doctor_name', $doctorName, $body);
                    $body = str_replace('$PatientName', $name, $body);
                    $body = str_replace('$appointmentTime', $time, $body);
                    $body = str_replace('$appointmentDate', $appointment_date, $body);
                    $body = str_replace('$serviceName', $serviceName, $body);
                    $body = str_replace('$patientEmail', $email, $body);
                    $body = str_replace('$patientTelephone', $phone, $body);

                    $mail->Body = $body;
                    $mail->send();

                    } catch (Exception $e) {
                    echo json_encode(['error' => 'Email sending failed. Error: ' . $mail->ErrorInfo]);
                    }
                }

            // $mail2 = new PHPMailer(true);
            // $mail->CharSet = PHPMailer::CHARSET_UTF8;

            // try {
            //     $mail2->isSMTP();
            //     $mail2->Host = $host;
            //     $mail2->SMTPAuth = true;
            //     $mail2->Username = $user_name;
            //     $mail2->Password = $user_password;
            //     $mail2->SMTPSecure = $smtpsecure;
            //     $mail2->Port = $port;
            //     $mail2->setFrom($user_name);
            //     $mail2->addAddress($doctorEmail);
            //     $mail2->Subject = 'Neue Ernennung';
            //     $mail2->isHTML(true);
            //     $body = file_get_contents('./../../mail/doctor_mail.php');
            //     $body = str_replace('$LastInsertId', $lastInsertId, $body);
            //     $body = str_replace('$doctor_name', $doctorName, $body);
            //     $body = str_replace('$PatientName', $name, $body);
            //     $body = str_replace('$appointmentTime', $time, $body);
            //     $body = str_replace('$appointmentDate', $appointment_date, $body);
            //     $body = str_replace('$serviceName', $serviceName, $body);
            //     $body = str_replace('$patientEmail', $email, $body);
            //     $body = str_replace('$patientTelephone', $phone, $body);

            //     $mail2->Body = $body;
            //     $mail2->send();

            // } catch (Exception $e) {
            //     echo json_encode(['error' => 'Email sending failed. Error: ' . $mail2->ErrorInfo]);
            // }

            echo json_encode(['id' => $lastInsertId]);

            } else {
            echo json_encode(['error' => 'Bitte versuchen Sie es später erneut.']);
            }

        } else {
        echo json_encode(['error' => 'Ungültige Daten']);
        }

    } else {
    echo json_encode(['error' => 'Ungültige Anfragemethode']);
    }
?>