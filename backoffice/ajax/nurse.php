<?php 
include('../config/database.php');
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = mysqli_real_escape_string($connect,$_POST['id']);
    $sql = "select * from user where id=$id";
    $result = $connect->query($sql);
    if ($result) {
        $NurseData = $result->fetch_assoc();
        echo json_encode($NurseData);
    }   
}
?>