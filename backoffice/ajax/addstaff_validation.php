<?php 
include('../config/database.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $name = $_POST['name'];
    // $qualification = $_POST['qualification'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if(empty($name)){
        $message = "Name is required";
        echo json_encode(['name' => $message]);
    }
    // if(empty($qualification)){
    //     $message = "Qualification is required";
    //     echo json_encode(['qualification' => $message]);
    // }
    if(empty($email)){
        $message = "Email is required";
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
    if(empty($telephone)){
        $message = "Telephone is required";
        echo json_encode(['telephone' => $message]);
    }else{
        $sql = "select * from user where telephone = '$telephone' && deleted_at IS NULL";
        $result = $connect->query($sql);
        if($result->num_rows > 0){
            $message = "This telephone already exits";
            echo json_encode(['telephone' => $message]);
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