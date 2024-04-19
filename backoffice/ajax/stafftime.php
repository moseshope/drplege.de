<?php 
session_start();
$doctorId = $_SESSION['staff_id'];
include('../config/database.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // echo 111;
        $current_date = date("Y-m-d");
        $date = $_POST['date'] ? $_POST['date'] : $current_date;
        foreach ($date as $selectedDate) {
                // Check if 'selectedValues' is set in $_POST
                if (isset($_POST['selectedValues'])) {
                    $selectedValues = $_POST['selectedValues'];
                    $times = json_encode($selectedValues);
        
                    $checkQuery = "SELECT * FROM time_slots_user WHERE doctor_id='$doctorId' AND selected_date='$selectedDate'";
                    $checkResult = $connect->query($checkQuery);
        
                    if ($checkResult->num_rows > 0) {
                        $updateQuery = "UPDATE time_slots_user SET time = '$times' WHERE doctor_id = '$doctorId' AND selected_date = '$selectedDate'";
                        $updateResult = $connect->query($updateQuery);
                        if (!$updateResult) {
                            echo "Error updating record: " . $connect->error;
                        }
                    } else {
                        $insertQuery = "INSERT INTO time_slots_user (doctor_id, selected_date, time) VALUES ('$doctorId', '$selectedDate', '$times')";
                        $insertResult = $connect->query($insertQuery);
                        if (!$insertResult) {
                            echo "Error inserting record: " . $connect->error;
                        }
                    }
                } 
            }
}
if ($_SERVER["REQUEST_METHOD"] === "GET") {
        $date = $_GET['date'];
        $doctorId = $_GET['doctorId'];
        
        $checkQuery  = "select time from time_slots_user where doctor_id='$doctorId' && selected_date='$date'";
        $checkResult = $connect->query($checkQuery);

        $times = array(); // Initialize an array to store the response
    
        if ($checkResult->num_rows > 0) {
        //     $times = array(); // Initialize an array to store the times
            
            while ($row = $checkResult->fetch_assoc()) {
                $times[] = $row['time'];
            }

        } else{
                $times = null;
        }
        
        // Send the JSON-encoded response
        // header('Content-Type: application/json');
        echo json_encode($times);
        
}
?>