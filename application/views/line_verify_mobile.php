<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>LineLogin</title>
    <meta version="13">

    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">

    <style>
      body {
        background-color: orange;
      }

      .main {
        text-align: center;
      }
    </style>
</head>
<body>
    <input id="auth" type="hidden" value="<?= !empty($l_user) ? $l_user -> id : '' ?>">
    <!-- $header Start -->

    <div class="main">
      <header class="header clearfix">
        <style>
        .circular--portrait {
          position: absolute;
          width: 40px;
          height: 40px;
          overflow: hidden;
          border-radius: 50%;
          top: 16px;
        }

        @media screen and (max-width: 768px){
          .circular--portrait
          {
            background: #ccc;
            position: absolute;
            left: 20px;
            top: -8px;
            width: 40px;
            height: 40px;
          }

          .login_info {
            position: relative;
            top: 18px;
          }

          .logout-btn {
            position: absolute;
            right: 10px;
            top: 19px;
          }
        }

        .circular--portrait img {
          width: 100%;
          height: auto;
        }

        .home_live-top {
            height: inherit;
            max-width: 768px;
        }
        </style>
        <div class="header_info">
            <div class="header_info-right unauth ">

              <div>
                <form id="verify-from" method="post">
                  <input type="text" id="mobile" placeholder="請輸入手機號碼" />
                  <button type="submit">送出</button>
                </form>

              </div>


            </div>
        </div>

      </header>

      <style>
      </style>

      <script src="<?= base_url('js/libs/jquery-2.1.1.min.js') ?>"></script>
      <script>
      $(document).ready(function(){
        $("#verify-from").submit(function(e){
          var $mobile = $("#mobile").val();
          if($mobile.length < 10) {
            alert('請輸入手機號碼，至少10碼');
            return;
          }

          $.ajax({
            url: '<?= base_url('line_login/submit_mobile') ?>',
            type: 'POST',
            dataType: 'json',
            data:{
              mobile: $("#mobile").val()
            },
            success: function(d){
              if(d.error_msg) {
                alert(d.error_msg);
              } else {
                location.href = '<?= base_url("line_login/verify_mobile_code") ?>';
              }
            }
          });
          e.preventDefault();
        });
      });
      </script>
    </div>
  </body>
</html>
