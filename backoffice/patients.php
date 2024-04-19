<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    header("Location: login");
}
include('config/database.php');
include('./layout/header.php');
include('./layout/sidebar.php');

$id = $_SESSION['staff_id'];
$sql = "select * from user where id='$id' and deleted_at IS NULL";
$result = $connect->query($sql);
$row = $result->fetch_assoc();
$role = $row['role'];

if($role == 2){

    $searchTerm = isset($_GET['search']) ? $_GET['search'] : null;
    $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
    
    if(empty($startDate)){
        $startFormattedDate = null;
    }else{
        $startDateTime = DateTime::createFromFormat('Y-m-d', $startDate);
        $startFormattedDate = $startDateTime->format('d.m.Y');
    }
    
    if(empty($endDate)){
        $endFormattedDate = null;
    }else{
        $endDateTime = DateTime::createFromFormat('Y-m-d', $endDate);
        $endFormattedDate = $endDateTime->format('d.m.Y');
    }
    $orderBy = isset($_GET['orderby']) ? $_GET['orderby'] : null;
    
    $conditions = array();
    if ($searchTerm !== null) {
        $conditions[] = "(patients.name LIKE '%$searchTerm%' OR patients.email LIKE '%$searchTerm%' OR user.name LIKE '%$searchTerm%' OR services.services LIKE '%$searchTerm%')";
    }
    if ($startDate !== null && $endDate !== null) {
        $conditions[] = "STR_TO_DATE(patients.selected_date, '%d.%m.%Y') BETWEEN STR_TO_DATE('$startFormattedDate', '%d.%m.%Y') AND STR_TO_DATE('$endFormattedDate', '%d.%m.%Y')";
    }
    $whereClause = "";
    if (!empty($conditions)) {
        $whereClause ="WHERE patients.doctor='$id' AND patients.deleted_at IS NULL AND ". implode(" AND ", $conditions);
    }else{
        $whereClause = "WHERE patients.doctor='$id' AND patients.deleted_at IS NULL";
    }
    
    $orderClause = "";
    if ($orderBy !== null && in_array($orderBy, ['asc', 'desc'])) {
        $orderClause = "patients.status $orderBy,";
    } 
    
        $GetPatients = "SELECT patients.*, user.name AS doctor, services.services 
        FROM patients 
        LEFT JOIN user ON patients.doctor = user.id 
        LEFT JOIN services ON patients.services= services.id 
         $whereClause 
        ORDER BY $orderClause patients.id DESC";
    
        $PatientsResult = $connect->query($GetPatients);
        $PatientsList = array();
        
        if ($PatientsResult->num_rows > 0) {
            while ($row = $PatientsResult->fetch_assoc()) {
                $PatientsList[] = $row;
            }
        }
        
        $itemsPerPage = 20;
        $totalItems = count($PatientsList);
        $totalPages = ceil($totalItems / $itemsPerPage);
        $currentPage = isset($_GET['page']) ? max(1, min((int)$_GET['page'], $totalPages)) : 1;
        $startIndex = ($currentPage - 1) * $itemsPerPage;
        $endIndex = min($startIndex + $itemsPerPage - 1, $totalItems - 1);
}else{
    $GetStaff = "select * from user where deleted_at IS NULL AND role = 2";
    $StaffResult = $connect->query($GetStaff);
    $staffList = array();

    if ($StaffResult->num_rows > 0) {
        while ($row = $StaffResult->fetch_assoc()) {
            $staffList[] = $row;
        }
    }

    $searchTerm = isset($_GET['search']) ? $_GET['search'] : null;
    $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
    if(empty($startDate)){
        $startFormattedDate = null;
    }else{
        $startDateTime = DateTime::createFromFormat('Y-m-d', $startDate);
        $startFormattedDate = $startDateTime->format('d.m.Y');
    }

    if(empty($endDate)){
        $endFormattedDate = null;
    }else{
        $endDateTime = DateTime::createFromFormat('Y-m-d', $endDate);
        $endFormattedDate = $endDateTime->format('d.m.Y');
    }

    $orderBy = isset($_GET['orderby']) ? $_GET['orderby'] : null;

    $conditions = array();
    if ($searchTerm !== null) {
        $conditions[] = "(patients.name LIKE '%$searchTerm%' OR patients.email LIKE '%$searchTerm%' OR user.name LIKE '%$searchTerm%' OR services.services LIKE '%$searchTerm%')";
    }
    if ($startDate !== null && $endDate !== null) {
        $conditions[] = "STR_TO_DATE(patients.selected_date, '%d.%m.%Y') BETWEEN STR_TO_DATE('$startFormattedDate', '%d.%m.%Y') AND STR_TO_DATE('$endFormattedDate', '%d.%m.%Y')";
    }

    $whereClause = "";
    if (!empty($conditions)) {
        $whereClause = "WHERE patients.deleted_at IS NULL AND " . implode(" AND ", $conditions);
    }else{
        $whereClause = "WHERE patients.deleted_at IS NULL";
    }

    if ($orderBy !== null && in_array($orderBy, ['asc', 'desc'])) {

        $column = isset($_GET['column']) ? $_GET['column'] : null;
        if($column == 'doctor') {
            $orderClause = "user.name $orderBy";
        } else {
            $orderClause = "patients.status $orderBy";
        }

    } else {
        $orderClause = "patients.id DESC";
    }

    $GetPatients = "SELECT patients.*, user.name AS doctor, services.services FROM patients 
                    LEFT JOIN user ON patients.doctor = user.id 
                    LEFT JOIN services ON patients.services= services.id 
                    $whereClause
                    ORDER BY  
                    $orderClause";
    $PatientsResult = $connect->query($GetPatients);
    $PatientsList = array();

    if ($PatientsResult->num_rows > 0) {
        while ($row = $PatientsResult->fetch_assoc()) {
            $PatientsList[] = $row;
        }
    }

    $itemsPerPage = 20;
    $totalItems = count($PatientsList);
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = isset($_GET['page']) ? max(1, min((int)$_GET['page'], $totalPages)) : 1;
    $startIndex = ($currentPage - 1) * $itemsPerPage;
    $endIndex = min($startIndex + $itemsPerPage - 1, $totalItems - 1); 
}


?>

    <!-- Main -->
    <div id="main-content">
        <div class="p-2 w-100">
            <div class="d-flex justify-content-center align-items-center">
                <h1 class="page-heading">Patienten</h1>
            </div>
            <div class="px-2">

            <div class="d-flex justify-content-between flex-wrap">
                        <!-- <form method="post"> -->
                            <div class="dashboard-search m-2 mx-0">
                                    <i class="bi bi-search"></i>
                                    <input type="text" class="w-100" id="Search-input"
                                        placeholder="Suche" name="search" value="<?php echo $searchTerm?>">
                            </div>
                        <!-- </form> -->
                        <!-- <form method="post"> -->
                        <div class="d-flex flex-wrap align-items-center m-2">
                                <div class="input-date">
                                    <input class="mx-2" type="date" id="start-date" name="start_date" placeholder="Start Date" value="<?php echo $startDate; ?>">
                                </div>
                                <p class="mx-2 mb-0" style="font-size: var(--md-text); color: var(--main); font-weight: 500;">To</p>
                                <div class="input-date">
                                    <input class="mx-2" type="date" id="end-date" name="end_date" placeholder="End Date" value="<?php echo $endDate; ?>">
                                </div>
                                <div>
                                    <button type="submit" class="cursor-pointer showTimeBtn custom-main-btn mt-2 mt-md-0 mx-3" style="background-color: var(--main); color: white; border-radius: 16px;" id="applyDatepicker">Anwenden</button>
                                    <!-- <button class="cursor-pointer showAllBtn" style="background-color: var(--main); color: white;" id="FilterClear">Clear</button> -->
                                    
                                </div>
                                <div>
                                    <button type="submit" class="cursor-pointer showTimeBtn custom-main-btn mt-2 mt-md-0" style="background-color: var(--main); color: white; border-radius: 16px;" id="clearDatepicker">Löschen</button>
                                    <!-- <button class="cursor-pointer showAllBtn" style="background-color: var(--main); color: white;" id="FilterClear">Clear</button> -->
                                    
                                </div>
                            </div>
                            </div>
                        <!-- </form> -->
                    </div>

                <div class="mt-4 custom-table" id="Search-Options" onchange="handleSelect('Search-input')">
                    <div class=" table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <?php if($role == 2 || $role == 3){?>
                                        <td>#</td>
                                        <td>Name</td>
                                        <td>ID</td>
                                        <td>Leistung</td>
                                        <td>Besuch</td>
                                        <td>Rezept</td>
                                        <td class="text-center">Status<i class="fa-solid fa-arrow-up ms-2" stlye="font-size:14px" id="ASCstatus"></i><i class="fa-solid fa-arrow-down" stlye="font-size:14px" id="DESCstatus"></i></td>
                                        <td>
                                            <div class="d-flex justify-content-center">Optionen</div>
                                        </td>
                                    <?php }else{?>
                                        <td>#</td>
                                        <td>Name</td>
                                        <td>ID</td>
                                        <td>Leistung</td>
                                        <td>Arzt<i data-value="doctor" class="fa-solid fa-arrow-up-arrow-down Shorting ms-1" style="font-size: 14px;display: inline-block;"></i></td>
                                        <td>Termin</td>
                                        <td>Rezept</td>
                                        <td class="text-center">Status <i data-value="status" class="fa-solid fa-arrow-up-arrow-down Shorting" style="font-size: 14px;display: inline-block;"></i> </td>
                                        <td>
                                            <div class="d-flex justify-content-center">Optionen</div>
                                        </td>
                                    <?php }?>
                                </tr>
                            </thead>
                            <tbody>

                                <?php if($role == 2 || $role == 3){for ($i = $startIndex; $i <= $endIndex; $i++) {?>
                                    <tr class="patient-row">
                                        <td style="max-width: 100px;"><?php echo $i + 1; ?></td>
                                        <td style="min-width: 120px; max-width: 200px;">
                                            <div class="d-flex p-0 m-0 flex-column">
                                                <h5 class="mb-0">
                                                    <?php echo $PatientsList[$i]['name'];?>
                                                </h5>
                                                <p class="mb-0" style="color: var(--main); font-size: var(--sm-text);"><span
                                                        style="font-weight: 500;">E:
                                                    </span><a style="color: var(--main);"
                                                        href="mailto:<?php echo $PatientsList[$i]['email'];?>">
                                                        <?php echo $PatientsList[$i]['email'];?>
                                                    </a></p>
                                                <p class="mb-0" style="color: var(--main); font-size: var(--sm-text);"><span
                                                        style="color: var(--main);font-weight: 500;">T:
                                                    </span><a style="color: var(--main);"
                                                        href="tel:<?php echo $PatientsList[$i]['telephone'];?>">
                                                        <?php echo $PatientsList[$i]['telephone'];?>
                                                    </a></p>
                                            </div>
                                        </td>
                                        <td style="min-width: 80px;"><?php echo $PatientsList[$i]['id'];?></td>
                                        <td style="max-width: 200px;"><?php echo $PatientsList[$i]['services'];?></td>
                                        <td><?php echo $PatientsList[$i]['selected_date'];?> | <?php echo $PatientsList[$i]['visits'];?></td>
                                        
                                        <td><?php echo $PatientsList[$i]['recipe'];?></td>
                                        <?php
                                            if ($PatientsList[$i]['status'] === 'Vollendet') {
                                                $buttonClass = 'custom-success-btn';
                                            } elseif ($PatientsList[$i]['status'] === 'Abgesagt') {
                                                $buttonClass = 'custom-danger-btn';
                                            } 
                                             elseif ($PatientsList[$i]['status'] === 'Bevorstehende') {
                                                $buttonClass = 'custom-upcoming-btn';
                                            } 
                                           
                                            
                                        ?>

                                        <td class="d-flex justify-content-center flex-column align-items-center">
                                            <button class="statusBtn <?php echo $buttonClass; ?>" id="status-<?php echo $i + 1; ?>" data-id="<?php echo $PatientsList[$i]['id']; ?>">
                                                <?php 
                                                    $currentDate = date("d.m.Y");
                                                    $selectedDate = $PatientsList[$i]['selected_date'];
                                                    $status = $PatientsList[$i]['status'];
                                                    if($status == 'Vollendet' || $status == 'Abgesagt'){
                                                        echo $status;
                                                    }else{
                                                        if($selectedDate < $currentDate){
                                                            echo 'Ausstehend';
                                                        }else{
                                                            echo $status;
                                                        }
                                                    }
                                                ?>
                                            </button>
                                            <p class="mb-0" style="font-size: var(--xs-text); color: var(--secondary); width: fit-content;" id="appointmentTransfer-<?php echo $i + 1; ?>"> </p>
                                        </td>
                                        <td>
                                            <?php if($PatientsList[$i]['status'] != 'Vollendet') {?>
                                            <div class="d-flex justify-content-center dropdown">
                                                <span onclick="HandleDropMenu('Drop-menu-<?php echo $i + 1; ?>')"
                                                    style="border-radius: 50%;border: 1px solid var(--secondary);color: var(--secondary);"
                                                    class="px-1 cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-list"></i>
                                                </span>
                                                <ul id="Drop-menu-<?php echo $i + 1; ?>" class="dropdown-content">
                                                <li class="px-2 py-1 cursor-pointer addCalenderBtn" data-id="<?php echo $PatientsList[$i]['id'];?>" data-index="<?php echo $i; ?>" style="border-bottom: 1px solid gray;" data-bs-toggle="modal" onClick="addCalenderBtn(event)">
                                                    Add to calendar
                                                </li>
                                                <?php if($PatientsList[$i]['status'] != 'Abgesagt') {?>
                                                <li class="px-2 py-1 mx-2 cursor-pointer refreshBtn" data-id="<?php echo $PatientsList[$i]['id'];?>"   style="border-bottom: 1px solid #d7caca;;" data-bs-toggle="modal">
                                                Update
                                                </li>
                                                <li class="px-2 py-1 mx-2 cursor-pointer cancelBtn" data-id="<?php echo $PatientsList[$i]['id'];?>"  data-index="<?php echo $i; ?>" style="border-bottom: 1px solid #d7caca;;" data-bs-toggle="modal" onClick="cancelBtn(event)">
                                                Abbrechen
                                                </li>
                                                <?php } ?>
                                                <li class="px-2 py-1 cursor-pointer deleteBtn" data-id="<?php echo $PatientsList[$i]['id'];?>" style="color: #d1232a;" data-bs-toggle="modal">
                                                Löschen
                                                </li>
                                                </ul>
                                            </div>
                                        <?php } ?>
                                        </td>
                                    </tr>
                                    <?php }}else{ for ($i = $startIndex; $i <= $endIndex; $i++) {?>
                                        <tr class="doctor-row">
                                        <td style="max-width: 100px;"><?php echo $i + 1; ?></td>
                                        <td style="min-width: 120px; max-width: 200px;">
                                            <div class="d-flex p-0 m-0 flex-column">
                                                <h5 class="mb-0"><?php echo $PatientsList[$i]['name'];?></h5>
                                                <p class="mb-0" style="color: var(--main); font-size: var(--xs-text);"><span
                                                        style="font-weight: 500;">E:
                                                    </span><a style="color: var(--main);" href="mailto:<?php echo $PatientsList[$i]['email'];?>"><?php echo $PatientsList[$i]['email'];?></a></p>
                                                <p class="mb-0" style="color: var(--main); font-size: var(--xs-text);"><span
                                                        style="color: var(--main);font-weight: 500;">T:
                                                    </span><a style="color: var(--main);" href="tel:<?php echo $PatientsList[$i]['telephone'];?>"><?php echo $PatientsList[$i]['telephone'];?></a></p>
                                            </div>
                                        </td>
                                        <td style="min-width: 80px;"><?php echo $PatientsList[$i]['id'];?></td>
                                        <td style="max-width: 200px;"><?php echo $PatientsList[$i]['services'];?></td>
                                        <td><?php echo $PatientsList[$i]['doctor'];?></td>
                                        <td><?php echo $PatientsList[$i]['selected_date'];?> | <?php echo $PatientsList[$i]['visits'];?> Uhr</td>
                                        <td><?php echo $PatientsList[$i]['recipe'];?></td>
                                        <?php
                                                if ($PatientsList[$i]['status'] === 'Vollendet') {
                                                    $buttonClass = 'custom-success-btn';
                                                } elseif ($PatientsList[$i]['status'] === 'Abgesagt') {
                                                    $buttonClass = 'custom-danger-btn';
                                                } 
                                                elseif ($PatientsList[$i]['status'] == 'Bevorstehende') {
                                                    $buttonClass = 'custom-upcoming-btn';
                                                } 
                                            
                                                
                                            ?>

                                            <td class="d-flex justify-content-center flex-column align-items-center">
                                                <button class="statusBtn <?php echo $buttonClass; ?>" id="status-<?php echo $i + 1; ?>" data-id="<?php echo $PatientsList[$i]['id']; ?>">
                                                    <?php 
                                                        $currentDate = date("d.m.Y");
                                                        $selectedDate = $PatientsList[$i]['selected_date'];
                                                        $status = $PatientsList[$i]['status'];
                                                        if($status == 'Vollendet' || $status == 'Abgesagt'){
                                                            echo $status;
                                                        }else{
                                                            if($selectedDate < $currentDate){
                                                                echo 'Ausstehend';
                                                            }else{
                                                                echo $status;
                                                            }
                                                        }
                                                    ?>
                                                </button>
                                                <p class="mb-0" style="font-size: var(--xs-text); color: var(--secondary); width: fit-content;" id="appointmentTransfer-<?php echo $i + 1; ?>"> </p>
                                            </td>
                                        <td>
                                        <?php if($PatientsList[$i]['status'] != 'Vollendet') {?>
                                            <div class="d-flex justify-content-center">
                                                <?php if ($PatientsList[$i]['status'] !== 'Abgesagt') { ?>
                                                    <!-- Edit Button -->
                                                    <button class="btn btn-primary mx-1 patientsEditButton" data-id="<?php echo $PatientsList[$i]['id']; ?>" data-bs-toggle="modal">
                                                        Bearbeiten
                                                    </button>
                                                <?php } ?>
                                                <!-- Delete Button -->
                                                <button class="btn btn-danger mx-1 patientsDeleteButton" data-id="<?php echo $PatientsList[$i]['id']; ?>" data-bs-toggle="modal">
                                                    Löschen
                                                </button>
                                            </div>

                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php }}?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- pagination -->
                    <div class="white-table">
                    <ul class="custom-pagination" id="custom-pagination"></ul>
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
                    <button type="submit" class="success-button cursor-pointer"
                        data-bs-toggle="modal" data-bs-dismiss="modal" id="conformationYesBtn">Ja</button>
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

    <!-- cancel -->

    <div class="modal fade " id="cancelConfirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                <div class="d-flex justify-content-center align-items-center flex-column">
                    <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
                    <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
                </div>
                <div class="d-flex justify-content-center align-items-center">
                    <button type="submit" class="success-button cursor-pointer"
                        data-bs-toggle="modal" data-bs-dismiss="modal" id="cancelConformationYesBtn">Ja</button>
                    <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal">Nein</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade " id="cancel-show-info" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered " role="document">
            <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                <div class="d-flex justify-content-center align-items-center flex-column py-4">
                    <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich aktualisiert.</h1>
                </div>
                <div class="d-flex justify-content-center align-items-center">
                    <button type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal" id="cancelShowInfoBtn">Okay</button>
                </div>
            </div>
        </div>
    </div>

     <!-- delete -->

     <div class="modal fade " id="deleteConfirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                <div class="d-flex justify-content-center align-items-center flex-column">
                    <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
                    <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
                </div>
                <div class="d-flex justify-content-center align-items-center">
                    <button type="submit" class="success-button cursor-pointer"
                        data-bs-toggle="modal" data-bs-dismiss="modal" id="deleteConformationYesBtn">Ja</button>
                    <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal">Nein</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade " id="delete-show-info" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered " role="document">
            <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                <div class="d-flex justify-content-center align-items-center flex-column py-4">
                    <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich aktualisiert.</h1>
                </div>
                <div class="d-flex justify-content-center align-items-center">
                    <button type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal" id="deleteShowInfoBtn">Okay</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Details -->
    <form method="post" id="editPatientForm" action="./controller/updatepatient.php">
        <div class="modal fade " id="edit-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                    <div class="d-flex justify-content-center align-items-center mb-4 py-2">
                        <div class="flex-grow-1"></div>
                        <h1 class="modal-heading" style="font-weight: 800; font-size: var(--xl-text);">Terminangaben bearbeiten</h1>
                        <div class="flex-grow-1"></div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" style="width:50px;" aria-label="Close"></button>
                    </div>
                    <input type="hidden" class="form-control custom-input" id="PatientsId" name="id">
                    <p class="py-2" style="font-weight: 800;font-size: var(--md-text);">Patiernen: <span id="PatientsName"></span></p>
                    <div class="d-flex justify-content-between flex-wrap py-1">
                        <p><span style="font-weight: 800;">E-Mail: </span><a href="patientsEmail"><span id="patientsEmail"></span></a></p>
                        <p><span style="font-weight: 800;">Telefon: </span> <a href="patientsTelephone"><span id="patientsTelephone"></span></a></p>
                    </div>
                        <div class="form-group p-2 my-2">
                            <label class="my-1" for="time">Datum ändern</label>
                            <input type="date" name="date" class="form-control custom-input" id="datepicker" placeholder="Select date" value=''>
                        </div>
                       
                        <div class="form-group p-2 my-2">
                            <label class="my-1" for="time">Zeit ändern</label>
                            <select name="time" class="form-control custom-input" id="timeDropdown">
                                <!-- Options will be dynamically added here -->
                            </select>
                            <p class="error" id="time-error"></p>
                        </div>
                        <div class="form-group p-2 my-2">
                            <label class="my-1" for="Recipe">Rezept</label>
                            <textarea class="form-control custom-input" name="recipe" id="recipe" placeholder="Write here" style="height: 150px; resize: none;"></textarea>
                        </div>

                        <div class="form-group p-2 my-2">
                            <label class="my-1" for="status">Status</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="status" name="status" value="Vollendet">
                                <label class="form-check-label" for="status">Als abgeschlossen markieren</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center align-items-center my-3">
                            <button type="button" class="success-button cursor-pointer" id="patientSubmit">Einreichen</button>
                            <button type="button" class="cancel-button cursor-pointer"
                                data-bs-dismiss="modal">Cancel</button>
                        </div>
                       
                </div>
            </div>
        </div>
         <!-- conformation model  -->
        <div class="modal fade " id="editConfirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                    <div class="d-flex justify-content-center align-items-center flex-column">
                        <h1 class="modal-heading" style="font-weight: 800;">Are you sure?</h1>
                        <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <button type="button" class="success-button cursor-pointer" data-bs-target=""
                            data-bs-toggle="modal" data-bs-dismiss="modal" id="editconformationYesBtn">Ja</button>
                        <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal">Nein</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade " id="edit-show-info" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered " role="document">
                <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                    <div class="d-flex justify-content-center align-items-center flex-column py-4">
                        <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich aktualisiert.</h1>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <button type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal" id="">Okay</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- admin Edit Detail -->
     <form id="EditPatients" action="./controller/editpatients.php" method="post">
        <div class="modal fade " id="edit-patients" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                    <div class="d-flex justify-content-center align-items-center mb-4 py-2">
                    <div class="flex-grow-1"></div>
                        <h1 class="modal-heading" style="font-weight: 800; font-size: var(--xl-text);">Termin Details bearbeiten</h1>
                    <div class="flex-grow-1"></div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" style="width:50px;" aria-label="Close"></button>
                    </div>
                    <input type="hidden" name="id" value="" class="form-control custom-input" id="E-PatientsId">
                    <p class="py-2" style="font-weight: 800;font-size: var(--md-text);">Patiernen: <span  id="E-PatientsName"></span> </p>
                    <div class="d-flex justify-content-between flex-wrap py-1">
                        <p><span style="font-weight: 800;">E-Mail: </span> <a href="mailto:patientsEmail"><span id="E-patientsEmail"></span></a> </p>
                        <p><span style="font-weight: 800;">Telephone: </span><a href="tel:patientsTelephone"><span id="E-patientsTelephone"></span></a> </p>
                    </div>
                       
                        <div class="col-lg-12 col-12">
                            <div class="form-group p-2 my-2">
                                <label class="my-1" for="Status">Arzt wechseln</label>
                                <select name="doctor" class="form-control custom-input selectedDoctor" id="doctorSelect" value="">
                                <?php 
                                    foreach ($staffList as $staff) {
                                        $selected = ($staff['id'] == $patientsData['doctor']) ? 'selected' : '';
                                        echo "<option value='".$staff['id']."' id='".$staff['id']."'>" . $staff['name'] . "</option>";
                                    }
                                ?>
                                </select>
                                <p  class="error" id="doctor-error"></p>
                            </div>
                        </div>

                        <div class="col-lg-12 col-12">
                            <div class="form-group p-2 my-2">
                            <label class="my-1" for="time">Change Date</label>
                            <input type="date" name="date" class="form-control custom-input" id="E-datepicker" placeholder="Select date">
                            <p  class="error" id="date-error"></p>
                            </div>
                        </div>

                        <div class="col-lg-12 col-12">
                            <div class="form-group p-2 my-2">
                                <label class="my-1" for="Status">Zeit ändern</label>
                                <select name="time" class="form-control custom-input" id="timeList">
                                </select>
                                <p  class="error" id="E-time-error"></p>
                                <p  class="error text-danger" id="time-error2" style="display:none">Dieses Datum Zeit nicht verfügbar</p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center align-items-center my-3">
                            <button type="button" class="success-button cursor-pointer"
                                id="UpdatePatients">Update</button>
                            <button type="button" class="cancel-button cursor-pointer"
                                data-bs-dismiss="modal">Abbrechen</button>
                        </div>
                </div>
            </div>
        </div>

        <!-- Confirmation -->
        <div class="modal fade " id="EditConfirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                    <div class="d-flex justify-content-center align-items-center flex-column">
                        <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
                        <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <button type="button" class="success-button cursor-pointer"
                            id="EditConfirmationYesBtn">Ja</button>
                        <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal">Nein</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade " id="E-edit-show-info" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered " role="document">
                <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                    <div class="d-flex justify-content-center align-items-center flex-column py-4">
                        <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich aktualisiert.</h1>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <button type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal">Okay</button>
                    </div>
                </div>
            </div> 
        </div> 
    </form>

    <!-- admin delete conformation  -->
    <form method="post" action="./controller/deletepatients.php">
        <div class="modal fade " id="ConfirmationDelete" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <input type="hidden" name="id" value="" class="form-control custom-input" id="deletePatientsId">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                    <div class="d-flex justify-content-center align-items-center flex-column">
                        <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
                        <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <button type="button" class="success-button cursor-pointer"
                            data-bs-toggle="modal" data-bs-dismiss="modal" id="deleteConformation">Ja</button>
                        <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal">Nein</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade " id="show-info-delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered " role="document">
                <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
                    <div class="d-flex justify-content-center align-items-center flex-column py-4">
                        <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich gelöscht.</h1>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <button type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal">Okay</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- loader  -->
    <div class="spinner" id="loader" style="display:none">
		<div class="spinner-container">
			<div class="spinner-loader"></div>
		</div>
	</div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <script src="asset/js/index.js"></script>
    <script src="asset/js/pagination.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <?php if($role == 2 || $role == 3){?>
        <script src="asset/js/script2.js"></script>
        <script>

            $("#editPatientForm").validate({
                rules: {
                    time: {
                    required: true,
                },
                },
                messages: {
                    time: {
                    required: "time is required.",
                },
                },
                errorPlacement: function (error, element) {
                if (element.attr("name") == "time") {
                    error.insertAfter("#time-error");
                }
                error.addClass("text-danger");
                },
                highlight: function (element) {
                $(element).siblings(".error").addClass("text-danger");
                },
                unhighlight: function (element) {
                $(element).siblings(".error").removeClass("text-danger");
                },
            });

            $("#patientSubmit").on("click", function () {
                if ($("#editPatientForm").valid()) {
                    $('#edit-details').modal('hide');
                    $('#editConfirmation').modal('show');
                }
            })

            var appointmentTransferId = "";
            var buttonText = "";
            var cancelPatientId = "";
            var deletePatientId = "";
            var statusBtnId = "";
            function addCalenderBtn(event) {

                $('#Confirmation').modal('show');
                var patientId = event.target.getAttribute('data-id');
                var dataIndex = event.target.getAttribute('data-index');
                statusBtnId = 'status-' + (parseInt(dataIndex) + 1);
                appointmentTransferId = 'appointmentTransfer-' + (parseInt(dataIndex) + 1);
                buttonText = $('#' + statusBtnId).text().trim();
            }

            $('#conformationYesBtn').on('click', function() {
                $('#Confirmation').modal('hide');
                $('#show-info').modal('show');
            });
            $('#showInfoBtn').on('click', function() {
                $('#Confirmation').modal('hide');
                if(buttonText == 'Bevorstehende' || buttonText == 'Vollendet'){
                    $('#' + appointmentTransferId).html('Transfer Appointment').css('color', 'green');
                }else{
                    $('#' + appointmentTransferId).html('Appointment Not Transferred');
                }
            });
            var time = '';
            var date = '';
            var bookingId = '';
            $('.refreshBtn').on('click', function() {
                var patientId = $(this).data('id');

                $.ajax({
                url: './ajax/editpatients.php',
                method: 'GET',
                data: { patientId: patientId },
                    success: function(response) {
                        var patientsData = JSON.parse(response);
                        $('#PatientsId').val(patientsData.id);
                        $('#PatientsName').html(patientsData.name);
                        $('#patientsEmail').html(patientsData.email);
                        $('#patientsTelephone').html(patientsData.telephone);
                        $('#recipe').val(patientsData.recipe);
                        bookingId = patientsData.id
                        time = patientsData.visits;
                        var dateString = patientsData.selected_date;
                        var parts = dateString.split('.');
                        var day = parts[0];
                        var month = parts[1];
                        var year = parts[2];
                        date = year + "-" + month + "-" + day;
                        $('#datepicker').val(date);
                        $.ajax({
                            url: './ajax/changetime.php',
                            method: 'GET',
                            data: { date: date,
                                bookingId: bookingId },
                                success: function(response) {
                                    if(response == 'null'){
                                        alert("This date time not available");
                                    }
                                    var timeOptions = JSON.parse(response);

                                    timeDropdown = $('#timeDropdown');

                                    timeDropdown.empty();

                                    timeOptions.forEach(function(times) {
                                    var timeData = JSON.parse(times);
                                        timeData.forEach(function(timeOption) {
                                            var option = $('<option>', {
                                                value: timeOption,
                                                text: timeOption
                                            });
                                            if (timeOption === time) {
                                                option.prop('selected', true);
                                            }
                                            timeDropdown.append(option);
                                        })
                                    });
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error:', error);
                                }
                            });

                        // $('#status').val(patientsData.status);
                        $('#edit-details').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });
        
            $('#editconformationYesBtn').on('click', function() {
                $('#edit-show-info').modal('show');
            });

            // cancel 
            function cancelBtn(event) {
                $('#cancelConfirmation').modal('show');
                var patientId = event.target.getAttribute('data-id');
                var dataIndex = event.target.getAttribute('data-index');
                statusBtnId = 'status-' + (parseInt(dataIndex) + 1);
                cancelPatientId = event.target.getAttribute('data-id');
            }
            $('.cancelBtn').on('click', function() {
                // cancelPatientId = $(this).data('id');
                // statusId = 
                $('#cancelConfirmation').modal('show');
            });
            $('#cancelConformationYesBtn').on('click', function() {
                $('#cancel-show-info').modal('show');
            });
            $('#cancelShowInfoBtn').on('click', function() {
                $.ajax({
                url: './ajax/editpatients.php',
                method: 'POST',
                data: { cancelPatientId: cancelPatientId },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });

            // delete 
            $('.deleteBtn').on('click', function() {
                deletePatientId = $(this).data('id');
                $('#deleteConfirmation').modal('show');
            });
            $('#deleteConformationYesBtn').on('click', function() {
                $('#delete-show-info').modal('show');
            });
            $('#deleteShowInfoBtn').on('click', function() {
                $.ajax({
                url: './ajax/editpatients.php',
                method: 'POST',
                data: { deletePatientId: deletePatientId },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });

            function search() {
                let input, filter, options, option, i, txtValue;
                input = document.getElementById('Search-input');
                filter = input.value.toUpperCase();
                options = document.getElementById('Search-Options').getElementsByTagName('tbody');

                for (i = 0; i < options.length; i++) {
                    option = options[i];
                    txtValue = option.textContent || option.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                }
            }


            function filterByDate() {
                    const startDate = document.getElementById("start-date").value;
                    const endDate = document.getElementById("end-date").value;

                    const startDateObj = parseCustomDate(startDate);
                    const endDateObj = parseCustomDate(endDate);

                    const doctorRows = document.getElementsByClassName("patient-row");

                    for (let i = 0; i < doctorRows.length; i++) {
                        const dateString = doctorRows[i].getElementsByClassName("created-at")[0].textContent;
                        const dataDate = parseCustomDate(dateString);

                        if (dataDate >= startDateObj && dataDate <= endDateObj) {
                            doctorRows[i].style.display = "table-row";
                        } else {
                            doctorRows[i].style.display = "none";
                        }
                    }
            }
            
            function parseCustomDate(dateString) {
                    const [day, month, year] = dateString.split('.');
                    return new Date(`${year}-${month}-${day}`);
            }

            $('#datepicker').on('change', function() {
                var selectedDate = $(this).val();
                $.ajax({
                url: './ajax/changetime.php',
                method: 'GET',
                data: { 
                    date: selectedDate,
                    bookingId: bookingId
                 },
                    success: function(response) {
                        if(response == 'null'){
                            alert("This date time not available");
                        }
                        var timeOptions = JSON.parse(response);

                        timeDropdown = $('#timeDropdown');

                        timeDropdown.empty();

                        timeOptions.forEach(function(times) {
                        var timeData = JSON.parse(times);
                            timeData.forEach(function(time) {
                                timeDropdown.append('<option value="' + time + '">' + time + '</option>');
                            })
                        });
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
            <?php if($totalItems > $itemsPerPage){ ?>
                
            CreatePagination({
                elementId: "custom-pagination",
                totalPage: <?php echo $totalPages; ?>,
                currentPage:currentPage ? Number(currentPage) : 1
            })  
             <?php } ?>    
            $('#Search-input').on('keyup', function(e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    var searchValue = $(this).val();
                    var url = window.location.href.split('?')[0];
                    window.location.href = url + '?search=' + encodeURIComponent(searchValue);
                }
            });
            $('#applyDatepicker').on('click', function() {
                
                var startDate = $('#start-date').val();
                var endDate = $('#end-date').val();
                
                var url = window.location.href;
                var separator = url.indexOf('?') !== -1 ? '&' : '?';
                var newUrl = url + separator + 'start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);
                window.location.href = newUrl;
                
            });
            $('#ASCstatus').on('click', function() {
                var url = window.location.href;
                var orderby = 'asc';
                var newUrl;

                if (url.includes('orderby')) {
                    // If "orderby" parameter exists, toggle between 'asc' and 'desc'
                    newUrl = url.includes('orderby=desc') ? url.replace('orderby=desc', 'orderby=asc') : url.replace('orderby=asc', 'orderby=desc');
                } else {
                    // If "orderby" parameter doesn't exist, add it
                    newUrl = url + (url.includes('?') ? '&' : '?') + 'orderby=' + encodeURIComponent(orderby);
                }

                window.location.href = newUrl;
                
            });
            $('#DESCstatus').on('click', function() {
                var url = window.location.href;
                var orderby = 'desc';
                var newUrl;

                if (url.includes('orderby')) {
                    // If "orderby" parameter exists, toggle between 'asc' and 'desc'
                    newUrl = url.includes('orderby=asc') ? url.replace('orderby=asc', 'orderby=desc') : url.replace('orderby=desc', 'orderby=asc');
                } else {
                    // If "orderby" parameter doesn't exist, add it
                    newUrl = url + (url.includes('?') ? '&' : '?') + 'orderby=' + encodeURIComponent(orderby);
                }

                window.location.href = newUrl;
                
            });
            // ASCstatus

            $('#clearDatepicker').on('click', function() {
                var url = window.location.href;
                var baseUrl = url.split('?')[0];
                window.location.href = baseUrl;
            })
        
        </script>
    <?php }else{?>
        <script src="asset/js/script.js"></script>
        <script>
            $(document).ready(function() {

                $('#EditPatients').validate({
                    rules: {
                        doctor: {
                            required: true,
                        },
                        date: {
                            required: true,
                        },
                        time: {
                            required: true,
                        },
                    },
                    messages: {
                        doctor: {
                            required: "Doctor is required.",
                        },
                        date: {
                            required: "Date is required.",
                        },
                        time: {
                            required: "Time is required.",
                        },
                    },
                    errorPlacement: function(error, element) {
                        if (element.attr("name") == "doctor") {
                            error.insertAfter("#doctor-error");
                        }
                        else if (element.attr("name") == "date") {
                            error.insertAfter("#date-error");
                        }
                        else if (element.attr("name") == "time") {
                            error.insertAfter("#E-time-error");
                        }
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

        </script>
        <script>
            const params = new URLSearchParams(window.location.search);
            const currentPage = params.get('page');
            <?php if($totalItems > $itemsPerPage){ ?>
            CreatePagination({
                elementId: "custom-pagination",
                totalPage: <?php echo $totalPages; ?>,
                currentPage:currentPage ? Number(currentPage) : 1
            })
            <?php } ?>
        </script>
        <script>
            
            function filterByDate() {
                const startDate = document.getElementById("start-date").value;
                const endDate = document.getElementById("end-date").value;

                const startDateObj = parseCustomDate(startDate);
                const endDateObj = parseCustomDate(endDate);

                const doctorRows = document.getElementsByClassName("doctor-row");

                for (let i = 0; i < doctorRows.length; i++) {
                    const dateString = doctorRows[i].getElementsByClassName("created-at")[0].textContent;
                    const dataDate = parseCustomDate(dateString);

                    if (dataDate >= startDateObj && dataDate <= endDateObj) {
                        doctorRows[i].style.display = "table-row";
                    } else {
                        doctorRows[i].style.display = "none";
                    }
                }
            }

            // Custom function to parse dates in the format "24.12.2023"
            function parseCustomDate(dateString) {
                const [day, month, year] = dateString.split('.');
                return new Date(`${year}-${month}-${day}`);
            }

            var doctorId = '';
            $("#doctorSelect").change(function () {
                doctorId = $(this).children("option:selected").attr("id");
                $('#timeList').empty();
            })
            
            $('#E-datepicker').on('change', function() {
                var selectedDate = $(this).val();
                doctorId = $('#doctorSelect').val();
                
                $.ajax({
                    url: './ajax/datetimelist.php',
                    method: 'GET',
                    data: {
                            doctorId: doctorId,
                            selectedDate: selectedDate,
                    },
                    success: function (response) {

                        if(!response){
                                alert('This date time not available');
                                $('#timeList').empty();
                        } else {
                            try {
                                var timeArrayWrapper = JSON.parse(response);

                                // Check if timeArrayWrapper is not null and has the 'time' property
                                $('#timeList').empty();
                                if (timeArrayWrapper !== null && 'time' in timeArrayWrapper) {
                                    var timeArray = JSON.parse(timeArrayWrapper.time);


                                    timeArray.forEach(function (time) {
                                        $('#timeList').append('<option>' + time + '</option>');
                                    });
                                } 
                                
                            } catch (error) {
                                console.error('Error parsing JSON:', error);
                                // Handle the error if parsing fails
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                    }
                });

            })

            $('#Search-input').on('keyup', function(e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    var searchValue = $(this).val();
                    var url = window.location.href.split('?')[0];
                    window.location.href = url + '?search=' + encodeURIComponent(searchValue);
                    console.log("Search value:", searchValue);
                }
            });
            $('#applyDatepicker').on('click', function() {
                
                var startDate = $('#start-date').val();
                var endDate = $('#end-date').val();
                
                var url = window.location.href;
                var separator = url.indexOf('?') !== -1 ? '&' : '?';
                var newUrl = url + separator + 'start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);
                window.location.href = newUrl;
                
            });

            $('.Shorting').on('click', function() {
                var column = $(this).data('value');
                var url = window.location.href;
                var orderby = 'asc';
                if (url.includes('orderby')) {
                    var url2 = new URL(url);
                    var c = url2.searchParams.get("orderby");
                    orderby = (c == 'asc') ? 'desc' : 'asc';
                }
                var url2 = window.location.href.split('?')[0];
                newUrl = url2 + '?'+'orderby=' + encodeURIComponent(orderby) + '&column=' + encodeURIComponent(column);
                window.location.href = newUrl;
                
            });
            // ASCstatus

            $('#clearDatepicker').on('click', function() {
                var url = window.location.href;
                var baseUrl = url.split('?')[0];
                window.location.href = baseUrl;
            })
            
        </script>
    <?php }?>
    
    <!-- logout  -->
     <?php include('./layout/script.php')?>
</body>

</html>