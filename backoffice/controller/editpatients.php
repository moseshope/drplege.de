<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include ('../config/mail.php');
require '../vendor/autoload.php';
include ('../config/database.php');

$staffId = $_SESSION['staff_id'];

$sql = "select * from user where id='$staffId' and deleted_at IS NULL";
$result = $connect->query($sql);
$row = $result->fetch_assoc();
$role = $row['role'];

if ($role == 1) {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $id = mysqli_real_escape_string($connect, $_POST['id']);
                $doctorID = mysqli_real_escape_string($connect, $_POST['doctor']);

                // new doctor 
                $doctorData1 = "select * from user where id='$doctorID'";
                $doctorQuery1 = $connect->query($doctorData1);
                $doctorRow1 = $doctorQuery1->fetch_assoc();
                $doctorEmail1 = $doctorRow1['email'];
                $doctorName1 = $doctorRow1['name'];

                $patientData = "select * from patients where id='$id'";
                $patientQuery = $connect->query($patientData);
                $patientRow = $patientQuery->fetch_assoc();
                $bookingId = $patientRow['id'];
                $doctor = $patientRow['doctor'];
                $service = $patientRow['services'];
                $patientEmail = $patientRow['email'];
                $patientName = $patientRow['name'];
                $patientBirthdate = $patientRow['birthdate'];
                $patientTelephone = $patientRow['telephone'];

                $doctorData = "select * from user where id='$doctor'";
                $doctorQuery = $connect->query($doctorData);
                $doctorRow = $doctorQuery->fetch_assoc();
                $doctorEmail = $doctorRow['email'];
                $doctorName = $doctorRow['name'];

                $serviceData = "select * from services where id='$service'";
                $serviceQuery = $connect->query($serviceData);
                $serviceRow = $serviceQuery->fetch_assoc();
                $service_Name = $serviceRow['services'];


                $time = mysqli_real_escape_string($connect, $_POST['time']);
                $getDate = mysqli_real_escape_string($connect, $_POST['date']);
                $dateFormate = new DateTime($getDate);
                $date = $dateFormate->format('d.m.Y');
                $updated_at = date("Y-m-d");

                $sql = "update patients set doctor='$doctorID',selected_date='$date',visits='$time',updated_at='$updated_at' where id=$id";
                $query = $connect->query($sql);
                if ($query) {

                        // patient mail 
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
                        $mail->Subject = 'Terminverschiebung';
                        $mail->isHTML(true);

                        $body = file_get_contents('../mail/rebooking_patient.php');
                        $body = str_replace('$LastInsertId', $bookingId, $body);
                        $body = str_replace('$doctor_name', $doctorName1, $body);
                        $body = str_replace('$PatientName', $patientName, $body);
                        $body = str_replace('$appointmentTime', $time, $body);
                        $body = str_replace('$appointmentDate', $date, $body);
                        $body = str_replace('$serviceName', $service_Name, $body);
                        $body = str_replace('$patientEmail', $patientEmail, $body);
                        $body = str_replace('$patientTelephone', $patientTelephone, $body);
                        $mail->Body = $body;

                        $mail->send();

                        // new doctor mail 
                        // $mail1 = new PHPMailer(true);
                        // $mail->CharSet = PHPMailer::CHARSET_UTF8;
                        // $mail1->isSMTP();
                        // $mail1->Host = $host;
                        // $mail1->SMTPAuth = true;
                        // $mail1->Username = $user_name;
                        // $mail1->Password = $user_password;       
                        // $mail1->SMTPSecure = $smtpsecure;
                        // $mail1->Port = $port;
                        // $mail1->setFrom($user_name);
                        // $mail1->addAddress($doctorEmail1);
                        // $mail1->Subject = 'Neue Ernennung';
                        // $mail1->isHTML(true);
                        // $mail->CharSet = PHPMailer::CHARSET_UTF8;
                        // $body1 = file_get_contents('./../../mail/doctor_mail.php');
                        // $body1 = str_replace('$LastInsertId', $bookingId, $body);
                        // $body1 = str_replace('$doctor_name', $doctorName1, $body);
                        // $body1 = str_replace('$PatientName', $patientName, $body);
                        // $body1 = str_replace('$appointmentTime', $time, $body);
                        // $body1 = str_replace('$appointmentDate', $date, $body);
                        // $body1 = str_replace('$serviceName', $service_Name, $body);
                        // $body1 = str_replace('$patientEmail', $patientEmail, $body);
                        // $body1 = str_replace('$patientTelephone', $patientTelephone, $body);

                        // $mail1->Body = $body1;
                        // $mail1->send();

                        // old doctor mail 
                        // $mail2 = new PHPMailer(true);
                        // $mail->CharSet = PHPMailer::CHARSET_UTF8;
                        // $mail2->isSMTP();
                        // $mail2->Host = $host;
                        // $mail2->SMTPAuth = true;
                        // $mail2->Username = $user_name;
                        // $mail2->Password = $user_password;       
                        // $mail2->SMTPSecure = $smtpsecure;
                        // $mail2->Port = $port;
                        // $mail2->setFrom($user_name);
                        // $mail2->addAddress($doctorEmail);
                        // $mail2->Subject = 'Termin löschen Bestätigung';
                        // $mail2->isHTML(true);

                        // $body = file_get_contents('./../../mail/rebooking_doctor.php');
                        // $body = str_replace('$LastInsertId', $bookingId, $body);
                        // $body = str_replace('$doctor_name', $doctorName, $body);
                        // $body = str_replace('$PatientName', $patientName, $body);
                        // $body = str_replace('$appointmentTime', $time, $body);
                        // $body = str_replace('$appointmentDate', $date, $body);
                        // $body = str_replace('$serviceName', $service_Name, $body);
                        // $body = str_replace('$patientEmail', $patientEmail, $body);
                        // $body = str_replace('$patientTelephone', $patientTelephone, $body);

                        // $mail2->Body = $body;
                        // $mail2->send();

                        $redirectURL = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'default_page';
                        header("Location: $redirectURL");
                        }
                }
        } else {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $id = mysqli_real_escape_string($connect, $_POST['id']);

                $patientData = "select * from patients where id='$id'";
                $patientQuery = $connect->query($patientData);
                $patientRow = $patientQuery->fetch_assoc();
                $patientEmail = $patientRow['email'];
                $patientName = $patientRow['name'];
                $service = $patientRow['services'];
                $patientBirthdate = $patientRow['birthdate'];
                $patientTelephone = $patientRow['telephone'];
                $bookingId = $patientRow['id'];

                $doctorData = "select * from user where id='$staffId'";
                $doctorQuery = $connect->query($doctorData);
                $doctorRow = $doctorQuery->fetch_assoc();
                $doctorEmail = $doctorRow['email'];
                $doctorName = $doctorRow['name'];

                $serviceData = "select * from services where id='$service'";
                $serviceQuery = $connect->query($serviceData);
                $serviceRow = $serviceQuery->fetch_assoc();
                $service_Name = $serviceRow['services'];


                $time = mysqli_real_escape_string($connect, $_POST['time']);
                $getDate = mysqli_real_escape_string($connect, $_POST['date']);
                $dateFormate = new DateTime($getDate);
                $date = $dateFormate->format('d.m.Y');
                $updated_at = date("Y-m-d");

                $sql = "update patients set doctor='$staffId',selected_date='$date',visits='$time',updated_at='$updated_at' where id=$id";
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
                        $mail->addAddress($patientEmail);
                        $mail->Subject = 'Termin aktualisieren Bestätigen';
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
                        $mail->Body = $body;
                        $mail->send();

                        $redirectURL = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'default_page';
                        header("Location: $redirectURL");
                        }
                }
        }

?>
