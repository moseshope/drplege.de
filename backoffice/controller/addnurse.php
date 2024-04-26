<?php
include ('../config/database.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = mysqli_real_escape_string($connect, $_POST['name']);
        $email = mysqli_real_escape_string($connect, $_POST['email']);
        $originalPassword = $_POST['password'];
        $password = mysqli_real_escape_string($connect, md5($_POST['password']));
        $password_plain = mysqli_real_escape_string($connect,($_POST['password']));
        $status = mysqli_real_escape_string($connect, $_POST['status']);
        $role = 3;
        $created_at = date("Y-m-d");
        $sql = "insert into user(name,email,password,password_plain,status,role,created_at) values('$name','$email','$password','$password_plain','$status','$role','$created_at')";
        $query = $connect->query($sql);
        $redirectURL = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'default_page';
        header("Location: $redirectURL");
        }
?>