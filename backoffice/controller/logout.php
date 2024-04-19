<?php
session_start();
$staffId = $_SESSION['staff_id'];
if ($staffId) {
    unset($_SESSION['staff_id']);
    header('Location: ../login');
    }
?>