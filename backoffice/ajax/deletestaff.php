<?php 
include('../config/database.php');
if ($_SERVER["REQUEST_METHOD"] === "GET") {
        
    $id = mysqli_real_escape_string($connect,$_GET['id']);
        echo json_encode($id);
}
?>