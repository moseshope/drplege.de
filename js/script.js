const lang = sessionStorage.getItem("lang");
var currentStep = 1;
var updateProgressBar;
var selectedService = [];
var selectedDate = null;
var doctorId = "";
function updateSelectedData() {
  if (selectedService[0]) {
    $("#progressbar-2").addClass("selected-step");
  } else {
    $("#progressbar-2").removeClass("selected-step");
  }
  if (selectedService[1]) {
    $("#progressbar-3").addClass("selected-step");
  } else {
    $("#progressbar-3").removeClass("selected-step");
  }
  if (selectedService[2]) {
    $("#progressbar-4").addClass("selected-step");
  } else {
    $("#progressbar-4").removeClass("selected-step");
  }

  var selectedDataHtml = "";
  selectedService.forEach(function (item) {
    if (typeof item === "string") {
      selectedDataHtml += `<li>${item}</li>`;
    } else if (item.date && item.time) {
      selectedDataHtml += `
                <li>
                    ${item.date} | ${item.time}
                </li>
            `;
    } else {
      // const lang = sessionStorage.getItem("lang");
      selectedDataHtml += `
                <li>
                  ${lang && lang === "en" ? "Doctor" : "Doktor"}: ${
        item.DoctorName
      }<br>
                </li>
            `;
    }
  });

  var selectedDataElement = $(selectedDataHtml);
  $("#selectedData").empty().append(selectedDataElement);
}

function getDoctor(clickedElement) {
  var DoctorName = $(clickedElement).find("h4").text().trim();
  doctorId = $(clickedElement).find("h4").data("doctor-id");

  var Services = $(clickedElement)
    .find("ul li")
    .map(function () {
      return $(this).text().trim();
    })
    .get();
  $.ajax({
    url: "./ajax/availabledates.php",
    method: "GET",
    dataType: "JSON",
    data: {
      doctorId,
    },
    success: function (response) {
      console.log(response);
      selectedService.push({ DoctorName, Services, availableDates: response });
      updateSelectedData();
      generateCalendar();
    },
    error: function (xhr, status, error) {
      console.error("AJAX request failed:", status, error);
    },
  });
}

function getTime(clickedElement) {
  if (selectedDate) {
    var time = $(clickedElement).text().trim();
    selectedService.push({ date: selectedDate, time: time });
  }

  updateSelectedData();
  if (selectedService.length == 3) {
    $(".step-4").show();
  }
  $(".step-1, .step-2, .step-3").hide();
}

$(document).ready(function () {
  $("#userForm").validate({
    rules: {
      name: {
        required: true,
      },
      birthdate: {
        required: true,
      },
      email: {
        required: true,
        email: true,
      },
      phone: {
        required: true,
        digits: true,
        maxlength: 14,
      },
    },
    messages: {
      name: {
        required: "Name ist erforderlich.",
      },
      birthdate: {
        required: "Geburtsdatum ist erforderlich.",
      },
      email: {
        required: "E-Mail ist erforderlich.",
        email: "Bitte geben Sie eine gültige E-Mail Adresse ein.",
      },
      phone: {
        required: "Telefonnummer ist erforderlich.",
        digits: "Nur Zahlen erlaubt.",
        maxlength: "Kontakt sollte eine 14-stellige Nummer sein.",
      },
    },
    errorPlacement: function (error, element) {
      var fieldName = $(element).attr("name");
      error.insertAfter("#" + fieldName + "-error");
      error.addClass("text-danger");
    },
    highlight: function (element) {
      $(element).siblings(".error").addClass("text-danger");
    },
    unhighlight: function (element) {
      $(element).siblings(".error").removeClass("text-danger");
    },
  });

  var doctorContentContainer = $(
    '<ul class="doctor-list" id="doctorList"></ul>'
  ); // Create a container to hold doctorContent

  $(".service-button").click(function () {
    var service = $(this).data("name");
    var serviceName = $(this).text();
    var serviceId = $(this).val();

    $.ajax({
      url: "./ajax/employee.php",
      method: "GET",
      data: {
        service: service,
        serviceId: serviceId,
      },
      success: function (response) {
        var doctorData = JSON.parse(response);
        if (Array.isArray(doctorData) && doctorData.length > 0) {
          doctorContentContainer.empty();
          doctorData.forEach(function (doctor) {
            var services = doctor.services;
            // Create a list item for each doctor
            var doctorContent =
              $(`<li class="doctor-list-item next-step" data-id="${
                doctor.doctorId
              }" onClick="getDoctor(this)">
              <div class="doctor-image">
                <img src="${
                  doctor.profile != null && doctor.profile != ""
                    ? "https://drpleger.de/termin-buchen/images/" + doctor.profile
                    : "https://drpleger.de/termin-buchen/backoffice/asset/images/logo.png"
                }" style="height: 100px; width: 100px; border-radius: 100px; object-fit: containt;">
                  </div>
                    <div class="doctor-content">
                      <h4 data-doctor-id="${doctor.doctorId}">${
                doctor.doctorName
              }</h4>
                      <ul></ul>
                      </div>
                    </li>`);

            var servicesList = doctorContent.find("ul");

            // Loop through each service for the doctor and add it to the list
            services.forEach(function (service) {
              servicesList.append("<li>" + service + "</li>");
            });

            doctorContentContainer.append(doctorContent);
          });

          $("#doctorList").html(doctorContentContainer);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX request failed:", status, error);
      },
    });

    // selectedService.push(service);
    selectedService.push(serviceName);
    updateSelectedData();
  });

  // $('.doctor_data').click(function() {
  //                         // console.log(doctorContent);
  //                         var DoctorName = $(this).find('h4').text().trim();
  //                         var Services = $(this).find('ul li').map(function() {
  //                             return $(this).text().trim();
  //                         }).get();

  //                         selectedService.push({ DoctorName, Services });
  //                         updateSelectedData();
  // });

  $("#nextButton").click(function () {
    selectedService.pop();
    updateSelectedData();
  });

  $(".date-box").click(function (event) {
    var currentDate = new Date();
    var year = currentDate.getFullYear();
    var month = currentDate.getMonth() + 1;
    var day = currentDate.getDate();
    var currentFormattedDate =
      year +
      "-" +
      (month < 10 ? "0" : "") +
      month +
      "-" +
      (day < 10 ? "0" : "") +
      day;
    const clickedDate = event.target.id;
    selectedDate = properDateFormate(clickedDate);
    // var date = properDateFormate(clickedDate);
    // var doctor = selectedService[1]['DoctorName'];

    $.ajax({
      url: "./ajax/stafftime.php",
      method: "GET",
      data: {
        date: clickedDate,
        doctorId: doctorId,
      },
      success: function (response) {
        if (response) {
          var timeArrayWrapper = JSON.parse(response);
          $("#timeList").empty();
          if (Array.isArray(timeArrayWrapper) && timeArrayWrapper.length > 0) {
            var timeArray = timeArrayWrapper;
            $("#timeList").addClass("time-boxes");
            timeArray.forEach(function (time) {
              if (time == "Holiday") {
                $("#timeList").hide();
                $("#timeList3").hide();
                $("#timeList2").removeAttr("style");
                $("#timeList2").text("Holiday");
              } else if (time == "Not available") {
                $("#timeList").hide();
                $("#timeList3").hide();
                $("#timeList2").removeAttr("style");
                $("#timeList2").text("Not available");
              } else {
                $("#timeList").removeAttr("style");
                $("#timeList2").hide();
                $("#timeList3").hide();
                $("#timeList").append(
                  '<div class="time-box next-step" onclick="getTime(this)"><p>' +
                    time +
                    (lang && lang === "en" ? "" : " Uhr") +
                    "</p></div>"
                );
              }
            });
          }
        } else {
          $("#timeList").hide();
          $("#timeList3").hide();
          $("#timeList2").removeAttr("style");
          if (currentFormattedDate == clickedDate) {
            $("#timeList2").text(
              "Bitte planen Sie Ihren Termin mindestens einen Tag im Voraus."
            );
          } else {
            $("#timeList2").text(NoTimeSlot);
          }
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX request failed:", status, error);
      },
    });

    updateSelectedData();
  });

  $(".time-box").click(function () {
    if (selectedDate) {
      var time = $(this).text().trim();

      selectedService.push({ date: selectedDate, time: time });
    }
    updateSelectedData();
  });

  var patientId = "";
  $("#submitButton").click(function () {
    if ($("#userForm").valid()) {
      var name = $("#nameInput").val();
      var birthdate = $("#birthdateInput").val();
      var phone = $("#phoneInput").val();
      var email = $("#emailInput").val();
      var reminderChecked = $("#reminderCheckbox").is(":checked");
      if ($("#userForm").valid()) {
        selectedService.push(name, birthdate, phone, email);
      }
      var selectedServices = selectedService;
      var serviceName = selectedServices[0];
      var doctorName = selectedServices[1]["DoctorName"];
      var selectedDate = selectedServices[2]["date"];
      var time = selectedServices[2]["time"];
      $.ajax({
        url: "./ajax/user.php",
        method: "POST",
        data: {
          name: name,
          birthdate: birthdate,
          phone: phone,
          email: email,
          reminder: reminderChecked,
          serviceName: serviceName,
          doctorName: doctorName,
          doctorId: doctorId,
          time: time,
          selectedDate: selectedDate,
        },

        // dataType: 'json',
        beforeSend: function (response) {
          if (!response.email && !response.phone) {
            $("#loader").show();
          }
        },
        success: function (response) {
          patientId = response.id;
        },
        error: function (xhr, status, error) {
          console.error("AJAX request failed:", status, error);
        },
        complete: function () {
          if ($("#userForm").valid()) {
            $("#loader").hide();
            $("#patientId").html(patientId);
            $("#exampleModal").modal("show");
          }
        },
      });
    }
    updateSelectedData();
  });

  $("#bookingIdModel").click(function () {
    location.reload(true);
  });

  $("#cancelButton").click(function () {
    location.reload(true);
  });

  $("#progressbar-1").click(function () {
    while (selectedService.length > 0) {
      selectedService.pop();
    }
    if (selectedService.length == 0) {
      $(".step-1").show();
    }
    $(".step-2, .step-3, .step-4").hide();
    updateSelectedData();
    currentStep = 1;
  });

  $("#progressbar-2").click(function () {
    if ($(this).hasClass("selected-step")) {
      while (selectedService.length > 1) {
        selectedService.pop();
      }
      if (selectedService.length == 1) {
        $(".step-2").show();
      }
      $(".step-1, .step-3, .step-4").hide();
      updateSelectedData();
      currentStep = 2;
    }
  });
  $("#progressbar-3").click(function () {
    if ($(this).hasClass("selected-step")) {
      while (selectedService.length > 2) {
        selectedService.pop();
      }
      if (selectedService.length == 2) {
        $(".step-3").show();
      }
      $(".step-1, .step-2, .step-4").hide();
      updateSelectedData();
      currentStep = 3;
    }
  });

  // Set "active" class for the initial step circle
  $(".step-circle-" + currentStep).addClass("step-circle-active");

  $("#multi-step-form").find(".step").slice(1).hide();

  $(".next-step").click(function () {
    if (currentStep < 4) {
      $(".step-circle-" + currentStep).addClass("step-circle-active");
      $(".step-" + currentStep).addClass(
        "animate__animated animate__fadeOutLeft"
      );
      currentStep++;
      setTimeout(function () {
        $(".step").removeClass("animate__animated animate__fadeOutLeft").hide();
        $(".step-" + currentStep)
          .show()
          .addClass("animate__animated animate__fadeInRight");
        updateProgressBar();
      }, 500);
    }
  });

  $(".prev-step").click(function () {
    if (currentStep > 1) {
      $(".step-circle-" + currentStep).removeClass("step-circle-active");
      $(".step-" + currentStep).addClass(
        "animate__animated animate__fadeOutRight"
      );

      currentStep--;

      setTimeout(function () {
        $(".step")
          .removeClass("animate__animated animate__fadeOutRight")
          .hide();
        $(".step-" + currentStep)
          .show()
          .addClass("animate__animated animate__fadeInLeft");
        updateProgressBar();
      }, 500);
    }

    setTimeout(function () {
      $(".step").removeClass("animate__animated animate__fadeOutRight").hide();
      $(".step-" + currentStep)
        .show()
        .addClass("animate__animated animate__fadeInLeft");
      updateProgressBar();
    }, 500);
  });

  updateProgressBar = function () {
    var progressPercentage = ((currentStep - 1) / 3) * 100;
    $(".progress-bar").css("width", progressPercentage + "%");
  };
});

var Cal = function (divId) {
  //Store div id
  this.divId = divId;

  // Days of week, starting on Sunday
  this.DaysOfWeek = ["S", "M", "T", "W", "T", "F", "S"];

  // Months, stating on January
  this.Months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];

  // Set the current month, year
  var d = new Date();

  this.currMonth = d.getMonth();
  this.currYear = d.getFullYear();
  this.currDay = d.getDate();
};

// Goes to next month
Cal.prototype.nextMonth = function () {
  if (this.currMonth == 11) {
    this.currMonth = 0;
    this.currYear = this.currYear + 1;
  } else {
    this.currMonth = this.currMonth + 1;
  }
  this.showcurr();
};

// Goes to previous month
Cal.prototype.previousMonth = function () {
  if (this.currMonth == 0) {
    this.currMonth = 11;
    this.currYear = this.currYear - 1;
  } else {
    this.currMonth = this.currMonth - 1;
  }
  this.showcurr();
};
// Show current month
// Cal.prototype.showcurr = function () {
//   this.showMonth(this.currYear, this.currMonth);
// };

Cal.prototype.showMonth = function (y, m) {
  var self = this; // Keep a reference to the current object

  var d = new Date(),
    // First day of the week in the selected month
    firstDayOfMonth = new Date(y, m, 1).getDay(),
    // Last day of the selected month
    lastDateOfMonth = new Date(y, m + 1, 0).getDate(),
    // Last day of the previous month
    lastDayOfLastMonth =
      m === 0 ? new Date(y - 1, 11, 0).getDate() : new Date(y, m, 0).getDate();

  var currentYear = d.getFullYear();
  var currentMonth = d.getMonth();
  var currentDay = d.getDate();

  var html = "<table>";

  // Write selected month and year
  html += '<thead><tr class="month-year">';
  html += '<td colspan="7">' + this.Months[m] + " " + y + "</td>";
  html += "</tr></thead>";

  // Write the header of the days of the week
  html += '<tr class="days">';
  for (var i = 0; i < this.DaysOfWeek.length; i++) {
    html += "<td>" + this.DaysOfWeek[i] + "</td>";
  }
  html += "</tr>";

  // Write the days
  var i = 1;
  do {
    var dow = new Date(y, m, i).getDay();

    // If Sunday, start new row
    if (dow === 0) {
      html += "<tr>";
    }
    // If not Sunday but first day of the month,
    // write the last days from the previous month
    else if (i === 1) {
      html += "<tr>";
      var k = lastDayOfLastMonth - firstDayOfMonth + 1;
      for (var j = 0; j < firstDayOfMonth; j++) {
        html += '<td class="not-current">' + k + "</td>";
        k++;
      }
    }

    // Write the current day in the loop
    if (
      y < currentYear ||
      (y === currentYear && m < currentMonth) ||
      (y === currentYear && m === currentMonth && i < currentDay)
    ) {
      html += '<td class="not-current">' + i + "</td>";
    } else if (y === currentYear && m === currentMonth && i === currentDay) {
      // Make the date clickable and mark it as active when clicked
      html +=
        '<td class="today" onclick="Cal.prototype.dateClicked(' +
        y +
        "," +
        m +
        "," +
        i +
        ')">' +
        i +
        "</td>";
    } else {
      html +=
        '<td onclick="Cal.prototype.dateClicked(' +
        y +
        "," +
        m +
        "," +
        i +
        ')">' +
        i +
        "</td>";
    }

    // If Saturday, close the row
    if (dow === 6) {
      html += "</tr>";
    }
    // If not Saturday, but last day of the selected month,
    // write the next few days from the next month
    else if (i === lastDateOfMonth) {
      var k = 1;
      for (dow; dow < 6; dow++) {
        html += '<td class="not-current">' + k + "</td>";
        k++;
      }
    }

    i++;
  } while (i <= lastDateOfMonth);

  // Close the table
  html += "</table>";

  // Write HTML to the specified div
  document.getElementById(this.divId).innerHTML = html;
};

// Function to handle the click event on a date
Cal.prototype.dateClicked = function (year, month, day) {
  var date = year + "-" + (month + 1) + "-" + day;
};

// On Load of the window
// window.onload = function () {
//   // Start calendar
//   var c = new Cal("divCal");
//   c.showcurr();

//   // Bind next and previous button clicks
//   getId("btnNext").onclick = function () {
//     c.nextMonth();
//   };
//   getId("btnPrev").onclick = function () {
//     c.previousMonth();
//   };
// };

// Get element by id
function getId(id) {
  return document.getElementById(id);
}
