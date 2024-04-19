<?php 
session_start();
$doctorId = $_SESSION['doctor_id'];
include('../config/database.php');

if ($_SERVER["REQUEST_METHOD"] === "GET") {
        $date = $_GET['date'];
        $sql = "DELETE FROM time_slots_user WHERE doctor_id = '$doctorId' && selected_date = '$date'";
        $checkResult = $connect->query($sql);
}
?>