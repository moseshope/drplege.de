<?php
include ('../config/database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = mysqli_real_escape_string($connect, $_POST['id']);
        $name = mysqli_real_escape_string($connect, $_POST['name']);
        $email = mysqli_real_escape_string($connect, $_POST['email']);
        $telephone = mysqli_real_escape_string($connect, $_POST['telephone']);
        $status = mysqli_real_escape_string($connect, $_POST['status']);

        $sql = "select * from user where id='$id'";
        $result = $connect->query($sql);
        $row = $result->fetch_assoc();
        $services = $row['services'];
        $profile = $row['profile'];

        if ($_FILES["profile"]["name"]) {
                $originalFileName = $_FILES["profile"]["name"];
                $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
                $profileName = rand(11111111, 99999999) . "." . $extension;
                $path = move_uploaded_file($_FILES["profile"]["tmp_name"], "../../images/" . $profileName);
                } else {
                $profileName = $profile;
                }

        $staff_services = isset($_POST['staff_services']) ? $_POST['staff_services'] : '';

        if (is_array($staff_services)) {
                $sql = "DELETE FROM services_docs WHERE user_id='$id'";
                $connect->query($sql);
                $data = [];
                foreach ($staff_services as $key => $value) {
                        $data[] = "($id, $value)";
                        }
                $data = implode(",", $data);
                $sql = "INSERT INTO services_docs (user_id, service_id) VALUES $data;";
                $connect->query($sql);
        }
        $updated_at = date("Y-m-d");

        $sql = "update user set name='$name',email='$email',profile='$profileName',telephone='$telephone',status='$status' where id=$id";
        $query = $connect->query($sql);
        if ($query) {
                // header("Location: ./../nurse");
                }
        }

?>
