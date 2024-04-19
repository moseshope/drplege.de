<?php
include ('./../backoffice/config/database.php');
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $dateFormate = $_GET['date'];

    $dateTime = new DateTime($dateFormate);
    $formattedDate1 = $dateTime->format('d.m.Y');

    $date = date('Y-m-d', strtotime($dateFormate));
    $formattedDate = date('Y-m-d', strtotime($date));
    $doctorId = $_GET['doctorId'];
    $currentDate = date("Y-m-d");

    if($date > $currentDate){

        $sql = "select time from time_slots_user where doctor_id='$doctorId' && selected_date='$date'";
        $result = $connect->query($sql);
    
        $times = array();
        $times1 = array();
        $response = [];
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $time = json_decode($row['time'], true);
            $length = count($time);

            foreach ($time as $value) {
                $times[] = $value;
            }

            $sql1 = "select * from patients where doctor='$doctorId' && selected_date='$formattedDate1'";
            $result1 = $connect->query($sql1);
            while ($row1 = $result1->fetch_assoc()) {
                $times1[] = str_replace(" Uhr", "", $row1['visits']);
            }

            foreach ($times as $element) {
                // Check if the element exists in $array2
                if (!in_array($element, $times1)) {
                    // If not found, add it to the response array
                    $response[] = $element;
                }
            }
            // $row1 = $result1->fetch_assoc();
            
            // print_r($times);
            // print_r($response);
            echo json_encode($response);
        }
    }
}
?>