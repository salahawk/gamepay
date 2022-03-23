<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Gamerupee</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!-- P2P Additional CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" --}}
    {{-- rel="stylesheet"> --}}
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
        integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>
    <div class="container">
        <div class="row justify-content-md-center">
            <div class="col-12 col-md-6">
                <!---- Tab 1 content start-->
                <div class="row">
                    <div class="col-12 text-center logopos">
                        <div class="position-relative"><img src="{{ asset('assets/img/gamerupee.svg') }}"
                                width="100px" /></div>
                    </div>
                </div>
                <div class="bg-white border-radius12 mb-1 text-left p-3 p-md-4 mainpageform">
                    <div class="row pt-5">
                        <div class="col-6">
                            <input type="number" class="form-control border-radius6" id="amount" aria-describedby="name"
                                placeholder="Enter Amount">
                        </div>
                        <div class="col-6">
                            <select id="myDropdown" style="width:100%">
                                <option value="0" class="text-blue" data-description=""><span
                                        style="color:#ccc;">Currency</span></option>
                                <option value="0" class="text-blue"
                                    data-imagesrc="{{ asset('assets/img/Gamerupee.png') }}" data-description="">G RUPEE
                                </option>
                                <option value="1" class="text-blue"
                                    data-imagesrc="{{ asset('assets/img/JR_blue_d.png') }}" data-description="">JXRUPE
                                </option>
                                <option value="2" class="text-blue"
                                    data-imagesrc="{{ asset('assets/img/jusd_d.png') }}" data-description="">JUSD
                                </option>
                            </select>
                        </div>
                        <div class="col-12 text-center">
                            <p class="text-blue font14 text-center pt-1"><small>0.00073743 JAXRE = 1INR</small></p>
                        </div>
                        <div class="col-12 mb-2">
                            <select id="myDropdown1" style="width:100%">
                                <option value="0" class="text-blue">Network</option>
                                <option value="0" class="text-blue"
                                    data-imagesrc="{{ asset('assets/img/binance.png') }}" data-description="">BSC
                                </option>
                                <option value="1" class="text-blue"
                                    data-imagesrc="{{ asset('assets/img/awax.png') }}" data-description="">AWAX</option>
                                <option value="1" class="text-blue"
                                    data-imagesrc="{{ asset('assets/img/ethereum.png') }}" data-description="">ETH
                                </option>
                                <option value="1" class="text-blue"
                                    data-imagesrc="{{ asset('assets/img/polygon.png') }}" data-description="">POLYGON
                                </option>
                            </select>
                        </div>
                        <div class="col-12 mb-2">
                            <input type="text" class="form-control border-radius6" id="wallet_address" aria-describedby="name"
                                placeholder="Enter Destination Address">
                            <p class="text-blue font14 text-center pt-1 pb-0 mb-0"><small>Ex:
                                    0xd4654ad4ad4sad4sa6dwq886wa4d5</small></p>
                        </div>
                        <div class="col-12 mb-2">
                            <input type="text" class="form-control border-radius6" id="remarks" aria-describedby="name"
                                placeholder="Remarks">
                        </div>
                        <div class="col-12 mb-2">
                            <input type="number" class="form-control border-radius6" id="inr_value" aria-describedby="name"
                                placeholder="INR Value">
                            <p class="text-blue font14 text-center pt-1 pb-0 mb-0"><small>Min 500 to Max 50,000</small>
                            </p>
                        </div>
                        <div class="col-12 mb-2 pt-2">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                <label class="form-check-label" for="exampleCheck1">I agree Terms</label>
                            </div>
                        </div>
                        <div class="col-12 mb-2 pt-2">
                            <a href="#" class="btn btn-primary border-radius6 w-100" id="confirm_pay">Continue to Pay</a>
                        </div>
                        <div class="col-12 mb-2 pt-2">
                            <div class="text-center"><img src="{{ asset('assets/img/upi.png') }}" /></div>
                        </div>
                    </div>
                </div>

                <!---- Tab 1 content ends-->
            </div>
        </div>
    </div>
    <!--- Modal start---->
    <div class="modal fade kycmodal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Register</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <!--- Email Section start--->
                        <div class="row">
                            <div class="col-12">
                                <div>
                                    <form>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Verify Your Email</label>
                                            <input type="email" class="form-control" id="email_otp"
                                                aria-describedby="emailHelp">
                                            <small id="emailHelp" class="form-text text-danger">Enter Valid Email
                                                address</small>
                                        </div>
                                        <a href="#" class="btn btn-primary" id="email_getotp">Get Code</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div>
                                    <form>

                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Enter Email OTP</label>
                                            <input type="number" class="form-control" id="email_code"
                                                aria-describedby="mobile">
                                            <small id="" class="form-text text-danger">OTP Wrong</small>
                                        </div>

                                        <a href="#" class="btn btn-primary" id="email_submit">Submit</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!--- Email Section end--->
                        <!--- Mobile Section start--->
                        <div class="row">
                            <div class="col-12">
                                <div>
                                    <form>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Verify Your Mobile Number</label>
                                            <input type="number" class="form-control" id="mobile_number"
                                                aria-describedby="mobile">
                                            <small id="" class="form-text text-danger">Enter Valid Mobile Number</small>
                                        </div>
                                        <a href="#" class="btn btn-primary" id="mobile_getotp">Get Code</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div>
                                    <form>

                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Enter Mobile OTP</label>
                                            <input type="number" class="form-control" id="mobile_code"
                                                aria-describedby="mobile">
                                            <small id="" class="form-text text-danger">OTP Wrong</small>
                                        </div>

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
    <!--- Modal end---->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script src="{{ asset('assets/js/custom.select.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    <script type="text/javascript" src="{{ asset('assets/js/dropdown.js') }}"></script>
    <script>
        $('#myDropdown').ddslick({
            onSelected: function(selectedData) {
                //callback function: do something with selectedData;
            }
        });
        $('#myDropdown1').ddslick({
            onSelected: function(selectedData) {
                //callback function: do something with selectedData;
            }
        });
        $('#myDropdown2').ddslick({
            onSelected: function(selectedData) {
                //callback function: do something with selectedData;
            }
        });
        $('#myDropdown3').ddslick({
            onSelected: function(selectedData) {
                //callback function: do something with selectedData;
            }
        });
        $('#myDropdown4').ddslick({
            onSelected: function(selectedData) {
                //callback function: do something with selectedData;
            }
        });

        $(document).on('click', '#confirm_pay', function() {
          $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            method: "post",
            url: "{{ route('check-user') }}",
            data: {
                amount: $('#amount').val(),
                otp_data: $('#otp_data').val(),
                network: $("#myDropdown1").find('.dd-selected-text').text(),
                currency: $("#myDropdown").find('.dd-selected-text').text(),
                wallet_address: $('#wallet_address').val(),
                remarks: $('#remarks').val(),
                inr_value: $('#inr_value').val()
            },
            success: function (resp) {
              if (resp.user_exist == "no") {
                  $('#exampleModal').modal('show');
              }
            },
          });
        });

        $(document).on('click', '#mobile_getotp', function() {
          $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            method: "POST",
            url: "{{ route('send-mobile-otp') }}",
            data: {
              wallet_address: $('#wallet_address').val(),
              mobile_number: $('#mobile_number').val()
            },
            success: function (resp) { console.log(resp);
              
            },
          });
        });

        $(document).on('click', '#mobile_submit', function() { alert("OK");
          $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            method: "POST",
            url: "{{ route('submit-mobile-otp') }}",
            data: {
              submit_value: $('#mobile_code').val(),
              mobile_number: $('#mobile_number').val(),
              wallet_address: $('#wallet_address').val(),
            },
            success: function (resp) {
              if (resp.success == "success") {
                location.href = "{{ route('kyc') }}";
              }
            },
          });
        });

        $(document).on('click', '#email_getotp', function() {
          $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            method: "POST",
            url: "{{ route('send-email-otp') }}",
            data: {
              wallet_address: $('#wallet_address').val(),
              email_address: $('#email_otp').val()
            },
            success: function (resp) { console.log(resp);
              
            },
          });
        });

        $(document).on('click', '#email_submit', function() {
          $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            method: "POST",
            url: "{{ route('submit-email-otp') }}",
            data: {
              submit_value: $('#email_code').val(),
              email_address: $('#email_otp').val(),
              wallet_address: $('#wallet_address').val(),
            },
            success: function (resp) {
              if (resp.success == "success") {
                location.href = "{{ route('kyc') }}";
              }
            },
          });
        });


    </script>
</body>

</html>
