<?php 
session_start();
include('../config/database.php');
$id = $_SESSION['staff_id'];

$sql = "select * from user where id='$id' and deleted_at IS NULL";
$result = $connect->query($sql);
$row = $result->fetch_assoc();
$role = $row['role'];
if($role == 2){
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $date = $_GET['date'];
            $checkQuery  = "select time from time_slots_user where doctor_id='$id' && selected_date='$date'";
            $checkResult = $connect->query($checkQuery);
    
            $times = array(); 
        
            if ($checkResult->num_rows > 0) {
                
                while ($row = $checkResult->fetch_assoc()) {
                    $times[] = $row['time'];
                }
    
            } else{
                    $times = null;
            }
            
            echo json_encode($times);
    }
}
if($role == 3){

    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        $date = $_GET['date'];
        $bookingId = $_GET['bookingId'];
        $sql = "select doctor from patients where id='$bookingId'";
        $result = $connect->query($sql);
        $rowData = $result->fetch_assoc();
        $doctorId = $rowData['doctor'];   


        $checkQuery  = "select time from time_slots_user where doctor_id='$doctorId' && selected_date='$date'";
        $checkResult = $connect->query($checkQuery);

        $times = array(); 
    
        if ($checkResult->num_rows > 0) {
            
            while ($row = $checkResult->fetch_assoc()) {
                $times[] = $row['time'];
            }

        } else{
                $times = null;
        }
        
        echo json_encode($times);
}

}

?>