<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>GamePay Admin</title>
  <link rel="apple-touch-icon" href="{{asset('assets/img/admin-merchant/fev.png')}}">
  <link rel="shortcut icon" href="{{asset('assets/img/admin-merchant/fev.png')}}">
  <link href="{{asset('assets/css/admin-merchant/styles.css')}}" rel="stylesheet" />
  <link href="{{asset('assets/css/admin-merchant/custom.css')}}" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
</head>

<body class="bg-blue">
  <div id="layoutAuthentication">
    <div id="layoutAuthentication_content">
      <main>
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-5">
              <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="">
                  <h3 class="text-center font-weight-light my-4"><img src="{{asset('assets/img/admin-merchant/logo.png')}}" /></h3>
                  <h4 class="text-center font-weight-light"><strong>Login</strong></h4>
                </div>
                <div class="card-body">
                  <form method="POST" action="{{ route('admin.login') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <div class="form-floating mb-3">
                      <input class="form-control" id="inputEmail" type="email" name="email" placeholder="name@example.com" required/>
                      <label for="inputEmail">Email address</label>
                    </div>
                    <div class="form-floating mb-3">
                      <input class="form-control" id="inputPassword" type="password" name="password" placeholder="Password" required/>
                      <label for="inputPassword">Password</label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" id="inputRememberPassword" type="checkbox" value="" />
                      <label class="form-check-label" for="inputRememberPassword">Remember Password</label>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                      <a class="small" href="password.html">Forgot Password?</a>
                      <input class="btn btn-primary" type="submit" value="login"/>
                    </div>
                  </form>
                </div>

              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
    <div id="layoutAuthentication_footer">
      <footer class="py-4 bg-light mt-auto">
        <div class="container-fluid px-4">
          <div class="align-items-center justify-content-between small text-center">
            <div class="text-muted">Copyright &copy; Game Pay</div>
          </div>
        </div>
      </footer>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="{{asset('assets/js/admin-merchant/scripts.js')}}"></script>
</body>

</html>