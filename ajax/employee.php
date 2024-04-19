<?php
session_start();
$lang = (!empty($_SESSION['lang'])) ? $_SESSION['lang'] : 'de';
include('./../../lang/'.$lang.'.php');
include ('./../backoffice/config/database.php');
if ($_SERVER["REQUEST_METHOD"] === "GET") {
        
    $service = $_GET['service'];
    $sql = "SELECT * FROM user WHERE services like '%$service%' AND deleted_at IS NULL AND role = 2";
    $result = $connect->query($sql);
    $staffData = array();
    
    if ($result && $result->num_rows > 0) { 
        while ($row = $result->fetch_assoc()) {
            $serviceArray = explode('__', $row['services']);
            $servicesData = array();

            foreach ($serviceArray as $service) {
                if($lang == 'de'){
                    $sql1 = "SELECT * FROM services WHERE services='$service'";
                    $result1 = $connect->query($sql1);
                    if ($result1 && $result1->num_rows > 0) {
                        while ($serviceRow = $result1->fetch_assoc()) {
                            $servicesData[] = $serviceRow['services'];
                        }
                    }
                }else{
                    $sql1 = "SELECT * FROM services WHERE services='$service'";
                    $result1 = $connect->query($sql1);
                    if ($result1 && $result1->num_rows > 0) {
                        while ($serviceRow = $result1->fetch_assoc()) {
                            $servicesData[] = $serviceRow['services_en'];
                        }
                    }
                }
            }
            $rowData = array(
                'doctorId' => $row['id'],
                'doctorName' => $row['name'],
                'services' => $servicesData,
                'profile' => $row['profile']
            );

            $staffData[] = $rowData;
        }
    }
                     
    echo json_encode($staffData);
}
?>