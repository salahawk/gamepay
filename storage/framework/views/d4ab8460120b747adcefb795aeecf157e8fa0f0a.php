
<?php $__env->startSection('content'); ?>
    <div class="welcome">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="content">
                        <h2>View All Deposit</h2>
                        <p>We are working to enhance your experience!</p>
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
                    <input type="text" class="inn-box" readonly="" value="&#8377 ">
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
                        <input type="date" class="ipt-usr in-cs effect-15 form-control" name="fromdate" id='fromdate'
                            class="in-cs" value="">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="usr">Deposits Date To</label>
                        <input type="date" class="ipt-usr in-cs effect-15 form-control" name="todate" id='todate'
                            class="in-cs" value="">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="usr">Email</label>
                        <input type="text" class="ipt-usr in-cs effect-15 form-control" name="emailid" id='emailid'
                            class="in-cs" value="">
                    </div>
                </div>

                <div class="col-md-6">

                    <div class="form-group">
                        <label for="usr">Transaction Id</label>
                        <input type="text" class="ipt-usr in-cs effect-15 form-control" name="transid" id='transid'
                            class="in-cs" value="">
                    </div>

                </div>

                <div class="col-md-6">

                    <div class="form-group">
                        <label for="usr">Minimal amount</label>
                        <input type="text" class="ipt-usr in-cs effect-15 form-control" name="minamount" id='minamount'
                            class="in-cs" value="">
                    </div>

                </div>

                <div class="col-md-6">

                    <div class="form-group">
                        <label for="usr">Provider</label>
                        <input type="text" class="ipt-usr in-cs effect-15 form-control" name="provider" id='provider'
                            class="in-cs" value=" ">
                    </div>

                </div>

                <center>
                    <button type=" submit" class="btn btn-outline frm-btn" name="Search_val"
                        value="settlementAdd">Get</button><br>
                    <a onclick="exportExcel()" class="btn btn-outline frm-btn" name="submit"
                        value="settlementAdd">Download</a>
                </center>
            </div>

        </section>
    </form>
    <section class="h-tb-sec">
        <div class="h-tb">
            <div class="home-tb">

                <div class="table-responsive">

                    <table class="table table-bordered" id="dataTable">

                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Email</th>
                                <th>Gateway</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Transaction Date</th>
                                <th>Provider</th>
                                <th>Action</th>
                            </tr>
                        </thead>


                    </table>


                </div>
            </div>
        </div>

        <br>
        <br>
        <!-- section -->
    <?php $__env->stopSection(); ?>
	<?php $__env->startSection('footer_script'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>
    <script src="<?php echo e(asset('assets/js/main.js')); ?>"></script>
    
    
    
    
    <script src="<?php echo e(asset('js/demo/datatables-demo.js')); ?>"></script>
	<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\RapidGame\laravel\rapidpay\resources\views/admin/transactions/index.blade.php ENDPATH**/ ?>