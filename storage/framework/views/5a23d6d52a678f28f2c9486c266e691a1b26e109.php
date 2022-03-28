<html>
<head>
  <script src='https://cdn.veriff.me/sdk/js/1.1/veriff.min.js'></script>
  <script src='https://cdn.veriff.me/incontext/js/v1/veriff.js'></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
  </head>
  <body>
    <section class="h-100">
      <header class="container h-100">
        <div class="d-flex align-items-center justify-content-center h-100">
          <div class="d-flex flex-column">
            <div id='veriff-root'></div>
          </div>
        </div>
      </header>
    </section>
  </body>
  <script>
  const veriff = Veriff({
    host: 'https://stationapi.veriff.com',
    apiKey: 'fb35de50-15ca-420f-bf0b-e20f99a583ef',
    parentId: 'veriff-root',
    onSession: function(err, response) {
      window.veriffSDK.createVeriffFrame({ url: response.verification.url });
    }
  });
  veriff.setParams({
    vendorData: ' '
  });
  veriff.mount();
</script>
</html> <?php /**PATH D:\RapidGame\laravel\rapidpay\resources\views/merchants/kyc.blade.php ENDPATH**/ ?>