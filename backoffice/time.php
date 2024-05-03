<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
  header("Location: login");
  }
include ('config/database.php');
include ('./layout/header.php');
include ('./layout/sidebar.php');

$GetTime = "select * from time_slots";
$result = $connect->query($GetTime);
$timeList = array();


if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $timeList[] = $row['time'];
    }
  }



?>

<!-- Main -->
<div id="main-content">
  <div class="p-2 w-100">
    <div class="d-flex justify-content-center align-items-center">
      <h1 class="page-heading mb-5">Sprechzeiten</h1>
    </div>
    <div class="row w-100">
      <div class="col-12 col-lg-6 col-xl-5 col-xxxl-3 col-xxl-4 mb-5">
        <div class="my-calender px-4">
          <div class="d-flex justify-content-center py-3" style="color: var(--main);">
            <!-- <h4 style="font-weight:700;">Datum auswählen</h4> -->
          </div>
          <!-- <div class="calendar">
                            <div class="header">
                                <div class="cursor-pointer" onclick="prevMonth()" >
                                    <span class="prev icon" style="color: black;">&lt;</span>
                                    <span class="pre-month-year"></span>
                                </div>
                                <div class="cursor-pointer" onclick="nextMonth()">
                                    <span class="month-year next"></span>
                                    <span class="next icon">></span>
                                </div>
                            </div>
                            <table class="days">
                                <thead>
                                    <tr>
                                        <th>S</th>
                                        <th>M</th>
                                        <th>T</th>
                                        <th>W</th>
                                        <th>T</th>
                                        <th>F</th>
                                        <th>S</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div> -->
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
      <div class="col-12 col-lg-6">
        <div class="select-time">
          <div class="d-flex justify-content-center py-3" style="color: var(--main);">
            <h4 style="font-weight: 700;">Zeit auswählen</h4>
          </div>
          <!-- <form method="post"> from to date, button to apply and cancel -->
          <div class="mx-2">
            <div class="d-flex justify-content-md-center flex-wrap m-2">
              <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                <input type="text" id="dateRangePicker" name="daterange" class="form-control text-center">
              </div>
              <!-- <div class="input-date">
                <input class="mx-1" style="width: 140px;" type="date" id="start-date" name="start_date"
                  placeholder="Start Date" value="<?php echo $startDate; ?>">
              </div>
              <p class="mx-2 mb-0" style="font-size: var(--md-text); color: var(--main); font-weight: 500;">To</p>
              <div class="input-date">
                <input class="mx-1" style="width: 140px;" type="date" id="end-date" name="end_date"
                  placeholder="End Date" value="<?php echo $endDate; ?>">
              </div> -->
            </div>
            <div id="weekdaysSelectedValues" class="d-flex justify-content-md-center flex-wrap mx-2 mt-3 mb-2">
              <input id="weekSchedulerWidgetDayMon" type="checkbox" name="days[]" class="mx-2 check-days"
                value="1"><label for="weekSchedulerWidgetDayMon">MO</label></input>
              <input id="weekSchedulerWidgetDayTue" type="checkbox" name="days[]" class="mx-2 check-days"
                value="2"><label for="weekSchedulerWidgetDayTue">DI</label></input>
              <input id="weekSchedulerWidgetDayWed" type="checkbox" name="days[]" class="mx-2 check-days"
                value="3"><label for="weekSchedulerWidgetDayWed">MI</label></input>
              <input id="weekSchedulerWidgetDayThu" type="checkbox" name="days[]" class="mx-2 check-days"
                value="4"><label for="weekSchedulerWidgetDayThu">DO</label></input>
              <input id="weekSchedulerWidgetDayFri" type="checkbox" name="days[]" class="mx-2 check-days"
                value="5"><label for="weekSchedulerWidgetDayFri">FR</label></input>
            </div>
            <div class="d-flex justify-content-center align-items-center my-3">
              <button type="submit" class="cursor-pointer showTimeBtn custom-main-btn mx-3"
                style="background-color: var(--main); color: white; border-radius: 16px;"
                id="applyDatepicker">Anwenden</button>
              <!-- <button class="cursor-pointer showAllBtn" style="background-color: var(--main); color: white;" id="FilterClear">Clear</button> -->

              <button type="submit" class="cursor-pointer showTimeBtn custom-main-btn mx-3"
                style="background-color: var(--main); color: white; border-radius: 16px;"
                id="clearDatepicker">Löschen</button>
              <!-- <button class="cursor-pointer showAllBtn" style="background-color: var(--main); color: white;" id="FilterClear">Clear</button> -->

            </div>
          </div>
          <div class="mx-3 px-3">
            <label class='fw-bolder cursor-pointer' style="user-select: none;">
              <input type='checkbox' onchange="handleSelectAll()" id="selectAllCheckBox"> Alle auswählen
            </label>
          </div>
          <ul class="row" id="selectTime">
            <?php
            echo "<form>";
            foreach ($timeList as $time) {
              echo "<label class='col-12 col-md-6 col-lg-4'>
                <input type='checkbox' class='optionalFeature' name='optionalFeature[]' value='$time'> $time 
              </label>";
              }
            echo "</form>";
            ?>
          </ul>
          <div class="d-flex justify-content-center align-items-center mt-5 mb-3">
            <button type="submit" class="cancel-button cursor-pointer" style="margin-right: 3px;"
              data-bs-dismiss="modal" id="reset">Zurücksetzen</button>
            <button type="submit" class="success-button cursor-pointer " style="margin-left: 3px;" data-bs-target=""
              data-bs-toggle="modal" data-bs-dismiss="modal" id="refresh" style="margin-left: 5px;">Hinzufügen</button>
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
        <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal"
          style="margin-right: 3px;">Nein</button>
        <button type="submit" class="success-button cursor-pointer" data-bs-target="" data-bs-toggle="modal"
          data-bs-dismiss="modal" id="confirmationBtn" style="margin-left: 3px;">Ja</button>
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
          aktualisiert.</h1>
      </div>
      <div class="d-flex justify-content-center align-items-center">
        <button type="submit" class="success-button curdsor-pointer" data-bs-dismiss="modal"
          id="showInfoBtn">Okay</button>
      </div>
    </div>
  </div>
</div>


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
        <button type="button" class="cancel-button cursor-pointer" data-bs-dismiss="modal"
          style="margin-right: 3px;">Nein</button>
        <button type="submit" class="success-button cursor-pointer" data-bs-target="" data-bs-toggle="modal"
          data-bs-dismiss="modal" id="resetConfirmationBtn" style="margin-left: 3px;">Ja</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade " id="reset-show-info" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered " role="document">
    <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
      <div class="d-flex justify-content-center align-items-center flex-column py-4">
        <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich gelöscht.
        </h1>
      </div>
      <div class="d-flex justify-content-center align-items-center">
        <button type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal"
          id="resetShowInfoBtn">Okay</button>
      </div>
    </div>
  </div>
</div>
<!-- Bootstrap -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
  integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<script src="asset/js/index.js"></script>
<script src="asset/js/calender-2.js"></script>

<script>
function getDatesInRangeWithWeekdays(startDate, endDate, weekdays) {
  const dates = [];
  let currentDate = new Date(startDate);

  // Iterate through each date in the range
  while (currentDate <= endDate) {
    // Check if the current date's weekday is in the selected weekdays
    if (weekdays.includes(currentDate.getDay())) {
      dates.push(moment(currentDate).format("YYYY-MM-DD")); // Add the date to the list
    }
    // Move to the next day
    currentDate.setDate(currentDate.getDate() + 1);
  }
  return dates;
}

var start = moment().format("YYYY-MM-DD"),
  end = moment().format("YYYY-MM-DD");

function handleChangeRange() {
  let days = [];
  $("input.check-days:checked").each(function() {
    days.push(Number($(this).val()));
  });
  $(`.selected-date`).removeClass("selected-date");
  selectedDate = getDatesInRangeWithWeekdays(start, end, days);
  // console.log(selectedDate)
  selectedDate.forEach(val => {
    $(`#${val}`).addClass("selected-date");
  });

}

$("input.check-days").change(() => {
  handleChangeRange();
});

$('input[name="daterange"]').daterangepicker({
    locale: {
      format: 'YYYY-MM-DD',
    },
    minDate: moment().format("YYYY-MM-DD"),
    startDate: start,
    endDate: end,

  },

  function(_start, _end, label) {
    start = _start;
    end = _end;
    handleChangeRange();
  });
// select All Time
const timesList = document.querySelectorAll('#selectTime input');

timesList.forEach((t) => t.addEventListener('change', () => {
  let checkSelectAll = true;
  for (const time of timesList) {

    if (time.checked === false) checkSelectAll = false;
  }
  document.getElementById('selectAllCheckBox').checked = checkSelectAll;
}));

const handleSelectAll = (e) => {
  const selected = document.getElementById('selectAllCheckBox').checked;
  if (selected) {
    for (const time of timesList) {

      time.checked = true;
    }
  } else
    for (const time of timesList) time.checked = false;
};

var doctor_id = <?php echo $_SESSION['staff_id'] ?>;
var date = '';
var selectedValues = [];
var selectedDate = [];

function getDate(event) {
  document.getElementById('selectAllCheckBox').checked = false;
  date = event.target.id;

  if (event.ctrlKey) {
    if (selectedDate.includes(event.target.id)) {
      selectedDate = selectedDate.filter(date => date != event.target.id);
      event.target.classList.remove("selected-date");
    } else {
      selectedDate.push(event.target.id);
      event.target.classList.add("selected-date");
      seper
    }
  } else {
    selectedDate = [event.target.id]
    $.ajax({
      url: './ajax/stafftime.php',
      method: 'GET',
      data: {
        date: date,
        doctorId: doctor_id,
      },
      success: function(response) {
        var timeList = JSON.parse(response);
        updateUI(timeList);
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });
  }
}

function updateUI(timeList) {
  if (timeList !== null) {
    var parsedTimeList = JSON.parse(timeList);

    $('input.optionalFeature').prop('checked', false);
    parsedTimeList.forEach(function(time) {
      $('input.optionalFeature[value="' + time + '"]').prop('checked', true);
    });
  } else {
    $('input.optionalFeature').prop('checked', false);
  }
}

// $('input.optionalFeature').on('change', function() {
//     var selectedCheckboxes = $('input.optionalFeature:checked');
//     selectedValues = selectedCheckboxes.map(function() {
//         return $(this).val();
//     }).get();
// });

$('#refresh').on('click', function() {
  selectedValues = $('input.optionalFeature:checked').map(function() {
    return $(this).val();
  }).get();
  // location.reload();
  $('#Confirmation').modal('show');
})

$('#confirmationBtn').on('click', function() {
  $('#show-info').modal('show');
})
$('#showInfoBtn').on('click', function() {
  $.ajax({
    url: './ajax/stafftime.php',
    method: 'POST',
    data: {
      date: selectedDate,
      selectedValues: selectedValues,
    },
    success: function(response) {
      location.reload();
    },
    error: function(xhr, status, error) {
      console.error('Error:', error);
    }
  });
})

$('#reset').on('click', function() {
  $('#selectTime').find('input[type=checkbox]').prop('checked', false);
  $.ajax({
    url: './ajax/resettime.php',
    method: 'GET',
    data: {
      date: date,
    },
    success: function(response) {
      location.reload();
    },
    error: function(xhr, status, error) {
      console.error('Error:', error);
    }
  });
  // $('#resetConfirmation').modal('show');
})
$('#resetConfirmationBtn').on('click', function() {
  // $('#reset-show-info').modal('show');
})
$('#resetShowInfoBtn').on('click', function() {})
</script>

<script>
$(document).ready(function() {
  var getDate = []; // Define an array to store selected dates
  var selectedValues = [];

  $(document).click(function() {
    getDate = []; // Clear the array before populating it with new data
    $(".selected-date").each(function() {
      var date = $(this).attr("id");
      getDate.push(date);
    });
  });

  $('#refresh').on('click', function() {
    selectedValues = $('input.optionalFeature:checked').map(function() {
      return $(this).val();
    }).get();

    $('#Confirmation').modal('show');
  })

  $('#showInfoBtn').on('click', function() {
    $.ajax({
      url: './ajax/stafftime.php',
      method: 'POST',
      data: {
        date: getDate,
        selectedValues: selectedValues,
      },
      success: function(response) {
        // location.reload();
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });
  });
});
</script>
<!-- logout  -->
<?php include ('./layout/script.php') ?>
</body>

</html>