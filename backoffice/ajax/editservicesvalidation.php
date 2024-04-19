<?php
include('../config/database.php');
if ($_SERVER["REQUEST_METHOD"] === "POST") {
   $serviceId = mysqli_escape_string($connect, $_POST["servicesId"]);
   $serviceGermany = mysqli_escape_string($connect, $_POST['services']);
   $serviceEnglish = mysqli_escape_string($connect, $_POST['services_en']);
   $response;
   $isError = false;
   if (empty($serviceGermany)) {
      $response['germany'] = "Der Name des Dienstes ist erforderlich (Deutschland).";
      $isError = true;
   } else {      
      $germanyDuplication = "SELECT services FROM services WHERE services='$serviceGermany' AND deleted_at IS NULL AND id != '$serviceId'";
      $resultGermany = $connect->query($germanyDuplication);
      if ($resultGermany && $resultGermany->num_rows > 0) {
         $response["germany"] = "Der Dienstname ist bereits vorhanden";
         $isError = true;
      }
   }
   if (empty($serviceEnglish)) {
      $response["english"] = "Der Name des Dienstes ist erforderlich (Englisch).";
      $isError = true;
   } else {      
      $enlishDuplication = "SELECT services_en FROM services WHERE services_en='$serviceEnglish' AND deleted_at IS NULL AND  id != '$serviceId'";
      $resultEnglish = $connect->query($enlishDuplication);
      if ($resultEnglish && $resultEnglish->num_rows > 0) {
         $response["english"] = "Der Dienstname ist bereits vorhanden";
         $isError = true;
      }
   }
   if ($isError)
      echo json_encode($response);
   else
      echo "";
}
?>