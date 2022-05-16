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
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
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
                        <div class="col-12">
                            <div>
                                <div class="p-3 py-4 boxblue bg-light mb-3 text-info text-center">

                                    <h4 class="pb-3 text-blue">Verify KYC</h4>

                                    <div id="veriff-root" style="max-width: 600px;">Fastrack Verify</div>
                                </div>
                                <div class="p-3 py-4 boxblue text-center">
                                    <span class="centeror">OR</span>
                                    <h4 class=" text-center text-blue pb-3">Upload Documents</h4>
                                    <h3 style="color: cornflowerblue">
                                        @if (!empty($status))
                                            {{ $status }}
                                        @endif
                                    </h3>
                                    <form method="POST" action="{{ route('securepay.kyc.manual') }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $user_id }}" />
                                        <input type="hidden" name="deposit_id" value="{{ $deposit_id }}" />
                                        @if (empty($status))
                                        <div class="form-group">
                                            <label for="formFileLg" class="form-label text-blue">Please Upload Doc
                                                front</label>
                                            <input class="form-control form-control-lg" id="front" type="file"
                                                name="front" required/>
                                        </div>
                                        <div class="form-group">
                                            <label for="formFileLg" class="form-label text-blue">Please Upload Doc
                                                back</label>
                                            <input class="form-control form-control-lg" id="back" type="file"
                                                name="back" required/>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-lg">Upload</button>
                                        @else
                                        <div class="form-group">
                                            <p class="form-control form-control-lg" id="front">{{ $pan_front }}</p>
                                        </div>
                                        <div class="form-group">
                                            <p class="form-control form-control-lg" id="front">{{ $pan_back }}</p>
                                        </div>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src='https://cdn.veriff.me/sdk/js/1.1/veriff.min.js'></script>
    <script src='https://cdn.veriff.me/incontext/js/v1/veriff.js'></script>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script>
        const veriff = Veriff({
            host: 'https://stationapi.veriff.com',
            apiKey: 'fb35de50-15ca-420f-bf0b-e20f99a583ef',
            parentId: 'veriff-root',
            onSession: function(err, response) {
                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    method: "post",
                    data: {
                        cust_name: $("#veriff-given-name").val(),
                        user_id: "{{ $user_id }}",
                        veriff_id: response.verification.id,
                        veriff_url: response.verification.url,
                        sessionToken: response.verification.sessionToken,
                        wallet_address: $('#veriff-vendor-data').val()
                    },
                    url: "{{ route('securepay.kyc.process') }}",
                    success: function(resp) {
                        if (resp.status == "exist") {
                            window.location.replace(resp.url);
                        } else if (resp.status == "success") {
                            window.location.replace(response.verification.url);
                        }
                    }
                });
                window.veriffSDK.createVeriffFrame({
                    url: response.verification.url
                });
            }
        });
        veriff.setParams({
            person: {
                givenName: ' ',
                lastName: ' '
            },
            vendorData: ' '
        });
        veriff.mount();
    </script>
</body>

</html>
