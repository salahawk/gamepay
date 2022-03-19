<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
<title>Merchant Checkout Page</title>
<style type="text/css">
body {
	width:100%;
	margin: 0 auto;
	background-color:#e4eff5;
}
.new {
	width: 500px;
	margin:20px auto 0 auto; padding:0; font:normal 12px arial; color:#555; background:#fff; border:1px solid #d0d0d0; border-radius:5px; 
-webkit-box-shadow: -1px 3px 8px -1px rgba(0,0,0,0.75);
-moz-box-shadow: -1px 3px 8px -1px rgba(0,0,0,0.75);
box-shadow: -1px 3px 8px -1px rgba(0,0,0,0.75);
}
.signupbox { margin:20px auto 0 auto; padding:0; font:normal 12px arial; color:#555; background:#fff; border:1px solid #d0d0d0; border-radius:5px; 
-webkit-box-shadow: -1px 3px 8px -1px rgba(0,0,0,0.75);
-moz-box-shadow: -1px 3px 8px -1px rgba(0,0,0,0.75);
box-shadow: -1px 3px 8px -1px rgba(0,0,0,0.75);}
.signup-headingbg { background:#194e84; background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-size:5px 5px; height:35px; border-bottom:1px solid #dadada; font:bold 16px Tahoma; color:#ffffff; vertical-align:middle; }
.signuptextfield { display: block;  width:98%;  height: 15px;  padding: 6px 7px;  padding:6px\9; margin-left:10px;  font-size: 12px;  font-family: 'Titillium Web', sans-serif;
  line-height: 1.428571429;  color: #555; margin-bottom:5px;  background-color: #fff;  background-image: none;  border: 1px solid #ccc;
  border-radius: 4px;  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);  box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
  -webkit-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;  transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
  }		  
.signuptextfield:focus {  border-color: #66afe9;  outline: 0;  -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
  box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6); }
.labelfont {font:bold 11px Arial; color:#607a8c; text-decoration:none;}  		  
.signupbutton { background-color:#5cb85c; border:1px solid #4cae4c; width:40%; height:35px;  font:bold 14px Tahoma; text-align:center; color:#fff; cursor:pointer; border-radius:5px;}
.signupbutton:hover { background-color:#449d44; border:1px solid #398439; width:40%; height:35px;  font:bold 14px Tahoma; text-align:center; color:#fff; cursor:pointer; border-radius:5px;}
.borderleftradius { border-top-left-radius:5px; }
.borderrightradius { border-top-right-radius:5px; }
.gradientbg {/* IE10 Consumer Preview */ 
background-image: -ms-linear-gradient(top, #FEFEFF 0%, #BFD3E1 100%);

/* Mozilla Firefox */ 
background-image: -moz-linear-gradient(top, #FEFEFF 0%, #BFD3E1 100%);

/* Opera */ 
background-image: -o-linear-gradient(top, #FEFEFF 0%, #BFD3E1 100%);

/* Webkit (Safari/Chrome 10) */ 
background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #FEFEFF), color-stop(1, #BFD3E1));

/* Webkit (Chrome 11+) */ 
background-image: -webkit-linear-gradient(top, #FEFEFF 0%, #BFD3E1 100%);

/* W3C Markup, IE10 Release Preview */ 
background-image: linear-gradient(to bottom, #FEFEFF 0%, #BFD3E1 100%);}
</style>
<script type="text/javascript">
function submitForm() {
	document.frm1.submit();
}
</script>
</head>
<!-- <body onload="submitForm();"> -->
<body>
    <div class="new">
        <form method="post" action="<?php echo e(route('send-cashlesso')); ?>" name="frm1">
            <input type="hidden" name="_token" id="csrf_token" value="<?php echo e(csrf_token()); ?>" />
            <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" class="gradientbg">
                <tr>
                    <td colspan="3" align="center" valign="middle"></td>
                </tr>
                <tr>
                    <td colspan="3" align="center" valign="middle" class="signup-headingbg borderleftradius borderrightradius">Checkout Page</td>
                </tr>
                <tr>
                    <td align="right" valign="middle">&nbsp;</td>
                    <td align="center" valign="middle">&nbsp;</td>
                    <td align="center" valign="middle">&nbsp;</td>
                <tr>
                    <td width="28%" align="right" valign="middle" class="labelfont">AMOUNT: </td>
                    <td width="65%" align="left" valign="middle"><input
                        type="text" name="AMOUNT" class="signuptextfield" value=""  autocomplete="off" required/></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>
                <tr>
                    <td width="28%" align="right" valign="middle" class="labelfont">CUSTOMER NAME: </td>
                    <td width="65%" align="left" valign="middle"><input
                        type="text" name="CUST_NAME" class="signuptextfield" value="" autocomplete="off" required/></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>			
                        
                <tr>
                    <td width="28%" align="right" valign="middle" class="labelfont">CUSTOMER ADDRESS: </td>
                    <td width="65%" align="left" valign="middle"><input
                        type="text" name="CUST_STREET_ADDRESS1" class="signuptextfield" value="" autocomplete="off" required/></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>					
                <tr>
                    <td width="28%" align="right" valign="middle" class="labelfont">CUSTOMER ZIP: </td>
                    <td width="65%" align="left" valign="middle"><input
                        type="text" name="CUST_ZIP" value="" class="signuptextfield" autocomplete="off" required/></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>
                <tr>
                    <td width="28%" align="right" valign="middle" class="labelfont">CUSTOMER PHONE: </td>
                    <td width="65%" align="left" valign="middle"><input
                        type="text" name="CUST_PHONE" value="" class="signuptextfield" autocomplete="off" required/></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>
                <tr>
                    <td width="28%" align="right" valign="middle" class="labelfont">CUSTOMER EMAILID: </td>
                    <td width="65%" align="left" valign="middle"><input
                        type="text" name="CUST_EMAIL" class="signuptextfield" value="" autocomplete="off" required/></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>
                <tr>
                    <td width="28%" align="right" valign="middle" class="labelfont">PRODUCT DESC: </td>
                    <td width="65%" align="left" valign="middle"><input
                        type="text" name="PRODUCT_DESC" class="signuptextfield wallet_address" value="" autocomplete="off" required readonly/></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3" align="center" valign="middle">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3" align="center" valign="middle">
                    <input type="submit" id="button" class="signupbutton" value="Pay Now" onclick="javascript:submitForm()"/>						
                    </td>
                </tr>
                <tr>
                    <td colspan="3" align="center" valign="middle">&nbsp;</td>
                </tr>
            </table>
        </form>
    </div>
</body>

<script>
    let accounts;
    let account;
    let contract;
    const wallet_address = document.querySelector('.wallet_address');

    async function getAccount() {
        accounts = await ethereum.request({ method: 'eth_requestAccounts' });
        account = accounts[0];
        wallet_address.value = account;
    }
    getAccount();
</script>
</html>
<?php /**PATH E:\gamepay\resources\views/merchants/index.blade.php ENDPATH**/ ?>