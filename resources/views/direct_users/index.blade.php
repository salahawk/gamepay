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
                                placeholder="Enter Amount" required>
                            <label for="amountLbID" style="color: #f00; display: none;">This field is required.</label>
                        </div>
                        <div class="col-6">
                            <select id="myDropdown" style="width:100%">
                                <option value="0" class="text-blue" data-description=""><span
                                        style="color:#ccc;">Currency</span></option>
                                <option value="0" class="text-blue"
                                    data-imagesrc="{{ asset('assets/img/Gamerupee.png') }}" data-description="">G
                                    RUPEE
                                </option>
                                <option value="1" class="text-blue"
                                    data-imagesrc="{{ asset('assets/img/JR_blue_d.png') }}" data-description="">USDT
                                </option>
                                <option value="2" class="text-blue"
                                    data-imagesrc="{{ asset('assets/img/jusd_d.png') }}" data-description="">BTC
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
                                    data-imagesrc="{{ asset('assets/img/binance.png') }}" data-description="">BSC
                                </option>
                                <option value="1" class="text-blue"
                                    data-imagesrc="{{ asset('assets/img/awax.png') }}" data-description="">AWAX
                                </option>
                                <option value="1" class="text-blue"
                                    data-imagesrc="{{ asset('assets/img/ethereum.png') }}" data-description="">ETH
                                </option>
                                <option value="1" class="text-blue"
                                    data-imagesrc="{{ asset('assets/img/polygon.png') }}" data-description="">POLYGON
                                </option>
                            </select>
                            <label for="myDropdown1LbID" style="color: #f00; display: none;">This field is
                                required.</label>
                        </div>
                        <div class="col-12 mb-2">
                            <input type="text" class="form-control border-radius6" id="wallet_address"
                                aria-describedby="name" placeholder="Enter Destination Address" >
                            <label for="wallet_addressLbID" style="color: #f00; display: none;">This field is
                                required.</label>
                            <p class="text-blue font14 text-center pt-1 pb-0 mb-0"><small>Ex:
                                    0xd4654ad4ad4sad4sa6dwq886wa4d5</small></p>
                        </div>
                        <div class="col-12 mb-2">
                            <input type="hidden" class="form-control border-radius6" id="customer_name" aria-describedby="name"
                                placeholder="Remarks">
                            <input type="text" class="form-control border-radius6" id="remarks" aria-describedby="name"
                                placeholder="Remarks">
                            <label for="remarksLbID" style="color: #f00; display: none;">This field is required.</label>
                        </div>
                        <div class="col-12 mb-2">
                            <input type="number" class="form-control border-radius6" id="inr_value"
                                aria-describedby="name" placeholder="INR Value" readonly>
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
                            <a href="#" class="btn btn-primary border-radius6 w-100 disabled" id="confirm_pay">Continue
                                to Pay</a>
                        </div>
                        {{-- <div class="col-12 mb-2 pt-2">
                            <div class="text-center"><img src="{{ asset('assets/img/upi.png') }}" /></div>
                        </div> --}}
                    </div>
                </div>

                <!---- Tab 1 content ends-->
            </div>
        </div>
    </div>

    <div class="container" style="padding-top: 10%; display: none">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-10">
                <div class="middlepage1 shadow p-4 text-center"> <img src="{{ asset('assets/img/upi.png') }}"
                        class="img-fluid" />
                    <h3 class="text-center text-dark font-weight-bold">UPI Payment</h3>
                    <div class="row justify-content-center align-items-center">
                        <div class="col-12 col-md-6">
                            <form>
                                <div>
                                    <p class="text-dark">Please Enter Your UPI ID</p>
                                    <div class="row pb-3">
                                        <div class="col-12">
                                            <input type="hidden" id="user_id">
                                            <input class="form-control form-control-lg text-center" name="payeraddress"
                                                type="text" placeholder="Eg: Yourphonenumber@apl" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-0 pb-0">
                                    <input type="button" class="btnSubmit btn btn-info" value="Verify & Proceed" />
                                </div>
                                <div class="form-group mb-0 pb-0">
                                    <div class="loader m-auto" id="loader-4"> <span></span> <span></span> <span></span>
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
                                            <label for="email_otpLbID" style="color: #f00; display: none;">Enter Valid
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
                                            <label for="email_codeLbID" style="color: #f00; display: none;">OTP
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
                                            <label for="exampleInputEmail1">Verify Your Mobile Number</label>
                                            <input type="number" class="form-control" id="mobile_number"
                                                aria-describedby="mobile" placeholder="1234567890">
                                            <label for="mobile_numberLbID" style="color: #f00; display: none;">Enter
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
                                            <label for="mobile_codeLbID" style="color: #f00; display: none;">OTP
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
    <script type="text/javascript" src="{{ asset('assets/js/pages/direct_users/index.js') }}"></script>
</body>

</html>
