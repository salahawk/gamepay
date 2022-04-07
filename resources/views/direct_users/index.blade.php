@extends('layouts.app')
@section('contents')
    <div class="animated fadeIn">

        <!--  profile  -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">

                    <div class="row justify-content-md-center">
                        <div class="col-12 col-md-6">
                            <!---- Tab 1 content start-->
                            <div class="row">
                                {{-- <div class="col-12 text-center logopos">
                        <div class="position-relative">
                            <img src="{{ asset('assets/img/gamerupee.svg') }}" width="100px" />
                            BUY
                        </div>
                    </div> --}}
                                <div class="col-12 text-center pt-5">
                                    <h3 class="font-weight-bold">BUY</h3>
                                </div>
                            </div>
                            <div class="bg-white border-radius12 mb-1 text-left p-3 p-md-4 mainpageform">
                                <div class="row pt-5">
                                    <div class="col-6">
                                        <input type="number" class="form-control border-radius6" id="amount"
                                            aria-describedby="name" placeholder="Enter Amount" required>
                                        <label for="amountLbID" style="color: #f00; display: none;">This field is
                                            required.</label>
                                    </div>
                                    <div class="col-6">
                                        <select id="myDropdown" style="width:100%">
                                            <option value="0" class="text-blue" data-description=""><span
                                                    style="color:#ccc;">Currency</span></option>
                                            <option value="0" class="text-blue"
                                                data-imagesrc="{{ asset('assets/img/Gamerupee.png') }}"
                                                data-description="">G RUPEE
                                            </option>
                                            <option value="1" class="text-blue"
                                                data-imagesrc="{{ asset('assets/img/usdt.png') }}" data-description="">
                                                USDT
                                            </option>
                                            <option value="2" class="text-blue"
                                                data-imagesrc="{{ asset('assets/img/btc.png') }}" data-description="">BTC
                                            </option>
                                        </select>
                                        <label for="myDropdownLbID" style="color: #f00; display: none;">This field is
                                            required.</label>
                                    </div>
                                    <div class="col-12 text-center">
                                        <p class="text-blue font14 text-center pt-1"><small>1 INR = 1 G RUPEE</small></p>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <select id="myDropdown1" style="width:100%">
                                            <option value="0" class="text-blue">Network</option>
                                            <option value="0" class="text-blue"
                                                data-imagesrc="{{ asset('assets/img/binance.png') }}" data-description="">
                                                BSC
                                            </option>
                                            <option value="1" class="text-blue"
                                                data-imagesrc="{{ asset('assets/img/awax.png') }}" data-description="">
                                                AWAX
                                            </option>
                                            <option value="1" class="text-blue"
                                                data-imagesrc="{{ asset('assets/img/ethereum.png') }}"
                                                data-description="">ETH
                                            </option>
                                            <option value="1" class="text-blue"
                                                data-imagesrc="{{ asset('assets/img/polygon.png') }}" data-description="">
                                                POLYGON
                                            </option>
                                        </select>
                                        <label for="myDropdown1LbID" style="color: #f00; display: none;">This field is
                                            required.</label>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <input type="text" class="form-control border-radius6" id="wallet_address"
                                            aria-describedby="name" placeholder="Enter Destination Address">
                                        <label for="wallet_addressLbID" style="color: #f00; display: none;">This field is
                                            required.</label>
                                        <p class="text-blue font14 text-center pt-1 pb-0 mb-0"><small>Ex:
                                                0xd4654ad4ad4sad4sa6dwq886wa4d5</small></p>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <input type="hidden" class="form-control border-radius6" id="customer_name"
                                            aria-describedby="name" placeholder="Remarks">
                                        <input type="text" class="form-control border-radius6" id="remarks"
                                            aria-describedby="name" placeholder="Remarks">
                                        <label for="remarksLbID" style="color: #f00; display: none;">This field is
                                            required.</label>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <input type="number" class="form-control border-radius6" id="inr_value"
                                            aria-describedby="name" placeholder="INR Value" readonly>
                                        <p class="text-blue font14 text-center pt-1 pb-0 mb-0"><small>Min 500 to Max
                                                50,000</small>
                                        </p>
                                    </div>
                                    <div class="col-12 mb-2 pt-2">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                            <label class="form-check-label" for="exampleCheck1">I agree Terms</label>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-2 pt-2">
                                        <a href="#" class="btn btn-primary border-radius6 w-100 disabled"
                                            id="confirm_pay">Continue
                                            to Pay</a>
                                    </div>
                                    <div class="col-12 mb-2 pt-2">
                                        <div class="text-center"><img src="{{ asset('assets/img/upi (2).png') }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!---- Tab 1 content ends-->
                        </div>
                    </div>


                    <div class="container" style="padding-top: 10%; display: none">
                        <div class="row justify-content-center align-items-center">
                            <div class="col-lg-10">
                                <div class="middlepage1 shadow p-4 text-center"> <img
                                        src="{{ asset('assets/img/upi.png') }}" class="img-fluid" />
                                    <h3 class="text-center text-dark font-weight-bold">UPI Payment</h3>
                                    <div class="row justify-content-center align-items-center">
                                        <div class="col-12 col-md-6">
                                            <form>
                                                <div>
                                                    <p class="text-dark">Please Enter Your UPI ID</p>
                                                    <div class="row pb-3">
                                                        <div class="col-12">
                                                            <input type="hidden" id="user_id">
                                                            <input class="form-control form-control-lg text-center"
                                                                name="payeraddress" type="text"
                                                                placeholder="Eg: Yourphonenumber@apl" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group mb-0 pb-0">
                                                    <input type="button" class="btnSubmit btn btn-info"
                                                        value="Verify & Proceed" />
                                                </div>
                                                <div class="form-group mb-0 pb-0">
                                                    <div class="loader m-auto" id="loader-4"> <span></span> <span></span>
                                                        <span></span>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--- Modal start---->
                    <div class="modal fade kycmodal" id="emailOtpModal" tabindex="-1" aria-labelledby="emailOtpModal"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Email Verification</h5>
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
                                                            <label for="email_otpLbID"
                                                                style="color: #f00; display: none;">Enter Valid
                                                                Email</label>
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
                                                            <label for="email_codeLbID"
                                                                style="color: #f00; display: none;">OTP
                                                                Wrong</label>
                                                        </div>

                                                        <a href="#" class="btn btn-primary" id="email_submit">Submit</a>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!--- Email Section end--->
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal fade kycmodal" id="mobileOtpModal" tabindex="-1" aria-labelledby="mobileOtpModal"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="mobileOtpModalLabel">Mobile Verification</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div>
                                        <!--- Mobile Section start--->
                                        <div class="row">
                                            <div class="col-12">
                                                <div>
                                                    <form>
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">Verify Your Mobile
                                                                Number</label>
                                                            <input type="number" class="form-control" id="mobile_number"
                                                                aria-describedby="mobile" placeholder="1234567890">
                                                            <label for="mobile_numberLbID"
                                                                style="color: #f00; display: none;">Enter
                                                                Valid Mobile Number</label>
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
                                                            <label for="mobile_codeLbID"
                                                                style="color: #f00; display: none;">OTP
                                                                Wrong</label>
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
                </div>
            </div>
        </div>
    @endsection

    @section('footer_scripts')
				{{-- <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>  --}}
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
				<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
				<script src="{{ asset('assets/js/custom.select.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/dropdown.js') }}"></script>
        <script>
				$(document).ready(function(){
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
                $('[for="amountLbID"]').css("display", "none");
                $('[for="myDropdown1LbID"]').css("display", "none");
                $('[for="myDropdownLbID"]').css("display", "none");
                $('[for="wallet_addressLbID"]').css("display", "none");
                $('[for="remarksLbID"]').css("display", "none");
                if ($('#amount').val() == '' || $("#myDropdown1").find('.dd-selected-text').html() == 'Network' || $(
                        "#myDropdown").find('.dd-selected-text').html() == 'Currency' || $('#wallet_address').val() ==
                    '' || $('#remarks').val() == '' || $('#inr_value').val() == '') {
                    if ($('#amount').val() == '') {
                        $('[for="amountLbID"]').html("This field is required.");
                        $('[for="amountLbID"]').css("display", "inline");
                    }
                    if ($("#myDropdown1").find('.dd-selected-text').html() == 'Network')
                        $('[for="myDropdown1LbID"]').css("display", "inline");
                    if ($("#myDropdown").find('.dd-selected-text').html() == 'Currency')
                        $('[for="myDropdownLbID"]').css("display", "inline");
                    if ($('#wallet_address').val() == '')
                        $('[for="wallet_addressLbID"]').css("display", "inline");
                    if ($('#remarks').val() == '') {
                        $('[for="remarksLbID"]').html("This field is required.");
                        $('[for="remarksLbID"]').css("display", "inline");
                    }
                } else {
                    if (parseInt($('#amount').val()) < 500 || parseInt($('#amount').val()) > 50000) {
                        $('[for="amountLbID"]').html("Min 500 to Max 50,000");
                        $('[for="amountLbID"]').css("display", "inline");
                        return;
                    }
                    if ($('#remarks').val().length > 50) {
                        $('[for="remarksLbID"]').html("Max length 50");
                        $('[for="remarksLbID"]').css("display", "inline");
                        return;
                    }
                    $.ajax({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        method: "GET",
                        url: "{{ route('user-check') }}",
                        data: {
                            amount: $('#amount').val(),
                            network: $("#myDropdown1").find('.dd-selected-text').text(),
                            currency: $("#myDropdown").find('.dd-selected-text').text(),
                            wallet_address: $('#wallet_address').val(),
                            remarks: $('#remarks').val(),
                            inr_value: $('#inr_value').val()
                        },
                        success: function(resp) {
                            if (resp.user_verified == "no") {
                                $('#emailOtpModal').modal('show');
                            } else if (resp.user_verified == "only_email") {
															$('#mobileOtpModal').modal('show');
														}	else if (resp.user_id) {
                                $('#user_id').val(resp.user_id);
                                $('.container:first').hide();
                                $('.container:eq(1)').show();
                            } else {
                                document.querySelector('html').innerHTML = resp;
                            }
                        },
                    });
                }
            });

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
                        url: "{{ route('send-mobile-otp') }}",
                        data: {
                            wallet_address: $('#wallet_address').val(),
                            mobile_number: $('#mobile_number').val()
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
                        url: "{{ route('submit-mobile-otp') }}",
                        data: {
                            submit_value: $('#mobile_code').val(),
                            mobile_number: $('#mobile_number').val(),
                            wallet_address: $('#wallet_address').val(),
                            cust_name: $('#remarks').val(),
                        },
                        success: function(resp) {
                            if (resp.status == "success") {
                                alert("Mobile OTP is successful.");
                                location.href = "{{ route('kyc') }}" + "{{ '?user_id=' }}" + resp
                                    .user_id;
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
                        url: "{{ route('send-email-otp') }}",
                        data: {
                            wallet_address: $('#wallet_address').val(),
                            email_address: $('#email_otp').val(),
                            amount: $('#amount').val(),
                            network: $("#myDropdown1").find('.dd-selected-text').text(),
                            currency: $("#myDropdown").find('.dd-selected-text').text(),
                            remarks: $('#remarks').val(),
                            inr_value: $('#inr_value').val()
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
                        url: "{{ route('submit-email-otp') }}",
                        data: {
                            submit_value: $('#email_code').val(),
                            email_address: $('#email_otp').val(),
                            wallet_address: $('#wallet_address').val(),
                        },
                        success: function(resp) {
                            if (resp.status == "success") {
                                alert("Email OTP is successful.");
                                $('#emailOtpModal').modal('toggle');
                                $('#mobileOtpModal').modal('toggle');
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

            $(document).on('click', '.btnSubmit', function() {
                $(this).prop('disabled', true);
                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    method: "post",
                    url: "{{ route('validate-vpa') }}",
                    data: {
                        payer_address: $('input[name="payeraddress"]').val(),
                        user_id: $('#user_id').val(),
                    },
                    success: function(resp) {
                        $('.btnSubmit').prop('disabled', false);
                        // if (resp.status == "fail") {
                        //   alert("Sorry, but you are no longer valid to make a transaction.");
                        // }
                        document.querySelector('html').innerHTML = resp;
                    },
                });
            });

            $('#exampleCheck1').click(function() {
                $(this).is(':checked') ? $('a#confirm_pay').removeClass('disabled') : $('a#confirm_pay').addClass(
                    'disabled');
            });
            $("input#amount").blur(function() {
                $("input#inr_value").val($(this).val());
            })

            $('#wallet_address').on('keypress', function(event) {
                var regex = new RegExp("^[a-zA-Z0-9]+$");
                var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                if (!regex.test(key)) {
                    event.preventDefault();
                    return false;
                }
            });

            $('#remarks').on('keypress', function(event) {
                var regex = new RegExp("^[a-zA-Z0-9]+$");
                var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                if (!regex.test(key)) {
                    event.preventDefault();
                    return false;
                }
            });

            document.addEventListener('contextmenu', event => event.preventDefault());

					});
        </script>
    @endsection
