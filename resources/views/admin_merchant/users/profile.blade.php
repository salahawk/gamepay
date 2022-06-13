@extends('admin_merchant.layouts.app')
@section('header_styles')

@endsection
@section('contents')
<main>
  <div class="container-fluid px-4">
    <div class="admincard pt-5">
      <div class="card mb-4 boxshadow">
        <div class="card-header py-3 fw-bold"> <i class="fa-solid fa-user"></i> My profile</div>
        <div class="card-body">
          <div class="tabs">
            <div class="tab-button-outer">
              <ul id="tab-button">
                <li><a href="#tab01">My Personal details</a></li>
                <li><a href="#tab02">Integration</a></li>
                <li><a href="#tab03">Reset Password</a></li>
              </ul>
            </div>
            <div class="tab-select-outer">
              <select id="tab-select">
                <option value="#tab01">My Personal details</option>
                <option value="#tab02">Integration</option>
                <option value="#tab03">Reset Password</option>
              </select>
            </div>
            <div id="tab01" class="tab-contents pt-4">
              <h3>My Personal details</h3>
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th>Business Name</th>
                    <td>{{$merchant->name}}</td>
                  </tr>
                  <tr>
                    <th>Email</th>
                    <td>{{$merchant->email}}</td>
                  </tr>
                  <tr>
                    <th>Phone</th>
                    <td>{{$merchant->mobile}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div id="tab02" class="tab-contents pt-4">
              <h3>Integration</h3>
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th>Key</th>
                    <td>{{$merchant->key}}</td>
                  </tr>
                  <tr>
                    <th>Salt</th>
                    <td>{{$merchant->salt}}</td>
                  </tr>
                  <tr>
                    <th>URL</th>
                    <td>https://gamepay.online/</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div id="tab03" class="tab-contents pt-4">
              <h3>Reset Password</h3>
              <div class="col-sm-12 col-md-12 text-center">
                <form class="form-inline d-inline">
                  <div class="row">
                    <div class="col-12 col-md-3">
                      <div class="form-group  d-inline" style="display: inline">
                        <input type="password" name="old" class="form-control" id="oldpass" placeholder="Enter Old Password" required>
                      </div>
                    </div>
                    <div class="col-12 col-md-3">
                      <div class="form-group  d-inline" style="display: inline">
                        <input type="password" name="new" class="form-control" id="enternew" placeholder="Enter new Password" required>
                      </div>
                    </div>
                    <div class="col-12 col-md-3">
                      <div class="form-group  d-inline" style="display: inline">
                        <input type="password" class="form-control" id="confirmnew" placeholder="Confirm new password" required>
                      </div>
                    </div>
                    <div class="col-12 col-md-3">
                      <div class="form-group  d-inline" style="display: inline">
                        <button type="submit" class="btn btn-success w-100">Change</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
@endsection
@section('footer_scipts')
<script>
  $(function() {
    var $tabButtonItem = $('#tab-button li'),
      $tabSelect = $('#tab-select'),
      $tabContents = $('.tab-contents'),
      activeClass = 'is-active';

    $tabButtonItem.first().addClass(activeClass);
    $tabContents.not(':first').hide();

    $tabButtonItem.find('a').on('click', function(e) {
      var target = $(this).attr('href');

      $tabButtonItem.removeClass(activeClass);
      $(this).parent().addClass(activeClass);
      $tabSelect.val(target);
      $tabContents.hide();
      $(target).show();
      e.preventDefault();
    });

    $tabSelect.on('change', function() {
      var target = $(this).val(),
        targetSelectNum = $(this).prop('selectedIndex');

      $tabButtonItem.removeClass(activeClass);
      $tabButtonItem.eq(targetSelectNum).addClass(activeClass);
      $tabContents.hide();
      $(target).show();
    });
  });

  $('form.form-inline').on('submit', function(e) {
    e.preventDefault();
    if ($("#enternew").val() != $("#confirmnew").val()) {
      alert("Confirmation password does not match.");
      $("#enternew").val("");
      $("#confirmnew").val("");
      return;
    }
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      method: "post",
      data: {
        old: $("#oldpass").val(),
        new: $("#enternew").val(),
      },
      url: "{{ route('admin-merchant.users.changePassword') }}",
      success: function(resp) {
        if (resp.status == "success") {
          alert(resp.message);
          location.reload();
        } else {
          alert(resp.message);
          $('form.form-inline input').each(function() {
            $(this).val("");
          })
          return;
        }
      },
    })
  })
</script>
@endsection