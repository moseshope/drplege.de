<?php
session_start();
include ('../config/database.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if all required fields are provided
    if (!empty($_POST['currentPassword']) && !empty($_POST['newPassword'])) {
        // Your database connection and session handling code
        $id = $_SESSION['staff_id'];

        $doctorData = "SELECT * FROM user WHERE id='$id'";
        $result = $connect->query($doctorData);
        $row = $result->fetch_assoc();

        $currentPassword = md5($_POST['currentPassword']);
        if ($currentPassword == $row['password']) {
            // Update the hashed password
            $newPasswordHashed = md5($_POST['newPassword']);
            $updateQuery = "UPDATE user SET password='$newPasswordHashed' WHERE id='$id'";
            $updateResult = $connect->query($updateQuery);

            // Update the plain text password
            $newPasswordPlain = $_POST['newPassword'];
            $updateQueryPlain = "UPDATE user SET password_plain='$newPasswordPlain' WHERE id='$id'";
            $updateResultPlain = $connect->query($updateQueryPlain);

            if ($updateResult && $updateResultPlain) {
                echo json_encode(array('success' => true));
                } else {
                echo json_encode(array('error' => 'Failed to update password.'));
                }
            } else {
            echo json_encode(array('error' => 'Invalid current password.'));
            }
        } else {
        echo json_encode(array('error' => 'Current password or new password is empty.'));
        }
    } else {
    // Handle if the request method is not POST
    echo json_encode(array('error' => 'Invalid request method.'));
    }
?>