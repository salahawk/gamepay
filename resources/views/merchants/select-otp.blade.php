<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<title>Merchant Checkout Page</title>
<link rel="stylesheet" href="{{ asset('assets/css/pages/common.css') }}">

</head>
<!-- <body onload="submitForm();"> -->
<body>
    <div class="contaniter">
    <div class="row">
        <div class="new col-6">
            <form method="post" name="frm1">
                <input type="hidden" name="otp_data" id="otp_data" value="{{ $data }}" />
                <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" class="gradientbg">
                    <tr>
                        <td colspan="3" align="center" valign="middle"></td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center" valign="middle" class="signup-headingbg borderleftradius borderrightradius">OTP Verification</td>
                    </tr>
                    <tr>
                        <td align="right" valign="middle">&nbsp;</td>
                        <td align="center" valign="middle">&nbsp;</td>
                        <td align="center" valign="middle">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="28%" align="right" valign="middle" class="labelfont">Mobile number: </td>
                        <td width="65%" align="left" valign="middle"><input
                            type="text" name="mobile" class="signuptextfield mobile mobile-number" value="" autocomplete="off" required/></td>
                        <td width="7%" align="left" valign="middle">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center" valign="middle">
                        <input type="button" class="signupbutton otp-send" value="Send"/>						
                        </td>
                    </tr>
                    <tr>
                        <td width="28%" align="right" valign="middle" class="labelfont">Received Code: </td>
                        <td width="65%" align="left" valign="middle"><input
                            type="number" name="mobile_code" class="signuptextfield mobile-code" value=""  autocomplete="off" required/></td>
                        <td width="7%" align="left" valign="middle">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center" valign="middle">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center" valign="middle">
                        <input type="button"  class="signupbutton otp-confirm" value="Confirm" />						
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center" valign="middle">&nbsp;</td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="new col-6">
            <form method="post" name="frm1">
                <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" class="gradientbg">
                    <tr>
                        <td colspan="3" align="center" valign="middle"></td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center" valign="middle" class="signup-headingbg borderleftradius borderrightradius">Email Verification</td>
                    </tr>
                    <tr>
                        <td align="right" valign="middle">&nbsp;</td>
                        <td align="center" valign="middle">&nbsp;</td>
                        <td align="center" valign="middle">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="28%" align="right" valign="middle" class="labelfont">EMAIL ADDRESS: </td>
                        <td width="65%" align="left" valign="middle"><input
                            type="text" name="email" class="signuptextfield email" value="" autocomplete="off" required/></td>
                        <td width="7%" align="left" valign="middle">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center" valign="middle">
                        <input type="text" id="button" class="signupbutton" value="Send" />						
                        </td>
                    </tr>
                    <tr>
                        <td width="28%" align="right" valign="middle" class="labelfont">Recived Code: </td>
                        <td width="65%" align="left" valign="middle"><input
                            type="number" name="email_code" class="signuptextfield" value=""  autocomplete="off" required/></td>
                        <td width="7%" align="left" valign="middle">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center" valign="middle">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center" valign="middle">
                        <input type="button" id="button" class="signupbutton" value="Confirm" />						
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center" valign="middle">&nbsp;</td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    </div>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).on('click', '.otp-send', function() {
    $.ajax({
      headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      method: "post",
      url: "{{ route('send-otp') }}",
      data: {
          mobile_number: $('.mobile-number').val(),
          otp_data: $('#otp_data').val()
      },
      success: function (resp) {
        if (resp.success == "success") {
            $('.mobile-number').after('<p>We have sent code to your mobile number. Fill the number in the below box.</p>')
        }
      },
    });
})
</script>


</html>
