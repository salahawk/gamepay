@extends('layouts.app')
@section('contents')
    <div class="animated fadeIn">
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
                                    placeholder="Enter Amount" required>
                                <label for="amountLbID" style="color: #f00; display: none;">This field is required.</label>
                            </div>
                            <div class="col-6">
                                <select id="myDropdown" style="width:100%">
                                    <option value="0" class="text-blue" data-description=""><span
                                            style="color:#ccc;">Currency</span></option>
                                    <option value="0" class="text-blue"
                                        data-imagesrc="{{ asset('assets/img/Gamerupee.png') }}" data-description="">G RUPEE
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
                                <input type="number" class="form-control border-radius6" id="inr_value"
                                    aria-describedby="name" placeholder="INR Value" readonly>
                                <p class="text-blue font14 text-center pt-1 pb-0 mb-0"><small>Min 500 to Max
                                        50,000</small>
                                </p>
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
                                        data-imagesrc="{{ asset('assets/img/ethereum.png') }}" data-description="">ETH
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
                                <input type="text" class="form-control border-radius6" id="receiver_address"
                                    aria-describedby="name" placeholder="Enter Destination Address" value="">
                            </div>
                            <div class="col-12 mb-2">
                                <input type="text" class="form-control border-radius6" id="wallet_address"
                                    aria-describedby="name" placeholder="Enter Your Wallet Address">
                                <label for="wallet_addressLbID" style="color: #f00; display: none;">This field is
                                    required.</label>
                                <p class="text-blue font14 text-center pt-1 pb-0 mb-0"><small>Ex:
                                        0xd4654ad4ad4sad4sa6dwq886wa4d5</small></p>

                                <div class="col-12 mb-2 pt-2">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                        <label class="form-check-label" for="exampleCheck1">I agree Terms</label>
                                    </div>
                                </div>
                                <div class="col-12 mb-2 pt-2">
                                    <a href="#" class="btn btn-primary border-radius6 w-100 disabled"
                                        id="confirm_pay">Create Request</a>
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
            </div>
        </div>
        <div class="container" style="padding-top: 5%; display: none">
          <div class="row justify-content-md-center">
              <div class="col-12 col-md-6">
                  <!---- Tab 1 content start-->
                  <div class="row">
                      <div class="col-12 text-center logopos">
                          <div class="position-relative">
                              <img src="{{ asset('assets/img/gamerupee.svg') }}" width="100px" />
                          </div>
                      </div>
                  </div>
                  <div class="bg-white border-radius12 mb-1 text-left p-3 p-md-4 mainpageform">
                      <div class="row pt-5">
                          <div class="p-3 col-12 text-center">
                              <p class="text-blue">
                                  <i><img src="{{ asset('assets/img/info.svg') }}" class=""
                                          style="width: 25px" /></i><br />
                                  <small>Please do not press back button or close the screen<br />
                                      until you submit the Transaction hash.</small>
                              </p>
                          </div>
  
                          <div class="col-12 mb-2">
                              <input type="text" class="form-control border-radius6" aria-describedby="name"
                                  placeholder="Enter Tx Hash" id="txn_hash" />
                          </div>
                          <div class="col-12 mb-2">
                              <input type="text" class="form-control border-radius6" aria-describedby="name"
                                  placeholder="Remarks" id="remark" />
                          </div>
  
                          <div class="col-12 mb-2 pt-2">
                              <a href="#" class="btn btn-primary border-radius6 w-100" id="txn_confirm" type="button">Submit</a>
                          </div>
                          <div class="p-3 text-center col-12">
                              <p class="text-blue">
                                  This page will be expired in
                                  <span class="text-danger">30 mins</span>.
                              </p>
                          </div>
                          <div class="col-12 mb-2 pt-2">
                              <div class="text-center"><img src="{{ asset('assets/img/upi (2).png') }}" /></div>
                          </div>
                      </div>
                  </div>
                  <!---- Tab 1 content ends-->
              </div>
          </div>
      </div>
    </div>
    @endsection

    @section('footer_scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
    <script src="{{ asset('assets/js/custom.select.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/dropdown.js') }}"></script>
    <script>
        $(document).ready(function() {
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
                if ($('#amount').val() == '' || $("#myDropdown1").find('.dd-selected-text').html() ==
                    'Network' || $("#myDropdown").find('.dd-selected-text').html() == 'Currency' || $(
                        '#wallet_address').val() == '' || $('#inr_value').val() == '') {
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
                    // if ($('#remarks').val().length > 50) {
                    //     $('[for="remarksLbID"]').html("Max length 50");
                    //     $('[for="remarksLbID"]').css("display", "inline");
                    //     return;
                    // }
                    // show modal confirmation
                    // $('#confirmModal').modal('show');
                    $('.container:first').hide();
                    $('.container:eq(1)').show();
                }
            });

            $(document).on('click', '#txn_confirm', function() {
              $(this).prop('disabled', true);
                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    method: "POST",
                    url: "{{ route('process-payout') }}",
                    data: {
                        amount: $('#amount').val(),
                        network: $("#myDropdown1").find('.dd-selected-text').text(),
                        currency: $("#myDropdown").find('.dd-selected-text').text(),
                        wallet_address: $('#wallet_address').val(),
                        receiver: $('#receiver_address').val(),
                        remarks: $('#remark').val(),
                        inr_value: $('#inr_value').val(),
                        user_id: "{{ $user->id }}",
                        txn_hash: $('#txn_hash').val()
                    },
                    success: function(resp) {
                        // if (resp.user_verified == "no") {
                        //     $('#emailOtpModal').modal('show');
                        // } else if (resp.user_verified == "only_email") {
                        //     $('#mobileOtpModal').modal('show');
                        // } else if (resp.user_id) {
                        //     $('#user_id').val(resp.user_id);
                        //     $('.container:first').hide();
                        //     $('.container:eq(1)').show();
                        // } else {
                        //     document.querySelector('html').innerHTML = resp;
                        // }
                    },
                });
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
                                location.href = "{{ route('kyc') }}" +
                                    "{{ '?user_id=' }}" + resp
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
                $(this).is(':checked') ? $('a#confirm_pay').removeClass('disabled') : $('a#confirm_pay')
                    .addClass(
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
