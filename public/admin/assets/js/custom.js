const base_url_id = $("#base_url_id").val();

var datavalues_csrf = {},
  csrf_token_hash = $("meta[name=X-CSRF-TOKEN]").attr("content");

datavalues_csrf["csrf_token"] = csrf_token_hash;

$.ajaxSetup({
  data: datavalues_csrf,
  statusCode: {
    401: function () {
      location.reload();
    },
  },
});

$(function () {
  //Alert Popup
  if (document.querySelector(".alert.alertPopup")) {
    $(".alert.alertPopup")
      .delay(4000)
      .slideUp(200, function () {
        $(this).alert("close");
      });
  }

  //Datepicker
  if (document.querySelector(".datepicker")) {
    $(".datepicker").datepicker({
      uiLibrary: "bootstrap5",
      format: "mm/dd/yyyy",
    });
  }
  if (document.querySelector(".datepicker1")) {
    $(".datepicker1").datepicker({
      uiLibrary: "bootstrap5",
      format: "mm/dd/yyyy",
    });
  }

  //Datatable
  if (document.querySelector(".dataTableList")) {
    var t = $(".dataTableList").DataTable({
      lengthChange: true,
      searching: true,
      ordering: true,
      // responsive: true,
      fixedHeader: true,
      stateSave: true,
      pageLength: 10,
      columnDefs: [
        {
          searchable: false,
          orderable: false,
          targets: [0, -1],
        },
      ],
      order: [[0, "asc"]],
    });

    t.on("order.dt search.dt", function () {
      t.column(0, { search: "applied", order: "applied" })
        .nodes()
        .each(function (cell, i) {
          cell.innerHTML = i + 1;
        });
    }).draw();
  }
});

//Generate Code
function generateCode(type, length) {
  var text = "";
  var possible =
    "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

  for (var i = 0; i < length; i++) {
    text += possible.charAt(Math.floor(Math.random() * possible.length));
  }

  $("#" + type).val(text);
}

//To check the number
function isNumberKey(evt) {
  var charCode = evt.which ? evt.which : evt.keyCode;
  if (charCode > 31 && (charCode < 48 || charCode > 57)) return false;
  return true;
}

//Copy link
function copyShareLink(event, element, copyTextElement) {
  var copyText = $(copyTextElement).val();

  document.addEventListener(
    "copy",
    function (e) {
      e.clipboardData.setData("text/plain", copyText);
      e.preventDefault();
    },
    true
  );

  document.execCommand("copy");
  alert("Text copied. Now you can paste into a text message to share.");
}

//This function is used to fetch the emotion overview data
function loadTotalApiCallsByMonth(uri, selectedYr) {
  $("#growthReportId").html(selectedYr);
  $(".growthReportId-div a").removeClass("active");
  $(".growthReportId-div a[data-year='" + selectedYr + "']").addClass("active");

  $.ajax({
    url: uri,
    cache: false,
    type: "POST",
    data: {
      selectedYr,
      csrf_token: csrf_token_hash,
    },
    success: function (response) {
      let result = JSON.parse(response);
      if (result.status == "success") {
        let selectedYr = result.selectedYr;
        let monthNames = result.monthNames;
        let monthData = result.monthData;

        totalRevenueChart.updateOptions({
          series: [
            {
              name: selectedYr,
              data: monthData,
            },
          ],
          xaxis: {
            categories: monthNames,
          },
        });
      }
    },
  });
}

function email_template_preview(id, type) {
  $.ajax({
    type: "POST",
    url: base_url_id + "admin/email_template/get_email_template_preview",
    data: { id, type, csrf_token: csrf_token_hash },
    cache: false,
    success: function (response) {
      if (response) {
        var myFrame = $(".email_template").contents().find("body");
        myFrame.html(response);
      }
    },
  });
}

// This function is used to play the video on click play button. The play button display as a image
function play_video(obj, video_id) {
  var video = $("#" + video_id).get(0);
  if (video.paused) {
    video.play();
  } else {
    video.pause();
  }

  return false;
}

//Country Code
if (document.querySelector(".countryCode-btn")) {
  $(document).on("click", ".countryCode-li", function () {
    let countryCode = $(this).data("val");

    $("#country_code").val(countryCode);
    $(".countryCode-btn").html("+" + countryCode);
    $(".countryCode-li").removeClass("active");
    $(this).addClass("active");
  });
}

//Format US Phone number
if (document.querySelector(".format_phone")) {
  document
    .querySelector(".format_phone")
    .addEventListener("input", function (e) {
      var x = e.target.value
        .replace(/\D/g, "")
        .match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
      e.target.value = !x[2]
        ? x[1]
        : "(" + x[1] + ") " + x[2] + (x[3] ? "-" + x[3] : "");
    });
}

//Add new risk value
if (document.querySelector("#addNewRiskValSpecsForm")) {
  $(document).on("submit", "#addNewRiskValSpecsForm", function () {
    let riskvalspecId = $("#riskvalspecId").val(),
      riskvalspecFrom = $("#riskvalspecFrom").val(),
      riskvalspecTo = $("#riskvalspecTo").val(),
      riskvalspecFinalVal = $("#riskvalspecFinalVal").val();

    $.ajax({
      type: "POST",
      url: base_url_id + "admin/riskscore/addUpdateRiskScore",
      data: {
        id: riskvalspecId,
        min_val: riskvalspecFrom,
        max_val: riskvalspecTo,
        final_val: riskvalspecFinalVal,
        csrf_token: csrf_token_hash,
      },
      cache: false,
      success: function (response) {
        if (response.status == "error") {
          let errors = response.errors;
          if (errors.min_val != "") {
            $("#riskvalspecFrom").addClass("is-invalid");
            $(".riskvalspecFrom-error").html(errors.min_val);
          } else {
            $("#riskvalspecFrom").removeClass("is-invalid");
            $(".riskvalspecFrom-error").html(errors.min_val);
          }

          if (errors.max_val != "") {
            $("#riskvalspecTo").addClass("is-invalid");
            $(".riskvalspecTo-error").html(errors.max_val);
          } else {
            $("#riskvalspecTo").removeClass("is-invalid");
            $(".riskvalspecTo-error").html(errors.max_val);
          }

          if (errors.final_val != "") {
            $("#riskvalspecFinalVal").addClass("is-invalid");
            $(".riskvalspecFinalVal-error").html(errors.final_val);
          } else {
            $("#riskvalspecFinalVal").addClass("is-invalid");
            $(".riskvalspecFinalVal-error").html(errors.final_val);
          }
        } else {
          window.location.reload();
        }
      },
    });
  });
}

//Edit new risk value
if (document.querySelector(".editRiskValSpec")) {
  $(document).on("click", ".editRiskValSpec", function () {
    let id = $(this).data("val");

    $.ajax({
      type: "GET",
      url: base_url_id + "admin/riskscore/edit/" + id,
      cache: false,
      success: function (response) {
        if (response.status == "error") {
          alert("No result found. Please try again.");
          window.location.reload();
        } else {
          let riskscore_data = response.riskscore_data;

          $(".addNewRiskValSpec").click();

          $("#riskvalspecId").val(id);
          $("#riskvalspecFrom").val(riskscore_data[0]["min_val"]);
          $("#riskvalspecTo").val(riskscore_data[0]["max_val"]);
          $("#riskvalspecFinalVal").val(riskscore_data[0]["final_val"]);
        }
      },
    });
  });
}
