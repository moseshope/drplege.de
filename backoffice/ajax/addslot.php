<?php 
include('../config/database.php');
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if(isset($_POST['slotId']) && isset($_POST['selectedSlot'])){
        $id = mysqli_real_escape_string($connect,$_POST['slotId']);
        $slot = mysqli_real_escape_string($connect,$_POST['selectedSlot']);
        $updated_at = date("Y-m-d");
        $sql = "select * from time_slots where time = '$slot' && deleted_at IS NULL && id != '$id'";
        $result = $connect->query($sql);
        if($result->num_rows > 0){
            $message = "This slot already exits";
            echo json_encode($message);
        }else{
            $sql = "update time_slots set time='$slot', updated_at='$updated_at' where id='$id'";
            $result = $connect->query($sql);
        }
    }
    elseif(isset($_POST['deleteSlotId'])){
        $id = mysqli_real_escape_string($connect,$_POST['deleteSlotId']);
        $deleted_at = date("Y-m-d");
        $sql = "update time_slots set deleted_at='$deleted_at' where id='$id'";
        $result = $connect->query($sql);
    }
    else{
        $slot = mysqli_real_escape_string($connect,$_POST['slot']);
        if(empty($slot)){
            echo 111;
            $message = "Slot is required";
            echo json_encode($message);
        }else{
            $sql = "select * from time_slots where time = '$slot' && deleted_at IS NULL";
            $result = $connect->query($sql);
            if($result->num_rows > 0){
                $message = "This slot already exits";
                echo json_encode($message);
            }else{
                $created_at = date("Y-m-d");
                $sql = "insert into time_slots(time,created_at) values('$slot','$created_at')";
                $result = $connect->query($sql);
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $id = mysqli_real_escape_string($connect,$_GET['dataId']);
    $sql = "select time from time_slots where id = '$id'";
    $result = $connect->query($sql);
    $row = $result->fetch_assoc();
    $timeData = $row['time'];
    echo json_encode($timeData);
}
?>