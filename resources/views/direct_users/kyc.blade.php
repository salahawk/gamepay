@extends('layouts.app')
@section('contents')
<div class="animated fadeIn">
  <div class="row">
    <div class="col-lg-12">
        <div class="card">
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
                                @if (empty($status))
                                <div class="p-3 py-4 boxblue bg-light mb-3 text-info text-center">

                                    <h4 class="pb-3 text-blue">Verify KYC</h4>

                                    <div id="veriff-root" style="max-width: 600px;">Fastrack Verify</div>
                                </div>
                                @endif
                                <div class="p-3 py-4 boxblue text-center">
                                  @if (empty($status))<span class="centeror">OR</span>@endif
                                    <h4 class=" text-center text-blue pb-3">Upload Documents</h4>
																		<h3 style="color: cornflowerblue">@if(!empty($status)) {{$status}} @endif</h3>
                                    <form method="POST" action="{{ route('kyc-manual') }}" enctype="multipart/form-data">
																				@csrf
																				<input type="hidden" name="user_id" value="{{$user_id}}" />
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
  </div>
</div>
</div>
</div>
@endsection

@section('footer_scripts')
    <script src='https://cdn.veriff.me/sdk/js/1.1/veriff.min.js'></script>
    <script src='https://cdn.veriff.me/incontext/js/v1/veriff.js'></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
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
                    url: "{{ route('kyc-process') }}",
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

    @endsection
</body>

</html>
