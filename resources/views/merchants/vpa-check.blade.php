<!doctype html>
<html lang="en">
<head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="csrf-token" content="{{ csrf_token() }}" />
<!-- Bootstrap CSS -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
<link rel="shortcut icon" href="{{asset('assets/img/fev.png')}}" type="image/x-icon">
<title>Coinclifvoucher</title>
<style>
.mainbg {
    background: #f8f9fc
}
.shadow {
    box-shadow: 0 .15rem 1rem 0 rgba(58,59,69,.05)!important;
}
.middlepage1 {
    background: #fff;
    border-radius: 5px
}
.btn-info {
    background: #354c9e;
    border-color: #354c9e;
}
.btn-info:active, .btn-info:focus, .btn-info:hover {
    background: #354c9e;
    border-color: #354c9e;
}
.btn-info:not(:disabled):not(.disabled).active, .btn-info:not(:disabled):not(.disabled):active, .show>.btn-info.dropdown-toggle {
    background: #354c9e;
    border-color: #354c9e;
}
	
	#loader-4 span {
    display: inline-block;
    width: 20px;
    height: 20px;
    border-radius: 100%;
    background-color: #3498db;
    margin:5px 0px;
    opacity: 0;
}
#loader-4 span:nth-child(1) {
    animation: opacitychange 1s ease-in-out infinite;
}
#loader-4 span:nth-child(2) {
    animation: opacitychange 1s ease-in-out 0.33s infinite;
}
#loader-4 span:nth-child(3) {
    animation: opacitychange 1s ease-in-out 0.66s infinite;
}
@keyframes opacitychange {
0%, 100% {
opacity: 0;
}
60% {
opacity: 1;
}
}
</style>
</head>

<body class="mainbg">

<!--- header end ---->
<div class="container" style="padding-top: 10%;">
  <div class="row justify-content-center align-items-center">
    <div class="col-lg-10">
      <div class="middlepage1 shadow p-4 text-center"> <img src="{{asset('assets/img/upi.png')}}" class="img-fluid" />
        <h3 class="text-center text-dark font-weight-bold">UPI Payment</h3>
        <div class="row justify-content-center align-items-center">
          <div class="col-12 col-md-6">
            <form>
              <div>
                <p class="text-dark">Please Enter Your UPI ID</p>
                <div class="row pb-3">
                  <div class="col-12">
                    <input class="form-control form-control-lg text-center" name="payeraddress" type="text" placeholder="Eg: Yourphonenumber@apl" required>
                  </div>
                </div>
              </div>
              <div class="form-group mb-0 pb-0">
                <input type="button" class="btnSubmit btn btn-info" value="Verify & Proceed" />
              </div>
				      <div class="form-group mb-0 pb-0">
                <div class="loader m-auto" id="loader-4"> <span></span> <span></span> <span></span> </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!--- features end ----> 

<!-- Optional JavaScript --> 
<!-- jQuery first, then Popper.js, then Bootstrap JS --> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script> 
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<script>
  alert("sdfsdf");
	$(document).ready(function() {
		$(document).on('click', '.btnSubmit', function() { alert("OK")
      $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        method: "post",
        url: "{{ route('check-vpa') }}",
        data: {
            payer_address: $('input[name="payeraddress"]').val(),
        },
        success: function (resp) {
          console.log(resp);
        },
      });
    });
	});
	</script> 
{{-- <script src="js/nav.js"></script> --}}
</body>
</html>