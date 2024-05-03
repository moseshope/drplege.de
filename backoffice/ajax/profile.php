<?php
session_start();
include ('../config/database.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_POST['name']) && !empty($_POST['email'])) {
        $id = $_SESSION['staff_id'];
        $currentPassword = md5($_POST['currentPassword']);
        $newPasswordHashed = md5($_POST['newPassword']);
        $password_plain = $_POST['newPassword'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $doctorData = "SELECT * FROM user WHERE id='$id'";
        $result = $connect->query($doctorData);
        $row = $result->fetch_assoc();
        if (!empty($_POST['currentPassword']) && $currentPassword == $row['password']) {
            if (!empty($_POST['newPassword'])) {
                $updateQuery = "UPDATE user SET password='$newPasswordHashed', name='$name', email='$email', password_plain='$password_plain' WHERE id='$id'";
                $updateResult = $connect->query($updateQuery);
                if ($updateResult) {
                    echo json_encode(array('success' => true));
                    } else {
                    echo json_encode(array('error' => 'Ungültiges aktuelles Passwort.'));
                    }
                } else {
                $updateQuery = "UPDATE user SET name='$name', email='$email' WHERE id='$id'";
                $updateResult = $connect->query($updateQuery);
                if ($updateResult) {
                    echo json_encode(array('success' => true));
                    } else {
                    echo json_encode(array('error' => 'Ungültiges aktuelles Passwort.'));
                    }
                }
            } elseif (empty($_POST['currentPassword']) && empty($_POST['newPassword'])) {
            $updateQuery = "UPDATE user SET name='$name', email='$email' WHERE id='$id'";
            $updateResult = $connect->query($updateQuery);
            if ($updateResult) {
                echo json_encode(array('success' => true));
                } else {
                echo json_encode(array('error' => 'Erforderliche Felder sind leer.'));
                }
            } else {
            echo json_encode(array('error' => 'Ungültiges aktuelles Passwort.'));
            }
        } else {
        echo json_encode(array('error' => 'Erforderliche Felder sind leer.'));
        }
    } else {
    // Handle if the request method is not POST
    echo json_encode(array('error' => 'Invalid request method.'));
    }

?>