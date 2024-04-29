<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
  header("Location: login");
  }
include ('config/database.php');
include ('layout/header.php');
include ('layout/sidebar.php');

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
  $conditions[] = "(user.name LIKE '%$searchTerm%' OR user.email LIKE '%$searchTerm%')";
  }
if ($startDate !== null && $endDate !== null) {
  $conditions[] = "user.created_at BETWEEN '$startDate' AND '$endDate'";
  }

// Construct the WHERE clause
$whereClause = "";
if (!empty($conditions)) {
  $whereClause = "WHERE user.deleted_at IS NULL  AND role = 3 AND " . implode(" AND ", $conditions);
  } else {
  $whereClause = "WHERE user.deleted_at IS NULL  AND role = 3";
  }

$orderBy = isset($_GET['orderby']) ? $_GET['orderby'] : null;
if ($orderBy !== null && in_array($orderBy, ['asc', 'desc'])) {

  $column = isset($_GET['column']) ? $_GET['column'] : null;
  if ($column == 'doctor') {
    $orderClause = "user.name $orderBy";
    } else {
    $orderClause = "patients.status $orderBy";
    }

  } else {
  $orderClause = "user.id DESC";
  }

$GetStaff = "SELECT user.*, COUNT(CASE WHEN patients.status = 'Vollendet' THEN patients.id ELSE NULL END) AS patient_count
    FROM user
    LEFT JOIN patients ON user.id = patients.doctor
    $whereClause
    GROUP BY user.id ORDER BY $orderClause";
// Execute the SQL query
$StaffResult = $connect->query($GetStaff);
$staffList = array();

if ($StaffResult->num_rows > 0) {
  while ($row = $StaffResult->fetch_assoc()) {
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
      <h1 class="page-heading">Mitarbeiter</h1>
    </div>
    <div class="px-2">
      <div class="d-flex flex-wrap">
        <div class="dashboard-search my-auto">
          <i class="bi bi-search"></i>
          <input type="text" class="w-100" id="Search-input" placeholder="Suche" name="search"
            value="<?php echo $searchTerm ?>">
        </div>
        <div class="flex-grow-1"></div>

        <button type="submit" class="cursor-pointer custom-secondary-button my-auto" data-bs-toggle="modal"
          data-bs-target="#add-nurse"><i class="bi bi-plus" style="color: white; "></i>Mitarbeiter hinzufügen</button>
      </div>
      <div class="mt-4 custom-table" id="Search-Options" onchange="handleSelect('Search-input')">
        <div class=" table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <td>#</td>
                <td>Name</td>
                <td>E-Mail</td>
                <td class="text-center">Status<i data-value="status"
                    class="fa-solid fa-arrow-up-arrow-down Shorting ms-1"
                    style="font-size: 14px;display: inline-block;"></i> </td>
                <td>
                  <div class="d-flex justify-content-center">Optionen</div>
                </td>
              </tr>
            </thead>
            <tbody>
              <?php for ($i = $startIndex; $i <= $endIndex; $i++) {
                ; ?>
                <tr class="doctor-row">
                  <td class="align-middle" style="max-width: 100px;"><?php echo $i + 1; ?></td>
                  <td class="align-middle" style="max-width: 100px;"><?php echo $staffList[$i]['name']; ?></td>
                  <td class="align-middle" style="max-width: 100px;"><?php echo $staffList[$i]['email']; ?></td>
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
                      <?php if ($staffList[$i]['status'] === '1' || $staffList[$i]['status'] === '0') {
                        ; ?>
                        <!-- Edit button -->
                        <div class="editNurseButton" data-id="<?php echo $staffList[$i]['id']; ?>" data-bs-toggle="modal">
                          <i class="fas fa-edit p-2 cursor-pointer"></i>
                        </div>
                        <!-- Delete button -->
                        <form method="post" action="./controller/deletenurse.php">
                          <input type="hidden" name="id" value="<?= $staffList[$i]['id'] ?>">
                          <button type="button" class="iconButton deleteNurseButton"
                            data-id="<?php echo $staffList[$i]['id']; ?>">
                            <i class="fas fa-trash cursor-pointer text-danger p-2"></i>
                          </button>
                        <?php } ?>
                      </form>
                    </div>

                  </td>
                </tr>
              <?php } ?>
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
<!-- Add Nurse -->
<form id="AddNurse" method="post" action="./controller/addnurse.php">
  <div class="modal fade " id="add-nurse" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
        <div class="d-flex justify-content-center align-items-center py-2">
          <div class="flex-grow-1"></div>
          <h1 class="modal-heading" style="font-weight: 800;">Mitarbeiter hinzufügen</h1>
          <div class="flex-grow-1"></div>
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" style="width:50px;"
            aria-label="Close"></button>
        </div>
        <div class="row">
          <div class="col-lg-6 col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="Name">Name</label>
              <input type="text" name="name" class="form-control custom-input" id="Name" placeholder="Name">
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
              <p class="error mb-0" id="password-error"></p>
            </div>
          </div>

          <div class="col-lg-6 col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="Status">Status</label>
              <select name="status" class="form-select cursor-pointer custom-input" value="" id="StaffStatus-Options-E">
                <option selected value="Aktiv">Aktiv</option>
                <option value="Deaktiviert">Deaktiviert</option>
              </select>
              <span class="error" id="status-error"></span>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-center align-items-center py-2 my-3">
          <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal"
            style="margin-right: 5px;">Abbrechen</button>
          <button type="submit" id="addNurseBtn" class="success-button cursor-pointer"
            style="margin-left: 5px;">Hinzufügen</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Nurse Confirmation -->
  <div class="modal fade " id="NurseConfirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
        <div class="d-flex justify-content-center align-items-center flex-column">
          <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
          <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
        </div>
        <div class="d-flex justify-content-center align-items-center">
          <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal"
            style="margin-right: 3px;">Nein</button>
          <button type="button" id="NurseConfirmationYesBtn" class="success-button cursor-pointer"
            data-bs-target="#show-info" style="margin-left: 3px;">Ja</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Nurse show info  -->
  <div class="modal fade " id="nurseShowInfo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
        <div class="d-flex justify-content-center align-items-center flex-column py-4">
          <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich
            aktualisiert.</h1>
        </div>
        <div class="d-flex justify-content-center align-items-center">
          <button id="NurseShowInfoBtn" type="submit" class="success-button cursor-pointer"
            data-bs-dismiss="modal">Okay</button>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- Edit Staff -->
<form id="Editnurse" action="./controller/editnurse.php" method="post">
  <div class="modal fade" id="edit-nurse" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
        <div class="d-flex justify-content-center align-items-center py-2">
          <div class="flex-grow-1"></div>
          <h1 class="modal-heading" style="font-weight: 800;">Mitarbeiter bearbeiten</h1>
          <div class="flex-grow-1"></div>
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" style="width:50px;"
            aria-label="Close"></button>

        </div>
        <div class="row">
          <input type="hidden" name="id" value="" class="form-control custom-input" id="NurseID">
          <div class=" col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="Name">Name</label>
              <input type="text" name="name" value="" class="form-control custom-input" id="NurseName"
                placeholder="Enter Name">
              <span class="error" id="name-edit-error"></span>
            </div>
          </div>
          <div class="col-12">
            <div class="form-group p-2 my-2">
              <label class="my-1" for="email">E-Mail</label>
              <input type="email" name="email" value="" class="form-control custom-input" id="NurseEmail"
                placeholder="Enter email">
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
              <select name="status" class="form-select cursor-pointer custom-input" id="NurseStatus">
                <option <?php echo $isAktiv; ?> value="1">Aktiv</option>
                <option <?php echo $isDeaktiviert; ?> value="0">Deaktiviert</option>
              </select>
              <span class="error" id="status-error"></span>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-center align-items-center py-2 my-3">
          <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal" id="cancelNurse"
            style="margin-right: 5px;">Abbrechen</button>
          <button type="submit" class="success-button cursor-pointer" id="UpdateNurseBtn"
            name="UpdateNurseBtn">Aktualisieren</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Nurse Confirmation -->
  <div class="modal fade " id="EditNurseConfirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
        <div class="d-flex justify-content-center align-items-center flex-column">
          <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
          <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
        </div>
        <div class="d-flex justify-content-center align-items-center">
          <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal"
            style="margin-right: 3px;">Nein</button>
          <button type="button" class="success-button cursor-pointer" data-bs-target="#ShowNurseConfirm"
            data-bs-toggle="modal" data-bs-dismiss="modal" style="margin-left: 3px;">Ja</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade " id="ShowNurseConfirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
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

<!-- Nurse Delete Confirmation -->
<form method="post" action="./controller/deletenurse.php">
  <div class="modal fade " id="deleteNurseConfirmation" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <input type="hidden" name="id" value="" class="form-control custom-input" id="deleteNurseId">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
        <div class="d-flex justify-content-center align-items-center flex-column">
          <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
          <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
        </div>
        <div class="d-flex justify-content-center align-items-center">
          <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal"
            style="margin-right: 3px;">Nein</button>
          <button type="submit" class="success-button cursor-pointer" data-bs-target="#ShowNurseInfo"
            data-bs-toggle="modal" data-bs-dismiss="modal" id="deleteNurseYesBtn" style="margin-left: 3px;">Ja</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade " id="ShowNurseInfo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
        <div class="d-flex justify-content-center align-items-center flex-column py-4">
          <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich
            gel�scht.</h1>
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
  var newUrl = url + separator + 'start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(
    endDate);
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
  newUrl = url2 + '?' + 'orderby=' + encodeURIComponent(orderby) + '&column=' + encodeURIComponent(column);
  window.location.href = newUrl;
});

$('#clearDatepicker').on('click', function() {
  var url = window.location.href;
  var baseUrl = url.split('?')[0];
  window.location.href = baseUrl;
})

$('#cancelNurse').on('click', function() {
  location.reload(true);
})
</script>
<!-- logout script  -->
<?php include ('layout/script.php') ?>
</body>

</html>