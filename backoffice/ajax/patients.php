<?php 
include('../config/database.php');
if ($_SERVER["REQUEST_METHOD"] === "POST") {
        
        $id = mysqli_real_escape_string($connect,$_POST['id']);
    
        $sql = "select * from patients where id=$id";
        // $sql = "SELECT patients.*, user.name AS doctor
        // FROM patients
        // JOIN user ON patients.doctor = user.id
        // WHERE patients.id = '$id'";
        
        $result = $connect->query($sql);
        if ($result) {
            $staffData = $result->fetch_assoc();
            
            echo json_encode($staffData);
        }
    
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $currentDate = date("Y-m-d");
    $dateString = mysqli_real_escape_string($connect,$_GET['date']);
    $formattedDate = date("Y-m-d", strtotime($dateString));
    $searchQuery = mysqli_real_escape_string($connect,$_GET['searchQuery']);
    if($formattedDate >= $currentDate){
    $responseData = array();
    $date = new DateTime($dateString);
    $formattedDate = $date->format('d.m.Y');
    
        $sql = " SELECT patients.*, user.name AS doctor, services.services AS services 
        FROM patients 
        JOIN user ON patients.doctor = user.id 
        JOIN services ON patients.services = services.id 
        WHERE patients.selected_date = '$formattedDate'
        AND  patients.status = 'Bevorstehende'
        AND  patients.deleted_at IS NULL
        AND (patients.name LIKE '%$searchQuery%' OR user.name LIKE '%$searchQuery%' OR services.services LIKE '%$searchQuery%')";
   
    $sql1 = "select * from patients where selected_date='$formattedDate' && status='Vollendet'";
    
    $result = $connect->query($sql);
    $result1 = $connect->query($sql1);

    $responseData['data'] = array();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $responseData['data'][] = array(
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'doctor' => $row['doctor'],
                    'services' => $row['services'],
                    'visits' => $row['visits'],
                );
            }
        }
    
    $responseData['statusDoneData'] = array();
        if ($result1 && $result1->num_rows > 0) {
            while ($row1 = $result1->fetch_assoc()) {
                $responseData['statusDoneData'][] = array(
                    'id' => $row1['id'],
                    'name' => $row1['name'],
                    'doctor' => $row1['doctor'],
                    'services' => $row1['services'],
                    'visits' => $row1['visits'],
                );
            }
        }

        echo json_encode($responseData);
    }else{
        echo json_encode(null);
    }
}
?>