<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    header("Location: login");
}
$doctorId =$_SESSION['staff_id'];
include('config/database.php');
include('./layout/header.php');
include('./layout/sidebar.php');

$GetServices = "select * from services where deleted_at IS NULL";
$result = $connect->query($GetServices);
$servicesList = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $servicesList[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $staff_services = isset($_POST['staff_services']) ? $_POST['staff_services'] : array();
        
    if (is_array($staff_services)) {
        $sql = "DELETE FROM services_docs WHERE user_id = '$doctorId'";
        $result = $connect->query($sql);
        $data = [];
        foreach ($staff_services as $key => $value) {
            $data[] = "($doctorId, $value)";
        }
        $data = implode(",", $data);
        $sql = "INSERT INTO services_docs (user_id, service_id) VALUES $data;";
        $result = $connect->query($sql);
    }
}

$checkedService = [];

$checkedServices = "SELECT s.services, sd.service_id from services_docs AS sd LEFT JOIN services AS s ON sd.service_id = s.id where user_id='$doctorId'";
$checkedResult = $connect->query($checkedServices);
if ($checkedResult && $checkedResult->num_rows > 0) {
    while ($service = $checkedResult->fetch_assoc()) {
        $checkedService[] = $service['service_id'];
    }
}

?>

    <!-- Main -->
<form method='post'>
    <div id="main-content">
        <div class="p-2 w-100">
            <div class="d-flex justify-content-center align-items-center">
                <h1 class="page-heading mb-5">Dienstleistungen</h1>
            </div>
            <div class="row w-100">
                
                <div class="col-12 col-lg-6">
                    <div class="select-time">
                        <div class="d-flex justify-content-center py-3" style="color: var(--main);">
                            <h4 style="font-weight: 700;">Dienstliste</h4>
                        </div>
                        
                       
                            <ul class="row" id="selectTime">
                                <?php foreach ($servicesList as $service) { ?>
                                    <label class="col-12">
                                        <input type="checkbox" class="optionalFeature" name="staff_services[]" value="<?= $service['id'] ?>" <?php if (in_array($service['id'], $checkedService)) echo 'checked'; ?>>
                                        <?= $service['services'] ?>
                                    </label>
                                <?php } ?>
                            </ul>
                   
                        <div class="d-flex justify-content-center align-items-center mt-5 mb-3">
                            <button type="button" class="success-button cursor-pointer mx-3" data-bs-target=""
                                data-bs-toggle="modal" data-bs-dismiss="modal" id="refresh">Einreichen</button>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modals -->
    <!-- Confirmation -->
    <div class="modal fade " id="Confirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                <div class="d-flex justify-content-center align-items-center flex-column">
                    <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
                    <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
                </div>
                <div class="d-flex justify-content-center align-items-center">
                    <button type="button" class="success-button cursor-pointer" data-bs-target=""
                        data-bs-toggle="modal" data-bs-dismiss="modal" id="confirmationBtn">Ja</button>
                    <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal">Nein</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade " id="show-info" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered " role="document">
            <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                <div class="d-flex justify-content-center align-items-center flex-column py-4">
                    <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich aktualisiert.</h1>
                </div>
                <div class="d-flex justify-content-center align-items-center">
                    <button type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal" id="showInfoBtn">Okay</button>
                </div>
            </div>
        </div>
    </div>
</form>

    <!-- reset comformation model  -->

    <!-- Confirmation -->
    <div class="modal fade " id="resetConfirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                <div class="d-flex justify-content-center align-items-center flex-column">
                    <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
                    <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
                </div>
                <div class="d-flex justify-content-center align-items-center">
                    <button type="submit" class="success-button cursor-pointer" data-bs-target=""
                        data-bs-toggle="modal" data-bs-dismiss="modal" id="resetConfirmationBtn">Ja</button>
                    <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal">Nein</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade " id="reset-show-info" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered " role="document">
            <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                <div class="d-flex justify-content-center align-items-center flex-column py-4">
                    <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich gelöscht.</h1>
                </div>
                <div class="d-flex justify-content-center align-items-center">
                    <button type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal" id="resetShowInfoBtn">Okay</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <script src="asset/js/index.js"></script>
    <script src="asset/js/calender.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- <script>
    
</script> -->
<script>

    // var selectedValues = [];

    // // function getDate(event) {
    //     // document.getElementById('selectAllCheckBox').checked = false;
    //     // date = event.target.id;
    //     $.ajax({
    //         url: './ajax/staffservice.php',
    //         method: 'GET',
    //         data: { 
    //             doctorId: doctor_id,
    //         },
    //         success: function (response) {
    //             var serviceList = JSON.parse(response);
    //             updateUI(serviceList);
    //         },
    //         error: function (xhr, status, error) {
    //             console.error('Error:', error);
    //         }
    //     });
    //     // }
        
    //     function updateUI(serviceList) {
    //         if (serviceList !== null) {
    //             var parsedServiceList = serviceList;

    //         $('input.optionalFeature').prop('checked', false);
    //         parsedServiceList.forEach(function (service) {
    //             console.log(service)
    //             $('input.optionalFeature[value="' + service + '"]').prop('checked', true);
    //         });
    //     } else {
    //         $('input.optionalFeature').prop('checked', false);     }
    //     }

    // $('input.optionalFeature').on('change', function() {
    //     var selectedCheckboxes = $('input.optionalFeature:checked');
    //     selectedValues = selectedCheckboxes.map(function() {
    //         return $(this).val();
    //     }).get();
    // });

    $('#refresh').on('click', function() {
            $('#Confirmation').modal('show');
    })

    $('#confirmationBtn').on('click', function() {
            $('#show-info').modal('show');
    })
    // $('#showInfoBtn').on('click', function() {
       
    //     window.location.reload(true);
    //     window.location.reload(true);
            
    // })

    // $('#reset').on('click', function() {
    //         $('#resetConfirmation').modal('show');
    // })
    // $('#resetConfirmationBtn').on('click', function() {
    //         $('#reset-show-info').modal('show');
    // })
    // $('#resetShowInfoBtn').on('click', function() {
    //     $.ajax({
    //         url: './ajax/resettime.php',
    //         method: 'GET',
    //         data: { 
    //             date: date, 
    //         },
    //         success: function (response) {
    //             location.reload();
    //         },
    //         error: function (xhr, status, error) {
    //             console.error('Error:', error);
    //         }
    //     });
    // })

</script>
    <!-- logout  -->
    <?php include('./layout/script.php')?>
</body>

</html>