<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>GamePay Admin</title>
  <link rel="apple-touch-icon" href="{{asset('assets/img/admin-merchant/fev.png')}}">
  <link rel="shortcut icon" href="{{asset('assets/img/admin-merchant/fev.png')}}">
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="{{asset('assets/css/admin-merchant/styles.css')}}" rel="stylesheet" />
  <link href="{{asset('assets/css/admin-merchant/custom.css')}}" rel="stylesheet" />
  <link href="{{asset('assets/css/admin-merchant/bootstrap-datepicker.css')}}" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.12.0/css/jquery.dataTables.min.css" rel="stylesheet" />
  @yield('header_styles')
</head>

<body class="sb-nav-fixed">
  @include('admin_merchant.layouts.top')
  <div id="layoutSidenav">
    <div id="layoutSidenav_nav">
      @include('admin_merchant.layouts.leftmenu')
    </div>
    <div id="layoutSidenav_content">

      @yield('contents')

      @include('admin_merchant.layouts.footer')
    </div>
  </div>


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="{{asset('assets/js/admin-merchant/bootstrap-datepicker.min.js')}}"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script src="{{asset('assets/js/admin-merchant/scripts.js')}}"></script>
  <script src="https://cdn.datatables.net/1.12.0/js/jquery.dataTables.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/web3/1.7.2-rc.0/web3.min.js" integrity="sha512-REWiGZVmhU2S5eIov/DuNrsq4djWnPaAHSvXrbLLLaI0r/gW+wh1utIzxt0iB4IQLgXhNDj5mR0YMBjrkKhVMA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script type="text/javascript">
    $(function() {

      // INITIALIZE DATEPICKER PLUGIN
      $('.datepicker').datepicker({
        clearBtn: true,
        autoclose: true,
        format: "yyyy-mm-dd"
      });


      // FOR DEMO PURPOSE
      $('#reservationDate').on('change', function() {
        var pickedDate = $('input').val();
        $('#pickedDate').html(pickedDate);
      });
    });

    $("#sidenavAccordion").find('a').each(function() {
      $(this).removeClass('active');
    });

    let urlArray = window.location.href.split("/");
    $("." + urlArray[urlArray.length - 1].replace("#", "")).addClass('active');




    let abi = [{"inputs":[{"internalType":"string","name":"name","type":"string"},{"internalType":"string","name":"symbol","type":"string"},{"internalType":"uint8","name":"decimals","type":"uint8"},{"internalType":"address","name":"_peg_token","type":"address"},{"internalType":"uint256","name":"_peg_exchange_rate","type":"uint256"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":true,"internalType":"address","name":"spender","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"uint256","name":"id","type":"uint256"},{"indexed":false,"internalType":"address","name":"merchant","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"Approve_Chargeback","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"uint256","name":"id","type":"uint256"},{"indexed":false,"internalType":"address","name":"merchant","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"Reject_Chargeback","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"uint256","name":"id","type":"uint256"},{"indexed":false,"internalType":"address","name":"merchant","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"Request_Chargeback","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"inputs":[{"internalType":"address","name":"operator","type":"address"},{"internalType":"address","name":"merchant","type":"address"}],"name":"add_operator_merchant","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"admin_fee_wallet","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"address","name":"spender","type":"address"}],"name":"allowance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"approve","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"id","type":"uint256"}],"name":"approve_chargeback","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"banker","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"burn","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"account","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"burnFrom","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"chargeback_wallet","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"clerk_info","outputs":[{"internalType":"address","name":"merchant","type":"address"},{"internalType":"uint256","name":"process_limit","type":"uint256"},{"internalType":"enum GAMERE.ClerkType","name":"clerk_type","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"subtractedValue","type":"uint256"}],"name":"decreaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"find_valid_release_index","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"gate_keeper","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"account","type":"address"},{"internalType":"uint256","name":"releaseIndex","type":"uint256"}],"name":"harvest","outputs":[{"internalType":"uint256","name":"pendingAmount","type":"uint256"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"addedValue","type":"uint256"}],"name":"increaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"merchant_rate","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"merchant_to_operator","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"account","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"mint","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"minter_info","outputs":[{"internalType":"address","name":"merchant","type":"address"},{"internalType":"uint256","name":"minting_limit","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"operator_rate","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"paid_rolling_reserve_amount","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"peg_exchange_rate","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"peg_token","outputs":[{"internalType":"contract IERC20","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"message_hash","type":"uint256"},{"internalType":"uint256","name":"response_hash","type":"uint256"},{"internalType":"enum GAMERE.TransferStatus","name":"process_status","type":"uint8"}],"name":"process_transfer","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"id","type":"uint256"}],"name":"reject_chargeback","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"renounceOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"merchant","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"request_chargeback","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"rolling_reserve_rate","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"rolling_reserve_wallet","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"_admin_fee_wallet","type":"address"}],"name":"set_admin_fee_wallet","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"_banker","type":"address"}],"name":"set_banker","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"_chargeback_wallet","type":"address"}],"name":"set_chargeback_wallet","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"clerk","type":"address"},{"internalType":"enum GAMERE.ClerkType","name":"clerk_type","type":"uint8"},{"internalType":"address","name":"merchant","type":"address"},{"internalType":"uint256","name":"limit","type":"uint256"}],"name":"set_clerk_info","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"_gate_keeper","type":"address"}],"name":"set_gate_keeper","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"minter","type":"address"},{"internalType":"address","name":"merchant","type":"address"},{"internalType":"uint256","name":"limit","type":"uint256"}],"name":"set_minter_info","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"minter","type":"address"},{"internalType":"uint256","name":"limit","type":"uint256"}],"name":"set_minting_limit","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"contract IERC20","name":"_peg_token","type":"address"},{"internalType":"uint256","name":"_peg_exchange_rate","type":"uint256"}],"name":"set_peg_token","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"clerk","type":"address"},{"internalType":"uint256","name":"limit","type":"uint256"}],"name":"set_process_limit","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"rate","type":"uint256"}],"name":"set_rolling_reserve_rate","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"_rolling_reserve_wallet","type":"address"}],"name":"set_rolling_reserve_wallet","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"rate","type":"uint256"}],"name":"set_tx_fee_rate","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"_tx_fee_wallet","type":"address"}],"name":"set_tx_fee_wallet","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"rate","type":"uint256"}],"name":"set_withdrawal_fee_rate","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"_withdrawal_fee_wallet","type":"address"}],"name":"set_withdrawal_fee_wallet","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"_withdrawal_wallet","type":"address"}],"name":"set_withdrawal_wallet","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"total_rolling_reserve_amount","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transfer","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"sender","type":"address"},{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transferFrom","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"amount","type":"uint256"},{"internalType":"uint256","name":"message_hash","type":"uint256"}],"name":"transfer_to_banker","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"tx_fee_rate","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tx_fee_wallet","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"message_hash","type":"uint256"}],"name":"verify_transfer","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"token","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"withdrawByAdmin","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"withdrawal_fee_rate","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"withdrawal_fee_wallet","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"withdrawal_wallet","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"}]
    
    async function onClickConnect() {
      const tokenInfo = {
        address: '0xfc714A248F7170674955B7f1BEBA13E0690E62F8',
        name: 'GAMERE'
      };

      const web3 = new Web3(Web3.givenProvider);
      const accounts = await web3.eth.requestAccounts();
      const account = accounts[0];

      $('#btnNavbarSearch').removeClass("btn-dark");
      $('#btnNavbarSearch').addClass("btn-success");
      $('#btnNavbarSearch').text(account.substr(0, 11) + "...");
      $('#btnNavbarSearch').attr('disabled', 'disabled');

      let contract = new web3.eth.Contract(abi, tokenInfo.address);
      let bal = await contract.methods.balanceOf(account).call();
      let gamereBal = Web3.utils.fromWei(bal, 'ether');
      console.log(gamereBal);
      $(document).find('.balance').text(gamereBal + " GR");
    }
  </script>
  @yield('footer_scipts')

  <!-- Modal withdraw money -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog adminmodal" role="document">
      <div class="modal-content bg-blue">
        <div class="modal-header">
          <h5 class="modal-title text-white" id="exampleModalLabel">Withdraw </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div>
            <div class="bg-light rounded p-2">
              <div class="row">
                <div class="col-5 my-auto"><img src="{{asset('assets/img/admin-merchant/gamerupee.svg')}}" width="27px" /> <strong>GAMERE</strong></div>
                <div class="col-7"><input type="text" class="form-control forninput mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="00.00"></div>
              </div>
              <div class="row">
                <div class="col-12">
                  <p class="pb-0 mb-0 text-muted">Available Balance: 00.00</p>
                </div>
              </div>
            </div>
            <a href="#" class="btn btn-info w-100 mt-3">Withdraw</a>
          </div>
        </div>

      </div>
    </div>
  </div>
  <!-- Modal add money -->
  <div class="modal fade" id="addmoney" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog adminmodal" role="document">
      <div class="modal-content bg-blue">
        <div class="modal-header">
          <h5 class="modal-title text-white" id="exampleModalLabel">Add Money </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div>
            <div class="bg-light rounded p-2">
              <div class="row">
                <div class="col-5 my-auto"><img src="{{asset('assets/img/admin-merchant/gamerupee.svg')}}" width="27px" /> <strong>GAMERE</strong></div>
                <div class="col-7"><input type="text" class="form-control forninput mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="00.00"></div>
              </div>
              <div class="row">
                <div class="col-12">
                  <p class="pb-0 mb-0 text-muted">Available Balance: 00.00</p>
                </div>
              </div>
            </div>
            <a href="#" class="btn btn-info w-100 mt-3">Add</a>
          </div>
        </div>

      </div>
    </div>
  </div>
</body>

</html>