@extends('layouts.app')
@section('contents')
<div class="animated fadeIn"> 
  <!--  profile details -->
  <div class="row">
    <div class="col-lg-12">
      <div class="box1 mb-5 overflow-hidden">
        <div class="bg-lightgrey bg-soft">
          <div class="row">
            <div class="col-7">
              <div class="text-primary p-3">
                <h4 class="text-dark pb-2">My Profile</h4>
                <p class="text-dark font-weight-bold">{!! $user->first_name !!} {{" "}} {!! $user->last_name!!}</p>
              </div>
            </div>
            <div class="col-5 d-flex justify-content-end"> <img src="{{asset('assets/images/img.png')}}" alt="" class="img-fluid"> </div>
          </div>
        </div>
        <div class="card-body pt-0">
          <div class="row">
            <div class="col-sm-2">
              <div class="avatar-md profile-user-wid"> <span class="usericon">EG</span> </div>
            </div>
            <div class="col-sm-10">
              <div class="pt-4">
                <div class="row">
                  <div class="col-12 mb-3 mb-md-0 col-md-6">
                    <h5 class="font-size-15"><strong>Email:</strong></h5>
                    <p class="text-muted mb-0">{{$user->email}}</p>
                  </div>
                  <div class="col-12 mb-3 mb-md-0 col-md-6">
                    <h5 class="font-size-15"><strong>Mobile:</strong></h5>
                    <p class="text-muted mb-0">{{$user->mobile}}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /# column --> 
  </div>
  <!--  profile details --> 
  <!--  profile  -->
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h4 class="box-title">Validation Information </h4>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <div class="card-body">
              <div class="">
                <div class="row tabtext">
                  <div class="col-12 col-md-3 my-2 text-muted">Email Address:</div>
                  <div class="col-12 col-md-9 my-2 text-muted wordwrap">{{$user->email}}</div>
                </div>
                <div class="row tabtext">
                  <div class="col-12 col-md-3 my-2 text-muted">Mobile No:</div>
                  <div class="col-12 col-md-9 my-2 text-muted wordwrap">{{$user->mobile}}</div>
                </div>
                <div class="row tabtext">
                  <div class="col-12 col-md-3 my-2 text-muted">Password:</div>
                  <div class="col-12 col-md-9 my-2 text-muted wordwrap">
                    <button class="btn red-btns" data-toggle="modal" data-target="#changepassword" style="font-size: 10px;" type="submit">Change Password</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h4 class="box-title">KYC <span class="alert-warning p-1 ml-2 borderradius"><small>Pending</small></span> <span class="alert-success p-1 ml-2 borderradius"><small>Verified</small></span> <a href="{{ route('profile.edit') }}" class="btn btn-outline-success float-right" > Edit</a></h4>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <div class="card-body">
              <div class="">
                <div class="row tabtext">
                  <div class="col-12 col-md-3 my-2 text-muted">First Name:</div>
                  <div class="col-12 col-md-9 my-2 text-muted wordwrap">{{$user->first_name}}</div>
                </div>
                <div class="row tabtext">
                  <div class="col-12 col-md-3 my-2 text-muted">Last Name:</div>
                  <div class="col-12 col-md-9 my-2 text-muted wordwrap">{{$user->last_name}}</div>
                </div>
                <div class="row tabtext">
                  <div class="col-12 col-md-3 my-2 text-muted">Date of Birth:</div>
                  <div class="col-12 col-md-9 my-2 text-muted wordwrap">16.06.1996</div>
                </div>
                <div class="row tabtext">
                  <div class="col-12 col-md-3 my-2 text-muted">Gender:</div>
                  <div class="col-12 col-md-9 my-2 text-muted wordwrap">Male</div>
                </div>
                <div class="row tabtext">
                  <div class="col-12 col-md-3 my-2 text-muted">Address Line 1:</div>
                  <div class="col-12 col-md-9 my-2 text-muted wordwrap">N/A</div>
                </div>
                <div class="row tabtext">
                  <div class="col-12 col-md-3 my-2 text-muted">Address Line 2:</div>
                  <div class="col-12 col-md-9 my-2 text-muted wordwrap">N/A</div>
                </div>
                <div class="row tabtext">
                  <div class="col-12 col-md-3 my-2 text-muted">City:</div>
                  <div class="col-12 col-md-9 my-2 text-muted wordwrap">Chennai</div>
                </div>
                <div class="row tabtext">
                  <div class="col-12 col-md-3 my-2 text-muted">State:</div>
                  <div class="col-12 col-md-9 my-2 text-muted wordwrap">Tamilnadu</div>
                </div>
                <div class="row tabtext">
                  <div class="col-12 col-md-3 my-2 text-muted">Pincode:</div>
                  <div class="col-12 col-md-9 my-2 text-muted wordwrap">6532410</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /# column -->
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h4 class="box-title">Banking Information <span class="alert-warning p-1 ml-2 borderradius"><small>Pending</small></span> <span class="alert-success p-1 ml-2 borderradius"><small>Verified</small></span> <a href="" class="btn btn-outline-success float-right"> Edit</a></h4>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <div class="card-body">
              <div class="">
                <div class="row tabtext">
                  <div class="col-12 col-md-3 my-2 text-muted">PAN:</div>
                  <div class="col-12 col-md-9 my-2 text-muted wordwrap">ABDJHE2254D</div>
                </div>
                <div class="row tabtext">
                  <div class="col-12 col-md-3 my-2 text-muted">Account No:</div>
                  <div class="col-12 col-md-9 my-2 text-muted wordwrap">98765430000010</div>
                </div>
                <div class="row tabtext">
                  <div class="col-12 col-md-3 my-2 text-muted">IFSC:</div>
                  <div class="col-12 col-md-9 my-2 text-muted wordwrap">ICICI00000202</div>
                </div>
                <div class="row tabtext">
                  <div class="col-12 col-md-3 my-2 text-muted">ID Proof Status:</div>
                  <div class="col-12 col-md-9 my-2 text-muted wordwrap"><span class="alert-success p-1 borderradius"><small>Verified</small></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /# column --> 
  </div>
  <!--  /profile -->
  <div class="clearfix"></div>
</div>
@endsection