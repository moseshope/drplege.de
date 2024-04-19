<?php 
include('../config/database.php');
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $id = mysqli_real_escape_string($connect,$_GET['id']);
    $currentDate = date("Y-m-d");
    $endDate = date('Y-m-d', strtotime($currentDate . ' + 6 days'));
    $sql = "SELECT selected_date,time FROM time_slots_user WHERE doctor_id = '$id' AND selected_date BETWEEN '$currentDate' AND '$endDate'";   $result = $connect->query($sql);

    if ($result) {
        $timeSlots = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($timeSlots);
    }
}
?>