<?php
include('../config/database.php');
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $deletedata = mysqli_real_escape_string($connect, $_POST["id"]);
    $deleted_at = date("Y-m-d");
    $query = "update services set deleted_at='$deleted_at' where id='$deletedata'";
    $result = $connect->query($query);
}
?>