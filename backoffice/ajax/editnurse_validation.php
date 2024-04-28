<?php 
include('../config/database.php');
if ($_SERVER["REQUEST_METHOD"] === "POST") {
   
    $id = mysqli_real_escape_string($connect,$_POST['id']);
    $name = mysqli_real_escape_string($connect,$_POST['name']);
    $email = mysqli_real_escape_string($connect,$_POST['email']);
    if(empty($name)){
        $message = "Name is required";
        echo json_encode(['name' => $message]);
    }else{
        $sql = "select name from user where name = '$name' && deleted_at IS NULL && id != '$id'";
        $result = $connect->query($sql);
        if($result->num_rows > 0){
            $message = "Diese Name existiert bereits.";
            echo json_encode(['name' => $message]);
        }
    }
    if(empty($email)){
        $message = "E-Mail ist erforderlich.";
        echo json_encode(['email' => $message]);
    }elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format";
        echo json_encode(['email' => $message]);
    }else{
        $sql = "select email from user where email = '$email' && deleted_at IS NULL && id != '$id'";
        $result = $connect->query($sql);
        if($result->num_rows > 0){
            $message = "Diese E-Mail existiert bereits.";
            echo json_encode(['email' => $message]);
        }
    }
}
?>