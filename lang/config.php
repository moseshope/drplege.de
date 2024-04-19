<?php
  session_start();
  if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang;
  }
  echo 1;
  exit;
?>