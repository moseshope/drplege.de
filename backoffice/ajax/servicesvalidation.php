<?php
include('../config/database.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {

   $serviceGermany = mysqli_escape_string($connect,$_POST['services']);
   $serviceEnglish = mysqli_escape_string($connect,$_POST["services_en"]);

   $response;
   $isError = false;

   $germanyDuplication = "select services from services where services='$serviceGermany'";
   $enlishDuplication = "select services_en from services where services_en='$serviceEnglish'";

   $resultEnglish = mysqli_query($connect, $enlishDuplication);
   $resultGermany = mysqli_query($connect, $germanyDuplication);

   if (empty($serviceGermany)) {
      $response['germany'] = "Der Name des Dienstes ist erforderlich (Deutschland).";
      $isError = true;
   } 
   if (mysqli_num_rows($resultGermany)) {
      $response["germany"] = "Der Dienstname ist bereits vorhanden";
      $isError = true;
   } 
   if (empty($serviceEnglish)) {
      $response["english"] = "Der Name des Dienstes ist erforderlich (Englisch).";
      $isError = true;
   } 
   if (mysqli_num_rows($resultEnglish)) {
      $response["english"] = "Der Dienstname ist bereits vorhanden";
      $isError = true;
   } 

   if ($isError)
      echo json_encode($response);
   else
      echo "";
}
?>