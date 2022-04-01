<!doctype html>
<html lang="en">
<head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!-- Bootstrap CSS -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
<link rel="shortcut icon" href="<?php echo e(asset('assets/img/fev.png')); ?>" type="image/x-icon">
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
.box3 {
    border: solid 1px #f4f5f9;
}
#circle {
    transform: translate(-50%, -50%);
    width: 40px;
    height: 40px;
}
.loader {
    width: calc(100% - 0px);
    height: calc(100% - 0px);
    border: 5px solid #f4f5f9;
    border-top: 5px solid #30FF06;
    border-radius: 50%;
    animation: rotate 1.5s linear infinite;
}
@keyframes  rotate {
100% {
transform: rotate(360deg);
}
}
	
	@media (max-width:768px) {
		.middlepage1 p { font-size:14px; line-height:18px;}
		.middlepage1 h3 { font-size: 20px;}
		.middlepage1 .mainimg img { width: 100px;}
	}
}
</style>
</head>

<body class="mainbg">

<!--- header end ---->
<div class="container" style="padding-top: 10%;">
  <div class="row justify-content-center align-items-center">
    <div class="col-lg-10">
      <div class="middlepage1 shadow p-4">
        <div class="alert-info p-3 rounded"> <img src="<?php echo e(asset('assets/img/help.png')); ?>" width="25" class="float-left mr-3 mt-2" />
          <p class="pb-0 mb-0">Note: Please do not press back button or close the screen until the payment is complete.</p>
        </div>
        <div class="text-center mainimg">
          <h3 class="pt-3">Complete your payment</h3>
          <img src="<?php echo e(asset('assets/img/complete.png')); ?>" class="mb-4" /> </div>
        <div class="row mb-4 d-flex">
          <div class="col-12 col-md-4">
            <div class="text-center box3 p-3 p-md-4 h-100"> <img src="<?php echo e(asset('assets/img/icon1.png')); ?>" class="mb-2 float-left float-md-none mr-2 mr-md-0" />
              <p class="pb-0 mb-0">Note: Go to UPI ID linked mobile app or Click on the notification from your UPI ID linked mobile app</p>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="text-center box3 p-3 p-md-4 h-100"> <img src="<?php echo e(asset('assets/img/icon2.png')); ?>" class="mb-2 float-left float-md-none mr-2 mr-md-0" />
              <p class="pb-0 mb-0">Note: Check pending transactions</p>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="text-center box3 p-3 p-md-4 h-100"> <img src="<?php echo e(asset('assets/img/icon3.png')); ?>" class="mb-2 float-left float-md-none mr-2 mr-md-0" />
              <p class="pb-0 mb-0">Note: Complete the payment by selecting the bank and enter UPI PIN</p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="box3 rounded p-3"> <img src="<?php echo e(asset('assets/img/help.png')); ?>" width="30" class="float-left mr-3 mt-2" />
              <p class="pb-0 mb-0">Note: If you have entered the UPI ID friends or family, they will need to authorize the payment from their UPI App.</p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="text-center">
              <p class="pt-3 pb-3 text-info">Your Session automatically will Expire in : 7 mins</p>
              <div id="circle" class="m-auto">
                <div class="loader"> </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!--- features end ----> 

<!-- Optional JavaScript --> 
<!-- jQuery first, then Popper.js, then Bootstrap JS --> 
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script> 
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script> 
<script>
	$(document).ready(function() {
		$('.leftmenutrigger').on('click', function(e) {
			$('.side-nav').toggleClass("open");
			e.preventDefault();
		});
	});
	</script> 
<script src="js/nav.js"></script>
</body>
</html><?php /**PATH D:\RapidGame\laravel\rapidpay\resources\views/merchants/upi-response.blade.php ENDPATH**/ ?>