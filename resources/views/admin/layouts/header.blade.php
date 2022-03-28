<div class="container-fluid">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
            data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <i class="fa fa-align-right"></i>
        </button>
        <?php if(isset($_SESSION['usertype']) && $_SESSION['usertype']=='admin'){?>
        <a class="navbar-brand" href="dashboard.php"><?php echo $merchantHead; ?> <span class="main-color">PANEL</span></a>
        <?php } elseif(isset($_SESSION['usertype']) && $_SESSION['support']==1){?>
        <a class="navbar-brand" href="#">Support Manager <span class="main-color">PANEL</span></a>
        <?php } elseif(isset($_SESSION['usertype']) && $_SESSION['support']==2){?>
        <a class="navbar-brand" href="#">Support Team <span class="main-color">PANEL</span></a>
        <?php } else{?>
        <a class="navbar-brand" href="dashboard.php"><?php echo 'userHead'; ?> <span class="main-color">PANEL</span></a>
        <?php } ?>
    </div>
    <div class="collapse navbar-collapse navbar-right" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
            <li><a href="#" title="Expand screen"><i data-show="show-side-navigation1" class="fa fa-bars show-side-btn"></i></a></li>
            <li><a title="Log Out" class="log-out" href="logout.php"><i class="fa fa-power-off"
                        aria-hidden="true"></i></a></li>
        </ul>
    </div>
</div>
<script>
    $('[title="Expand screen"]').click(function() {
        $('.side-nav').toggleClass("show-side-nav");
        $('#contents').toggleClass("margin");
    });
</script>