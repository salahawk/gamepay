<!DOCTYPE html>
<script src='https://cdn.veriff.me/sdk/js/1.1/veriff.min.js'></script>
<script src='https://cdn.veriff.me/incontext/js/v1/veriff.js'></script>

<div id='veriff-root'></div>
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

</html>