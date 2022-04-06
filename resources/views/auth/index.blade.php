<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- Page Title -->
    <title>Coinpaise</title>
    <!-- / -->
    {{-- @notifyCss --}}
    <!---Font Icon-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- / -->

    <!-- Plugin CSS -->
    <link href="{{ asset('assets/static/plugin/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">


    <!-- / -->

    <!-- Theme Style -->
    <link href="{{ asset('assets/static/css/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/static/css/color/default.css') }}" rel="stylesheet" id="color_theme">
    <!-- / -->

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/img/fev.png') }}" />
    <!-- / -->
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
                <a class="navbar-brand" href="#"> <img src="{{ asset('assets/img/logo.png') }}" /></a>
                <!-- / -->

                <!-- Mobile Toggle -->
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar"
                    aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation"> <span></span>
                    <span></span> <span></span> </button>
                <!-- / -->

                <!-- Top Menu -->
                <div class="collapse navbar-collapse justify-content-end" id="navbar">
                    <ul class="navbar-nav ml-auto">

                        <li><a class="nav-btn nav-link" href="index.html">Exchange</a></li>
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

        <!-- Home Banner Start -->
        <section id="home" class="home-banner-01 gray-bg-g border-bottom">
            <div class="container">
                <div class="row full-screen align-items-center">
                    <div class="col col-md-7 col-lg-7 col-xl-7 p-80px-tb md-p-30px-b sm-p-60px-t m-50px-t">
                        <div class="home-text-center p-50px-r md-p-0px-r">

                            <h1 class="font-alt">Trade Bitcoin and other cryptocurrencies</h1>
                            <p>coinsplashes has made investing simple for millions around the world.</p>
                            <div class="subscribe-box">
                                <a class="m-btn m-btn-theme2nd" href="#">Get started </a>
                                <img src="{{ asset('assets/img/banner.png') }}" class="mt-3 mt-md-5" />
                            </div>

                        </div>
                        <!-- home-text-center -->
                    </div>
                    <div class="col col-md-5 col-lg-5 col-xl-5 home-right m-50px-t md-m-0px-t">
                        <div class="home-right-inner p-5">
                            <!---- Login and register starts----->
                            <ul class="nav nav-pills nav-fill navtop">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#menu1" data-toggle="tab">Sign up</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#menu2" data-toggle="tab">Log in</a>
                                </li>

                            </ul>
                            <div class="tab-content float-right">
                                <div class="tab-pane active" role="tabpanel" id="menu1">
                                    <div class="pt-3">
                                        <form class="contactform pt-3" method="post" action="{{ route('signup') }}">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6 pb-3">
                                                    <div class="form-group">
                                                        <input name="firstname" type="text" placeholder="First Name"
                                                            class="validate form-control firstname" required=""
                                                            onkeydown="return /[a-z]/i.test(event.key)">
                                                        <span class="input-focus-effect"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 pb-3">
                                                    <div class="form-group">
                                                        <input name="lastname" type="text" placeholder="Last Name"
                                                            class="validate form-control" required=""
                                                            onkeydown="return /[a-z]/i.test(event.key)">
                                                        <span class="input-focus-effect"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pb-3">
                                                    <div class="form-group">
                                                        <input type="email" placeholder="Email" name="email"
                                                            class="validate form-control" required="">
                                                        <span class="input-focus-effect"></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 pb-3">
                                                    <div class="form-group">
                                                        <input type="tel" placeholder="Mobile" name="mobile"
                                                            class="validate form-control" required="" maxlength="12" />
                                                        <span class="input-focus-effect"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pb-3">
                                                    <div class="form-group">
                                                        <input type="password" placeholder="Password" name="password"
                                                            class="validate form-control" required="">
                                                        <span class="input-focus-effect"></span>
                                                    </div>
                                                </div>


                                                <div class="col-md-12">
                                                    <div class="send">
                                                        <button class="m-btn m-btn-theme2nd w-100" type="submit"
                                                            name="send" id="submit">Register</button>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group pt-3 text-center pb-0 mb-0">
                                                        by signup I accept <a href="#">terms and conditions</a>
                                                    </div>
                                                </div>

                                            </div>
                                        </form>

                                    </div>
                                </div>
                                <div class="tab-pane" role="tabpanel" id="menu2">
                                    <div class="pt-3">
                                        <form class="contactform pt-3" method="post" action="{{ route('login') }}">
                                            <div class="row">
                                                @csrf
                                                <div class="col-md-12 pb-3">
                                                    <div class="form-group">
                                                        <input type="email" placeholder="Email" name="email"
                                                            class="validate form-control" required="">
                                                        <span class="input-focus-effect"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-12  pb-3">
                                                    <div class="form-group">
                                                        <input type="passsword" placeholder="Password" name="password"
                                                            class="validate form-control" required="">
                                                        <span class="input-focus-effect"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-12  pb-3">
                                                    <div class="form-group">
                                                        <a href="#">Forgot password?</a>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="send">
                                                        <button class="m-btn m-btn-theme2nd w-100" type="submit"
                                                            name="send"> Login</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                            <!--- Login and regsiter ends----->
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <!-- col -->
                </div>
            </div>
            <!-- container -->
        </section>
        <!-- / -->

        <!-- Featre  -->
        <section class="section border-bottom">
            <div class="container">
                <div class="row align-items-center">

                    <div class="col-md-6">
                        <div class="feature-box-02">
                            <h3 class="m-20px-b font-alt">Products</h3>

                            <p>Coinpaise.com is backed by a team of world-class financial experts and the best
                                technology talent. We work hard to implement time-tested investment strategies and
                                develop an investment experience that’s personalized for you.</p>
                            <p>Our mission is to make payments simple, through our innovative thinking and skillful
                                team, by bringing the future technologies to you today.</p>

                        </div>
                    </div>
                    <!-- col -->
                    <div class="col-md-6 sm-m-30px-t"> <img src="{{ asset('assets/img/banner2.png') }}" title=""
                            alt="">
                    </div>
                </div>
                <!-- row -->
            </div>
        </section>
        <!-- / -->

        <!-- Price Table -->
        <section id="price" class="section border-bottom">
            <div class="container">

                <div class="row">
                    <div class="col-md-4 m-15px-tb">
                        <div class="price-table-01">
                            <div class="pt-head">
                                <div class="pt-name">
                                    <label class="theme-g-bg">Multiple Payment options</label>
                                </div>
                                <h4 class="coloryellow"><i class="fa-solid fa-money-bill"></i></h4>

                            </div>
                            <div class="pt-body">
                                <ul>
                                    <li>Buy with a credit card, debit card, wallets or bank transfer.</li>

                                </ul>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 m-15px-tb sm-m-30px-tb">
                        <div class="price-table-01">
                            <div class="pt-head theme-2nd standard">
                                <div class="pt-name">
                                    <label class="theme-g-bg">World class security</label>
                                </div>
                                <h4 class="text-white"><i class="fa-solid fa-shield"></i></h4>

                            </div>
                            <div class="pt-body">
                                <ul>
                                    <li>We’ve left no stone unturned to make coinpaise is the most secure exchange.
                                    </li>

                                </ul>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 m-15px-tb">
                        <div class="price-table-01">
                            <div class="pt-head">
                                <div class="pt-name">
                                    <label class="theme-g-bg">Trade your favorite coin</label>
                                </div>
                                <h4 class="coloryellow"><i class="fa-solid fa-chart-line"></i></h4>

                            </div>
                            <div class="pt-body">
                                <ul>
                                    <li>Buy and trade bitcoin, ethereum and other popular cryptocurrencies with your
                                        fiat currency.</li>

                                </ul>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- container -->
        </section>
        <!-- / -->

    </main>
    <!-- Main End -->

    <!-- Footer Start -->
    <footer class="footer-light">
        <section class="footer-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-lg-5 sm-m-15px-tb md-m-30px-b">
                        <h4 class="font-alt">About Us</h4>
                        <p class="footer-text">We saw the potential in cryptocurrency to revolutionize the financial
                            industry, making payments faster, more secure, and more reliable with lower costs. </p>
                    </div>
                    <!-- col -->

                    <div class="col-6 col-md-4 col-lg-4 sm-m-15px-tb">
                        <h4 class="font-alt">Company</h4>
                        <ul class="fot-link">
                            <li><a href="policy.html">Privacy Policy</a></li>
                            <li><a href="terms.html">Terms and Conditions</a></li>
                            <li><a href="refund.html">Refund Policy </a></li>
                            <li><a href="contact.html">Contact us</a></li>
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
    <script src="{{ asset('assets/static/js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('assets/static/js/jquery-migrate-3.0.0.min.js') }}"></script>

    <!-- Plugins -->
    <script src="{{ asset('assets/static/plugin/bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/static/plugin/bootstrap/js/bootstrap.min.js') }}"></script>


    <!-- custom -->
    <script src="{{ asset('assets/static/js/custom.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script>
        @if (Session::has('message'))
            toastr.options =
            {
            "closeButton" : true,
            "progressBar" : true
            }
            toastr.success("{{ session('message') }}");
        @endif

        @if (Session::has('error'))
            toastr.options =
            {
            "closeButton" : true,
            "progressBar" : true
            }
            toastr.error("{{ session('error') }}");
        @endif

        @if (Session::has('info'))
            toastr.options =
            {
            "closeButton" : true,
            "progressBar" : true
            }
            toastr.info("{{ session('info') }}");
        @endif

        @if (Session::has('warning'))
            toastr.options =
            {
            "closeButton" : true,
            "progressBar" : true
            }
            toastr.warning("{{ session('warning') }}");
        @endif
    </script>
</body>
<!-- Body End -->

</html>
