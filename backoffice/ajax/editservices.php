<?php
include('../config/database.php');
if($_SERVER["REQUEST_METHOD"] === "GET")
{
    $id = mysqli_real_escape_string($connect,$_GET["servicesId"]);
    $sql = "select * from services where id = '$id'";
    $result = $connect->query($sql);
    $row = $result->fetch_assoc();
    $serviceId = $row["id"];
    $germany = $row["services"];
    $english = $row["services_en"];
    echo json_encode(['germany' => $germany,'english' => $english,'serviceId' => $serviceId]);
}
?>