


<?php $__env->startSection('header_style'); ?>
	<link href="<?php echo e(asset('assets/js/datatables/datatables.min.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('assets/js/datatables/plugins/bootstrap/datatables.bootstrap.css')); ?>" rel="stylesheet" type="text/css" />
    <script>
        function exportExcel() {
            var fromdate=$("#fromdate").val();
            var todate=$("#todate").val();
            window.location.href = 'fail_deposit_export.php?fromdate='+fromdate+'&todate='+todate;
        }

        var pos;
        <?php if(isset($_SESSION['usertype']) && $_SESSION['usertype']!='admin'): ?>
            pos=5;
        <?php else: ?>
            pos=4;
        <?php endif; ?>
        $(document).ready(function() {
            $('#dataTable').DataTable( {
                "order": [[ pos, "desc" ]]
            } );
        } );
    </script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
      <div class="welcome">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
              <div class="content">
                <h2>View Pending/Failure Deposits</h2>
              </div>
            </div>
          </div>
        </div>
      </div>
      <section class="forms-sec">
        <div class="main-frms">
          <div class="av-b total-dep">
            <div class="box-cs">
                 <span>Total Deposits Today </span>
                  <input type="text" class="inn-box" readonly="" value="&#8377 <?php echo e($totDeposit); ?>">
             </div>
          </div>
        </div>
      </section>
	  	<form action="" method="get">
			  <section class="frm-sec">
				<div class="row">
				  <div class="col-md-6">
				   
					  <div class="form-group">
							<label for="usr">Deposits Date From</label>
							<input type="date" class="ipt-usr in-cs effect-15 form-control" name="fromdate" id='fromdate' class="in-cs" value="" required="">
					</div>
				  </div>

				   <div class="col-md-6">

					<div class="form-group">
							<label for="usr">Deposits Date To</label>
							<input type="date" class="ipt-usr in-cs effect-15 form-control" name="todate" id='todate' class="in-cs"  value="" required="">
					</div>
					  
				  </div>
				  				    <div class="col-md-6">
				   
					  <div class="form-group">
							<label for="usr">Email</label>
							<input type="text" class="ipt-usr in-cs effect-15 form-control" name="emailid" id='emailid' class="in-cs" value="" >
					</div>
				  </div>

				   <div class="col-md-6">

					<div class="form-group">
							<label for="usr">Transaction Id</label>
							<input type="text" class="ipt-usr in-cs effect-15 form-control" name="transid" id='transid' class="in-cs"  value="" >
					</div>
					  
				  </div>
				  
				  <div class="col-md-6">

					<div class="form-group">
							<label for="usr">Minimal amount</label>
							<input type="text" class="ipt-usr in-cs effect-15 form-control" name="minamount" id='minamount' class="in-cs"  value="<?php echo e($minamount); ?>" >
					</div>
					  
				  </div>
				  
				  <div class="col-md-6">

					<div class="form-group">
							<label for="usr">Provider</label>
							<input type="text" class="ipt-usr in-cs effect-15 form-control" name="provider" id='provider' class="in-cs"  value="<?php echo e($service_provider); ?>" >
					</div>
					  
				  </div>
				  
				  <center>
					<button type="submit" class="btn btn-outline frm-btn" name="Search_val" value="settlementAdd">Get</button>
					&nbsp;
					<a onclick="exportExcel()" value="Download" class="btn btn-outline frm-btn">Download</a>
				  </center>
				</div>
				
			  </section>
	  	</form>
     <section class="h-tb-sec">
        <div class="h-tb">
          <div class="home-tb">
            
              <div class="table-responsive">

              <table class="table table-bordered" id="dataTable2">
 
				<thead>                   
						 <tr>
							<th>Transaction ID</th>
							<th>Email</th>
							<th>Gateway</th>
							<th>Amount</th>	
							<th>Status</th>						
							<th>Transaction Date</th>
							<th>Provider</th>
						  </tr>
				</thead>
				<tbody>	
				<tbody>
			</table>


  </div>
            </div>
        </div>
      </section>
      <br>
      <br>
<?php $__env->stopSection(); ?>
    
<?php $__env->startSection('footer_script'); ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>
    <script src='js/main.js'></script>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="js/demo/datatables-demo.js"></script>

	<script>
        var table = $('#dataTable2').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?php echo e(route('admin.pending.data')); ?>",
            type: "GET"
        },
        columns: [
            {data: 'txnid', name: 'txnid'},
            {data: 'email', name: 'email'},
            {data: 'bank', name: 'bank'},
            {data: 'amount', name: 'amount'},
            {data: 'status', name: 'status'},
            {data: 'created_date', name: 'created_date'},
            {data: 'provider', name: 'provider'},
        ],
    });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\Collabrate_bluepadu\gamepay\resources\views/admin/deposits/pending.blade.php ENDPATH**/ ?>