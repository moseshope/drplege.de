<?php
include ('./../backoffice/config/database.php');
if ($_SERVER["REQUEST_METHOD"] === "GET") {

  $doctorId = $_GET['doctorId'];

  $sql = "SELECT selected_date from time_slots_user where doctor_id='$doctorId' AND selected_date > '" . date("Y-m-d") . "'";
  $result = $connect->query($sql);
  $availableDates = array();

  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $availableDates[] = $row['selected_date'];
      }
    }
  }
echo json_encode($availableDates);
?>