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

    // setting status
    let email = "{{ $email_status }}";
    let mobile = "{{ $mobile_status }}";
    let kyc = "{{ $kyc_status }}";

    $(document).on('click', '#confirm_pay', function() {
        // $('[for="amountLbID"]').css("display", "none");
        // $('[for="myDropdown1LbID"]').css("display", "none");
        // $('[for="myDropdownLbID"]').css("display", "none");
        // $('[for="wallet_addressLbID"]').css("display", "none");
        // $('[for="remarksLbID"]').css("display", "none");
        // if ($('#amount').val() == '' || $("#myDropdown1").find('.dd-selected-text').html() == 'Network' || $("#myDropdown").find('.dd-selected-text').html() == 'Currency' || $('#wallet_address').val() == '' || $('#remarks').val() == '' || $('#inr_value').val() == '') {
        //     if ($('#amount').val() == '') {
        //         $('[for="amountLbID"]').html("This field is required.");
        //         $('[for="amountLbID"]').css("display", "inline");
        //     }
        //     if ($("#myDropdown1").find('.dd-selected-text').html() == 'Network')
        //         $('[for="myDropdown1LbID"]').css("display", "inline");
        //     if ($("#myDropdown").find('.dd-selected-text').html() == 'Currency')
        //         $('[for="myDropdownLbID"]').css("display", "inline");
        //     if ($('#wallet_address').val() == '')
        //         $('[for="wallet_addressLbID"]').css("display", "inline");
        //     if ($('#remarks').val() == '') {
        //         $('[for="remarksLbID"]').html("This field is required.");
        //         $('[for="remarksLbID"]').css("display", "inline");
        //     }
        // } else {
        //     if (parseInt($('#amount').val()) < 500 || parseInt($('#amount').val()) > 50000) {
        //         $('[for="amountLbID"]').html("Min 500 to Max 50,000");
        //         $('[for="amountLbID"]').css("display", "inline");
        //         return;
        //     }
        //     if ($('#remarks').val().length > 50) {
        //         $('[for="remarksLbID"]').html("Max length 50");
        //         $('[for="remarksLbID"]').css("display", "inline");
        //         return;
        //     }
        // $.ajax({
        //     headers: {
        //         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        //     },
        //     method: "post",
        //     url: "{{ route('user.check') }}",
        //     data: {
        //         amount: $('#amount').val(),
        //         network: $("#myDropdown1").find('.dd-selected-text').text(),
        //         currency: $("#myDropdown").find('.dd-selected-text').text(),
        //         wallet_address: $('#wallet_address').val(),
        //         remarks: $('#remarks').val(),
        //         inr_value: $('#inr_value').val()
        //     },
        //     success: function(resp) {
        //         if (resp.user_verified == "no") {
        //             $('#emailOtpModal').modal('show');
        //         } else {
        //             $('#user_id').val(resp.user_id);
        //             $('.container:first').hide();
        //             $('.container:eq(1)').show();
        //         }
        //     },
        // });
        // }

        if (email != "verified") $("#emailOtpModal").modal("show");
        else if (mobile != "verified") $("#mobileOtpModal").modal("show");
        else if (kyc != "verified") location.href = "{{ route('kyc') }}" + "{{ '?user_id=' }}" + "{{ $user_id }}";
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
                    mobile_number: $('#mobile_number').val(),
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
                url: "{{ route('submit-mobile-otp') }}",
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
                        location.href = "{{ route('kyc') }}" + "{{ '?user_id=' }}" + resp.user_id;
                        } else {
                        $('.container:first').hide();
                        $('.container:eq(1)').show();
                        $('#mobileOtpModal').modal('toggle');
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
                url: "{{ route('send-email-otp') }}",
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
                url: "{{ route('submit-email-otp') }}",
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
                        location.href = "{{ route('kyc') }}" + "{{ '?user_id=' }}" + resp.user_id;
                        } else {
                        $('.container:first').hide();
                        $('.container:eq(1)').show();
                        }
                    } else {
                        alert("Email OTP verification is failed. Please try again");
                        $('#email_submit').removeClass("disabled");
                        $('#email_getotp').removeClass("disabled");
                        return;
                        // location.href = "{{ route('home') }}";
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
                user_id: "{{ $user_id }}"
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
    
    $('#myDropdown').find('.dd-selected-text').html("{{ $crypto }}");
    $('#myDropdown1').find('.dd-selected-text').text("{{ $network }}");

    $('#myDropdown').find('a.dd-selected').css('background-color', '#e9ecef');
    $('#myDropdown1').find('a.dd-selected').css('background-color', '#e9ecef');
    $("#myDropdown").css("pointer-events", "none");
    $("#myDropdown1").css("pointer-events", "none");
})
