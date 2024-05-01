<?php
session_start();

$lang = (!empty($_SESSION['lang'])) ? $_SESSION['lang'] : 'de';
include ('lang/' . $lang . '.php');
include ('./backoffice/config/database.php');

$GetServices = "select * from services where deleted_at IS NULL";
$ServiceResult = $connect->query($GetServices);
$servicesArray = array();
if ($ServiceResult->num_rows > 0) {
	while ($row = $ServiceResult->fetch_assoc()) {
		$servicesArray[] = $row;
		}
	}
?>

<!DOCTYPE html>
<html>

<head>

  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  <meta name="google" content="notranslate" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex,nofollow" />
  <!-- Tittle -->
  <title>Dr. Pleger - Termin buchen</title>

  <!-- Google Fonts  -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

  <!-- Jquery -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://site-assets.fontawesome.com/releases/v6.2.1/css/all.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="shortcut icon" href="images/favicon.svg">
</head>

<body>
  <!-- Header Section Start -->
  <nav class="navbar navbar-expand-lg header">
    <div class="container">
      <a class="navbar-brand" href="https://drpleger.de/">
        <img src="images/logo.png">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
        aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse header-menu justify-content-end" id="navbarNavAltMarkup">
        <div class="navbar-nav">
          <a class="nav-link" href="https://drpleger.de/<?= $ln['nav_link_lang'] ?>"><?= $ln['home'] ?></a>
          <a class="nav-link" href="https://drpleger.de/<?= $ln['nav_link_lang'] ?>#team">Team</a>
          <a class="nav-link" href="https://drpleger.de/<?= $ln['nav_link_lang'] ?>#Services"><?= $ln['services'] ?></a>
          <a class="nav-link" href="https://drpleger.de/<?= $ln['nav_link_lang'] ?>#practice"><?= $ln['praxis'] ?></a>
          <a class="nav-link" href="https://drpleger.de/<?= $ln['nav_link_lang'] ?>#contact"><?= $ln['contact'] ?></a>
        </div>
      </div>
    </div>
  </nav>
  <!-- Header Section End -->

  <!-- Step Form Section Start -->
  <section class="step-form">
    <div class="container" id="container">
      <div class="main-heading">

        <h1><?= $ln['title'] ?></h1>
      </div>
      <div class="main-step-form">
        <div class="progress px-1" style="height: 2px;">
          <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
            aria-valuemax="100"></div>
        </div>

        <div class="row justify-content-center">

        </div>
        <div class="step-container d-flex justify-content-center mb-5">
          <div class="step-circle-1 selected-date">
            <div class="step-circle selected-step" id="progressbar-1"></div>
            <p><?= $ln['step1'] ?></p>
          </div>
          <div class="step-circle-2">
            <div class="step-circle " id="progressbar-2"></div>
            <p><?= $ln['step2'] ?></p>
          </div>
          <div class="step-circle-3">
            <div class="step-circle " id="progressbar-3"></div>
            <p><?= $ln['step3'] ?></p>
          </div>
          <div class="step-circle-4">
            <div class="step-circle " id="progressbar-4"></div>
            <p><?= $ln['step4'] ?></p>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-7 col-md-12 col-12">

            <div id="multi-step-form">
              <div class="step step-1">
                <div class="treatment-box">
                  <p><?= $ln['select_service'] ?></p>
                  <div class="common-box  mb-4 treatment-list">
                    <ul>
                      <?php
											if ($lang == 'de') {
												foreach ($servicesArray as $service) {
													echo "<li class='next-step service-button' data-name=" . $service['services'] . " value='" . $service['id'] . "'><button type='button'>" . $service['services'] . "</button></li>";
													}
												} else {
												foreach ($servicesArray as $service) {
													echo "<li class='next-step service-button' data-name=" . $service['services'] . " value='" . $service['id'] . "'><button type='button'>" . $service['services_en'] . "</button></li>";
													}
												}
											?>
                    </ul>
                  </div>
                </div>

                <!-- <button type="button" class="btn btn-primary next-step">Next</button> -->
              </div>

              <div class="step step-2" style="display: none;">
                <div class="treatment-box">
                  <p><?= $ln['select_doctor'] ?></p>
                  <div class="common-box  mb-4 doctor-list-box">
                    <ul class="doctor-list next-step" id="doctorList">

                    </ul>
                  </div>
                </div>
              </div>
              <div class="step step-3" style="display: none;">
                <div class="treatment-box">
                  <p><?= $ln['select_date'] ?></p>
                  <?php /*<!-- <div class="common-box  mb-4 doctor-list-box">
														<div class="calendar-wrapper">
															<button id="btnPrev" type="button"></button>
															<button id="btnNext" type="button"></button>
															<div id="divCal">
																
															</div>

														</div>
													</div> -->*/ ?>
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
                          <th><?= $ln['calander_days'] ?></th>
                          <th><?= $ln['calander_days_wednesday'] ?></th>
                          <th><?= $ln['calander_days'] ?></th>
                          <th>F</th>
                          <th>S</th>
                          <th>S</th>
                        </tr>
                      </thead>
                      <!-- <tbody id="calendarBody" onclick="getDate(event)" class="date-box"></tbody> -->
                      <tbody id="calendarBody" data-available-date="" class="date-box"></tbody>
                    </table>
                  </div>
                </div>
                <div class="treatment-box mt40">
                  <p class="mb20"><?= $ln['select_time'] ?></p>
                  <div class="common-box  mb-4 doctor-list-box time-boxes next-step" id="timeList" style="display:none">
                  </div>
                  <div class="common-box  mb-4 doctor-list-box text-center" id="timeList2" style="display:none">
                  </div>
                  <div class="common-box  mb-4 doctor-list-box text-center" id="timeList3" style="">
                    <?= $ln['select_date'] ?></div>
                </div>
              </div>
              <div class="step step-4" style="display: none;">
                <div class="treatment-box">
                  <p><?= $ln['patient_details'] ?></p>
                  <div class="common-box  mb-4 doctor-list-box">
                    <form class="patient-form" method="post" id="userForm">
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-field">
                            <input type="text" name="name" id="nameInput" placeholder="Name">
                            <p class="error m-0" id="name-error"></p>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-field">
                            <input type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="birthdate"
                              id="birthdateInput" placeholder="<?= $ln['birth_date_input'] ?>">
                              <p class="error m-0" id="birthdate-error"></p>
                          </div>
                          <!-- <div class="form-field">
													<input type="date" name="birthdate" id="birthdateInput" placeholder="Datum der Geburt*">
													<p  class="error" id="birthdate-error"></p> -->
                          <!-- </div> -->
                        </div>
                        <div class="col-md-6">
                          <div class="form-field">
                            <input type="number" name="phone" id="phoneInput"
                              placeholder="<?= $ln['telephone_input'] ?>">
                            <p class="error m-0" id="phone-error"></p>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-field">
                            <input type="email" name="email" id="emailInput" placeholder="E-Mail">
                            <!-- <p class="error m-0" id="email-error"></p> -->
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-field checkbox">
                            <input type="checkbox" id="reminderCheckbox" disabled>
                            <label for="reminderCheckbox"><?= $ln['reminder_checkbox'] ?></label>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-field button-flex">
                            <button type="button" id="submitButton"
                              data-bs-toggle="modal"><?= $ln['submit_button'] ?></button>
                            <button type="button" class="cancel-btn"
                              id="cancelButton"><?= $ln['cancel_button'] ?></button>
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-5 col-md-12 col-12">
            <div class="appointment-main-box">
              <div class="back-btn">
                <button type="button" class="prev-step" id="nextButton"><i
                    class="fa-light fa-arrow-left"></i><?= $ln['next_button'] ?></button>
              </div>
              <div class="appointment-box">
                <h4><?= $ln['appointment_details'] ?>:</h4>
                <ul id="selectedData">
                  </u>
              </div>
            </div>
            <div class="Note-Box">
              <span style="font-weight: 400;"><i class="fa-regular fa-envelope me-3"></i><a
                  href="mailto:info@drpleger.de"><?= $ln['note_info'] ?></a> </span>
              <span style="font-weight: 400;"><i class="fa-regular fa-phone me-3 my-1"></i><a
                  href="tel:03921 - 30 68">03921 - 30 68</a> </span>
              <span style="font-weight: 400;"><i class="fa-regular fa-fax me-3 mb-2"></i><a
                  href="tel:03921 - 63 51 77">03921 - 63 51 77</a> </span>
              <span style="font-weight: 400;"><i class="fa-solid fa-location-dot me-3"></i><a href=""><span
                    style="margin-left: 2px;display: inline-block;"><?= $ln['note_location1'] ?></span><br><span
                    style="margin-left: 30px;"><?= $ln['note_location2'] ?></span> <span
                    style="margin-left: 30px;"><?= $ln['note_location3'] ?></span></a> </span>
            </div>
          </div>
        </div>
      </div>
      <div class="mt-5">
        <hr class="mx-auto">
        <div class="mt-2 text-center">
          <a href="javascript:void(0);" class="lang <?= ($lang == 'de') ? 'red-text' : ''; ?>"
            data-lang="de"><?= $ln['lang_text'] ?></a>
          <a href="" class="mx-2">|</a>
          <a href="javascript:void(0);" class="lang <?= ($lang == 'en') ? 'red-text' : ''; ?>"
            data-lang="en">English</a>
        </div>
      </div>


      <!-- </div> -->
  </section>
  <!-- <h4>First select Language :</h4> -->
  <!-- Step Form Section End -->
  <div class="confirm-box modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <div class="popup-box">
            <h1 style="font-size: 25px;"><?= $ln['confirmation_window'] ?> <span id="patientId"></span></h1>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="bookingIdModel">Okay</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="spinner" id="loader" style="display:none">
    <div class="spinner-container">
      <div class="spinner-loader"></div>
    </div>
  </div>
  <script>
  var NoTimeSlot = "<?= $ln['no_time_slot'] ?>";
  </script>
  <script>
  var DoctorDetail = "<?= $ln['doctor_detail'] ?>";
  </script>
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/script.js"></script>
  <script src="js/calender.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
  <!-- form validation -->
  <script>
  function getDate(event) {
    const clickedDate = event.target.textContent;
    console.log(clickedDate);
    const newDate = formatDate(clickedDate);
  }

  function formatDate(date) {
    const year = new Date().getFullYear();
    const month = new Date().getMonth() + 1;

    const formattedMonth = month < 10 ? `0${month}` : month;
    const formattedDay = date < 10 ? `0${date}` : date;

    return `${formattedDay}.${formattedMonth}.${year}`;

  }

  function properDateFormate(date) {
    const dateObject = new Date(date);

    const month = dateObject.getMonth() + 1;
    const year = dateObject.getFullYear();
    const day = dateObject.getDate();

    const formattedMonth = month < 10 ? `0${month}` : month;
    const formattedDay = day < 10 ? `0${day}` : day;

    return `${formattedDay}.${formattedMonth}.${year}`;

  }
  </script>
  <script>
  $(document).ready(function() {
    // When any language link is clicked
    $(".lang").click(function() {
      // Remove 'red-text' class from all language links
      $(".lang").removeClass("red-text");

      // Add 'red-text' class to the clicked language link
      $(this).addClass("red-text");

      var lang = $(this).data("lang");
      $.ajax({
        url: "../lang/config.php",
        method: "GET",
        data: {
          lang: lang,
        },
        success: function(response) {
          sessionStorage.setItem('lang', lang);
          location.reload();
        },
        error: (e) => console.log(e),
      });
    });
  });
  </script>


</body>

</html>