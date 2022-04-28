<!doctype html>
<html lang="en">
<head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title>Game Middle</title>
<style>
.container, .row {
    height: 100%;
    min-height: 100%;
}
html, body {
    height: 100%;
    background: #fbfbfb
}
.middlepage {
    background: #fff;
}
</style>
</head>
<body>
<div class="container">
  <div class="row justify-content-center align-items-center">
    <div class="col-lg-5">
      <div class="middlepage p-4">
        <h3 class="text-center pb-3">Verify your Email and Mobile</h3>
        <div class="main"> 
          <!--- Email Section start--->
          <div class="row email_pane">
            <div class="col-12 pb-3">
              <div>
                <form>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Verify Your Email</label>
                    <input type="email" class="form-control" id="email_otp" aria-describedby="emailHelp" value="{{ $email }}" disabled>
                    <small id="emailHelp" class="form-text text-danger">Enter Valid Email address</small> </div>
                    <a href="#" class="btn btn-primary" id="email_getotp">Get Code</a>
                </form>
              </div>
            </div>
          </div>
          <div class="row email_pane">
            <div class="col-12">
              <div>
                <form>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Enter Email OTP</label>
                    <input type="number" class="form-control" id="email_code" aria-describedby="mobile">
                    <small id="" class="form-text text-danger">OTP Wrong</small> </div>
                  <a href="#" class="btn btn-primary" id="email_submit">Submit</a>
                </form>
              </div>
            </div>
          </div>
          <!--- Email Section end---> 
          <!--- Mobile Section start--->
          <div class="row mobile_pane">
            <div class="col-12 pb-3">
              <div>
                <form>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Verify Your Mobile Number</label>
                    <input type="text" class="form-control" id="mobile_number" value="{{ $phone }}" aria-describedby="mobile" disabled>
                    <small id="" class="form-text text-danger">Enter Valid Mobile Number</small> </div>
                  <a href="#" class="btn btn-primary" id="mobile_getotp">Get Code</a>
                </form>
              </div>
            </div>
          </div>
          <div class="row mobile_pane">
            <div class="col-12">
              <div>
                <form>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Enter Mobile OTP</label>
                    <input type="number" class="form-control" id="mobile_code" aria-describedby="mobile">
                    <small id="" class="form-text text-danger">OTP Wrong</small> </div>
                  <a href="#" class="btn btn-primary" id="mobile_submit">Submit</a>
                </form>
              </div>
            </div>
          </div>
          <!--- Mobile Section end---> 
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Optional JavaScript --> 
<!-- jQuery first, then Popper.js, then Bootstrap JS --> 
<script src="https://code.jquery.com/jquery.min.js"></script> 
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>

<script>
  let email = "{{ $email_status }}";
  let mobile = "{{ $mobile_status }}";
  let kyc = "{{ $kyc_status }}";

  if (email != "verified") $("#emailOtpModal").modal("show");
  else if (mobile != "verified") $("#mobileOtpModal").modal("show");
  else if (kyc != "verified") location.href = "{{ route('securepay.kyc') }}" + "{{ '?user_id=' }}" + "{{ $user_id }}";
  
  if (email == "verified" && mobile == "verified" && kyc != "verified") {
    location.href = "{{ route('kyc') }}" + "{{ '?user_id=' }}" + "{{ $user_id }}";
  } else if (email == "verified") {
    $(".email_pane *").attr("disabled", true);
    $(".email_pane *").addClass("disabled");
  }  else if (mobile == "verified") {
    $(".mobile_pane *").attr("disabled", true);
    $(".mobile_pane *").addClass("disabled");
  }

  $(document).on('click', '#mobile_getotp', function() {
      var mobileRegex = /\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/;
      if (!mobileRegex.test($('#mobile_number').val()))
          $('[for="mobile_numberLbID"]').css("display", "inline");
      else {
          $('#mobile_getotp').addClass("disabled");
          $.ajax({
              headers: {
                  "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
              },
              method: "POST",
              url: "{{ route('securepay.sendMobileOtp') }}",
              data: {
                  wallet_address: "{{ $address }}",
                  mobile_number: "{{ $phone }}",
                  user_id: "{{ $user_id }}"
              },
              success: function(resp) {
                  if (resp.status == "success") {
                      alert("Mobile OTP is successfully sent to the mobile number.");
                      $('[for="mobile_numberLbID"]').css("display", "none");
                  } else {
                      alert("Mobile OTP is failed. Please try again.");
                      $('#mobile_getotp').removeClass("disabled");
                      return;
                  }
              },
          });
      }
  });

  $(document).on('click', '#mobile_submit', function() {
      if ($('input#mobile_code').val() == '')
          $('[for="mobile_codeLbID"]').css("display", "inline");
      else {
          $('#mobile_submit').addClass("disabled");
          $.ajax({
              headers: {
                  "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
              },
              method: "POST",
              url: "{{ route('securepay.submitMobileOtp') }}",
              data: {
                  submit_value: $('#mobile_code').val(),
                  mobile_number: $('#mobile_number').val(),
                  wallet_address: $('#wallet_address').val(),
                  user_id: "{{ $user_id }}"
              },
              success: function(resp) {
                  if (resp.status == "success") {
                      alert("Mobile OTP is successful.");
                      if (kyc != 'verified') {
                      location.href = "{{ route('securepay.kyc') }}" + "{{ '?user_id=' }}" + resp.user_id;
                      } else {
                      // $('.container:first').hide();
                      // $('.container:eq(1)').show();
                      $('#mobileOtpModal').modal('toggle');
                      location.href = "{{ $awayUrl }}";
                      }
                  } else {
                      alert("Mobile OTP is failed. Please try again.");
                      $('#mobile_getotp').removeClass("disabled");
                      $('#mobile_submit').removeClass("disabled");
                      return;
                  }
              },
          });
      }
  });

  $(document).on('click', '#email_getotp', function() {
      emailRegex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      if (!emailRegex.test($("input#email_otp").val()))
          $('[for="email_otpLbID"]').css("display", "inline");
      else {
          $('#email_getotp').addClass("disabled");
          $.ajax({
              headers: {
                  "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
              },
              method: "POST",
              url: "{{ route('securepay.sendEmailOtp') }}",
              data: {
                  email_address: $('#email_otp').val(),
                  user_id: "{{ $user_id }}"
              },
              success: function(resp) {
                  if (resp.status == "success") {
                      // $('#email_getotp').removeClass("disabled");
                      alert("Email OTP is successfully sent. Please wait 1 min.");
                  } else {
                    alert("Sending email OTP is failed. Please try again.");
                    return;
                  }
              },
          });
      }
  });

  $(document).on('click', '#email_submit', function() {
      if ($("input#email_code").val() == "")
          $('[for="email_codeLbID"]').css("display", "inline");
      else {
          $('#email_submit').addClass("disabled");
          $.ajax({
              headers: {
                  "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
              },
              method: "POST",
              url: "{{ route('securepay.submitEmailOtp') }}",
              data: {
                  submit_value: $('#email_code').val(),
                  email_address: $('#email_otp').val(),
                  wallet_address: $('#wallet_address').val(),
                  user_id: "{{ $user_id }}"
              },
              success: function(resp) {
                  if (resp.status == "success") {
                      alert("Email OTP is successful.");
                      $('#emailOtpModal').modal('toggle');
                      if (mobile != "verified") {
                      $('#mobileOtpModal').modal('toggle');
                      } else if (kyc != 'verified') {
                      location.href = "{{ route('securepay.kyc') }}" + "{{ '?user_id=' }}" + resp.user_id;
                      } else {
                      // $('.container:first').hide();
                      // $('.container:eq(1)').show();

                      // redirect to theexternal UPI April 15
                      location.href = "{{ $awayUrl }}";
                      }
                  } else {
                      alert("Email OTP verification is failed. Please try again");
                      $('#email_submit').removeClass("disabled");
                      $('#email_getotp').removeClass("disabled");
                      return;
                  }
              },
          });
      }
  });
</script>
</body>
</html>