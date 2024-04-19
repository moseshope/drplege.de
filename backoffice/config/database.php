<?php

// $serverName = "db5015669980.hosting-data.io";
//    $userName = "dbu453001";
//    $password = "Rsc=+xM3K7<xMuZ_";
//    $database = "dbs12790101";


   $serverName = "localhost";
   $userName = "root";
   $password = "";
   $database = "dbs12790101";



$connect = mysqli_connect($serverName, $userName, $password, $database);
if (!$connect) {
	die("error" . mysqli_error($connect));
}
//else {echo('success');}
?>