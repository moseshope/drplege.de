<?php 
include('../config/database.php');
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = mysqli_real_escape_string($connect,$_POST['name']);
    $email = mysqli_real_escape_string($connect,$_POST['email']);
    $password = mysqli_real_escape_string($connect,$_POST['password']);
    $confirm_password = mysqli_real_escape_string($connect,$_POST['confirm_password']);
    if(empty($name)){
        $message = "Name is required";
        echo json_encode(['name' => $message]);
    }else{
        $sql = "select * from user where name = '$name' && deleted_at IS NULL";
        $result = $connect->query($sql);
        if($result->num_rows>0){
            $message = "Diese Name existiert bereits.";
            echo json_encode(["name"=> $message]);
        }
    }
    if(empty($email)){
        $message = "E-Mail ist erforderlich";
        echo json_encode(['email' => $message]);
    }elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format";
        echo json_encode(['email' => $message]);
    }else{
        $sql = "select * from user where email = '$email' && deleted_at IS NULL";
        $result = $connect->query($sql);
        if($result->num_rows > 0){
            $message = "Diese E-Mail existiert bereits.";
            echo json_encode(['email' => $message]);
        }
    }   
    if(empty($password)){
        $message = "Password is required";
        echo json_encode(['password' => $message]);
    }
    if(empty($confirm_password)){
        $message = "Confirm password is required";
        echo json_encode(['confirm_password' => $message]);
    }
}
?>