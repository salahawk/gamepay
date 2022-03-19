@extends('admin.layouts.app')

@section('content')

<div class="welcome">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="content">
          <h2>Welcome to Dashboard</h2>
          <p>We are working to enhance your experience!</p>
        </div>
      </div>
    </div>
  </div>
</div>
<section class="statistics">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3">
        
        <div class="panel-crd crd-1">
      <div class="simple-widget">
            <div class="growth"><i class="fa fa-file-text" aria-hidden="true"></i></div>
            <h3> {{ $settelemeAmt }}</h3>
            <p>Settlement</p>
            <div class="more-div">
              <a href="view_gw_settlement.php" class="more-btn">More</a>
            </div>
          </div>
    </div>
      </div>
      <div class="col-md-3">
        <div class="panel-crd crd-2">
      <div class="simple-widget">
            <div class="growth"><i class="fa fa-money" aria-hidden="true"></i></div>
            <h3> {{ $expenseAmt }}</h3>
            <p>Expenses</p>
            <div class="more-div">
              <a href="view_expenses.php" class="more-btn">More</a>
            </div>
          </div>
    </div>
      </div>
      <div class="col-md-3">
        <div class="panel-crd crd-3">
      <div class="simple-widget">
            <div class="growth"><i class="fa fa-arrow-up" aria-hidden="true"></i></div>
            <h3> {{ $cashoutamount }}</h3>
            <p>Cash Outs</p>
            <div class="more-div">
              <a href="view_cash_detail.php" class="more-btn">More</a>
            </div>
          </div>
    </div>
      </div>
      <div class="col-md-3">
        <div class="panel-crd crd-1">
          <div class="simple-widget" style="background-image: -webkit-linear-gradient(right, #11f16b, #08ad4f);">
            <div class="growth"><i class="fa fa-credit-card-alt" aria-hidden="true"></i></div>
            <h3> {{ $total_avilable_balance }}</h3>
            <p>Available Balance</p>
            <div class="more-div">
              <a href="" class="more-btn">More</a>
            </div>
          </div>
          </div>
      </div>
    </div>
  </div>
</section>
<section class="charts">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
            
          <div id="myChart2"></div>
            <span class="hide-mark"></span>
            <script src="https://cdn.anychart.com/releases/8.0.0/js/anychart-base.min.js"></script>
            <script type="text/javascript">
              anychart.onDocumentReady(function() {
                // set the data
                var data = {
                  header: ["Name", "amount"],
                  rows: [
                          <?php
                            while($row = mysqli_fetch_array($flowresult,MYSQLI_ASSOC)) {	
                            if(isset($_SESSION['usertype']) && $_SESSION['usertype']!='admin') {  
                              $result = $row['date'];
                            } else {
                              $month=date('M',strtotime($row['date']));
                              $day=date('d',strtotime($row['date']));
                              $result = $day." ".$month;
                            }
                          ?>
                          ["<?php echo $result;?>", <?php echo $row['amt'];?>],						
                          <?php } ?>
                ]};
                // create the chart
                var chart = anychart.column();
                // add data
                chart.data(data);
                // set the chart title
                chart.title("Deposit Statistics");
                // draw
                chart.container("myChart2");
                chart.draw();
              });
            </script>
      </div>
      <div class="col-md-6">
        <section class="pie-c">
            <div class="pieID-center">
              <div class="pieID pie">
              
              </div>
            </div>
            <ul class="pieID legend">
              <li>
                <em>Settlements</em>
                <span>{{ $settelemeAmt }}</span>
              </li>
              <li>
                <em>Expenses</em>
                <span> {{ $expenseAmt }}</span>
              </li>
              <li>
                <em>Cashouts</em>
                <span> {{ $cashoutamount }}</span>
              </li>
              <li>
                <em>Available balance</em>
                <span> {{ $total_avilable_balance }}</span>
              </li>
            </ul>
        </section>
      </div>
    </div>
  </div>

      <script src=" {{asset('assets/js/createPie.js') }}"></script>

  <hr>
</section>

<section class="srch-tb">
    <div class="dataTables_length" id="dataTable_length"> <select name="dataTable_length" aria-controls="myTable" class="custom-select custom-select-sm form-control form-control-sm"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select></div>
    <div class="search-bar-div">
      <input class="form-control search-box" id="myInput" type="text" placeholder="Search table..">
      <i class="fa fa-search fa-fw search-ic-in" aria-hidden="true"></i>
    </div>
</section>

<section class="h-tb-sec">
  <div class="h-tb">
    <div class="home-tb">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Transaction ID </th>
                <th>Email </th>
                <th>Date and time</th>
                <th>Amount</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="myTable">
              @foreach($lastrecord as $rowfive)
              {{ $status = strtoupper(substr($rowfive['status'], 0, 1)); }}					
              <tr>
                <td><a id="ord-id" href="">{{ $rowfive['txnid'] }}</a></td>
                <td><a id="ord-id" href="">{{ $rowfive['email'] }}</a></td>
                <td>
                  <span class="text-muted"><i class="wb wb-time"></i>{{ $rowfive['created_date'] }}</span>
                </td>
                <td>{{ $rowfive['amount'] }}</td>
                <td>
                  @if($status=='S')
                  <div class="badge badge-table badge-success">{{ $rowfive['status'] }}</div>
                  @elseif($status=='F')
                  <div class="badge badge-table badge-canceled">{{ $rowfive['status'] }}</div>
                  @elseif($status=='')
                  <div class="badge badge-table badge-pending">{{ 'Initiated' }}</div>
                @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
  </div>
</section>
<br>
<br>
</section>
@endsection
@section('footer_script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>
<script src="{{asset('assets/js/main.js') }}"></script>
<script src="{{asset('js/demo/datatables-demo.js') }}"></script>
@endsection
