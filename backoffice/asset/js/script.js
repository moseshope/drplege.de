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
        // minlength: 6,
      },
    },
    messages: {
      email: {
        required: "Bitte geben Sie Ihre E-Mail Adresse ein",
        email: "Bitte geben Sie eine gültige E-Mail Adresse ein",
      },
      password: {
        required: "Bitte geben Sie Ihr Passwort ein",
        // minlength: "Ihr Passwort muss mindestens 6 Zeichen lang sein",
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
  $.validator.addMethod(
    "validatePhone",
    function (value, element) {
      return this.optional(element) || /^(?:\+?\s?)?\d{10,14}$/.test(value);
    },
    "Bitte geben Sie eine gültige Telefonnummer ein."
  );

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
      password: {
        required: true,
      },
      confirm_password: {
        required: true,
        equalTo: "#password",
      },
      telephone: {
        required: true,
        validatePhone: true,
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
      password: {
        required: "Das Passwort ist erforderlich.",
      },
      confirm_password: {
        required: "Bitte geben Sie Ihr Kennwort ein",
        equalTo: "Passwörter stimmen nicht überein",
      },
      telephone: {
        required: "Telefonnummer ist erforderlich.",
        validatePhone: "Bitte geben Sie eine gültige Telefonnummer ein.",
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
        validatePhone: true,
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
        validatePhone: "Bitte geben Sie eine gültige Telefonnummer ein.",
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
        $("#E-PatientsId").val(patientsData.id);
        $("#E-PatientsName").html(patientsData.name);
        $("#E-patientsEmail").html(patientsData.email);
        $("#E-patientsTelephone").html(patientsData.telephone);
        $("#doctorSelect").val(patientsData.doctor);
        $("#time").val(patientsData.visits);

        // Show the edit-patients modal
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
    $("#ConfirmationDelete").modal("show");
    $.ajax({
      url: "./ajax/deletepatients.php",
      method: "GET",
      data: { id: id },
      beforeSend: function (response) {
        $("#loader").show();
      },
      success: function (response) {
        if (response) {
          $("#loader").hide();
          var patientsData = JSON.parse(response);
          $("#deletePatientsId").val(patientsData);
        }
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      },
    });
  });

  // profile form validation
  $("#profileForm").validate({
    rules: {
      current_password: {
        required: true,
        minlength: 6,
      },
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
        minlength: "Your password must be at least 6 characters long",
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
  // add service
  $("#ShowServicesBtn").on("click", function () {
    $("#show-services").modal("show");
  });

  $("#servicesAddBtn").on("click", function () {
    if ($("#ServiceForm").valid()) {
      var service = $("#add").val();
      var serviceID = $("#serviceId").val();
      $.ajax({
        url: "./ajax/servicevalidation.php",
        method: "POST",
        data: {
          add_service: service,
          service_id: serviceID,
        },
        success: function (response) {
          if (response) {
            var responseData = JSON.parse(response);
            $("#service-error").text(responseData).addClass("text-danger");
          } else {
            $("#service-error").hide();
            $("#show-services").modal("hide");
            $("#Confirmation").modal("show");
          }
        },
        error: function (xhr, status, error) {
          console.error("Error:", error);
        },
      });
    }
  });

  $("#ConfirmationYesBtn").on("click", function () {
    $("#show-info").modal("show");
    $("#Confirmation").modal("hide");
  });
  // add staff
  $("#addStaffBtn").on("click", function () {
    if ($("#AddStaff").valid()) {
      var name = $("#Name").val();
      var email = $("#email").val();
      var telephone = $("#Telephone").val();
      var password = $("#password").val();
      var confirm_password = $("#confirm_password").val();
      $.ajax({
        url: "./ajax/addstaff_validation.php",
        method: "POST",
        data: {
          name: name,
          email: email,
          telephone: telephone,
          password: password,
          confirm_password: confirm_password,
        },
        success: function (response) {
          if (response) {
            var jsonObjects = response.split("}{");
            var responseData = JSON.parse(jsonObjects);
            if (responseData.name) {
              $("#name-error").text(responseData.name).addClass("text-danger");
            } else {
              $("#name-error").hide();
            }
            if (responseData.email) {
              $("#email-error")
                .text(responseData.email)
                .addClass("text-danger");
            } else {
              $("#email-error").hide();
            }
            if (responseData.telephone) {
              $("#telephone-error")
                .text(responseData.telephone)
                .addClass("text-danger");
            } else {
              $("#telephone-error").hide();
            }
            if (responseData.password) {
              $("#password-error")
                .text(responseData.password)
                .addClass("text-danger");
            } else {
              $("#password-error").hide();
            }
            if (responseData.confirm_password) {
              $("#confirm_password-error")
                .text(responseData.confirm_password)
                .addClass("text-danger");
            } else {
              $("#confirm_password-error").hide();
            }
          } else {
            $("#add-staff").modal("hide");
            // $("#StaffConfirmation").modal("show");
          }
        },
        error: function (xhr, status, error) {
          console.error("Error:", error);
        },
      });
    }
  });
  // $("#StaffConfirmationYesBtn").on("click", function () {
  //   $("#staffShowInfo").modal("show");
  //   $("#StaffConfirmation").modal("hide");
  // });
  // $("#StaffShowInfoBtn").on("click", function () {
  //   $("#staffShowInfo").modal("hide");
  // });

  // Add Nurse

  $("#AddNurse").validate({
    rules: {
      name: {
        required: true,
      },
      email: {
        required: true,
        email: true,
      },
      password: {
        required: true,
      },
      confirm_password: {
        required: true,
        equalTo: "#password",
      },
      status: {
        required: true,
      },
    },
    messages: {
      name: {
        required: "Name ist erforderlich.",
      },
      email: {
        required: "E-Mail ist erforderlich.",
        email: "Bitte geben Sie eine gültige E-Mail-Adresse ein.",
      },
      password: {
        required: "Das Passwort ist erforderlich.",
      },
      confirm_password: {
        required: "Bitte geben Sie Ihr Kennwort ein",
        equalTo: "Passwörter stimmen nicht überein.",
      },
      status: {
        required: "Status ist erforderlich.",
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

  $("#addNurseBtn").on("click", function () {
    if ($("#AddNurse").valid()) {
      var name = $("#Name").val();
      var email = $("#email").val();
      var password = $("#password").val();
      var confirm_password = $("#confirm_password").val();
      $.ajax({
        url: "./ajax/addnurse_validation.php",
        method: "POST",
        data: {
          name: name,
          email: email,
          password: password,
          confirm_password: confirm_password,
        },
        success: function (response) {
          if (response) {
            var jsonObjects = response.split("}{");
            var responseData = JSON.parse(jsonObjects);
            if (responseData.name) {
              $("#name-error").text(responseData.name).addClass("text-danger");
            } else {
              $("#name-error").hide();
            }

            if (responseData.email) {
              $("#email-error")
                .text(responseData.email)
                .addClass("text-danger");
            } else {
              $("#email-error").hide();
            }

            if (responseData.password) {
              $("#password-error")
                .text(responseData.password)
                .addClass("text-danger");
            } else {
              $("#password-error").hide();
            }
            if (responseData.confirm_password) {
              $("#confirm_password-error")
                .text(responseData.confirm_password)
                .addClass("text-danger");
            } else {
              $("#confirm_password-error").hide();
            }
          } else {
            $("#add-nurse").modal("hide");
            // $("#NurseConfirmation").modal("show");
          }
        },
        error: function (xhr, status, error) {
          console.error("Error:", error);
        },
      });
    }
  });

  // Confirm Add

  $("#NurseConfirmationYesBtn").on("click", function () {
    $("#NurseConfirmation").modal("hide");
    $("#nurseShowInfo").modal("show");
  });
  $("#NurseShowInfoBtn").on("click", function () {
    $("#NurseConfirmation").modal("hide");
  });
  // Edit Nurse validation
  $("#Editnurse").validate({
    rules: {
      name: {
        required: true,
      },
      email: {
        required: true,
        email: true,
      },
      status: {
        required: true,
      },
    },
    messages: {
      name: {
        required: "Name ist erforderlich.",
      },
      email: {
        required: "E-Mail ist erforderlich.",
        email: "Bitte geben Sie eine gültige E-Mail-Adresse ein.",
      },
      status: {
        required: "Status ist erforderlich.",
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

  // Edit Nurse Modal
  // set value edit employee form
  $(".editNurseButton").on("click", function () {
    var id = $(this).data("id");
    $.ajax({
      url: "./ajax/nurse.php",
      method: "POST",
      data: { id: id },
      success: function (response) {
        var NurseData = JSON.parse(response);
        $("#NurseID").val(NurseData.id);
        $("#NurseName").val(NurseData.name);
        $("#NurseEmail").val(NurseData.email);
        $("#NurseStatus").val(NurseData.status);
        $("#edit-nurse").modal("show");
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      },
    });
  });

  // Edit Nurse Validation

  $("#UpdateNurseBtn").on("click", function () {
    if ($("#Editnurse").valid()) {
      var id = $("#NurseID").val();
      var name = $("#NurseName").val();
      var email = $("#NurseEmail").val();
      $.ajax({
        url: "./ajax/editnurse_validation.php",
        method: "POST",
        data: {
          id: id,
          name: name,
          email: email,
        },
        success: function (response) {
          if (response) {
            var jsonObjects = response.split("}{");
            var responseData = JSON.parse(jsonObjects);
            if (responseData.name) {
              $("#name-edit-error")
                .text(responseData.name)
                .addClass("text-danger");
            } else {
              $("#name-edit-error").hide();
            }
            if (responseData.email) {
              $("#email-edit-error")
                .text(responseData.email)
                .addClass("text-danger");
            } else {
              $("#email-edit-error").hide();
            }
          } else {
            // $("#EditNurseConfirm").modal("show");
            $("#edit-nurse").modal("hide");
          }
        },
        error: function (xhr, status, error) {
          console.error("Error:", error);
        },
      });
    }
  });

  // Delete Nurse

  $(".deleteNurseButton").on("click", function () {
    var id = $(this).data("id");
    $.ajax({
      url: "./ajax/deletenurse.php",
      method: "GET",
      data: { id: id },
      success: function (response) {
        var nurseData = JSON.parse(response);
        $("#deleteNurseId").val(nurseData);
        // $("#deleteNurseConfirmation").modal("show");
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      },
    });
  });

  //   update staff
  $("#UpdateStaffBtn").on("click", function () {
    if ($("#EditStaff").valid()) {
      var id = $("#StaffId").val();
      var name = $("#StaffName").val();
      var email = $("#StaffEmail").val();
      var telephone = $("#StaffTelephone").val();
      $.ajax({
        url: "./ajax/editstaff_validation.php",
        method: "POST",
        data: {
          id: id,
          name: name,
          email: email,
          telephone: telephone,
        },
        success: function (response) {
          if (response) {
            var jsonObjects = response.split("}{");
            var responseData = JSON.parse(jsonObjects);
            if (responseData.name) {
              $("#name-edit-error")
                .text(responseData.name)
                .addClass("text-danger");
            } else {
              $("#name-edit-error").hide();
            }
            if (responseData.email) {
              $("#email-edit-error")
                .text(responseData.email)
                .addClass("text-danger");
            } else {
              $("#email-edit-error").hide();
            }
            if (responseData.telephone) {
              $("#telephone-edit-error")
                .text(responseData.telephone)
                .addClass("text-danger");
            } else {
              $("#telephone-edit-error").hide();
            }
          } else {
            $("#edit-staff").modal("hide");
            // $("#EditStaffConfirmation").modal("show");
          }
        },
        error: function (xhr, status, error) {
          console.error("Error:", error);
        },
      });
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
    if ($("#EditPatients").valid()) {
      $("#edit-patients").modal("hide");
      $("#EditConfirmation").modal("show");
    }
  });
  $("#EditConfirmationYesBtn").on("click", function () {
    $("#EditConfirmation").modal("hide");
    $("#E-edit-show-info").modal("show");
  });

  // delete patients
  $("#deleteConformation").on("click", function () {
    $("#ConfirmationDelete").modal("hide");
    $("#show-info-delete").modal("show");
  });

  // update profile
  $("#profileSubmit").on("click", function () {
    if ($("#profileForm").valid()) {
      var currentPassword = $("#current_password").val();
      $.ajax({
        url: "./ajax/profile.php",
        method: "POST",
        data: { currentPassword: currentPassword },
        success: function (response) {
          if (response) {
            var responseData = JSON.parse(response);
            $("#current_password-error")
              .text(responseData)
              .addClass("text-danger");
          } else {
            $("#current_password-error").hide();
            $("#Confirmation").modal("show");
          }
        },
        error: function (xhr, status, error) {
          console.error("Error:", error);
        },
      });
    }
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
        // console.log(staffData);
        $("#StaffId").val(staffData.id);
        $("#StaffName").val(staffData.name);
        $("#StaffEmail").val(staffData.email);
        $("#StaffTelephone").val(staffData.telephone);
        $("#StaffStatus-Options-E").val(staffData.status);
        // $("#Services-Options-E").val(staffData.services);
        $(`input[type=checkbox]`).prop("checked", false);
        staffData.services?.forEach((service) => {
          $(`input[type=checkbox][value='${service}']`).prop("checked", true);
        });
        // Assuming staffData.profile is defined somewhere
        if (staffData.profile) {
          $("#image-preview-E").attr("src", `https://drpleger.de/termin-buchen/images/${staffData.profile}`);
          $('#open-image-picker-E').text('Bild ändern');
        } else {
          $('#open-image-picker-E').text('Bild hochladen')
          // If staffData.profile does not exist, set the src attribute to an empty string
          $("#image-preview-E").attr("src", `https://drpleger.de/termin-buchen/images/logo.png`);
        }
       
        $("#edit-staff").modal("show");

        // updateCheckboxForServices(staffData.services);
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      },
    });
  });

  function updateCheckboxForServices(selectedServices) {
    $(".form-check-input").each(function () {
      var service = $(this).val();
      if (selectedServices.includes(service)) {
        $(this).prop("checked", true);
      } else {
        $(this).prop("checked", false);
      }
    });
  }

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
        // $("#Confirmation").modal("show");
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      },
    });
  });

  // Services Form validation
  // Add Services validation
  var validator = $("#AddServices").validate({
    rules: {
      servicesEnglish: {
        required: true,
      },
      serviceGermany: {
        required: true,
      },
    },
    messages: {
      servicesEnglish: {
        required: "Der Name des Dienstes ist erforderlich (Englisch).",
      },
      serviceGermany: {
        required: "Der Name des Dienstes ist erforderlich (Deutschland).",
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

  // Clear Button

  $("#cancelSericesBtn").on("click", function () {
    $("#AddServices").trigger("reset");
    $("#serviceGermany-error").empty();
    $("#servicesEnglish-error").empty();
    validator.resetForm();
  });

  // Add Services Duplication and serverside validation

  $("#addServicesBtn").on("click", function () {
    $("#serviceGermany-error").text("").hide();
    $("#servicesEnglish-error").text("").hide();
    if ($("#AddServices").valid()) {
      var services = $("#serviceGermany").val();
      var services_en = $("#servicesEnglish").val();
      $.ajax({
        url: "./ajax/servicesvalidation.php",
        method: "POST",
        data: { services: services, services_en: services_en },
        success: function (response) {
          if (response) {
            var responseData = JSON.parse(response);
            if (responseData.germany) {
              $("#serviceGermany-error")
                .text(responseData.germany)
                .addClass("text-danger")
                .show();
            } else {
              $("#serviceGermany-error").text("").hide();
            }
            if (responseData.english) {
              $("#servicesEnglish-error")
                .text(responseData.english)
                .addClass("text-danger")
                .show();
            } else {
              $("#servicesEnglish-error").text("").hide();
            }
          } else {
            $("#add-services").modal("hide");
            $("#Confirmation").modal("show");
          }
        },
        error: function (xhr, status, error) {
          console.error("Error:", error);
        },
      });
    }
  });

  $("#ServicesShowInfoBtn").click(function () {
    $("#AddServices").trigger("reset");
    validator.resetForm();
    location.reload();
  });

  $("#ConfirmationNoBtn").click(function () {
    $("#AddServices").trigger("reset");
    validator.resetForm();
    location.reload();
  });

  // Confirm Messages

  $("#ConfirmationYesBtn").on("click", function () {
    var services = $("#serviceGermany").val();
    var services_en = $("#servicesEnglish").val();
    $.ajax({
      url: "./ajax/addservices.php",
      method: "POST",
      data: { services: services, services_en: services_en },
      success: function (response) {
        $("#ShowInfo").modal("show");
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      },
    });
  });

  // Edit Services validation

  var validatorEdit = $("#EditServices").validate({
    rules: {
      editEnglish: {
        required: true,
      },
      editGermany: {
        required: true,
      },
    },
    messages: {
      editEnglish: {
        required: "Der Name des Dienstes ist erforderlich (Englisch).",
      },
      editGermany: {
        required: "Der Name des Dienstes ist erforderlich (Deutschland).",
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

  // Delete Get ID
  $(".deleteservices").on("click", function (e) {
    var id = $(this).attr("data-id");
    $("#deleteid").val(id);
  });

  //  Delete Query Services
  $("#deleteConfirmationYesBtn").on("click", function () {
    var id = $("#deleteid").val();
    $.ajax({
      url: "./ajax/deleteservices.php",
      method: "POST",
      data: {
        id: id,
      },
      success: function (response) {
        $("#deletedConfirmation").modal("hide");
        // $("#deleteSlotShowInfo").modal("show");
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      },
    });
  });

  $("#ServicesShow").on("click", function () {
    location.reload();
  });

  // Get Data Services
  $(".editservices").on("click", function () {
    var servicesId = $(this).attr("data-id");
    $.ajax({
      url: "./ajax/editservices.php",
      method: "GET",
      data: {
        servicesId: servicesId,
      },
      success: function (response) {
        var responseData = JSON.parse(response);
        $("#editId").val(responseData.serviceId);
        $("#editGermany").val(responseData.germany);
        $("#editEnglish").val(responseData.english);
        // $('#edit-slot').modal('show');
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      },
    });
  });

  // Edit Services
  $("#updateServicesBtn").on("click", function () {
    $("#editGermany-error").text("").hide();
    $("#editEnglish-error").text("").hide();
    if ($("#EditServices").valid()) {
      var services = $("#editGermany").val();
      var services_en = $("#editEnglish").val();
      var servicesId = $("#editId").val();
      $.ajax({
        url: "./ajax/editservicesvalidation.php",
        method: "POST",
        data: {
          servicesId: servicesId,
          services: services,
          services_en: services_en,
        },
        success: function (response) {
          if (response) {
            var responseData = JSON.parse(response);
            if (responseData.germany) {
              $("#editGermany-error")
                .text(responseData.germany)
                .addClass("text-danger")
                .show();
            } else {
              $("#editGermany-error").text("").hide();
            }
            if (responseData.english) {
              $("#editEnglish-error")
                .text(responseData.english)
                .addClass("text-danger")
                .show();
            } else {
              $("#editEnglish-error").text("").hide();
            }
          } else {
            $("#edit-services").modal("hide");
            $("#EditSlotConfirmation").modal("show");
          }
        },
        error: function (xhr, status, error) {
          console.error("Error:", error);
        },
      });
    }
  });

  $("#cancelEdit").on("click", function () {
    validatorEdit.resetForm();
    location.reload();
    $("#EditServices").trigger("reset");
  });

  $("#EditConfirmationYesBtn").on("click", function () {
    var services = $("#editGermany").val();
    var services_en = $("#editEnglish").val();
    var servicesId = $("#editId").val();

    $.ajax({
      url: "./ajax/updateservices.php",
      method: "POST",
      data: {
        servicesId: servicesId,
        services: services,
        services_en: services_en,
      },
      success: function (response) {
        $("#EditSlotConfirmation").modal("hide");
        $("#editSlotShowInfo").modal("show");
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      },
    });
  });
});
