<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include('../config/mail.php');
require '../vendor/autoload.php';
include('../config/database.php');

if ($_SERVER["REQUEST_METHOD"] === "GET") {

    $id = $_GET['patientId'];
    $sql = "select * from patients where id=$id";
    $result = $connect->query($sql);
    if ($result) {
        $patientData = $result->fetch_assoc();
        echo json_encode($patientData);
    }

}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if(isset($_POST['cancelPatientId'])){
        $id = $_POST['cancelPatientId'];
        echo $id;
        $status = "abgesagt";
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
        $service = $rowData['services'];
        $patientEmail = $rowData['email'];
        $patientTelephone = $rowData['telephone'];

        $doctorData = "select * from user where id='$doctor'";
        $doctorQuery = $connect->query($doctorData);
        $doctorRow = $doctorQuery->fetch_assoc();
        $doctorEmail = $doctorRow['email'];
        $doctorName = $doctorRow['name'];

        $serviceData = "select * from services where id='$service'";
        $serviceQuery = $connect->query($serviceData);
        $serviceRow = $serviceQuery->fetch_assoc();
        $service_Name = $serviceRow['services'];
        
        $sql = "update patients set status='$status' where id=$id";
        $result = $connect->query($sql);
        if ($result) {

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
            $mail->Subject = 'Termin absagen Bestätigung';
            $mail->isHTML(true);

            $body = file_get_contents('./../../mail/cancel_appointment.php');
            $body = str_replace('$LastInsertId', $bookingId, $body);
            $body = str_replace('$doctor_name', $doctorName, $body);
            $body = str_replace('$PatientName', $patientName, $body);
            $body = str_replace('$appointmentTime', $time, $body);
            $body = str_replace('$appointmentDate', $formattedDate, $body);
            $body = str_replace('$serviceName', $service_Name, $body);
            $body = str_replace('$patientEmail', $patientEmail, $body);
            $body = str_replace('$patientTelephone', $patientTelephone, $body);
            $mail->Body = $body;

            $mail->send();
        }
        echo json_encode(true);
    }
    if(isset($_POST['deletePatientId'])){
        $id = $_POST['deletePatientId'];
        echo $id;
        $deleted_at = date("Y-m-d");
        $sql = "update patients set deleted_at='$deleted_at' where id=$id";
        if ($connect->query($sql) === TRUE) {
            $sqlSelect = "SELECT * FROM patients WHERE id=$id";
            $result = $connect->query($sqlSelect);
            $row = $result->fetch_assoc();
            $bookingId = $row['id'];
            $doctor = $row['doctor'];
            $patientName = $row['name'];
            $time = $row['visits'];
            $date = $row['selected_date'];
            $dateConvert = DateTime::createFromFormat('d.m.Y', $date);
            $formattedDate = $dateConvert->format('Y-m-d');
            $patientEmail = $row['email'];

            $doctorData = "select * from user where id='$doctor'";
            $doctorQuery = $connect->query($doctorData);
            $doctorRow = $doctorQuery->fetch_assoc();
            $doctorName = $doctorRow['name'];

            // $mail = new PHPMailer(true);
            // $mail->CharSet = PHPMailer::CHARSET_UTF8;
            // $mail->isSMTP();
            // $mail->Host = $host;
            // $mail->SMTPAuth = true;
            // $mail->Username = $user_name;
            // $mail->Password = $user_password;       
            // $mail->SMTPSecure = $smtpsecure;
            // $mail->Port = $port;
            // $mail->setFrom($user_name);
            // $mail->addAddress($patientEmail);
            // $mail->Subject = 'Termin absagen Bestätigung';
            // $mail->isHTML(true);

            // $body = file_get_contents('./../../mail/cancel_appointment.php');
            // $body = str_replace('$LastInsertId', $bookingId, $body);
            // $body = str_replace('$doctor_name', $doctorName, $body);
            // $body = str_replace('$PatientName', $patientName, $body);
            // $body = str_replace('$appointmentTime', $time, $body);
            // $body = str_replace('$appointmentDate', $formattedDate, $body);
            // $mail->Body = $body;

            // $mail->send();
        }
        echo json_encode(true);
    }
    
}

?>