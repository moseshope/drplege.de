<?php 
session_start();
include('../config/database.php');
if(!empty($_POST['currentPassword'])){

    $id = $_SESSION['staff_id'];
    
    $doctorData = "select * from user where id='$id'";
    $result = $connect->query($doctorData);
    $row = $result->fetch_assoc();
    
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $currentPassword = md5($_POST['currentPassword']);
        if($currentPassword == $row['password']){
        }else{
            $error = "Aktuelles Passwort ungültig.";
            echo json_encode($error);
        }
    }
}
?>