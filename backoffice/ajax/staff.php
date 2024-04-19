<?php 
include('../config/database.php');
if ($_SERVER["REQUEST_METHOD"] === "POST") {
        
    $id = mysqli_real_escape_string($connect,$_POST['id']);

    $sql = "select * from user where id=$id";
    $result = $connect->query($sql);
    if ($result) {
        $staffData = $result->fetch_assoc();
        echo json_encode($staffData);
    }
}
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $doctorId = mysqli_real_escape_string($connect,$_GET['doctorId']);

    $sql = "select time from user where id=$doctorId";
    $result = $connect->query($sql);
    if ($result) {
        $staffData = $result->fetch_assoc();
        echo json_encode($staffData);
    }
}
?>