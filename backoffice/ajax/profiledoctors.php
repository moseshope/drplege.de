<?php
session_start();
include ('../config/database.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_POST['name']) && !empty($_POST['email'])) {
        $id = $_SESSION['staff_id'];
        $profileImageName = handleProfileImageUpload($id);
        $currentPassword = md5($_POST['currentPassword']);
        $newPasswordHashed = md5($_POST['newPassword']);
        $password_plain = $_POST['newPassword'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $telephone = $_POST['telephone'];        
        $doctorData = "SELECT * FROM user WHERE id='$id'";
        $result = $connect->query($doctorData);
        $row = $result->fetch_assoc();
        if (!empty($_POST['currentPassword']) && $currentPassword == $row['password']) {
            if (!empty($_POST['newPassword'])) {
                $updateQuery = "UPDATE user SET password='$newPasswordHashed', name='$name', email='$email', telephone='$telephone', password_plain='$password_plain', profile='$profileImageName' WHERE id='$id'";
                $updateResult = $connect->query($updateQuery);
                if ($updateResult) {
                    echo json_encode(array('success' => true));
                } else {
                    echo json_encode(array('error' => 'Ungültiges aktuelles Passwort.'));
                }
            } else {
                $updateQuery = "UPDATE user SET name='$name', email='$email', telephone='$telephone', profile='$profileImageName' WHERE id='$id'";
                $updateResult = $connect->query($updateQuery);
                if ($updateResult) {
                    echo json_encode(array('success' => true));
                } else {
                    echo json_encode(array('error' => 'Ungültiges aktuelles Passwort.'));
                }
            }
        } 
        elseif (empty($_POST['currentPassword']) && empty($_POST['newPassword']) ) {
            $updateQuery = "UPDATE user SET name='$name', email='$email', telephone='$telephone', profile='$profileImageName' WHERE id='$id'";
                $updateResult = $connect->query($updateQuery);
                if ($updateResult) {
                    echo json_encode(array('success' => true));
                } else {
                    echo json_encode(array('error' => 'Erforderliche Felder sind leer.'));
                }
        } 
        else {
            echo json_encode(array('error' => 'Ungültiges aktuelles Passwort.'));
        }
    } else {
        echo json_encode(array('error' => 'Erforderliche Felder sind leer.'));
    }
} else {
    // Handle if the request method is not POST
    echo json_encode(array('error' => 'Invalid request method.'));
}

// Function to handle profile image upload
function handleProfileImageUpload($userId) {
    global $connect;
    if (!empty($_FILES["profileImage"]["name"])) {
        // If a file is uploaded
        $originalFileName = $_FILES["profileImage"]["name"];
        $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
        // Get the current timestamp
        $timestamp = time();
        // Concatenate the timestamp with the original filename (separated by an underscore)
        $profileName = $timestamp . "_" . $originalFileName;
        // Move the uploaded file to the desired location with the composed profile name
        $uploadDirectory = "../../images/";
        $profilePath = $uploadDirectory . $profileName;
        if (move_uploaded_file($_FILES["profileImage"]["tmp_name"], $profilePath)) {
            // If the previous profile image exists, delete it
            $doctorData = "SELECT profile FROM user WHERE id='$userId'";
            $result = $connect->query($doctorData);
            $row = $result->fetch_assoc();
            $previousProfile = $row['profile'];
            if (!empty($previousProfile) && file_exists("../../images/" . $previousProfile)) {
                unlink("../../images/" . $previousProfile);
            }
            return $profileName;
        } else {
            // Handle file upload error
            echo json_encode(array('error' => 'Failed to upload profile image.'));
            exit;
        }
    } else {
        // If no file is uploaded, return the existing profile name
        $doctorData = "SELECT profile FROM user WHERE id='$userId'";
        $result = $connect->query($doctorData);
        $row = $result->fetch_assoc();
        return $row['profile'];
    }
}
?>