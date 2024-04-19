<?php 
session_start();
include('../config/database.php');
$id = $_SESSION['staff_id'];
$sql = "select * from user where id='$id' and deleted_at IS NULL";
$result = $connect->query($sql);
$row = $result->fetch_assoc();
$role = $row['role'];
if($role == 1){

    if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $id = mysqli_real_escape_string($connect,$_GET['doctorId']);
            $date = mysqli_real_escape_string($connect,$_GET['selectedDate']);
            $currentDate = date("Y-m-d");
            $sql = "SELECT time FROM time_slots_user WHERE doctor_id = '$id' AND selected_date='$date'";  
            $result = $connect->query($sql);
        
            if ($result && $result->num_rows > 0) {
                $timeSlots = $result->fetch_assoc();
                echo json_encode($timeSlots);
            }
    }
}
else{
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        $date = mysqli_real_escape_string($connect,$_GET['selectedDate']);
            $sql = "SELECT time FROM time_slots_user WHERE doctor_id = '$id' AND selected_date='$date'";  
            $result = $connect->query($sql);
        
            if ($result && $result->num_rows > 0) {
                $timeSlots = $result->fetch_assoc();
                echo json_encode($timeSlots);
            }
    }
}
?>