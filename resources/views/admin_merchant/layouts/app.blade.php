<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<meta name="description" content="" />
<meta name="author" content="" />
<title>GamePay Admin</title>
<link rel="apple-touch-icon" href="{{asset('assets/img/admin-merchant/fev.png')}}">
<link rel="shortcut icon" href="{{asset('assets/img/admin-merchant/fev.png')}}">
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
<link href="{{asset('assets/css/admin-merchant/styles.css')}}" rel="stylesheet" />
<link href="{{asset('assets/css/admin-merchant/custom.css')}}" rel="stylesheet" />
<link href="{{asset('assets/css/admin-merchant/bootstrap-datepicker.css')}}" rel="stylesheet" />
<script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
<link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
</head>
<body class="sb-nav-fixed">
@include('admin_merchant.layouts.top')
<div id="layoutSidenav">
  <div id="layoutSidenav_nav">
    @include('admin_merchant.layouts.leftmenu')
  </div>
  <div id="layoutSidenav_content">

    @yield('contents')

    @include('admin_merchant.layouts.footer')
  </div>
</div>
	
	
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script> 
<script src="{{asset('assets/js/admin-merchant/bootstrap-datepicker.min.js')}}"></script> 

<script type="text/javascript">
$(function () {

    // INITIALIZE DATEPICKER PLUGIN
    $('.datepicker').datepicker({
        clearBtn: true,
        format: "dd/mm/yyyy"
    });


    // FOR DEMO PURPOSE
    $('#reservationDate').on('change', function () {
        var pickedDate = $('input').val();
        $('#pickedDate').html(pickedDate);
    });
});

$("#sidenavAccordion").find('a').each(function() {
    $(this).removeClass('active');
});

let urlArray = window.location.href.split("/");
$("." + urlArray[urlArray.length - 1]).addClass('active');

</script> 
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="{{asset('assets/js/admin-merchant/scripts.js')}}"></script> 
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script> 
<script src="{{asset('assets/js/admin-merchant/datatables-simple-demo.js')}}"></script>

@yield('footer_scipts')

	<!-- Modal withdraw money -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog adminmodal" role="document">
    <div class="modal-content bg-blue">
      <div class="modal-header">
        <h5 class="modal-title text-white" id="exampleModalLabel">Withdraw </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div>
		  <div class="bg-light rounded p-2">
			  <div class="row">
			  	<div class="col-5 my-auto"><img src="img/gamerupee.svg" width="27px" /> <strong>GAMERE</strong></div>
				  <div class="col-7"><input type="text" class="form-control forninput mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="00.00"></div>
			  </div>
			  <div class="row">
			  	<div class="col-12">
				  	<p class="pb-0 mb-0 text-muted">Available Balance: 00.00</p>
				  </div>
			  </div>
			</div>
			<a href="#" class="btn btn-info w-100 mt-3">Withdraw</a>
		  </div>
      </div>
      
    </div>
  </div>
</div>
	<!-- Modal add money -->
<div class="modal fade" id="addmoney" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog adminmodal" role="document">
    <div class="modal-content bg-blue">
      <div class="modal-header">
        <h5 class="modal-title text-white" id="exampleModalLabel">Add Money </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div>
		  <div class="bg-light rounded p-2">
			  <div class="row">
			  	<div class="col-5 my-auto"><img src="img/gamerupee.svg" width="27px" /> <strong>GAMERE</strong></div>
				  <div class="col-7"><input type="text" class="form-control forninput mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="00.00"></div>
			  </div>
			  <div class="row">
			  	<div class="col-12">
				  	<p class="pb-0 mb-0 text-muted">Available Balance: 00.00</p>
				  </div>
			  </div>
			</div>
			<a href="#" class="btn btn-info w-100 mt-3">Add</a>
		  </div>
      </div>
      
    </div>
  </div>
</div>
</body>
</html>
