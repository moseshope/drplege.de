<?php
include('../config/database.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = mysqli_real_escape_string($connect,$_POST['id']);
        $deleted_at = date("Y-m-d");
        $sql = "update user set deleted_at='$deleted_at', status='deleted' where id=$id";
        $query = $connect->query($sql);
        if ($query) {
              header("Location: ./../nurse");
        }
}
?>
