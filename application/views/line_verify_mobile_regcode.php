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
                <form id="verify-from">
                  <input type="text" id="reg_code" placeholder="請輸入驗證碼" />
                  <input type="text" id="intro_code" placeholder="請輸入推薦碼(非必填)" />
                  <button>送出</button>
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
          var $reg_code = $("#reg_code").val();
          var $intro_code = $("#intro_code").val();
          if($reg_code.length < 4) {
            alert('請輸入驗證碼，至少4碼');
            return;
          }

          $.ajax({
            url: '<?= base_url('line_login/verify_mobile_reg_code') ?>',
            type: 'POST',
            dataType: 'json',
            data:{
              reg_code: $reg_code,
              intro_code: $intro_code,
            },

            success: function(d){
              if(d.error_msg) {
                alert(d.error_msg);
              } else {
                alert("驗證完成");
                location.href = '<?= base_url("line_login") ?>';
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
