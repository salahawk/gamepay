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
    <div class="container" style="padding-top: 10%;">
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

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>

    <script>
        $(document).on('click', '.btnSubmit', function() {
            $(this).prop('disabled', true);
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                method: "post",
                url: "{{ route('securepay.validate') }}",
                data: {
                    payer_address: $('input[name="payeraddress"]').val(),
                    external_user_id: "{{ $external_user_id }}",
                },
                success: function(resp) {
                    console.log(resp);
                    $('.btnSubmit').prop('disabled', false);
                    // if (resp.status == "fail") {
                    //   alert("Sorry, but you are no longer valid to make a transaction.");
                    // }
                    document.querySelector('html').innerHTML = resp;
                },
            });
        });
    </script>
</body>

</html>