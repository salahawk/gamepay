<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>GamePay Admin</title>
  <link rel="apple-touch-icon" href="{{asset('assets/img/admin/fev.png')}}">
  <link rel="shortcut icon" href="{{asset('assets/img/admin/fev.png')}}">
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="{{asset('assets/css/admin/styles.css')}}" rel="stylesheet" />
  <link href="{{asset('assets/css/admin/custom.css')}}" rel="stylesheet" />
  <link href="{{asset('assets/css/admin/bootstrap-datepicker.css')}}" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.12.0/css/jquery.dataTables.min.css" rel="stylesheet" />
  @yield('header_styles')
</head>

<body class="sb-nav-fixed">
  @include('admin.layouts.top')
  <div id="layoutSidenav">
    <div id="layoutSidenav_nav">
      @include('admin.layouts.leftmenu')
    </div>
    <div id="layoutSidenav_content">

      @yield('contents')

      @include('admin.layouts.footer')
    </div>
  </div>


  <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>  -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="{{asset('assets/js/admin/bootstrap-datepicker.min.js')}}"></script>

  <script type="text/javascript">
    $(function() {

      // INITIALIZE DATEPICKER PLUGIN
      $('.datepicker').datepicker({
        clearBtn: true,
        autoclose: true,
        format: "yyyy-mm-dd"
      });


      // FOR DEMO PURPOSE
      $('#reservationDate').on('change', function() {
        var pickedDate = $('input').val();
        $('#pickedDate').html(pickedDate);
      });
    });

    $("#sidenavAccordion").find('a').each(function() {
      $(this).removeClass('active');
    });

    let urlArray = window.location.href.split("/");
    $("." + urlArray[urlArray.length - 1].replace("#", "")).addClass('active');
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script> 
  <script src="{{asset('assets/js/admin/scripts.js')}}"></script>
  <script src="https://cdn.datatables.net/1.12.0/js/jquery.dataTables.min.js"></script>
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
                <div class="col-5 my-auto"><img src="{{asset('assets/img/admin/gamerupee.svg')}}" width="27px" /> <strong>GAMERE</strong></div>
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
                <div class="col-5 my-auto"><img src="{{asset('assets/img/admin/gamerupee.svg')}}" width="27px" /> <strong>GAMERE</strong></div>
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