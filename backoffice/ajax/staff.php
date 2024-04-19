<?php
include ('../config/database.php');
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id = mysqli_real_escape_string($connect, $_POST['id']);

    $sql = "select * from user where id=$id";
    $result = $connect->query($sql);
    if ($result) {
        $staffData = $result->fetch_assoc();
        $getService = "SELECT user_id, service_id FROM services_docs WHERE user_id='" . $staffData['id'] . "'";
        $services = [];
        $sericesResult = $connect->query($getService);
        if ($sericesResult->num_rows > 0) {
            while ($row1 = $sericesResult->fetch_assoc()) {
                $services[] = $row1['service_id'];
                }
            $staffData['services'] = $services;
            }
        echo json_encode($staffData);
        }
    }
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $doctorId = mysqli_real_escape_string($connect, $_GET['doctorId']);

    $sql = "select time from user where id=$doctorId";
    $result = $connect->query($sql);
    if ($result) {
        $staffData = $result->fetch_assoc();
        echo json_encode($staffData);
        }
    }
?>