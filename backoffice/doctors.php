<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
  header("Location: login");
  }
include ('config/database.php');
include ('./layout/header.php');
include ('./layout/sidebar.php');

// get services 
$GetServices = "select * from services where deleted_at IS NULL";
$ServiceResult = $connect->query($GetServices);
$servicesArray = array();

if ($ServiceResult->num_rows > 0) {
  while ($row = $ServiceResult->fetch_assoc()) {
    $servicesArray[] = $row;
    }
  }


$searchTerm = isset($_GET['search']) ? $_GET['search'] : null;
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;

$conditions = array();
if ($searchTerm !== null) {
  $sql = "SELECT sd.user_id FROM services_docs AS sd LEFT JOIN services AS s ON sd.service_id = s.id WHERE s.services LIKE '%$searchTerm%'";

  $result = $connect->query($sql);
  $userIds = [];
  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $userIds[] = "'{$row["user_id"]}'";
      }
    }
    
  if (count($userIds) > 0) {
    $userIds = implode(",", $userIds);
    $conditions[] = "(user.name LIKE '%$searchTerm%' OR user.email LIKE '%$searchTerm%' OR user.telephone LIKE '%$searchTerm%' OR user.id IN ($userIds))";
    }
  } else {
  $conditions[] = "(user.name LIKE '%$searchTerm%' OR user.email LIKE '%$searchTerm%')";
  }
if ($startDate !== null && $endDate !== null) {
  $conditions[] = "user.created_at BETWEEN '$startDate' AND '$endDate'";
  }

// Construct the WHERE clause
$whereClause = "";
if (!empty($conditions)) {
  $whereClause = "WHERE (user.status = '1' OR user.status = '0') AND role = 2 AND " . implode(" AND ", $conditions);
  } else {
  $whereClause = "WHERE (user.status = '1' OR user.status = '0') AND role = 2";
  }


$orderBy = isset($_GET['orderby']) ? $_GET['orderby'] : null;
if ($orderBy !== null && in_array($orderBy, ['asc', 'desc'])) {

  $column = isset($_GET['column']) ? $_GET['column'] : null;
  if ($column == 'doctor') {
    $orderClause = "user.id $orderBy";
    } else {
    $orderClause = "user.id $orderBy";
    }

  } else {
  $orderClause = "user.id ASC";
  }

$GetStaff = "SELECT user.*, COUNT(CASE WHEN patients.status = 'durchgeführt' THEN patients.id ELSE NULL END) AS patient_count
FROM user
LEFT JOIN patients ON user.id = patients.doctor
$whereClause
GROUP BY user.id ORDER BY $orderClause";
// Execute the SQL query
$StaffResult = $connect->query($GetStaff);
$staffList = array();

if ($StaffResult && $StaffResult->num_rows > 0) {
  while ($row = $StaffResult->fetch_assoc()) {
    $getService = "SELECT sd.user_id, s.services FROM services_docs AS sd LEFT JOIN services AS s ON sd.service_id = s.id WHERE sd.user_id='" . $row['id'] . "'";
    $services = [];
    $sericesResult = $connect->query($getService);
    if ($sericesResult->num_rows > 0) {
      while ($row1 = $sericesResult->fetch_assoc()) {
        $services[] = $row1['services'];
        }
      $row['serviceList'] = $services;
      }
    $staffList[] = $row;
    }
  }

// Pagination
$itemsPerPage = 20;
$totalItems = count($staffList);
$totalPages = ceil($totalItems / $itemsPerPage);
$currentPage = isset($_GET['page']) ? max(1, min((int) $_GET['page'], $totalPages)) : 1;
$startIndex = ($currentPage - 1) * $itemsPerPage;
$endIndex = min($startIndex + $itemsPerPage - 1, $totalItems - 1);
// }

?>
<!-- Main -->
<div id="main-content">
  <div class="p-2 w-100">
    <div class="d-flex justify-content-center align-items-center">
      <h1 class="page-heading">Doktoren</h1>
    </div>
    <div class="px-2">

      <div class="d-flex flex-wrap">
        <!-- <div class="dashboard-search m-2 mx-0">
      <i class="bi bi-search"></i>
      <input type="text" class="w-100" id="Search-input"
      placeholder="Search" oninput="search()">
      </div> -->
        <!-- <form method="get"> -->
        <div class="dashboard-search my-auto">
          <i class="bi bi-search"></i>
          <input type="text" class="w-100" id="Search-input" placeholder="Suche" name="search"
            value="<?php echo $searchTerm ?>">
            <div class="" id="searchCancel" type="button">
              <i class="bi bi-x-square cursor-pointer"></i>
            </div>
        </div>
        <!-- </form> -->
        <div class="flex-grow-1"></div>

        <?php if ($role == 1) { ?>
                <button type="submit" class="cursor-pointer custom-secondary-button my-auto" data-bs-toggle="modal"
                  data-bs-target="#add-staff"><i class="bi bi-plus" style="color: white; "></i>Doktor</button>
        <?php } ?>
      </div>

      <div class="mt-4 custom-table" id="Search-Options" onchange="handleSelect('Search-input')">
        <div class=" table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <?php if ($role == 1) { ?>
                        <td>#</td>
                        <td>Arzt</td>
                        <td class="text-center">Leistung</td>
                        <td class="text-center">Status<i data-value="status"
                            class="fa-solid fa-arrow-up-arrow-down Shorting ms-1"
                            style="font-size: 14px;display: inline-block;"></i> </td>
                        <td>
                          <div class="d-flex justify-content-center">Optionen</div>
                        </td>
                <?php } else { ?>
                        <td>#</td>
                        <td>Arzt</td>
                        <td class="text-center">Leistung</td>
                        <td class="text-center">Status </td>
                <?php } ?>
              </tr>
            </thead>
            <tbody>
              <?php if ($role == 1) {
                for ($i = $startIndex; $i <= $endIndex; $i++) {
                  ; ?>
                              <tr class="doctor-row">
                                <td style="max-width: 100px;"><?php echo $i + 1; ?></td>
                                <td style="min-width: 150px; max-width: 250px;">
                                  <div class="d-flex p-0 m-0 flex-column">
                                    <h5 class="mb-0"><?php echo $staffList[$i]['name']; ?></h5>
                                    <p class="mb-0" style="color: var(--main); font-size: var(--sm-text);"><span
                                        style="font-weight: 500;">E:
                                      </span><a style="color: var(--main);"
                                        href="mailto:<?php echo $staffList[$i]['email']; ?>"><?php echo $staffList[$i]['email']; ?></a>
                                    </p>
                                    <?php if (!empty($staffList[$i]['telephone'])): ?>
                                            <p class="mb-0" style="color: var(--main); font-size: var(--sm-text);">
                                              <span style="color: var(--main); font-weight: 500;">T:</span>
                                              <a style="color: var(--main);" href="tel:<?php echo $staffList[$i]['telephone']; ?>">
                                                <?php echo $staffList[$i]['telephone']; ?>
                                              </a>
                                            </p>
                                    <?php endif; ?>

                                  </div>
                                </td>
                                <td class="">
                                  <div class="text-start mx-auto">
                                    <ul>
                                      <?php
                                      $serviceList = isset($staffList[$i]['serviceList']) ? $staffList[$i]['serviceList'] : [];
                                      foreach ($serviceList as $service) {
                                        ?>
                                              <li class="text-left"><?= $service ?></li>
                                      <?php } ?>
                                    </ul>
                                  </div>
                                </td>
                                <!-- <td  class="created-at"><?php //echo $staffList[$i]['created_at']; ?></td> -->
                                <!-- <td  class="created-at"><?php echo date('d.m.Y', strtotime($staffList[$i]['created_at'])); ?></td> -->

                                <!-- <td class="cursor-pointer text-center patientList" data-id="<?php echo $staffList[$i]['id']; ?>" id="treated"><?php echo $staffList[$i]['patient_count']; ?></td> -->
                                <!-- <td class="text-center"><?php //echo $staffList[$i]['time']; ?></td> -->
                                <!-- <td class="text-center">
              <?php
              if (!empty($staffList[$i]['time']) && $staffList[$i]['time'] != 'null') {
                $timesArray = $staffList[$i]['time'] ? json_decode($staffList[$i]['time'], true) : [];
                echo implode(', ', $timesArray);
                } else {
                echo 'No';
                }
              ?>
              <button class="cursor-pointer showTimeBtn"
              style="background-color: var(--main); color: white;"
              data-id="<?php echo $staffList[$i]['id']; ?>">Alle anzeigen</button>
              </td> -->
                                <?php
                                if ($staffList[$i]['status'] === '1') {
                                  $buttonClass = 'custom-success-btn';
                                  $buttonText = 'Aktiv';
                                  } elseif ($staffList[$i]['status'] === '0') {
                                  $buttonClass = 'custom-warnings-btn';
                                  $buttonText = 'Deaktiviert';
                                  } else {
                                  $buttonClass = 'custom-danger-btn';
                                  $buttonText = 'Deleted';
                                  }
                                ?>
                                <td class="text-center"><button
                                    class="cursor-default <?php echo $buttonClass; ?>"><?php echo $buttonText; ?></button>
                                </td>
                                <td>
                                  <div class="d-flex justify-content-center">
                                    <?php if ($staffList[$i]['status'] === '1' || $staffList[$i]['status'] === '0') { ?>
                                            <!-- Edit button -->
                                            <div class="editStaffButton" data-id="<?php echo $staffList[$i]['id']; ?>" data-bs-toggle="modal">
                                              <i class="fas fa-edit cursor-pointer p-2"></i>
                                            </div>
                                            <!-- Delete button -->
                                            <form method="post" action="./controller/deletestaff.php">
                                              <input type="hidden" name="id" value="<?= $staffList[$i]['id'] ?>">
                                              <button type="button" class="iconButton deleteStaffButton"
                                                data-id="<?php echo $staffList[$i]['id']; ?>">
                                                <i class="fas fa-trash cursor-pointer text-danger p-2"></i>
                                              </button>
                                      <?php } ?>
                                    </form>
                                  </div>
                                </td>
                              </tr>
                      <?php }
                } else {
                for ($i = $startIndex; $i <= $endIndex; $i++) { ?>
                              <tr class="doctor-row">
                                <td style="max-width: 100px;"><?php echo $i + 1; ?></td>
                                <td style="min-width: 150px; max-width: 250px;">
                                  <div class="d-flex p-0 m-0 flex-column">
                                    <h5 class="mb-0"><?php echo $staffList[$i]['name']; ?></h5>
                                    <p class="mb-0" style="color: var(--main); font-size: var(--sm-text);"><span
                                        style="font-weight: 500;">E:
                                      </span><a style="color: var(--main);"
                                        href="mailto:<?php echo $staffList[$i]['email']; ?>"><?php echo $staffList[$i]['email']; ?></a>
                                    </p>
                                    <p class="mb-0" style="color: var(--main); font-size: var(--sm-text);"><span
                                        style="color: var(--main);font-weight: 500;">T:
                                      </span><a style="color: var(--main);"
                                        href="tel:<?php echo $staffList[$i]['telephone']; ?>"><?php echo $staffList[$i]['telephone']; ?></a>
                                    </p>
                                  </div>
                                </td>
                                <td class="text-center">
                                  <button class="cursor-pointer showAllBtn" style="background-color: var(--main); color: white;"
                                    data-id="<?php echo $staffList[$i]['id']; ?>">Alle anzeigen</button>
                                </td>
                                <!-- <td  class="created-at"><?php //echo $staffList[$i]['created_at']; ?></td> -->
                                <!-- <td  class="created-at"><?php echo date('d.m.Y', strtotime($staffList[$i]['created_at'])); ?></td> -->

                                <!-- <td class="cursor-pointer text-center patientList" data-id="<?php echo $staffList[$i]['id']; ?>" id="treated"><?php echo $staffList[$i]['patient_count']; ?></td> -->
                                <!-- <td class="text-center"><?php //echo $staffList[$i]['time']; ?></td> -->
                                <!-- <td class="text-center">
              <?php
              if (!empty($staffList[$i]['time']) && $staffList[$i]['time'] != 'null') {
                $timesArray = $staffList[$i]['time'] ? json_decode($staffList[$i]['time'], true) : [];
                echo implode(', ', $timesArray);
                } else {
                echo 'No';
                }
              ?> 
              <button class="cursor-pointer showTimeBtn"
              style="background-color: var(--main); color: white;"
              data-id="<?php echo $staffList[$i]['id']; ?>">Alle anzeigen</button>
              </td> -->
                                <?php
                                if ($staffList[$i]['status'] === '1') {
                                  $buttonClass = 'custom-success-btn';
                                  $buttonText = 'Aktiv';
                                  } elseif ($staffList[$i]['status'] === '0') {
                                  $buttonClass = 'custom-warnings-btn';
                                  $buttonText = 'Deaktiviert';
                                  } else {
                                  $buttonClass = 'custom-danger-btn';
                                  $buttonText = 'Deleted';
                                  }
                                ?>
                                <td class="text-center"><button
                                    class="cursor-default <?php echo $buttonClass; ?>"><?php echo $buttonText; ?></button>
                                </td>
                                <!-- <td>
              <div class="d-flex justify-content-center dropdown">
              <?php if ($staffList[$i]['status'] === '1' || $staffList[$i]['status'] === '0') {
                ; ?>
              <span onclick="HandleDropMenu('Drop-menu-<?php echo $i + 1; ?>')"
              style="border-radius: 50%;border: 1px solid var(--secondary);color: var(--secondary);"
              class="px-1 cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-list"></i>
              </span>
              <ul id="Drop-menu-<?php echo $i + 1; ?>" class="dropdown-content">
              <li class="px-2 py-1 mx-2  cursor-pointer editButton" data-id="<?php echo $staffList[$i]['id']; ?>"    style="border-bottom: 1px solid #d7caca;" data-bs-toggle="modal">
              Bearbeiten
              </li>
              <li class="px-2 py-1 mx-2 cursor-pointer deleteButton" data-id="<?php echo $staffList[$i]['id']; ?>"    style="color: var(--main);" data-bs-toggle="modal">
              Löschen
              </li>

              </ul>
              <?php } else { ?>
              <label style="color: green;">Inaktiv</label>
              <?php } ?>
              </div>
              </td> -->
                              </tr>
                      <?php }
                } ?>
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

<!-- Add Staff -->
<form id="AddStaff" method="post" action="./controller/addstaff.php" enctype="multipart/form-data">
  <div class="modal fade" id="add-staff" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
        <div class="d-flex justify-content-center align-items-center py-2">
          <div class="flex-grow-1"></div>
          <h1 class="modal-heading" style="font-weight: 800; margin-left:72px">Doktor hinzufügen</h1>
          <div class="flex-grow-1"></div>
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" style="width:50px;"
            aria-label="Close"></button>
        </div>

        <div class="row">
          <div class="col-lg-6 col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="Name">Name</label>
              <input autofocus type="text" name="name" class="form-control custom-input" id="Name" placeholder="Name">
              <span class="error" id="name-error"></span>
            </div>
          </div>
          <div class="col-lg-6 col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="email">E-Mail</label>
              <input type="email" name="email" class="form-control custom-input" id="email" placeholder="E-Mail">
              <span class="error" id="email-error"></span>
            </div>
          </div>
          <div class="col-lg-6 col-12">
            <div class="form-group p-2 mt-2">
              <label class="my-1" for="password" style="font-family: Cambridge-Round-Regular;">Passwort</label>
              <div class="d-flex align-items-center input-with-icon hideinputFocus"
                style="background-color: var(--input-bg); border-radius: 4px;">
                <input type="password" class="form-control custom-input" name="password" id="password"
                  placeholder="Passwort eingeben">
                <i class="bi bi-eye-fill mx-2 mr-4 cursor-pointer" id="toggle-password-icon"
                  onclick="togglePasswordVisibility()"></i>
              </div>
              <script>
              function togglePasswordVisibility() {
                const passwordInput = document.getElementById('password');
                const eyeIcon = document.getElementById('toggle-password-icon');

                // Toggle password visibility
                if (passwordInput.type === 'password') {
                  passwordInput.type = 'text';
                  // Change icon to eye-slash when password is visible
                  eyeIcon.classList.remove('bi-eye-fill');
                  eyeIcon.classList.add('bi-eye-slash-fill');
                } else {
                  passwordInput.type = 'password';
                  // Change icon back to eye-fill when password is hidden
                  eyeIcon.classList.remove('bi-eye-slash-fill');
                  eyeIcon.classList.add('bi-eye-fill');
                }
              }
              </script>
              <p class="error mb-0" id="password-error"></p>
            </div>
          </div>
          <div class="col-lg-6 col-12">
            <div class="form-group p-2 mt-2">
              <label class="my-1" for="confirm_password" style="font-family: Cambridge-Round-Regular;">Passwort
                bestätigen</label>
              <div class="d-flex align-items-center input-with-icon hideinputFocus"
                style="background-color: var(--input-bg); border-radius: 4px;">
                <input type="password" class="form-control custom-input" name="confirm_password" id="confirm_password"
                  placeholder="Passwort bestätigen">
                <i class="bi bi-eye-fill mx-2 mr-4 cursor-pointer" id="toggle-confirm-password-icon"
                  onclick="toggleConfirmPasswordVisibility()"></i>
              </div>
              <script>
              function toggleConfirmPasswordVisibility() {
                const confirmPasswordInput = document.getElementById('confirm_password');
                const eyeIcon = document.getElementById('toggle-confirm-password-icon');

                // Toggle password visibility
                if (confirmPasswordInput.type === 'password') {
                  confirmPasswordInput.type = 'text';
                  // Change icon to eye-slash when password is visible
                  eyeIcon.classList.remove('bi-eye-fill');
                  eyeIcon.classList.add('bi-eye-slash-fill');
                } else {
                  confirmPasswordInput.type = 'password';
                  // Change icon back to eye-fill when password is hidden
                  eyeIcon.classList.remove('bi-eye-slash-fill');
                  eyeIcon.classList.add('bi-eye-fill');
                }
              }
              </script>
              <p class="error mb-0" id="confirm_password-error"></p>
            </div>
          </div>

          <div class="col-lg-6 col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="Status">Status</label>
              <select name="status" class="cursor-pointer form-select custom-input" id="Status">
                <option selected>Aktiv</option>
                <option>Deaktiviert</option>
              </select>
              <span class="error" id="status-error"></span>
            </div>
          </div>
          <div class="col-lg-6 col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="Telephone">Telefon</label>
              <input type="text" name="telephone" class="form-control custom-input" id="Telephone"
                placeholder="Telefon">
              <span class="error" id="telephone-error"></span>
            </div>
          </div>
          <div class="col-lg-6 col-12">
            <div class="form-group p-2 select-group my-2" id="select-group">
              <label class="my-1" for="Services">Leistung</label>
              <div style="height: 150px; overflow-x: hidden; overflow-y:scroll;">
                <?php
                foreach ($servicesArray as $service) {
                  echo '<div class="form-check">';
                  echo '<input class="form-check-input" type="checkbox" name="staff_services[]" id="' . $service['services'] . '" value="' . $service['id'] . '">';
                  echo '<label class="form-check-label" for="' . $service['services'] . '">' . $service['services'] . '</label>';
                  echo '</div>';
                  }
                ?>
              </div>
              <span class="error" id="staff_services-error"></span>
            </div>
          </div>
          <div class="col-lg-6 col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="Status">Profil</label>
              <input type="file" name="profile" class="form-control custom-input d-none" id="profile-image"
                aria-invalid="false" accept="image/*">
              <button class="custom-main-btn my-2 w-100 d-block" type="button" id="open-image-picker"><i
                  class="bi bi-upload me-2"></i>Bild hochladen</button>
              <div class="text-center"><img id="image-preview" class="user-select-none" width="100px"></div>
              <span class="error" id="status-error"></span>
            </div>
          </div>
          <!-- <div class="col-lg-6 col-12 ">
        </div> -->
        </div>
        <div class="d-flex justify-content-center align-items-center py-2 my-3">
          <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal"
            style="margin-right: 5px;">Abbrechen</button>
          <button type="submit" id="addStaffBtn" class="success-button cursor-pointer"
            style="margin-left: 5px;">Hinzufügen</button>
        </div>
      </div>
    </div>
  </div>
  <!-- staff Confirmation -->
  <div class="modal fade " id="StaffConfirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
        <div class="d-flex justify-content-center align-items-center flex-column">
          <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
          <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
        </div>
        <div class="d-flex justify-content-center align-items-center">
          <button type="button" class="cancel-button cursor-pointer mx-1" data-bs-dismiss="modal"
            style="margin-right: 3px;">Nein</button>
          <button type="button" id="StaffConfirmationYesBtn" class="success-button cursor-pointer mx-1"
            style="margin-left: 3px;">Ja</button>
        </div>
      </div>
    </div>
  </div>
  <!-- staff show info  -->
  <div class="modal fade " id="staffShowInfo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
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

<!-- Services list model  -->
<div class="modal fade " id="services-list" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered " role="document">
    <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
      <div class="d-flex justify-content-center align-items-center mb-4">
        <h1 class="modal-heading" style="font-weight: 800;">Leistungen</h1>
      </div>
      <ul style="color: black;font-size: var(--md-text);font-weight:500;" id="showAll">
      </ul>
      <div class="d-flex justify-content-center align-items-center">
        <button type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal">Okay</button>
      </div>
    </div>
  </div>
</div>

<!-- time list model  -->

<div class="modal fade " id="time-list" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered " role="document">
    <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
      <div class="d-flex justify-content-center align-items-center mb-4">
        <h1 class="modal-heading" style="font-weight: 800;">Zeitliste</h1>
      </div>
      <ul style="color: black;font-size: var(--md-text);font-weight:500;" id="timeList">
      </ul>
      <div class="d-flex justify-content-center align-items-center">
        <button type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal">Okay</button>
      </div>
    </div>
  </div>
</div>
<!-- <div class="modal fade" id="services-list" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
<div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
<div class="d-flex justify-content-center align-items-center mb-4">
<h1 class="modal-heading" style="font-weight: 800;">Service List</h1>
</div>
<ul style="color: black; font-size: var(--md-text); font-weight: 500;" id="showAll"></ul>
<div class="d-flex justify-content-center align-items-center">
<button type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal">Okay</button>
</div>
</div>
</div> -->
</div>
<!-- patients list model  -->
<div class="modal fade " id="patient-list" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered " role="document">
    <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
      <div class="d-flex justify-content-center align-items-center mb-4">
        <h1 class="modal-heading" style="font-weight: 800;">Patientenliste</h1>
      </div>
      <ul style="color: black;font-size: var(--md-text);font-weight:500;" id="patientList">
        <!-- <li>
      <div class="row">
      <div class="col-6">Jhon Deo</div>
      <div class="col-6"><span>Visited on: </span>21.11.2023
      </div>
      </div>
      </li>
      <li>
      <div class="row">
      <div class="col-6">Jhon Deo</div>
      <div class="col-6"><span>Visited on: </span>21.11.2023
      </div>
      </div>
      </li>
      <li>
      <div class="row">
      <div class="col-6">Jhon Deo</div>
      <div class="col-6"><span>Visited on: </span>21.11.2023
      </div>
      </div>
      </li>
      <li>
      <div class="row">
      <div class="col-6">Jhon Deo</div>
      <div class="col-6"><span>Visited on: </span>21.11.2023
      </div>
      </div>
      </li>
      <li>
      <div class="row">
      <div class="col-6">Jhon Deo</div>
      <div class="col-6"><span>Visited on: </span>21.11.2023
      </div>
      </div>
      </li>
      <li>
      <div class="row">
      <div class="col-6">Jhon Deo</div>
      <div class="col-6"><span>Visited on: </span>21.11.2023
      </div>
      </div>
      </li>
      <li>
      <div class="row">
      <div class="col-6">Jhon Deo</div>
      <div class="col-6"><span>Visited on: </span>21.11.2023
      </div>
      </div>
      </li> -->
      </ul>
      <div class="d-flex justify-content-center align-items-center">
        <button type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal">Okay</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Staff -->
<form id="EditStaff" action="./controller/editstaff.php" method="post" enctype="multipart/form-data">
  <div class="modal fade " id="edit-staff" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 16px; width: 800px;">
        <div class="d-flex justify-content-center align-items-center py-2">
          <div class="flex-grow-1"></div>
          <h1 class="modal-heading" style="font-weight: 800;">Doktor bearbeiten</h1>
          <div class="flex-grow-1"></div>
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" style="width:50px;"
            aria-label="Close"></button>
        </div>

        <!-- <form> -->

        <div class="row">
          <input type="hidden" name="id" value="" class="form-control custom-input" id="StaffId">
          <div class="col-lg-6 col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="Name">Name</label>
              <input type="text" name="name" value="" class="form-control custom-input" id="StaffName"
                placeholder="Name">
              <span class="error" id="name-edit-error"></span>
            </div>
          </div>
          <div class="col-lg-6 col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="email">E-Mail</label>
              <input type="email" name="email" value="" class="form-control custom-input" id="StaffEmail"
                placeholder="E-Mail">
              <span class="error" id="email-edit-error"></span>
            </div>
          </div>

          <?php
          // Assuming $status is retrieved or set somewhere in your PHP code
          $status = isset($status) ? $status : ''; // Initialize $status variable if not already set
          
          // Check if $status is set to "Aktiv" or "Deaktiviert"
          $isAktiv = ($status == 1) ? 'selected' : '';
          $isDeaktiviert = ($status == 0) ? 'selected' : '';
          ?>

          <div class="col-lg-6 col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="Status">Status</label>
              <select name="status" class="form-select cursor-pointer custom-input" id="StaffStatus-Options-E">
                <option <?php echo $isAktiv; ?> value="1">Aktiv</option>
                <option <?php echo $isDeaktiviert; ?> value="0">Deaktiviert</option>
              </select>
              <span class="error" id="status-error"></span>
            </div>
          </div>

          <div class="col-lg-6 col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="Telephone">Telefon</label>
              <input type="text" name="telephone" value="" class="form-control custom-input" id="StaffTelephone"
                placeholder="Telefon">
              <span class="error" id="telephone-edit-error"></span>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="form-group p-2 select-group my-2" id="select-group">
              <label class="my-1" for="Services">Leistungen</label>
              <div style="height: 150px; overflow-x: hidden; overflow-y:scroll;">
                <?php
                foreach ($servicesArray as $service) {
                  echo '<div class="form-check">';
                  echo '<input class="form-check-input" type="checkbox" name="staff_services[]" id="edit_' . $service['services'] . '" value="' . $service['id'] . '">';
                  echo '<label class="form-check-label" for="edit_' . $service['services'] . '">' . $service['services'] . '</label>';
                  echo '</div>';
                  }
                ?>
              </div>

              <span class="error" id="staff_services-error"></span>
            </div>
          </div>

          <div class="col-lg-6 col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="Status">Profil</label>
              <input type="file" name="profile" class="form-control custom-input d-none" id="profile-image-E"
                aria-invalid="false" accept="image/*">
              <button class="custom-main-btn d-block w-100 my-2" type="button" id="open-image-picker-E"><i
                  class="bi bi-upload mx-2"></i></button>
              <div class="text-center"><img id="image-preview-E" width="100px"></div>
              <span class="error" id="status-error"></span>
            </div>
          </div>


          <!-- <div class="col-lg-6 col-12">
          <div class="form-group p-2 my-2">
          <label class="my-1" for="Status">Role</label>
          <select name="role" class="form-control custom-input" id="role">
          <option value='2'>Arzt</option>
          <option value='3'>Schwester</option>
          </select>
          <span  class="error" id="status-error"></span>
          </div>
          </div> -->

        </div>
        <div class="d-flex justify-content-center align-items-center py-2 my-3">
          <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal" style="margin-right: 5px;"
            id="cancelStaff">Abbrechen</button>
          <button type="button" class="success-button cursor-pointer mx-2" id="UpdateStaffBtn">Aktualisieren</button>
        </div>

        <!-- Confirmation -->
        <div class="modal fade " id="EditConfirmation" tabindex="-1" role="dialog"
          aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
              <div class="d-flex justify-content-center align-items-center flex-column">
                <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
                <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
              </div>
              <div class="d-flex justify-content-center align-items-center">
                <button type="button" class="cancel-button cursor-pointer mx-1" data-bs-dismiss="modal"
                  style="margin-right: 3px;">Nein</button>
                <button type="submit" class="success-button cursor-pointer mx-1" data-bs-toggle="modal"
                  data-bs-dismiss="modal" style="margin-left: 3px;">Ja</button>
              </div>
            </div>
          </div>
        </div>
        <!-- </form> -->
      </div>
    </div>
  </div>
  <!-- edit conformation -->
  <div class="modal fade " id="EditStaffConfirmation" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
        <div class="d-flex justify-content-center align-items-center flex-column">
          <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
          <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
        </div>
        <div class="d-flex justify-content-center align-items-center">
          <button type="button" class="cancel-button cursor-pointer mx-1" data-bs-dismiss="modal"
            style="margin-right: 3px;">Nein</button>
          <button type="button" class="success-button cursor-pointer mx-1" data-bs-toggle="modal"
            data-bs-dismiss="modal" id="UpdateStaffConfirmationBtn" style="margin-left: 3px;">Ja</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade " id="edit-show-info" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
        <div class="d-flex justify-content-center align-items-center flex-column py-4">
          <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich
            aktualisiert.</h1>
        </div>
        <div class="d-flex justify-content-center align-items-center">
          <button type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal"
            id="UpdateStaffShowInfoBtn">Okay</button>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- Confirmation -->
<form method="post" action="./controller/deletestaff.php">
  <div class="modal fade " id="deleteStaffConfirmation" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <input type="hidden" name="id" value="" class="form-control custom-input" id="deleteStaffId">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
        <div class="d-flex justify-content-center align-items-center flex-column">
          <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
          <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
        </div>
        <div class="d-flex justify-content-center align-items-center">
          <button type="button" class="cancel-button cursor-pointer mx-1" data-bs-dismiss="modal"
            style="margin-right: 3px;">Nein</button>
          <button type="submit" class="success-button cursor-pointer" data-bs-target="#ShowNurseInfo"
            data-bs-toggle="modal" data-bs-dismiss="modal" id="deleteNurseYesBtn" style="margin-left: 3px;">Ja</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade " id="show-info" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
        <div class="d-flex justify-content-center align-items-center flex-column py-4">
          <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich
            gelöscht.</h1>
        </div>
        <div class="d-flex justify-content-center align-items-center">
          <button type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal">Okay</button>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="asset/js/index.js"></script>
<script src="asset/js/pagination.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="./asset/js/script.js"></script>
<script>
$(document).ready(function() {
  // show service 
  $('.showAllBtn').on('click', function() {
    var id = $(this).data('id');
    $.ajax({
      url: './ajax/servicelist.php',
      method: 'GET',
      data: {
        id: id
      },
      success: function(response) {

        var staffData = JSON.parse(response);
        var serviceString = staffData.services;
        // var serviceArray = serviceString.split('__');
        for (var i = 0; i < serviceArray.length; i++) {
          serviceArray[i] = serviceArray[i].trim();
        }
        var servicesList = '';
        serviceArray.forEach(function(service) {
          servicesList += '<li> ' + service.trim() + '</li>';
        });
        $('#showAll').html(servicesList);
        $('#services-list').modal('show');
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });
  });
  // show time 
  $('.showTimeBtn').on('click', function() {
    var id = $(this).data('id');
    $.ajax({
      url: './ajax/timelist.php',
      method: 'GET',
      data: {
        id: id
      },
      success: function(response) {
        var responseData = JSON.parse(response);
        responseData.sort(function(a, b) {
          return new Date(a.selected_date) - new Date(b.selected_date);
        });
        var timeSlotsList = '';
        responseData.forEach(function(slot) {
          var selectedDate = slot.selected_date;
          var times = JSON.parse(slot.time);
          times.sort();
          timeSlotsList += '<li>Date: ' + selectedDate + '<ul>';

          times.forEach(function(time) {
            timeSlotsList += '<li>' + time + '</li>';
          });

          timeSlotsList += '</ul></li>';
        });

        // Display the list
        $('#timeList').html(
          '<ul class="show-services" style="color: #381e14;font-size: var(--md-text);font-weight:500;">' +
          timeSlotsList + '</ul>');
        $('#time-list').modal('show');
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });
  });

  $('.patientList').on('click', function() {
    var id = $(this).data('id');
    $.ajax({
      url: './ajax/patientlist.php',
      method: 'GET',
      data: {
        id: id
      },
      success: function(response) {
        var patientData = JSON.parse(response);
        var patientList = $('#patientList');
        var patientCount = patientData.length;
        patientList.empty();
        for (var i = 0; i < patientData.length; i++) {
          var patient = patientData[i];
          var listItem = $('<li>');

          listItem.html(`
<div class="row">
<div class="col-6">${patient.name}</div>
<div class="col-6"><span>Visited on: </span>${patient.created_at}</div>
</div>
`);

          patientList.append(listItem);
        }
        $('#patient-list').modal('show');
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });
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
          currentPage: currentPage ? Number(currentPage) : 1
        })
<?php } ?>
</script>

<!-- <script>
function filterByDate() {
const startDate = document.getElementById("start-date").value;
const endDate = document.getElementById("end-date").value;

const startDateObj = new Date(startDate);
const endDateObj = new Date(endDate);

const doctorRows = document.getElementsByClassName("doctor-row");

for (let i = 0; i < doctorRows.length; i++) {
const dateString = doctorRows[i].getElementsByClassName("created-at")[0].textContent;
const dataDate = new Date(dateString);

if (dataDate >= startDateObj && dataDate <= endDateObj) {
doctorRows[i].style.display = "table-row";
} else {
doctorRows[i].style.display = "none";
}
}
}
</script> -->
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
</script>


<script>
$(document).ready(function() {
  $('#Services-Options-E').on('change', function() {
    var selectedOptions = $(this).val();
    $('#Services-input-E').val(selectedOptions ? selectedOptions.join(', ') : '');
  });

  $('#Services-Options').on('change', function() {
    var selectedOptions = $(this).val();
    $('#Services-input').val(selectedOptions ? selectedOptions.join(', ') : '');
  });
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
  newUrl = url2 + '?' + 'orderby=' + encodeURIComponent(orderby) + '&column=' + encodeURIComponent(column);
  window.location.href = newUrl;

});

$('#clearDatepicker').on('click', function() {
  var url = window.location.href;
  var baseUrl = url.split('?')[0];
  window.location.href = baseUrl;
})

$('#cancelStaff').on('click', function() {
  location.reload(true);
})
</script>
<script>
document.getElementById("open-image-picker").addEventListener('click', () => {
  document.getElementById('profile-image').click()
});

function redirectToEmployeePage() {
  window.location.href = "employees";
}

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
<script>
document.getElementById("open-image-picker-E").addEventListener('click', () => {
  document.getElementById('profile-image-E').click()
});

function redirectToEmployeePage() {
  window.location.href = "employees";
}

document.getElementById('profile-image-E').addEventListener('change', function(event) {
  var file = event.target.files[0];
  if (file) {
    var reader = new FileReader();
    reader.onload = function(e) {
      var image = document.getElementById('image-preview-E');
      image.src = e.target.result;
      // Set max height and width
      image.style.maxHeight = '100px';
      image.style.maxWidth = '100px';
    };
    reader.readAsDataURL(file);
  }
});
</script>

<!-- logout script  -->
<?php include ('./layout/script.php') ?>

<script>
$("#edit-staff, #add-staff").on("shown.bs.modal", function() {
  $(this).find('input').eq(0).focus();
});

$("#add-staff").on("show.bs.modal", function() {
  $(this).find('input:not([type=checkbox])').val('');
  $(this).find('input[type=checkbox]').prop('checked', false);
});
</script>
</body>

</html>