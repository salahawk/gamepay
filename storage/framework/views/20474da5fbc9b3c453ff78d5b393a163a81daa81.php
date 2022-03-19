<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,600,700,800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Droid+Sans" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/main.css')); ?>">

    <?php echo $__env->yieldContent('header_style'); ?>

</head>

<body>
    <!-- Nav with sidebar start-->
    <aside class="side-nav" id="show-side-navigation1">
        <i class="fa fa-bars close-aside hidden-sm hidden-md hidden-lg" data-close="show-side-navigation1"></i>
        <div class="heading">
            <img src="<?php echo e(asset('assets/img/newlogo.jpg')); ?>" alt="">
            <div class="info">
                <h3><a href="#">User</a></h3>
                <?php if (isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'admin') {
                    echo "<p>$merchantUser</p>";
                } elseif (isset($_SESSION['support']) && $_SESSION['support'] == 1) {
                    echo '<p>Support Manager</p>';
                } elseif (isset($_SESSION['support']) && $_SESSION['support'] == 2) {
                    echo '<p>Support Team</p>';
                } else {
                    echo "<p>'paymentUser'</p>";
                }
                ?>
            </div>
        </div>
        <?php echo $__env->make('admin.layouts.leftmenu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </aside>

    <section id="contents">
        <nav class="navbar navbar-default">
            <?php echo $__env->make('admin.layouts.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </nav>

        <!--content start-->
        <?php echo $__env->yieldContent('content'); ?>
        <!--content end-->

    </section>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    
    <?php echo $__env->yieldContent('footer_script'); ?>
</body>

</html>
<?php /**PATH D:\RapidGame\laravel\rapidpay\resources\views/admin/layouts/app.blade.php ENDPATH**/ ?>