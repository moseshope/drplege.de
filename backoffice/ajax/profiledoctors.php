<?php
session_start();
include ('../config/database.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if all required fields are provided
    if (!empty($_POST['currentPassword']) && !empty($_POST['newPassword']) && !empty($_POST['name']) && !empty($_POST['email'])) {
        // Your database connection and session handling code
        $id = $_SESSION['staff_id'];

        $doctorData = "SELECT * FROM user WHERE id='$id'";
        $result = $connect->query($doctorData);
        $row = $result->fetch_assoc();
        $profile = $row['profile'];

        if (!empty($_FILES["profile"]["name"])) {
            // If a file is uploaded
            $originalFileName = $_FILES["profile"]["name"];
            $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            // Get the current timestamp
            $timestamp = time();
            // Concatenate the timestamp with the original filename (separated by an underscore)
            $profileName = $timestamp . "_" . $originalFileName;
            // Move the uploaded file to the desired location with the composed profile name
            $uploadDirectory = "../../images/";
            $profilePath = $uploadDirectory . $profileName;
            if (move_uploaded_file($_FILES["profile"]["tmp_name"], $profilePath)) {
                // If the previous profile image exists, delete it
                if (!empty($profile) && file_exists("../../images/" . $profile)) {
                    unlink("../../images/" . $profile);
                }
            } else {
                // Handle file upload error
                echo json_encode(array('error' => 'Failed to upload profile image.'));
                exit;
            }
        } else {
            // If no file is uploaded, use the existing profile name
            $profileName = $profile;
        }

        $currentPassword = md5($_POST['currentPassword']);
        if ($currentPassword == $row['password']) {
            // Update the hashed password
            $newPasswordHashed = md5($_POST['newPassword']);
            $name = $_POST['name'];
            $email = $_POST['email'];
            $telephone = $_POST['telephone']; // Add the telephone field update
            $updateQuery = "UPDATE user SET password='$newPasswordHashed', name='$name', email='$email', telephone='$telephone', profile='$profileName' WHERE id='$id'";
            $updateResult = $connect->query($updateQuery);

            if ($updateResult) {
                echo json_encode(array('success' => true));
            } else {
                echo json_encode(array('error' => 'Failed to update profile.'));
            }
        } else {
            echo json_encode(array('error' => 'Invalid current password.'));
        }
    } else {
        echo json_encode(array('error' => 'Required fields are empty.'));
    }
} else {
    // Handle if the request method is not POST
    echo json_encode(array('error' => 'Invalid request method.'));
}
?>
