$(document).ready(function () {
  $("#ServiceForm").validate({
    rules: {
      services: {
        required: true,
      },
    },
    messages: {
      services: {
        required: "Services is required.",
      },
    },
    errorPlacement: function (error, element) {
      if (element.attr("name") == "services") {
        error.insertAfter("#service-error");
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

  // login form validation
  $("#loginForm").validate({
    rules: {
      email: {
        required: true,
        email: true,
      },
      password: {
        required: true,
      },
    },
    messages: {
      email: {
        required: "Bitte geben Sie Ihre E-Mail-Adresse ein",
        email: "Bitte geben Sie eine gültige E-Mail Adresse ein",
      },
      password: {
        required: "Bitte geben Sie Ihr Kennwort ein",
      },
    },
    errorPlacement: function (error, element) {
      if (element.attr("name") == "email") {
        error.insertAfter("#email-error");
      } else if (element.attr("name") == "password") {
        error.insertAfter("#password-error");
      }
      error.addClass("text-danger");
    },
    highlight: function (element) {
      $(element).siblings(".error").addClass("text-danger");
    },
    unhighlight: function (element) {
      $(element).siblings(".error").removeClass("text-danger");
    },
    submitHandler: function (form) {
      form.submit();
    },
  });

  // add staff or employees validation
  $("#AddStaff").validate({
    rules: {
      name: {
        required: true,
      },
      qualification: {
        required: true,
      },
      email: {
        required: true,
        email: true,
      },
      telephone: {
        required: true,
        digits: true,
        minlength: 10, // Assuming 10-digit phone number
      },
      status: {
        required: true,
      },
      staff_services: {
        required: true,
      },
    },
    messages: {
      name: {
        required: "Name ist erforderlich.",
      },
      qualification: {
        required: "Qualifikation ist erforderlich.",
      },
      email: {
        required: "E-Mail ist erforderlich.",
        email: "Bitte geben Sie eine gültige E-Mail Adresse ein.",
      },
      telephone: {
        required: "Telefonnummer ist erforderlich.",
        digits: "Nur Zahlen sind erlaubt.",
        minlength: "Kontakt sollte eine 10-stellige Nummer sein.",
      },
      status: {
        required: "Status ist erforderlich.",
      },
      staff_services: {
        required: "Dienstleistungen ist erforderlich.",
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

  // edit employess validation
  $("#EditStaff").validate({
    rules: {
      name: {
        required: true,
      },
      qualification: {
        required: true,
      },
      email: {
        required: true,
        email: true,
      },
      telephone: {
        required: true,
        digits: true,
        minlength: 10, // Assuming 10-digit phone number
      },
      status: {
        required: true,
      },
      staff_services: {
        required: true,
      },
    },
    messages: {
      name: {
        required: "Name ist erforderlich.",
      },
      qualification: {
        required: "Qualifikation ist erforderlich.",
      },
      email: {
        required: "E-Mail ist erforderlich.",
        email: "Bitte geben Sie eine gültige E-Mail Adresse ein.",
      },
      telephone: {
        required: "Telefonnummer ist erforderlich.",
        digits: "Nur Zahlen sind erlaubt.",
        minlength: "Kontakt sollte eine 10-stellige Nummer sein.",
      },
      status: {
        required: "Status ist erforderlich.",
      },
      staff_services: {
        required: "Dienstleistungen ist erforderlich.",
      },
    },
    errorPlacement: function (error, element) {
      var fieldName = $(element).attr("name");
      error.insertAfter("#" + fieldName + "-edit-error");
      error.addClass("text-danger");
    },
    highlight: function (element) {
      $(element).siblings(".error").addClass("text-danger");
    },
    unhighlight: function (element) {
      $(element).siblings(".error").removeClass("text-danger");
    },
  });

  // edit patients
  $(".patientsEditButton").on("click", function () {
    var id = $(this).data("id");
    $.ajax({
      url: "./ajax/patients.php",
      method: "POST",
      data: { id: id },
      success: function (response) {
        var patientsData = JSON.parse(response);
        $("#PatientsId").val(patientsData.id);
        $("#PatientsName").html(patientsData.name);
        $("#patientsEmail").html(patientsData.email);
        $("#patientsTelephone").html(patientsData.telephone);
        $("#doctor-input").val(patientsData.doctor);
        $("#time").val(patientsData.visits);
        $("#edit-patients").modal("show");
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      },
    });
  });

  // show multiple service
  // $('.showAllBtn').on('click', function() {
  //     var id = $(this).data('id');
  //     console.log(id);

  //     // $.ajax({
  //     //     url: './ajax/patients.php',
  //     //     method: 'POST',
  //     //     data: { id: id },
  //     //     success: function(response) {
  //     //         var patientsData = JSON.parse(response);
  //     //         console.log(patientsData.visits)
  //     //         $('#PatientsId').val(patientsData.id);
  //     //         $('#PatientsName').html(patientsData.name);
  //     //         $('#patientsEmail').html(patientsData.email);
  //     //         $('#patientsTelephone').html(patientsData.telephone);
  //     //         $('#doctor-input').val(patientsData.doctor);
  //     //         $('#time').val(patientsData.visits);
  //     //         $('#edit-patients').modal('show');
  //     //     },
  //     //     error: function(xhr, status, error) {
  //     //         console.error('Error:', error);
  //     //     }

  //     // });

  // });

  // delete patients
  $(".patientsDeleteButton").on("click", function () {
    var id = $(this).data("id");
    $.ajax({
      url: "./ajax/deletepatients.php",
      method: "GET",
      data: { id: id },
      success: function (response) {
        var patientsData = JSON.parse(response);
        $("#deletePatientsId").val(patientsData);
        $("#Confirmation").modal("show");
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      },
    });
  });
  $("#profileSubmitDoctor").on("click", function () {
    var currentPassword = $("#current_password").val();
    if(currentPassword !== ''){
  // profile form validation
  $("#profileForm").validate({
    rules: {
      // current_password: {
      //   required: true,
      //   minlength: 6,
      // },
      password: {
        required: true,
        minlength: 6,
      },
      confirm_password: {
        required: true,
        minlength: 6,
        equalTo: "#password", // Validation to ensure it matches the password field
      },
    },
    messages: {
      current_password: {
        required: "Bitte geben Sie Ihr Passwort ein",
        // minlength: "Ihr Passwort muss mindestens 6 Zeichen lang sein",
        equalTo:"Aktuelles Passwort ungültig."
      },
      password: {
        required: "Bitte geben Sie Ihr Passwort ein",
        minlength: "Ihr Passwort muss mindestens 6 Zeichen lang sein",
      },
      confirm_password: {
        required: "Bitte geben Sie Ihr Passwort ein",
        minlength: "Ihr Passwort muss mindestens 6 Zeichen lang sein",
        equalTo: "Passwörter stimmen nicht überein",
      },
    },
    errorPlacement: function (error, element) {
      if (element.attr("name") == "current_password") {
        error.insertAfter("#current_password-error");
      } else if (element.attr("name") == "password") {
        error.insertAfter("#password-error");
      } else if (element.attr("name") == "confirm_password") {
        error.insertAfter("#confirm_password-error");
      }
      error.addClass("text-danger");
    },
    highlight: function (element) {
      $(element).siblings(".error").addClass("text-danger");
    },
    unhighlight: function (element) {
      $(element).siblings(".error").removeClass("text-danger");
    },
    submitHandler: function (form) {
      form.submit();
    },
  });
    }
  })
  // add service
  $("#ShowServicesBtn").on("click", function () {
    $("#show-services").modal("show");
  });

  $("#servicesAddBtn").on("click", function () {
    if ($("#ServiceForm").valid()) {
      $("#show-services").modal("hide");
      $("#Confirmation").modal("show");
    }
  });

  $("#ConfirmationYesBtn").on("click", function () {
    $("#show-info").modal("show");
    $("#Confirmation").modal("hide");
  });
  // add staff
  $("#addStaffBtn").on("click", function () {
    if ($("#AddStaff").valid()) {
      $("#add-staff").modal("hide");
      $("#StaffConfirmation").modal("show");
    }
  });
  $("#StaffConfirmationYesBtn").on("click", function () {
    $("#staffShowInfo").modal("show");
    $("#StaffConfirmation").modal("hide");
  });
  $("#StaffShowInfoBtn").on("click", function () {
    $("#staffShowInfo").modal("hide");
  });

  //   update staff
  $("#UpdateStaffBtn").on("click", function () {
    if ($("#EditStaff").valid()) {
      $("#edit-staff").modal("hide");
      $("#EditStaffConfirmation").modal("show");
    }
  });
  $("#UpdateStaffConfirmationBtn").on("click", function () {
    $("#edit-show-info").modal("show");
    $("#EditStaffConfirmation").modal("hide");
  });

  // delete staff
  $("#deleteStaffYesBtn").on("click", function () {
    $("#Confirmation").modal("hide");
    $("#show-info").modal("show");
  });

  // update patients
  $("#UpdatePatients").on("click", function () {
    $("#edit-patients").modal("hide");
    $("#EditConfirmation").modal("show");
  });
  $("#EditConfirmationYesBtn").on("click", function () {
    $("#EditConfirmation").modal("hide");
    $("#edit-show-info").modal("show");
  });

  // delete patients
  $("#deleteConformation").on("click", function () {
    $("#Confirmation").modal("hide");
    $("#show-info").modal("show");
  });

  // update profile
  $("#profileSubmitDoctor").on("click", function () {
    if ($("#profileForm").valid()) {
      var currentPassword = $("#current_password").val();
      $.ajax({
        url: "./ajax/profile.php",
        method: "POST",
        data: { currentPassword: currentPassword },
        success: function (response) {
          if (response) {
            var responseData = JSON.parse(response);
            $("#currentpassword-error").text(responseData).addClass("text-danger");
          } else {
            $("#currentpassword-error").hide();
            $("#Confirmation").modal("show");
          }
        },
        error: function (xhr, status, error) {
          console.error("Error:", error);
        },
      });
    }
    // if ($('#profileForm').valid()) {
    //     $('#Confirmation').modal('show');
    // }
  });

  // set value edit employee form
  $(".editButton").on("click", function () {
    var id = $(this).data("id");
    $.ajax({
      url: "./ajax/staff.php",
      method: "POST",
      data: { id: id },
      success: function (response) {
        var staffData = JSON.parse(response);
        $("#StaffId").val(staffData.id);
        $("#StaffName").val(staffData.name);
        $("#StaffEmail").val(staffData.email);
        $("#StaffTelephone").val(staffData.telephone);
        $("#StaffStatus-Options-E").val(staffData.status);
        $("#StaffServices-Options-E").val(staffData.services);
        $("#edit-staff").modal("show");
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      },
    });
  });

  // delete employee
  $(".deleteButton").on("click", function () {
    var id = $(this).data("id");
    $.ajax({
      url: "./ajax/deletestaff.php",
      method: "GET",
      data: { id: id },
      success: function (response) {
        var staffData = JSON.parse(response);
        $("#deleteStaffId").val(staffData);
        $("#Confirmation").modal("show");
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      },
    });
  });
});
