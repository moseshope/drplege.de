<?php 
include('../config/database.php');
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    
    $id = mysqli_real_escape_string($connect,$_GET['id']);

    $sql = "select * from user where id=$id";
    $result = $connect->query($sql);
    if ($result) {
        $staffData = $result->fetch_assoc();
        // $servicesArray = explode(',', $staffData['services']);
        // print_r($servicesArray);
        echo json_encode($staffData);
    }
}
?>