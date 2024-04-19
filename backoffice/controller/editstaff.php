<?php
include('../config/database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = mysqli_real_escape_string($connect,$_POST['id']);
        $name = mysqli_real_escape_string($connect,$_POST['name']);
        $qualification = mysqli_real_escape_string($connect,$_POST['qualification']);
        $email = mysqli_real_escape_string($connect,$_POST['email']);
        $telephone = mysqli_real_escape_string($connect,$_POST['telephone']);
        $status = mysqli_real_escape_string($connect,$_POST['status']);

        $sql = "select * from user where id='$id'";
        $result = $connect->query($sql);
        $row = $result->fetch_assoc();
        $services = $row['services'];
        $profile = $row['profile'];

        if($_FILES["profile"]["name"]){
                $originalFileName = $_FILES["profile"]["name"];
                $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
                $profileName = rand(11111111, 99999999). "." . $extension;
                $path = move_uploaded_file($_FILES["profile"]["tmp_name"], "../../images/" . $profileName);
        }else{
                $profileName = $profile;
        }
        
        $staff_services = isset($_POST['staff_services']) ? $_POST['staff_services'] : $services;

        if (is_array($staff_services)) {
                $staff_services = implode('__', $staff_services);
        }
        $updated_at = date("Y-m-d");

        $sql = "update user set name='$name',qualification='$qualification',email='$email',profile='$profileName',telephone='$telephone',status='$status',services='$staff_services' where id=$id";
        $query = $connect->query($sql);
        if ($query) {
                // header("Location: ./../nurse");
        }
}

?>
