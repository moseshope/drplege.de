<?php
include('../config/database.php');
if ($_SERVER["REQUEST_METHOD"] === "POST") {
   $germany = mysqli_real_escape_string($connect,$_POST['services']);
   $english = mysqli_real_escape_string($connect,$_POST['services_en']);
   $created_at = date("Y-m-d");
   $insertData = "INSERT INTO services (services, services_en,created_at) VALUES ('$germany', '$english','$created_at')";
   $result =  $connect->query($insertData);
}
?>