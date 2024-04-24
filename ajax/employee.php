<?php
session_start();
$lang = (!empty($_SESSION['lang'])) ? $_SESSION['lang'] : 'de';
include ('./../../lang/' . $lang . '.php');
include ('./../backoffice/config/database.php');
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    $service = $_GET['service'];
    $serviceId = $_GET['serviceId'];
    $sql = "SELECT * FROM services_docs WHERE service_id = $serviceId";
    $result = $connect->query($sql);
    $userIds = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $userIds[] = $row["user_id"];
            }
        }

    $sql = "SELECT * FROM user WHERE id IN (" . implode(',', $userIds) . ") AND deleted_at IS NULL AND role = 2";
    $result = $connect->query($sql);
    $staffData = array();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $getService = "SELECT sd.user_id, s.services" . ($lang == 'de' ? '' : '_en') . " FROM services_docs AS sd LEFT JOIN services AS s ON sd.service_id = s.id WHERE sd.user_id='" . $row['id'] . "'";
            $services = [];
            $sericesResult = $connect->query($getService);
            if ($sericesResult && $sericesResult->num_rows > 0) {
                while ($row1 = $sericesResult->fetch_assoc()) {
                    $services[] = $row1['services' . ($lang == 'de' ? '' : '_en')];
                    }
                $row['serviceList'] = $services;
                }
            $rowData = array(
                'doctorId' => $row['id'],
                'doctorName' => $row['name'],
                'services' => $services,
                'profile' => $row['profile']
            );

            $staffData[] = $rowData;
            }
        }

    echo json_encode($staffData);
    }
?>