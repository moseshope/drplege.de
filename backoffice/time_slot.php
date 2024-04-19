<?php
session_start();
if (! isset($_SESSION['staff_id'])) {
    header("Location: login");
}
include ('config/database.php');
include ('layout/header.php');
include ('layout/sidebar.php');


$sql = "select * from time_slots where time NOT IN ('Holiday', 'Not available') ORDER BY time ASC";
$result = $connect->query($sql);
$timeList = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $timeList[] = $row;
    }
}

$itemsPerPage = 20;
$totalItems = count($timeList);
$totalPages = ceil($totalItems / $itemsPerPage);
$currentPage = isset($_GET['page']) ? max(1, min((int) $_GET['page'], $totalPages)) : 1;
$startIndex = ($currentPage - 1) * $itemsPerPage;
$endIndex = min($startIndex + $itemsPerPage - 1, $totalItems - 1);

?>
    <!-- Main -->
    <div id="main-content">
        <div class="p-2 w-100">
            <div class="d-flex justify-content-center align-items-center">
                <h1 class="page-heading">Zeit hinzufügen</h1>
            </div>
            <div class="d-flex flex-wrap">
                <form method="post">
                    <!-- <div class="dashboard-search m-2 mx-0">
                        <i class="bi bi-search"></i>
                        <input type="text" class="w-100" id="Search-input"
                            placeholder="Search" name="search" value="<?php echo $searchTime ?>">
                    </div> -->
                </form>
            <div class="flex-grow-1">
            </div>
                <button type="submit" class="cursor-pointer custom-secondary-button mt-2 mt-sm-0 m-2"
                data-bs-toggle="modal" data-bs-target="#add-slot"><i class="bi bi-plus"
                style="color: white;width: 20px;height: 20px;"></i>Zeit hinzufügen</button>
            </div>
            
            <div class="px-2">
                <div class="mt-4 custom-table" id="Search-Options" onchange="handleSelect('Search-input')">
                    <div class=" table-responsive">
                        <table class="table pb-3" style="max-width: 500px;">
                            <thead>
                                <tr>
                                    <td style="width:10px;">#</td>
                                    <td class="text-center" style="min-width: 200px;">Zeit</td>
                                    <td>
                                        <div>Optionen</div>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = $startIndex; $i <= $endIndex; $i++) {
                                    ; ?>
                                    <tr class="doctor-row">
                                        <td>
                                            <?php echo $i + 1; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php echo $timeList[$i]['time']; ?>
                                        </td>
                                        <?php if ($timeList[$i]['time'] != 'Not available' && $timeList[$i]['time'] != 'Holiday') { ?>
                                            <td>
                                            <div class="d-flex justify-content-center" style="width: 100px;">
                                                <!-- Edit Button -->
                                                <div class="editButton" data-id="<?php echo $timeList[$i]['id']; ?>" data-bs-toggle="modal">
                                                    <i class="fas fa-edit cursor-pointer me-3"></i>
                                                </div>
        
                                                <!-- Delete Button -->
                                                <div class="todeletebutton text-danger cursor-pointer" data-id="<?php echo $timeList[$i]['id']; ?>" data-bs-toggle="modal">
                                                    <i class="fas fa-trash ms-3 me-4"></i>
                                                </div>
                                            </div>

                                            </td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <!-- pagination -->
                        
                    <div class="white-table">
                    <!-- <ul class="custom-pagination" id="custom-pagination"></ul> -->
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modals -->

 <!-- Add Staff -->
    <form id="AddSlot" method="post" action="">
        <div class="modal fade " id="add-slot" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                    <div class="d-flex justify-content-center align-items-center py-2">
                        <div class="flex-grow-1"></div>    
                        <h1 class="modal-heading" style="font-weight: 800;">Zeit hinzufügen</h1>
                        <div class="flex-grow-1"></div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" style="width:50px;" aria-label="Close"></button>
                    </div>
                        <div class="row">
                            <div class="col-lg-12 col-12">
                                <div class="form-group p-2 my-2">
                                    <label class="my-1" for="Name">Zeit</label>
                                    <input type="time" name="slot" class="form-control custom-input" id="slot" min="00:00" max="23:59">
                                    <span  class="error" id="slot-error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center align-items-center py-2 my-3">
                            <button type="button" class="cancel-button cursor-pointer mx-1" data-bs-dismiss="modal">Abbrechen</button>
                            <button type="button"  id="addSlotBtn" class="success-button cursor-pointer mx-1">Hinzufügen</button>
                        </div>
                </div>
            </div>
        </div>
         <!-- staff Confirmation -->
         <div class="modal fade " id="Confirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                    <div class="d-flex justify-content-center align-items-center flex-column">
                        <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
                        <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <button type="button" class="cancel-button cursor-pointer mx-1" data-bs-dismiss="modal">Nein</button>
                        <button type="button" id="toConfirmationYesBtn" class="success-button cursor-pointer mx-1" data-bs-target="#show-info"
                            >Ja</button>
                    </div>
                </div>
            </div>
        </div>
         <!-- staff show info  -->
         <div class="modal fade " id="ShowInfo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered " role="document">
                <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                    <div class="d-flex justify-content-center align-items-center flex-column py-4">
                        <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich gelöscht.</h1>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <button id="StaffShowInfoBtn" type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal">Okay</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

<!-- edit Staff -->
    <form id="EditSlot" method="post" action="">
        <div class="modal fade " id="edit-slot" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                    <div class="d-flex justify-content-center align-items-center py-2">
                        <div class="flex-grow-1"></div>
                        <h1 class="modal-heading" style="font-weight: 800;">Zeit bearbeiten</h1>
                        <div class="flex-grow-1"></div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" style="width:50px;" aria-label="Close"></button>
                    </div>
                        <div class="row">
                            <div class="col-lg-12 col-12">
                            <input type="hidden" name="slot_id" class="form-control custom-input" id="editSlotId" value="">
                                <div class="form-group p-2 my-2">
                                    <label class="my-1" for="Name">Slot</label>
                                    <input type="time" name="slot" class="form-control custom-input" id="editSlot" >
                                    <span  class="error" id="edit_slot-error"></span>

                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center align-items-center py-2 my-3">
                            <button type="button" id="updateSlotBtn" class="success-button cursor-pointer mx-1">Speichern</button>
                            <button type="button" class="cancel-button cursor-pointer mx-1"
                                data-bs-dismiss="modal">Abbrechen</button>
                        </div>
                </div>
            </div>
        </div>
        
         <!-- staff Confirmation -->
         <div class="modal fade " id="EditSlotConfirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                    <div class="d-flex justify-content-center align-items-center flex-column">
                        <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
                        <p class="mb-h text-danger">TDiese Aktion ist nicht rückgängig zu machen.</p>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <button type="button" class="cancel-button cursor-pointer mx-1" data-bs-dismiss="modal">Nein</button>
                        <button type="button" id="EditConfirmationYesBtn" class="success-button cursor-pointer mx-1"
                            >Ja</button>
                    </div>
                </div>
            </div>
        </div>
         <!-- staff show info  -->
         <div class="modal fade " id="editSlotShowInfo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered " role="document">
                <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                    <div class="d-flex justify-content-center align-items-center flex-column py-4">
                        <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich aktualisiert.</h1>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <button id="StaffShowInfoBtn" type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal">Okay</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- slot delete models  -->
    <div class="modal fade " id="deletedConfirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                    <div class="d-flex justify-content-center align-items-center flex-column">
                        <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
                        <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <button type="button" class="cancel-button cursor-pointer mx-1" data-bs-dismiss="modal">Nein</button>
                        <button type="button" id="deleteConfirmationYesBtn" class="success-button cursor-pointer mx-1"
                            >Ja</button>
                    </div>
                </div>
            </div>
        </div>
         <!-- staff show info  -->
         <div class="modal fade " id="deleteSlotShowInfo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered " role="document">
                <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                    <div class="d-flex justify-content-center align-items-center flex-column py-4">
                        <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich gelöscht.</h1>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <button id="slotShowInfoBtn" type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal">Okay</button>
                    </div>
                </div>
            </div>
        </div>


    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>


    <script src="asset/js/index.js"></script>
    <script src="asset/js/pagination.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="asset/js/script.js"></script>
    
    <script>
        $(document).ready(function() {

            $('#AddSlot').validate({
                rules: {
                    slot: {
                        required: true,
                    },
                },
                messages: {
                    slot: {
                        required: "Slot is required.",
                    },
                },
                errorPlacement: function(error, element) {
                    var fieldName = $(element).attr("name");
                    error.insertAfter("#" + fieldName + "-error");
                    error.addClass('text-danger');
                },
                highlight: function(element) {
                    $(element).siblings('.error').addClass('text-danger');
                },
                unhighlight: function(element) {
                    $(element).siblings('.error').removeClass('text-danger');
                },
            });
        });
        $('#toConfirmationYesBtn').on('click', function() {
            $('#Confirmation').modal('hide');
        });
        $('#addSlotBtn').on('click', function() {
            if ($('#AddSlot').valid()) {
                var selectedSlot = $('#slot').val();
                // var slotFormate = 'null';
                // if(selectedSlot){
                //     var [hours, minutes] = selectedSlot.split(':');
                //         var period = (hours >= 12) ? 'PM' : 'AM';
                        
                //         hours = (hours % 12) || 12;
                //         hours = (hours < 10) ? '0' + hours : hours;
                //         slotFormate = hours + ':' + minutes + ' ' + period;
                // }
                // else{
                //     slotFormate = null;
                // }
                $.ajax({
                    url: './ajax/addslot.php',
                    method: 'POST',
                    data: { slot: selectedSlot },
                    success: function(response) {
                        if(response){
                            var responseData = JSON.parse(response);
                            $('#slot-error').text(responseData).addClass('text-danger');
                        }else{
                            $('#slot-error').hide();
                            $('#ShowInfo').modal('hide');
                            $('#add-slot').modal('hide');
                            $('#Confirmation').modal('show');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                    
                });  
                
            }
        });


        $('.editButton').on('click', function() {
            var dataId = $(this).data('id');
            $.ajax({
                    url: './ajax/addslot.php',
                    method: 'GET',
                    data: { dataId: dataId },
                    success: function(response) {
                            var responseData = JSON.parse(response);
                            var [time, period] = responseData.split(' ');
                            $('#editSlotId').val(dataId);
                            $('#editSlot').val(time);
                            $('#edit-slot').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                    
                });  
        });

        $('#updateSlotBtn').on('click', function() {
            var slotId = $('#editSlotId').val();
            var selectedSlot = $('#editSlot').val();
            // var [hours, minutes] = selectedSlot.split(':');
            //     var period = (hours >= 12) ? 'PM' : 'AM';
                
            //     hours = (hours % 12) || 12;
            //     hours = (hours < 10) ? '0' + hours : hours;
            //     slotFormate = hours + ':' + minutes + ' ' + period;

            $.ajax({
                    url: './ajax/addslot.php',
                    method: 'POST',
                    data: { 
                        slotId: slotId,
                        selectedSlot : selectedSlot,
                     },
                    success: function(response) {
                        if(response){
                            var responseData = JSON.parse(response);
                            $('#edit_slot-error').text(responseData).addClass('text-danger');
                        }else{
                            $('#edit_slot-error').hide();
                            $('#edit-slot').modal('hide');
                            $('#EditSlotConfirmation').modal('show');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                    
                });  
           
        });
        $('#EditConfirmationYesBtn').on('click', function() {
            $('#EditSlotConfirmation').modal('hide');
            $('#editSlotShowInfo').modal('show');
        });

        var deleteSlotId = '';
        $('.todeletebutton').on('click', function() {
            deleteSlotId = $(this).data('id');
            $('#deletedConfirmation').modal('show');
        });
        $('#deleteConfirmationYesBtn').on('click', function() {
            $('#deletedConfirmation').modal('hide');
            $('#Confirmation').modal('hide');
            $('#deleteSlotShowInfo').modal('show');
            $.ajax({
                url: './ajax/addslot.php',
                method: 'POST',
                data: { 
                    deleteSlotId: deleteSlotId,
                    },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });

    </script>
    <script>
        const params = new URLSearchParams(window.location.search);
        const currentPage = params.get('page');
        <?php if ($totalItems > $itemsPerPage) { ?>
            CreatePagination({
                elementId: "custom-pagination",
                totalPage: <?php echo $totalPages; ?>,
                currentPage:currentPage ? Number(currentPage) : 1
            })
        <?php } ?>
    </script>

     <!-- logout script  -->
     <?php include ('layout/script.php') ?>
</body>

</html>