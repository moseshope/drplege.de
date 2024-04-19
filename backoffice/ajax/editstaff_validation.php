<?php 
include('../config/database.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $qualification = $_POST['qualification'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    
    if(empty($name)){
        $message = "Name is required";
        echo json_encode(['name' => $message]);
    }
    if(empty($qualification)){
        $message = "Qualification is required";
        echo json_encode(['qualification' => $message]);
    }
    if(empty($email)){
        $message = "Email is required";
        echo json_encode(['email' => $message]);
    }elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format";
        echo json_encode(['email' => $message]);
    }else{
        $sql = "select * from user where email = '$email' && deleted_at IS NULL  && id != '$id'";
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
        $sql = "select * from user where telephone = '$telephone' && deleted_at IS NULL  && id != '$id'";
        $result = $connect->query($sql);
        if($result->num_rows > 0){
            $message = "This telephone already exits";
            echo json_encode(['telephone' => $message]);
        }
    }
}
?>