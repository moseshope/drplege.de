<?php 
include('../config/database.php');
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $id = $_GET['patientId'];
    $sql = "update patients set status='Abgesagt' where id='$id'";
    $result = $connect->query($sql);
    echo json_encode(true);
}

?>