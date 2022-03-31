<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Merchant Checkout Page</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <style type="text/css">
        body {
            width: 100%;
            margin: 0 auto;
            background-color: #e4eff5;
        }

        .new {
            width: 500px;
            margin: 20px auto 0 auto;
            padding: 0;
            font: normal 12px arial;
            color: #555;
            background: #fff;
            border: 1px solid #d0d0d0;
            border-radius: 5px;
            -webkit-box-shadow: -1px 3px 8px -1px rgba(0, 0, 0, 0.75);
            -moz-box-shadow: -1px 3px 8px -1px rgba(0, 0, 0, 0.75);
            box-shadow: -1px 3px 8px -1px rgba(0, 0, 0, 0.75);
        }

        .signupbox {
            margin: 20px auto 0 auto;
            padding: 0;
            font: normal 12px arial;
            color: #555;
            background: #fff;
            border: 1px solid #d0d0d0;
            border-radius: 5px;
            -webkit-box-shadow: -1px 3px 8px -1px rgba(0, 0, 0, 0.75);
            -moz-box-shadow: -1px 3px 8px -1px rgba(0, 0, 0, 0.75);
            box-shadow: -1px 3px 8px -1px rgba(0, 0, 0, 0.75);
        }

        .signup-headingbg {
            background: #194e84;
            background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);
            background-image: linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);
            background-size: 5px 5px;
            height: 35px;
            border-bottom: 1px solid #dadada;
            font: bold 16px Tahoma;
            color: #ffffff;
            vertical-align: middle;
        }

        .signuptextfield {
            display: block;
            width: 98%;
            height: 15px;
            padding: 6px 7px;
            padding: 6px\9;
            margin-left: 10px;
            font-size: 12px;
            font-family: 'Titillium Web', sans-serif;
            line-height: 1.428571429;
            color: #555;
            margin-bottom: 5px;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
            -webkit-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
            transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
        }

        .signuptextfield:focus {
            border-color: #66afe9;
            outline: 0;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, .6);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, .6);
        }

        .labelfont {
            font: bold 11px Arial;
            color: #607a8c;
            text-decoration: none;
        }

        .signupbutton {
            background-color: #5cb85c;
            border: 1px solid #4cae4c;
            width: 40%;
            height: 35px;
            font: bold 14px Tahoma;
            text-align: center;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
        }

        .signupbutton:hover {
            background-color: #449d44;
            border: 1px solid #398439;
            width: 40%;
            height: 35px;
            font: bold 14px Tahoma;
            text-align: center;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
        }

        .borderleftradius {
            border-top-left-radius: 5px;
        }

        .borderrightradius {
            border-top-right-radius: 5px;
        }

        .gradientbg {
            /* IE10 Consumer Preview */
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
            background-image: linear-gradient(to bottom, #FEFEFF 0%, #BFD3E1 100%);
        }

    </style>

</head>

<body style="display:block;">
    <div class="new" style="display: block;">
        <form method="" id="form1" name="">
            <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" class="gradientbg">
                <tr>
                    <td colspan="3" align="center" valign="middle"></td>

                </tr>
                <tr>
                    <td colspan="3" align="center" valign="middle"
                        class="signup-headingbg borderleftradius borderrightradius">From Post</td>
                </tr>
                <tr>
                    <td align="right" valign="middle">&nbsp;</td>
                    <td align="center" valign="middle">&nbsp;</td>
                    <td align="center" valign="middle">&nbsp;</td>
                </tr>
                <tr>
                    <td width="28%" align="right" valign="middle" class="labelfont">KEY: </td>
                    <td width="65%" align="left" valign="middle"><input type="text" name="KEY" class="signuptextfield"
                            value="123456" autocomplete="off" id="key" readonly/></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>
                <tr>
                    <td width="28%" align="right" valign="middle" class="labelfont">TXNID: </td>
                    <td width="65%" align="left" valign="middle"><input type="text" id="TXN_ID" name="TXNID"
                            class="signuptextfield" value="" autocomplete="off" readonly/></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>
                <tr>
                    <td width="28%" align="right" valign="middle" class="labelfont">AMOUNT: </td>
                    <td width="65%" align="left" valign="middle"><input type="text" name="AMOUNT" class="signuptextfield"
                            value="" autocomplete="off" /></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>

                <tr>
                    <td width="28%" align="right" valign="middle" class="labelfont">CUSTOMER_NAME: </td>
                    <td width="65%" align="left" valign="middle"><input type="text" name="CUSTOMER_NAME"
                            class="signuptextfield" value="" autocomplete="off" /></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>
                </tr>
                <tr>
                    <td width="28%" align="right" valign="middle" class="labelfont">EMAIL: </td>
                    <td width="65%" align="left" valign="middle"><input type="text" name="EMAIL" class="signuptextfield"
                            value="" autocomplete="off" /></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>
                </tr>
                <tr>
                    <td width="28%" align="right" valign="middle" class="labelfont">PHONE: </td>
                    <td width="65%" align="left" valign="middle"><input type="text" name="PHONE" class="signuptextfield"
                            value="" autocomplete="off" /></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>

                <tr>
                    <td width="28%" align="right" valign="middle" class="labelfont">CRYPTO: </td>
                    <td width="65%" align="left" valign="middle"><input type="text" name="CRYPTO"
                            class="signuptextfield" value="" autocomplete="off" /></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>
                <tr>
                    <td width="28%" align="right" valign="middle" class="labelfont">NETWORK: </td>
                    <td width="65%" align="left" valign="middle"><input type="text" name="NETWORK" value=""
                            class="signuptextfield" autocomplete="off" /></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>
                <tr>
                    <td width="28%" align="right" valign="middle" class="labelfont">ADDRESS: </td>
                    <td width="65%" align="left" valign="middle"><input type="text" name="ADDRESS" value=""
                            class="signuptextfield" autocomplete="off" /></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>
                <tr>
                    <td width="28%" align="right" valign="middle" class="labelfont">REMARKS: </td>
                    <td width="65%" align="left" valign="middle"><input type="text" name="REMARKS"
                            class="signuptextfield" value="Testing remarks" autocomplete="off" readonly /></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>
                <tr>
                    <td width="28%" align="right" valign="middle" class="labelfont">KYC_STATUS: </td>
                    <td width="65%" align="left" valign="middle"><input type="text" name="KYC_STATUS"
                            class="signuptextfield" value="" autocomplete="off" /></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>
                <tr>
                    <td width="28%" align="right" valign="middle" class="labelfont">EMAIL_STATUS: </td>
                    <td width="65%" align="left" valign="middle"><input type="text" name="EMAIL_STATUS"
                            class="signuptextfield" value="" autocomplete="off" /></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>
                <tr>

                    <td width="28%" align="right" valign="middle" class="labelfont">MOBILE_STATUS: </td>
                    <td width="65%" align="left" valign="middle"><input type="text" name="MOBILE_STATUS"
                            class="signuptextfield" value="" autocomplete="off" /></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>
                <tr>

                    <td width="28%" align="right" valign="middle" class="labelfont">SURL: </td>
                    <td width="65%" align="left" valign="middle"><input type="text" name="SURL" class="signuptextfield"
                            value="https://gamepay.online/securepay/success" autocomplete="off" readonly/></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>
                <tr>

                    <td width="28%" align="right" valign="middle" class="labelfont">EURL: </td>
                    <td width="65%" align="left" valign="middle"><input type="text" name="EURL" class="signuptextfield"
                            value="https://gamepay.online/securepay/fail" autocomplete="off" readonly/></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>
                <tr>

                    <td width="28%" align="right" valign="middle" class="labelfont">CURL: </td>
                    <td width="65%" align="left" valign="middle"><input type="text" name="CURL" class="signuptextfield"
                            value="https://gamepay.online/securepay/callback" autocomplete="off" readonly/></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>
                <tr style="display: none;">
                    <td width="28%" align="right" valign="middle" class="labelfont">HASH: </td>
                    <td width="65%" align="left" valign="middle"><input type="hidden" name="HASH" class="signuptextfield"
                            value="" autocomplete="off" id="hash" readonly /></td>
                    <td width="7%" align="left" valign="middle">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3" align="center" valign="middle">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3" align="center" valign="middle">
                        <input type="button" id="submit_button" class="signupbutton" value="Deposit" />
                    </td>
                </tr>
                <tr>
                    <td colspan="3" align="center" valign="middle">&nbsp;</td>
                </tr>
            </table>
        </form>
    </div>

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/forge/0.8.2/forge.all.min.js"></script>
    <script>
        let salt = "salt123456789";
        let str = '';
        // let hashCalculated = '';
        $('#submit_button').on('click', function() {
            // caculate hash based on input params
            let formVal = $('#form1').serialize();
            value_obj = formVal.split("&").map(each => each.split("=")).reduce((a, b) => {
                return Object.assign(a, {
                    [b[0]]: b[1]
                })
            }, {});
            console.log(value_obj);
            let key_array = [
                "KEY",
                "TXNID",
                "AMOUNT",
                "CUSTOMER_NAME",
                "EMAIL",
                "PHONE",
                "CRYPTO",
                "NETWORK",
                "ADDRESS",
                "REMARKS",
                "KYC_STATUS",
                "EMAIL_STATUS",
                "MOBILE_STATUS",
                "SURL",
                "EURL",
                "CURL",
            ];

            key_array.forEach((item) => {
                str += value_obj[item] + "|";
            })
            str += salt;
            console.log("str: ", str);

            // hashCalculated = CryptoJS.SHA256(str);
            var md = forge.md.sha256.create();
            md.start();
            md.update(str, "utf8");
            var hashCalculated = md.digest().toHex();

            console.log("hashed: ", hashCalculated);


            value_obj["HASH"] = hashCalculated;

            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                method: "post",
                url: "{{ route('securepay.process') }}",
                data: value_obj,
                success: function(resp) {
                  
                },
            });
            // send ajax to securepay/process
        });

        // generate random txn id
        $('#TXN_ID').val(Math.floor(Math.random() * (999999999 - 100000000) ) + 100000000);
    </script>
</body>

</html>
