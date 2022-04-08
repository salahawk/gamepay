@extends('layouts.app')
@section('contents')
    <div class="animated fadeIn">
        <!--  profile details -->
        <div class="row">
            <div class="col-lg-12">
                <div class="box1 mb-5 overflow-hidden">
                    <div class="bg-lightgrey borderradius bg-soft p-3">
                        <div class="row justify-content-between">
                            <div class="col-8 col-md-6">
                                <h4 class="pb-3 text-info">Portfolio</h4>
                                <p class="text-muted mb-0 pb-0">Current Portfolio Value</p>
                                <h3 class="text-info pb-2 font-weight-bold"><img src="{{asset('assets/img/Gamerupee.png')}}" alt=""
                                    class="img-fluid"> {!! $total !!}</h3>
                            </div>
                            {{-- <div class="col-4 col-md-6 d-flex justify-content-end"> <img src="{{asset('assets/img/img1.png')}}" alt=""
                                    class="img-fluid"> </div> --}}
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
                    <div class="table-responsive">
                        <div class="table-stats order-table ov-h">
                            <table class="table ">
                                <thead>
                                    <tr>
                                        <th>Date/Time</th>
                                        <th>Wallet Address / Network</th>
                                        <th>Buy/Sell</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  @foreach($deposits as $deposit)
                                    <tr>
                                        <td>{!! $deposit->created_date !!}</td>
                                        <td>{!! $deposit->wallet!!}<br />
                                            <span class="font-weight-normal text-muted">{!! $deposit->crypto!!}</span>
                                        </td>
                                        <td>{!! $deposit->txn_type !!}</td>
                                        <td><img src="{{asset('assets/img/btc.png')}}" />{!! $deposit->amount !!}</td>
                                        <td><span class="badge badge-complete">{!! $deposit->status !!}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--  /profile -->
        <div class="clearfix"></div>
    </div>
@endsection
