<!DOCTYPE html>
<html lang="zxx">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Page Title -->
<title>Coinpaise</title>
<!-- / --> 

<!---Font Icon-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<!-- / --> 

<!-- Plugin CSS -->
<link href="{{asset('assets/static/plugin/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">

<!-- / --> 

<!-- Theme Style -->
<link href="{{asset('assets/static/css/styles.css')}}" rel="stylesheet">
<link href="{{asset('assets/static/css/color/default.css')}}" rel="stylesheet" id="color_theme">
<!-- / --> 

<!-- Favicon -->
<link rel="icon" href="{{asset('assets/img/fev.png')}}" />
<!-- / -->
</head>

<!-- Body Start -->
<body data-spy="scroll" data-target="#navbar" data-offset="98">

<!-- Loading -->
<div id="loading">
  <div class="load-circle"><span class="one"></span></div>
</div>
<!-- / --> 

<!-- Header -->
<header>
  <nav class="navbar header-nav fixed-top navbar-expand-lg header-nav-light">
    <div class="container"> 
      <!-- Brand --> 
      <a class="navbar-brand" href="{{ route('index') }}"> <img src="{{asset('assets/img/logo.png')}}"/></a> 
      <!-- / --> 
      
      <!-- Mobile Toggle -->
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation"> <span></span> <span></span> <span></span> </button>
      <!-- / --> 
      
      <!-- Top Menu -->
      <div class="collapse navbar-collapse justify-content-end" id="navbar">
        <ul class="navbar-nav ml-auto">
          <li><a class="nav-btn nav-link" href="{{ route('index')}}">Login</a></li>
          <li><a class="nav-btn nav-link" href="{{ route('index')}}">Sign Up</a></li>
        </ul>
      </div>
      <!-- / --> 
      
    </div>
    <!-- Container --> 
  </nav>
  <!-- Navbar --> 
</header>
<!-- Header End --> 

<!-- Main Start -->
<main> 

  @yield('contents')

</main>

<!-- Footer Start -->
<footer class="footer-light">
  <section class="footer-section">
    <div class="container">
      <div class="row">
        <div class="col-md-12 col-lg-5 sm-m-15px-tb md-m-30px-b">
          <h4 class="font-alt">About Us</h4>
          <p class="footer-text">We saw the potential in cryptocurrency to revolutionize the financial industry, making payments faster, more secure, and more reliable with lower costs. </p>
        </div>
        <!-- col -->
        
        <div class="col-6 col-md-4 col-lg-4 sm-m-15px-tb">
          <h4 class="font-alt">Company</h4>
          <ul class="fot-link">
             <li><a href="{{ route('privacy')}}">Privacy Policy</a></li>
            <li><a href="{{ route('terms')}}">Terms and Conditions</a></li>
            <li><a href="{{ route('refund-policy')}}">Refund Policy </a></li>
            <li><a href="{{ route('contact')}}">Contact us</a></li>
          </ul>
        </div>
        <!-- col -->
        
        <div class="col-md-4 col-lg-3 sm-m-15px-tb">
          <h4 class="font-alt">Get in touch</h4>
          <p># 1207/343 & 1207/1/343/1, 9th Main, 7th Sector, Hsr Layout Bangalore KA 560102, India. </p>
        </div>
        <!-- col --> 
        
      </div>
      <div class="footer-copy">
        <div class="row">
          <div class="col-12">
            <p> © 2022, LETOX TECHNOLOGIES PRIVATE LIMITED</p>
          </div>
          <!-- col --> 
        </div>
        <!-- row --> 
      </div>
      <!-- footer-copy --> 
      
    </div>
    <!-- container --> 
  </section>
</footer>
<!-- / --> 

<!-- jQuery --> 
<script src="{{asset('assets/static/js/jquery-3.2.1.min.js')}}"></script> 
<script src="{{asset('assets/static/js/jquery-migrate-3.0.0.min.js')}}"></script> 

<!-- Plugins --> 
<script src="{{asset('assets/static/plugin/bootstrap/js/popper.min.js')}}"></script> 
<script src="{{asset('assets/static/plugin/bootstrap/js/bootstrap.min.js')}}"></script> 

<!-- custom --> 
<script src="{{asset('assets/static/js/custom.js')}}"></script>
</body>
<!-- Body End -->

</html>
