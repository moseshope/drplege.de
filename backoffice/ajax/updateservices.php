<?php
include('../config/database.php');
if($_SERVER["REQUEST_METHOD"] === "POST")
{
    $id = mysqli_real_escape_string($connect,$_POST["servicesId"]);
    $germany = mysqli_real_escape_string($connect,$_POST['services']);
    $english = mysqli_real_escape_string($connect,$_POST['services_en']);
    $updated_at = date("Y-m-d");
    $updateData = "UPDATE services SET services='$germany',services_en='$english',updated_at='$updated_at' WHERE id='$id'";
    $query = $connect->query($updateData);
}
?>