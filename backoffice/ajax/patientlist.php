<?php 
include('../config/database.php');
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    
    $id = mysqli_real_escape_string($connect,$_GET['id']);

    $sql = "select * from patients where doctor=$id and status='durchgeführt'";
    $result = $connect->query($sql);
    if ($result) {
        $patientsData = array();
        while ($row = $result->fetch_assoc()) {
            // Add each patient data to the array
            $patientsData[] = $row;
        }
        echo json_encode($patientsData);
    }
}
?>