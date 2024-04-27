<?php
include ('../config/database.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = mysqli_real_escape_string($connect, $_POST['id']);
        $name = mysqli_real_escape_string($connect, $_POST['name']);
        $email = mysqli_real_escape_string($connect, $_POST['email']);
        $status = mysqli_real_escape_string($connect, $_POST['status']);

        // Translate status to database-friendly values
        if ($status === 'Aktiv') {
                $status = '1';
                } elseif ($status === 'Deaktiviert') {
                $status = '0';
                }
        $sql = "select * from user where id='$id'";
        $result = $connect->query($sql);
        $row = $result->fetch_assoc();
        $updated_at = date("Y-m-d");
        $sql = "update user set name='$name',email='$email',status='$status',updated_at='$updated_at' where id=$id";
        $query = $connect->query($sql);
        if ($query == true) {
                header("Location: ./../nurse");
                }
        }
?>