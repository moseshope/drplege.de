<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include ('../config/database.php');
include ('../config/mail.php');
require '../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = mysqli_real_escape_string($connect, $_POST['id']);
        $deleted_at = date("Y-m-d");
        $sql = "update patients set deleted_at='$deleted_at' where id=$id";
        $query = $connect->query($sql);
        if ($query) {
                $getData = "select * from patients where id='$id'";
                $resultData = $connect->query($getData);
                $rowData = $resultData->fetch_assoc();
                $bookingId = $rowData['id'];
                $doctor = $rowData['doctor'];
                $patientName = $rowData['name'];
                $time = $rowData['visits'];
                $date = $rowData['selected_date'];
                $dateConvert = DateTime::createFromFormat('d.m.Y', $date);
                $formattedDate = $dateConvert->format('Y-m-d');
                $patientEmail = $rowData['email'];

                $doctorData = "select * from user where id='$doctor'";
                $doctorQuery = $connect->query($doctorData);
                $doctorRow = $doctorQuery->fetch_assoc();
                $doctorName = $doctorRow['name'];

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
                $mail->Subject = 'Termin absagen Bestätigung';
                $mail->isHTML(true);

                $body = file_get_contents('../mail/cancel_appointment.php');
                $body = str_replace('$LastInsertId', $bookingId, $body);
                $body = str_replace('$doctor_name', $doctorName, $body);
                $body = str_replace('$PatientName', $patientName, $body);
                $body = str_replace('$appointmentTime', $time, $body);
                $body = str_replace('$appointmentDate', $formattedDate, $body);
                $mail->Body = $body;

                $mail->send();
                header("Location: ./../patients");
                }
        }

?>
