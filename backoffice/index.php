<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
  header("Location: login");
  }
include ('config/database.php');
include ('layout/header.php');
include ('layout/sidebar.php');

$id = $_SESSION['staff_id'];

$sql = "select * from user where id='$id' and deleted_at IS NULL";
$result = $connect->query($sql);
$row = $result->fetch_assoc();
$role = $row['role'];

$searchTerm = isset($_GET['search']) ? $_GET['search'] : null;

if ($role == 1) {

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['service_id']) {
      $serviceId = mysqli_real_escape_string($connect, $_POST['service_id']);
      $services = mysqli_real_escape_string($connect, $_POST['services']);
      $updated_at = date("Y-m-d");
      $sql = "update services set services='$services',updated_at='$updated_at' where id='$serviceId'";
      $result = $connect->query($sql);
      } else {

      $services = mysqli_real_escape_string($connect, $_POST['services']);
      $created_at = date("Y-m-d");

      $sql = "insert into services (services,created_at)
            VALUES ('$services','$created_at')";
      if ($connect->query($sql) === TRUE) {
        $message = "New services created successfully";
        }
      }
    }

  $GetServices = "select * from services where deleted_at IS NULL";
  $ServiceResult = $connect->query($GetServices);
  $servicesArray = array(); // Initialize an empty array to store services

  if ($ServiceResult->num_rows > 0) {
    while ($row = $ServiceResult->fetch_assoc()) {
      $servicesArray[] = $row; // Store services in the array
      }
    }

  $GetStaff = "select * from user where deleted_at IS NULL AND role = 2";
  $StaffResult = $connect->query($GetStaff);
  $staffList = array();

  $totalEmployee = $StaffResult->num_rows;

  if ($StaffResult->num_rows > 0) {
    while ($row = $StaffResult->fetch_assoc()) {
      $staffList[] = $row;
      }
    }

  $GetPatients = "select * from patients where deleted_at IS NULL";
  $PatientsResult = $connect->query($GetPatients);
  $PatientsList = array();

  $totalPatients = $PatientsResult->num_rows;

  // echo($totalEmployee);

  if ($PatientsResult->num_rows > 0) {
    while ($row = $PatientsResult->fetch_assoc()) {
      $PatientsList[] = $row;
      }
    }
  $itemsPerPage = 5;
  $totalItems = count($PatientsList);
  $totalPages = ceil($totalItems / $itemsPerPage);
  $currentPage = isset($_GET['page']) ? max(1, min((int) $_GET['page'], $totalPages)) : 1;
  $startIndex = ($currentPage - 1) * $itemsPerPage;
  $endIndex = min($startIndex + $itemsPerPage - 1, $totalItems - 1);

  } else {

  $GetStaff = "select * from user where deleted_at IS NULL";
  $StaffResult = $connect->query($GetStaff);
  $staffList = array();

  $totalEmployee = $StaffResult->num_rows;

  if ($StaffResult->num_rows > 0) {
    while ($row = $StaffResult->fetch_assoc()) {
      $staffList[] = $row;
      }
    }

  $GetPatients = "select * from patients where deleted_at IS NULL";
  $PatientsResult = $connect->query($GetPatients);
  $PatientsList = array();

  $totalPatients = $PatientsResult->num_rows;

  $GetPatients1 = "select * from patients where doctor='$id' AND deleted_at IS NULL AND status='durchgeführt'";
  $PatientsResult1 = $connect->query($GetPatients1);

  $totalPatients1 = $PatientsResult1->num_rows;

  if ($PatientsResult->num_rows > 0) {
    while ($row = $PatientsResult->fetch_assoc()) {
      $PatientsList[] = $row;
      }
    }
  $itemsPerPage = 5;
  $totalItems = count($PatientsList);
  $totalPages = ceil($totalItems / $itemsPerPage);
  $currentPage = isset($_GET['page']) ? max(1, min((int) $_GET['page'], $totalPages)) : 1;
  $startIndex = ($currentPage - 1) * $itemsPerPage;
  $endIndex = min($startIndex + $itemsPerPage - 1, $totalItems - 1);
  }



?>

<!-- Main -->
<div id="main-content">
  <div class="p-2 w-100">
    <div class="d-flex justify-content-center align-items-center">
      <h1 class="page-heading">Übersicht</h1>
    </div>
    
  </div>

  <div class="py-2 px-md-5 px-3 w-100">
    <div class="dashboard-search m-2 mx-0">
      <i class="bi bi-search"></i>
      <input type="text" class="w-100" id="Search-input" placeholder="Suche" value="<?= $searchTerm ?>">
    </div>
    <div class="row mt-3">
      <div class="col-xxl-8 col-12 mb-5">
        <div class="today-appointments px-4">
          <div class="d-flex justify-content-center py-3">
            <h4 id='appointmentHeading'>Heutige Termine</h4>
          </div>
          <div class="table-responsive" id="Search-Options" onchange="handleSelect('Search-input')">
            <table class="table table-hover">
              <thead>
                <tr>
                  <td>Patient</td>
                  <td>Arzt</td>
                  <td>Leistung</td>
                  <td class="text-center">Uhrzeit</td>
                  <td>
                    <div class="d-flex justify-content-center">Aktion</div>
                  </td>
                </tr>
              </thead>
              <tbody id="patientList">

              </tbody>
            </table>
          </div>
          <!-- pagination -->
          <div>
            <ul class="custom-pagination" id="custom-pagination">
            </ul>
          </div>
        </div>
      </div>
      <div class="col-xxl-4  col-12 mb-5 mb-xl-0">
        <div class="my-calender px-2">
          <div class="calendar">
            <div class="header">
              <div class="cursor-pointer" onclick="prevMonth()">
                <span class="prev icon" style="color: black;"><i
                    class="fa-solid fa-circle-chevron-left me-3 mt-1"></i></span>
                <span class="pre-month-year invisible position-absolute"></span>
              </div>
              <div>
                <span class="month-year next"></span>
              </div>
              <div class="cursor-pointer" onclick="nextMonth()">
                <span class="next-month-year text-dark invisible position-absolute"></span>
                <span class="icon text-dark"><i class="fa-solid fa-circle-chevron-right ms-3 mt-1"></i></span>
              </div>
            </div>
            <table class="days">
              <thead>
                <tr>
                  <th>M</th>
                  <th>D</th>
                  <th>M</th>
                  <th>D</th>
                  <th>F</th>
                  <th>S</th>
                  <th>S</th>
                </tr>
              </thead>
              <tbody id="calendarBody" onclick="getDate(event)"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modals -->

<!-- Add Staff -->
<form id="AddStaff" method="post" action="./controller/addstaff.php" enctype="multipart/form-data">
  <div class="modal fade " id="add-staff" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 32px;">
        <div class="d-flex justify-content-center align-items-center py-2">
          <div class="flex-grow-1"></div>
          <h1 class="modal-heading" style="font-weight: 800;">Personalhinzufugen</h1>
          <div class="flex-grow-1"></div>
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" style="width:50px;"
            aria-label="Close"></button>
        </div>
        <div class="row">
          <div class="col-lg-6 col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="Name">Name</label>
              <input type="text" name="name" class="form-control custom-input" id="Name" placeholder="Namen eingeben">
              <span class="error" id="name-error"></span>
            </div>
          </div>

          <div class="col-lg-6 col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="email">E-Mail</label>
              <input type="email" name="email" class="form-control custom-input" id="email"
                placeholder="E-Mail eingeben">
              <span class="error" id="email-error"></span>
            </div>
          </div>
          <div class="col-lg-6 col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="Telephone">Telefon</label>
              <input type="text" name="telephone" class="form-control custom-input" id="Telephone"
                placeholder="Telefon eingeben">
              <span class="error" id="telephone-error"></span>
            </div>
          </div>
          <div class="col-lg-6 col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="password">Passwort</label>
              <input type="password" name="password" class="form-control custom-input" id="password"
                placeholder="Passwort eingeben" require>
              <span class="error" id="password-error"></span>
            </div>
          </div>
          <div class="col-lg-6 col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="password">Passwort bestätigen</label>
              <input type="password" name="confirm_password" class="form-control custom-input" id="confirm_password"
                placeholder="Passwort eingeben" require>
              <span class="error" id="confirm_password-error"></span>
            </div>
          </div>
          <div class="col-lg-6 col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="Status">Status</label>
              <select name="status" class="form-select custom-input cursor-pointer" id="Status">
                <option>Aktiv</option>
                <option>Inaktiv</option>
              </select>
              <span class="error" id="status-error"></span>
            </div>
          </div>
          <div class="col-lg-6 col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="Status">Profil</label>
              <input type="file" name="profile" class="form-control custom-input d-none" id="profile-image"
                aria-invalid="false" accept="image/*">
              <button class="custom-main-btn d-block w-100" type="button" id="open-image-picker"><i
                  class="bi bi-upload mx-2"></i>Bild hochladen</button>
              <span class="error" id="status-error"></span>
            </div>
            <img id="image-preview">
          </div>
          <div class="col-lg-6 col-12">
            <div class="form-group p-2 select-group my-2" id="select-group">
              <label class="my-1" for="Services">Leistung</label>
              <div style="height: 150px; overflow-x:hidden; overflow-y:scroll;">
                <?php
                foreach ($servicesArray as $service) {
                  echo '<div class="form-check">';
                  echo '<input class="form-check-input" type="checkbox" name="staff_services[]" id="' . $service['services'] . '" value="' . $service['services'] . '">';
                  echo '<label class="form-check-label" for="' . $service['services'] . '">' . $service['services'] . '</label>';
                  echo '</div>';
                  }
                ?>
              </div>

              <span class="error" id="staff_services-error"></span>
            </div>
          </div>


        </div>
        <div class="d-flex justify-content-center align-items-center py-2 my-3">
          <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal"
            style="margin-right: 5px;">Abbrechen</button>
          <button type="button" id="addStaffBtn" class="success-button cursor-pointer">Aktualisieren</button>
        </div>
      </div>
    </div>
  </div>
  <!-- staff Confirmation -->
  <div class="modal fade " id="StaffConfirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 32px;">
        <div class="d-flex justify-content-center align-items-center flex-column">
          <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
          <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
        </div>
        <div class="d-flex justify-content-center align-items-center">
          <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal"
            style="margin-right: 3px;">Nein</button>
          <button type="button" id="StaffConfirmationYesBtn" class="success-button cursor-pointer"
            data-bs-target="#show-info" style="margin-left: 3px;">Ja</button>
        </div>
      </div>
    </div>
  </div>
  <!-- staff show info  -->
  <div class="modal fade " id="staffShowInfo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 32px;">
        <div class="d-flex justify-content-center align-items-center flex-column py-4">
          <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich
            aktualisiert.</h1>
        </div>
        <div class="d-flex justify-content-center align-items-center">
          <button id="StaffShowInfoBtn" type="submit" class="success-button cursor-pointer"
            data-bs-dismiss="modal">Okay</button>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- Services -->
<form id="ServiceForm" method="post">
  <div class="modal fade " id="show-services" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 32px;">
        <div class="d-flex justify-content-center align-items-center mb-4">
          <h1 class="modal-heading" style="font-weight: 800;">Leistung</h1>
        </div>
        <ul class="show-services" style="color: #381e14;font-size: var(--md-text);font-weight:500;">
          <?php
          foreach ($servicesArray as $service) {
            echo "<li>
              [<div class='row'>
                  <div class='col-8'>
                      <p class='col-8 mb-0'>" . $service['services'] . "</p>
                  </div>
                  
              </div>
          </li>";
            }
          ?>
        </ul>

      </div>
    </div>
  </div>

  <!-- Confirmation -->
  <div class="modal fade " id="Confirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 32px;">
        <div class="d-flex justify-content-center align-items-center flex-column">
          <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
          <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
        </div>
        <div class="d-flex justify-content-center align-items-center">
          <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal"
            style="margin-right: 3px;">Nein</button>
          <button type="button" id="ConfirmationYesBtn" class="success-button cursor-pointer"
            data-bs-target="#show-info" style="margin-left: 3px;">Ja</button>
        </div>
      </div>
    </div>
  </div>

  <!-- show info  -->
  <div class="modal fade " id="show-info" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 32px;">
        <div class="d-flex justify-content-center align-items-center flex-column py-4">
          <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich
            aktualisiert.</h1>
        </div>
        <div class="d-flex justify-content-center align-items-center">
          <button id="ShowInfoBtn" type="submit" class="success-button cursor-pointer"
            data-bs-dismiss="modal">Okay</button>
        </div>
      </div>
    </div>
  </div>
</form>

<form id="EditPatients" action="./controller/editpatients.php" method="post">
  <div class="modal fade" id="edit-patients" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" 
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 32px;">
        <div class="d-flex justify-content-center align-items-center py-2">
          <div class="flex-grow-1"></div>
          <h1 class="modal-heading" style="font-weight: 800; font-size: var(--xl-text);">Termin bearbeiten</h1>
          <div class="flex-grow-1"></div>
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" style="width:30px;"
            aria-label="Close"></button>
        </div>
        <input type="hidden" name="id" value="" class="form-control custom-input" id="PatientsId">
        <p class="py-2" style="font-weight: 800;font-size: var(--md-text);">Patient: <span id="PatientsName"></span>
        </p>
        <div class="d-flex justify-content-between flex-wrap py-1">
          <p><span style="font-weight: 800;">E-Mail: </span> <a href="mailto:patientsEmail"><span
                id="patientsEmail"></span></a> </p>
          <p><span style="font-weight: 800;">Telefon: </span> <a href="tel:patientsTelephone"><span
                id="patientsTelephone"></span></a> </p>
        </div>
        <?php if ($role == 1) { ?>
        <div class="col-lg-12 col-12">
          <div class="form-group p-2 my-2">
            <label class="my-1" for="Status">datum wechseln</label>
            <select name="doctor" class="form-select cursor-pointer custom-input selectedDoctor" id="doctorSelect" value="">
              <?php
                foreach ($staffList as $staff) {
                  $selected = ($staff['id'] == $patientsData['doctor']) ? 'selected' : '';
                  echo "<option value='" . $staff['id'] . "' id='" . $staff['id'] . "'>" . $staff['name'] . "</option>";
                  }
                ?>
            </select>
            <p class="error" id="doctor-error"></p>
          </div>
        </div>
        <div class="col-lg-12 col-12">
          <div class="form-group p-2 my-2">
            <label class="my-1" for="time">Arzt wechseln</label>
            <input type="date" name="date" class="form-control custom-input" id="datepicker" placeholder="Select date">
            <p class="error" id="date-error"></p>
          </div>
        </div>

        <div class="col-lg-12 col-12">
          <div class="form-group p-2 my-2">
            <label class="my-1" for="Status">Zeit ändern</label>
            <select name="time" class="form-select cursor-pointer custom-input" id="timeList">
            </select>
            <p class="error" id="time-error"></p>
          </div>
        </div>
        <?php } else { ?>
        <div class="col-lg-12 col-12">
          <div class="form-group p-2 my-2">
            <label class="my-1" for="time">Datum ändern</label>
            <input type="date" name="date" class="form-control custom-input" id="datepicker" placeholder="Select date">
            <p class="error" id="date-error"></p>
          </div>
        </div>

        <div class="col-lg-12 col-12">
          <div class="form-group p-2 my-2">
            <label class="my-1" for="Status">Uhrzeit ändern</label>
            <select name="time" class="form-select cursor-pointer custom-input" id="timeList">
            </select>
            <p class="error" id="time-error"></p>
          </div>
        </div>
        <?php } ?>

        <div class="d-flex justify-content-center align-items-center my-3">
          <button type="button" class="cancel-button cursor-pointer" style="margin-right: 5px;"
            data-bs-dismiss="modal">Abbrechen</button>
          <button type="button" class="success-button cursor-pointer" id="UpdatePatients">Aktualisieren</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Confirmation -->
  <div class="modal fade " id="EditConfirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 32px;">
        <div class="d-flex justify-content-center align-items-center flex-column">
          <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
          <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
        </div>
        <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal"
          style="margin-right: 3px;">Nein</button>
        <div class="d-flex justify-content-center align-items-center">
          <button type="button" class="success-button cursor-pointer" id="EditConfirmationYesBtn"
            style="margin-left: 3px;">Ja</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade " id="edit-show-info" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 32px;">
        <div class="d-flex justify-content-center align-items-center flex-column py-4">
          <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich
            aktualisiert.</h1>
        </div>
        <div class="d-flex justify-content-center align-items-center">
          <button type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal">Okay</button>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- service delete conformation model  -->

<div class="modal fade " id="deleteServiceConfirmation" tabindex="-1" role="dialog"
  aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
      <div class="d-flex justify-content-center align-items-center flex-column">
        <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
        <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
      </div>
      <div class="d-flex justify-content-center align-items-center">
        <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal"
          style="margin-right: 3px;">Nein</button>
        <button type="button" class="success-button cursor-pointer" id="deleteServiceYesBtn"
          style="margin-left: 3px;">Ja</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade " id="service-delete-show-info" tabindex="-1" role="dialog"
  aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered " role="document">
    <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
      <div class="d-flex justify-content-center align-items-center flex-column py-4">
        <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich gelöscht.
        </h1>
      </div>
      <div class="d-flex justify-content-center align-items-center">
        <button type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal"
          id="serviceOkBtn">Okay</button>
      </div>
    </div>
  </div>
  <!-- Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
  </script>
  <!-- Custom -->
  <script src="asset/js/index.js"></script>
  <script src="asset/js/calender.js"></script>
  <script src="asset/js/pagination.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
  <?php if ($role == 1) { ?>
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
          required: "Ein Arzt ist erforderlich.",
        },
        date: {
          required: "Das Datum ist erforderlich.",
        },
        time: {
          required: "Zeit ist erforderlich.",
        },
      },
      errorPlacement: function(error, element) {
        if (element.attr("name") == "doctor") {
          error.insertAfter("#doctor-error");
        } else if (element.attr("name") == "date") {
          error.insertAfter("#date-error");
        } else if (element.attr("name") == "time") {
          error.insertAfter("#time-error");
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
  var clickedDate = '';
  $(document).ready(function() {
    const date = new URLSearchParams(window.location.search).get('date');
    highlightSelectedDate(date);
    fetchData(); // Fetch data on page load
  });

  function getCurrentDate() {
    var todayDate = new Date();
    var todayDateString = todayDate.toISOString().split('T')[0];
    const paramDate = new URLSearchParams(window.location.search);
    return paramDate.has('date') ? String(paramDate.get('date')) : todayDateString;
  }

  function getCurrentPage() {
    const params = new URLSearchParams(window.location.search);
    return params.has('page') ? Number(params.get('page')) : 1;
  }

  function getDate(event) {
    clickedDate = event.target.id;
    if (!event.target.id) return;
    const newDate = formatDate(clickedDate);
    const currentPage = getCurrentPage();

    var formattedDate = new Date(clickedDate).toLocaleDateString('de-DE', {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    });
    $('#appointmentHeading').text(formattedDate);
    // $('#calendarBody .selected-date').removeClass('selected-date');

    updateUrlParam('date', clickedDate);

    $(event.target).addClass('selected-date');

    highlightSelectedDate(newDate);
    fetchData();
  }

  function fetchData() {
    const currentDate = getCurrentDate();
    const date = new URLSearchParams(window.location.search).get('date');
    const currentPage = getCurrentPage();
    const searchQuery = $('#Search-input').val().trim();

    $.ajax({
      url: './ajax/patients.php',
      method: 'GET',
      dataType: "JSON",
      data: {
        date: date,
        page: currentPage,
        searchQuery: searchQuery
      },
      success: function(data) {
        if (data == 'null') {
          updateUrlParam('date', '');
          location.reload(true);
        } else {
          try {
            var getData = data;
            var dataArray = getData.data;

            var listingsPerPage = 5;
            var totalRecord = dataArray.length;
            var AppointmentRecord = getData.statusDoneData.length;
            var totalPage = Math.ceil(totalRecord / listingsPerPage);
            updatePagination(totalPage);
            updatePatientTable(dataArray, listingsPerPage, totalRecord);

            const date = new URLSearchParams(window.location.search).get('date');
            highlightSelectedDate(date);

            updateUrlParam('search', searchQuery);

            $('#totalPatients').text(`${totalRecord}`);
            $('#completeAppointment').text(`${AppointmentRecord}`);
          } catch (error) {
            console.error('Error parsing JSON:', error);
          }
        }
      },
      error: function(xhr, status, error) {
        console.error('Error fetching patient data:', error);
      }
    });
  }

  updateUrlParam('page', getCurrentPage());
  updateUrlParam('date', getCurrentDate());


  function updatePatientTable(dataArray, listingsPerPage, totalRecord) {
    const tbody = $('#patientList');
    tbody.empty();

    if (dataArray.length === 0) {
      const noRecordRow = `<tr><td colspan="5" class="text-center">Keine Einträge gefunden</td></tr>`;
      tbody.append(noRecordRow);
      return;
    }

    var currentPage = getCurrentPage();
    var startIndex = (currentPage - 1) * listingsPerPage;
    var endIndex = Math.min(startIndex + listingsPerPage - 1, totalRecord - 1);

    var displayedListings = dataArray.slice(startIndex, endIndex + 1);


    displayedListings.forEach(function(patient, index) {
      const row = `<tr>
                                      <td>${patient.name}</td>
                                      <td>${patient.doctor}</td>
                                      <td>${patient.services}</td>
                                      <td class="text-center">${patient.visits} </td>
                                      <td>
                                        <div class="d-flex justify-content-center dropdown">
                                            <span class="px-1 cursor-pointer" onclick="openRebookingModal(${patient.id})">
                                                <i class="fa-solid fa-right-left"></i>
                                            </span>
                                            <span class="px-1 cursor-pointer" onclick="cancelRebookingModal(${patient.id})">
                                                <i class="fa-regular fa-circle-xmark" style="margin-left: .5rem;"></i>
                                            </span>
                                        </div>
                                      </td>

                                        <!-- Add other table cells based on your data structure -->
                                    </tr>`;
      tbody.append(row);
    });
  }

  function convertToAMPMFormat(time24) {
    const [hours, minutes] = time24.split(':');
    const parsedHours = parseInt(hours, 10);
    const period = parsedHours >= 12 ? 'PM' : 'AM';
    const formattedHours = parsedHours % 12 || 12; // Convert to 12-hour format

    return `${formattedHours}:${minutes} ${period}`;
  }

  function formatDate(date) {
    const year = new Date().getFullYear();
    const month = new Date().getMonth() + 1;

    const formattedMonth = month < 10 ? `0${month}` : month;
    const formattedDay = date < 10 ? `0${date}` : date;

    return `${year}-${formattedMonth}-${formattedDay}`;
  }

  function openRebookingModal(index) {

    $.ajax({
      url: './ajax/patients.php',
      method: 'POST',
      data: {
        id: index
      },
      success: function(response) {
        var patientsData = JSON.parse(response);

        // Populate data into the edit-patients modal
        $('#PatientsId').val(patientsData.id);
        $('#PatientsName').html(patientsData.name);
        $('#patientsEmail').html(patientsData.email);
        $('#patientsTelephone').html(patientsData.telephone);
        $('#doctorSelect').val(patientsData.doctor);
        $('#time').val(patientsData.visits);

        $('#edit-patients').modal('show');

        openRebookingModal();
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });
    $('#edit-patients').modal('show');
  }

  function updatePagination(totalPage) {
    const params = new URLSearchParams(window.location.search);
    const currentPage = getCurrentPage();
    CreatePagination({
      elementId: "custom-pagination",
      totalPage: totalPage,
      currentPage: currentPage
    });
  }

  function updateUrlParam(key, value) {
    const url = new URL(window.location.href);
    url.searchParams.set(key, value);
    window.history.replaceState({}, '', url);
  }

  function highlightSelectedDate(date) {
    $('#calendarBody .selected-date').removeClass('selected-date');
    $(`#${date}`).addClass('selected-date');
  }

  function editService(clickedElement) {
    var serviceId = clickedElement.id;
    $.ajax({
      url: './ajax/service.php',
      method: 'GET',
      data: {
        serviceId: serviceId
      },
      success: function(response) {
        var serviceData = JSON.parse(response);

        $('#serviceId').val(serviceData.id);
        $('#add').val(serviceData.services);
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });
  }

  var serviceId = "";

  function deleteService(clickedElement) {
    $('#show-services').modal('hide');
    $('#deleteServiceConfirmation').modal('show');
    serviceId = clickedElement.id;
  }

  $('#deleteServiceYesBtn').on('click', function() {
    $('#deleteServiceConfirmation').modal('hide');
    $('#service-delete-show-info').modal('show');
    $.ajax({
      url: './ajax/service.php',
      method: 'POST',
      data: {
        deleteServiceId: serviceId
      },
      success: function(response) {

      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });
  });

  $('#serviceOkBtn').on('click', function() {
    location.reload(true);
  })

  function selectDoctor(clickedElement) {
    var doctorId = clickedElement.id;

    $.ajax({
      url: './ajax/staff.php',
      method: 'GET',
      data: {
        doctorId: doctorId
      },
      success: function(response) {
        var timeArrayWrapper = JSON.parse(response);

        var timeArray = JSON.parse(timeArrayWrapper.time);
        $('#timeList').empty();
        timeArray.forEach(function(time) {
          $('#timeList').append('<option>' + time + '</option>');
        });
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });
  }

  function cancelRebookingModal(index) {
    // Show the confirmation modal
    $('#Confirmation').modal('show');

    // When the confirmation "Ja" button is clicked
    $('#ConfirmationYesBtn').click(function() {
      // Make AJAX request to cancel the rebooking
      $.ajax({
        url: './ajax/rebookingpatients.php',
        method: 'GET',
        data: {
          patientId: index
        },
        success: function(response) {
          location.reload(true); // Reload the page upon success
        },
        error: function(xhr, status, error) {
          console.error('Error:', error);
        }
      });
    });

    // When the confirmation "Nein" button is clicked, close the modal
    $('.cancel-button').click(function() {
      $('#Confirmation').modal('hide');
    });
  }
  </script>
  <script>
  $(document).ready(function() {
    $('#Services-Options').on('change', function() {
      var selectedOptions = $(this).val();
      $('#Services-input').val(selectedOptions ? selectedOptions.join(', ') : '');
    });
  });

  var doctorId = '';
  $("#doctorSelect").change(function() {
    doctorId = $(this).children("option:selected").attr("id");
    $('#timeList').empty();
  })
  $('#datepicker').on('change', function() {
    var selectedDate = $(this).val();
    doctorId = $('#doctorSelect').val();

    $.ajax({
      url: './ajax/datetimelist.php',
      method: 'GET',
      data: {
        doctorId: doctorId,
        selectedDate: selectedDate,
      },
      success: function(response) {

        if (!response) {
          // alert('This date time not available')
          $('#timeList').empty();
        } else {
          try {
            var timeArrayWrapper = JSON.parse(response);

            // Check if timeArrayWrapper is not null and has the 'time' property
            $('#timeList').empty();
            if (timeArrayWrapper !== null && 'time' in timeArrayWrapper) {
              var timeArray = JSON.parse(timeArrayWrapper.time);


              timeArray.forEach(function(time) {
                $('#timeList').append('<option>' + time + '</option>');
              });
            }

          } catch (error) {
            console.error('Error parsing JSON:', error);
            // Handle the error if parsing fails
          }
        }
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });

  })
  </script>
  <script>
  function redirectToPatientPage() {
    window.location.href = "patients";
  }
  </script>
  <script>
  document.getElementById("open-image-picker").addEventListener('click', () => {
    document.getElementById('profile-image').click()
  });

  function redirectToEmployeePage() {
    window.location.href = "login";
  }
  </script>
  <script>
  document.getElementById('profile-image').addEventListener('change', function(event) {
    var file = event.target.files[0];
    if (file) {
      var reader = new FileReader();
      reader.onload = function(e) {
        var image = document.getElementById('image-preview');
        image.src = e.target.result;
        // Set max height and width
        image.style.maxHeight = '100px';
        image.style.maxWidth = '100px';
      };
      reader.readAsDataURL(file);
    }
  });
  </script>
  <?php } else { ?>
  <script src="asset/js/script2.js"></script>
  <script>
  $(document).ready(function() {

    $('#EditPatients').validate({
      rules: {
        date: {
          required: true,
        },
        time: {
          required: true,
        },
      },
      messages: {

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
        } else if (element.attr("name") == "date") {
          error.insertAfter("#date-error");
        } else if (element.attr("name") == "time") {
          error.insertAfter("#time-error");
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
  var clickedDate = '';
  $(document).ready(function() {
    const date = new URLSearchParams(window.location.search).get('date');
    highlightSelectedDate(date);
    fetchData();
  });

  function getCurrentDate() {
    var todayDate = new Date();
    var todayDateString = todayDate.toISOString().split('T')[0];
    // var selectedDate = clickedDate ? clickedDate : todayDateString;
    const paramDate = new URLSearchParams(window.location.search);
    return paramDate.has('date') ? String(paramDate.get('date')) : todayDateString;
  }

  function getCurrentPage() {
    const params = new URLSearchParams(window.location.search);
    return params.has('page') ? Number(params.get('page')) : 1;
  }

  function getDate(event) {
    clickedDate = event.target.id;
    if (!event.target.id) return;
    const newDate = formatDate(clickedDate);
    const currentPage = getCurrentPage();

    var formattedDate = new Date(clickedDate).toLocaleDateString('de-DE', {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    });
    $('#appointmentHeading').text(formattedDate);
    // $('#calendarBody .selected-date').removeClass('selected-date');

    updateUrlParam('date', clickedDate);
    // updateUrlParam('page', currentPage);

    $(event.target).addClass('selected-date');

    highlightSelectedDate(newDate);
    fetchData();
  }
  // const currentPage = getCurrentPage();
  // var todayDate = new Date();
  // var todayDateString = todayDate.toISOString().split('T')[0];
  // updateUrlParam('page', currentPage);
  // updateUrlParam('date', todayDateString);

  function fetchData() {
    const currentDate = getCurrentDate();
    const date = new URLSearchParams(window.location.search).get('date');
    const currentPage = getCurrentPage();
    const searchQuery = $('#Search-input').val().trim();

    $.ajax({
      url: './ajax/patients.php',
      method: 'GET',
      data: {
        date: date,
        page: currentPage,
        searchQuery: searchQuery
      },
      success: function(data) {
        if (data == 'null') {
          updateUrlParam('date', '');
          location.reload(true);
        } else {

          try {
            var getData = JSON.parse(data);
            var dataArray = getData.data;

            var listingsPerPage = 5;
            var totalRecord = dataArray.length;
            // var AppointmentRecord = getData.statusDoneData.length;
            var totalPage = Math.ceil(totalRecord / listingsPerPage);
            updatePagination(totalPage);
            updatePatientTable(dataArray, listingsPerPage, totalRecord);

            // // Retrieve the date from the URL and highlight it in the calendar
            const date = new URLSearchParams(window.location.search).get('date');
            highlightSelectedDate(date);

            updateUrlParam('search', searchQuery);
            // updateUrlParam('page', getCurrentPage()); 
            // updateUrlParam('date', getCurrentDate()); 

            $('#totalPatients').text(`${totalRecord}`);
            // $('#completeAppointment').text(`${AppointmentRecord}`);
          } catch (error) {
            console.error('Error parsing JSON:', error);
          }
        }
      },
      error: function(xhr, status, error) {
        console.error('Error fetching patient data:', error);
      }
    });
  }
  updateUrlParam('page', getCurrentPage());
  updateUrlParam('date', getCurrentDate());

  function updatePatientTable(dataArray, listingsPerPage, totalRecord) {
    const tbody = $('#patientList');
    tbody.empty();

    if (dataArray.length === 0) {
      const noRecordRow = `<tr><td colspan="5" class="text-center">No records found</td></tr>`;
      tbody.append(noRecordRow);
      return;
    }

    var currentPage = getCurrentPage();
    var startIndex = (currentPage - 1) * listingsPerPage;
    var endIndex = Math.min(startIndex + listingsPerPage - 1, totalRecord - 1);

    var displayedListings = dataArray.slice(startIndex, endIndex + 1);


    displayedListings.forEach(function(patient, index) {
      const formattedVisitTime = patient.visits ?
        convertToAMPMFormat(patient.visits) :
        'N/A';
      const row = `<tr>
        <td>${patient.name}</td>
        <td>${patient.doctor}</td>
        <td>${patient.services}</td>
        <td class="text-center">${patient.visits} </td>
        <td>
        <div class="d-flex justify-content-center dropdown">
            <span class="px-1 cursor-pointer" onclick="openRebookingModal(${patient.id})">
                <i class="fa-solid fa-right-left"></i>
            </span>
            <span class="px-1 cursor-pointer" onclick="cancelRebookingModal(${patient.id})">
                <i class="fa-regular fa-circle-xmark" style="margin-left: .5rem;"></i>
            </span>
        </div>
      </td>
        <!-- Add other table cells based on your data structure -->
      </tr>`;
      tbody.append(row);
    });
  }

  function convertToAMPMFormat(time24) {
    const [hours, minutes] = time24.split(':');
    const parsedHours = parseInt(hours, 10);
    const period = parsedHours >= 12 ? 'PM' : 'AM';
    const formattedHours = parsedHours % 12 || 12; // Convert to 12-hour format

    return `${formattedHours}:${minutes} ${period}`;
  }

  function formatDate(date) {
    const year = new Date().getFullYear();
    const month = new Date().getMonth() + 1;

    const formattedMonth = month < 10 ? `0${month}` : month;
    const formattedDay = date < 10 ? `0${date}` : date;

    return `${year}-${formattedMonth}-${formattedDay}`;
  }

  function openRebookingModal(index) {
    console.log(index);
    $.ajax({
      url: './ajax/patients.php',
      method: 'POST',
      data: {
        id: index
      },
      success: function(response) {
        var patientsData = JSON.parse(response);
        $('#PatientsId').val(patientsData.id);
        $('#PatientsName').html(patientsData.name);
        $('#patientsEmail').html(patientsData.email);
        $('#patientsTelephone').html(patientsData.telephone);
        $('#doctor-input').val(patientsData.doctor);
        $('#time').val(patientsData.visits);

        $('#edit-patients').modal('show');

        openRebookingModal();
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });
    $('#edit-patients').modal('show');
  }

  function updatePagination(totalPage) {
    const params = new URLSearchParams(window.location.search);
    const currentPage = getCurrentPage();
    CreatePagination({
      elementId: "custom-pagination",
      totalPage: totalPage,
      currentPage: currentPage
    });
  }

  function updateUrlParam(key, value) {
    const url = new URL(window.location.href);
    url.searchParams.set(key, value);
    window.history.replaceState({}, '', url);
  }

  function highlightSelectedDate(date) {
    $('#calendarBody .selected-date').removeClass('selected-date');
    $(`#${date}`).addClass('selected-date');
  }

  $('#UpdatePatients').on('click', function() {
    if ($('#EditPatients').valid()) {
      $('#edit-patients').modal('hide');
      $('#EditConfirmation').modal('show');
    }
  })
  $('#EditConfirmationYesBtn').on('click', function() {
    $('#EditConfirmation').modal('hide');
    $('#edit-show-info').modal('show');
  })

  function getDoctorData(selectedOption) {
    // var doctorId = "";
    doctorId = selectedOption.value;
    var selectedText = selectedOption.innerText;
    var selectedOption = document.getElementById("doctor-Options");
    document.getElementById("doctor-input").value = selectedText;
    document.getElementById("doctorId-input").value = doctorId;

  }

  function selectDoctor(clickedElement) {
    var doctorId = clickedElement.id;

    $.ajax({
      url: './ajax/staff.php',
      method: 'GET',
      data: {
        doctorId: doctorId
      },
      success: function(response) {
        var timeArrayWrapper = JSON.parse(response);

        var timeArray = JSON.parse(timeArrayWrapper.time);
        $('#timeList').empty();
        timeArray.forEach(function(time) {
          $('#timeList').append('<option>' + time + '</option>');
        });
        // Log the length of the timeArray
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });
  }

  $('#datepicker').on('change', function() {
    var selectedDate = $(this).val();
    $.ajax({
      url: './ajax/datetimelist.php',
      method: 'GET',
      data: {
        selectedDate: selectedDate,
      },
      success: function(response) {

        if (!response) {
          alert('This date time not available')
          $('#timeList').empty();
        } else {
          try {
            var timeArrayWrapper = JSON.parse(response);

            $('#timeList').empty();
            if (timeArrayWrapper !== null && 'time' in timeArrayWrapper) {
              var timeArray = JSON.parse(timeArrayWrapper.time);


              timeArray.forEach(function(time) {
                $('#timeList').append('<option>' + time + '</option>');
              });
            }

          } catch (error) {
            console.error('Error parsing JSON:', error);
          }
        }
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });

  })

  function cancelRebookingModal(index) {
    // Show the confirmation modal
    $('#Confirmation').modal('show');

    // When the confirmation "Ja" button is clicked
    $('#ConfirmationYesBtn').click(function() {
      // Make AJAX request to cancel the rebooking
      $.ajax({
        url: './ajax/rebookingpatients.php',
        method: 'GET',
        data: {
          patientId: index
        },
        success: function(response) {
          location.reload(true); // Reload the page upon success
        },
        error: function(xhr, status, error) {
          console.error('Error:', error);
        }
      });
    });

    // When the confirmation "Nein" button is clicked, close the modal
    $('.cancel-button').click(function() {
      $('#Confirmation').modal('hide');
    });
  }
  </script>
  <?php } ?>
  <?php include ('layout/script.php') ?>
  <?php
  if (isset($_SESSION['success_message'])) {
    echo "<script>showToast('" . $_SESSION['success_message'] . "', 'success' );</script>";
    unset($_SESSION['success_message']);
    }
  ?>
  </body>

  </html>