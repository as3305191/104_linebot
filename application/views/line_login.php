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
          <?php if(empty($l_user)): ?>
            <div class="header_info-right unauth ">
                <?php
                  $nonce = "aaa123";
                  if(!empty($_promo_sn)) {
                    $nonce = $_promo_sn;
                  }
                  if(!empty($_promo_user_id)) {
                    $nonce = "puser_{$_promo_user_id}";
                  }

                  $line_call_back_url = BASE_URL . "/line_callback";
                  $line_cliend_id = LOGIN_CHANNEL_ID;
                ?>

                <a id="btn-line-reg" class="" href="https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=<?= $line_cliend_id ?>&redirect_uri=<?= $line_call_back_url ?>&state=<?= $rand_str ?>&bot_prompt=aggressive&scope=openid%20profile&nonce=<?= $nonce ?>">
                  <img src="<?=base_url('img/line688/WEB/btn_line_login.png')?>" height="40" />
                </a>

                <?php if(empty($p_user)): ?>
                <!-- <a class="btn login" href="javascript:void(0)">其他</a> -->
                <?php endif ?>
                </a>
            </div>
          <?php else: ?>
            <div class="header_info-right auth">
                <div class="login_info" style="margin-right: 122px;">
                    <div class="circular--portrait">
                      <img src="<?= $l_user -> line_picture ?>" style="height: 40px" />
                    </div>
                    <span class="hidden-xxs">歡迎：</span>
                    <span class="name"><?= $l_user -> line_name ?></span>
                    <span>金幣餘額: <?= number_format($sum_amt) ?></span>
                    <span class="wallet" name="wallet"></span>
                </div>
                <a href="<?=base_url('line_login/signout')?>" class="btn logout-btn">登出</a>
            </div>
          <?php endif ?>
        </div>

      </header>
      
      <style>
      </style>

      <script>
      </script>
    </div>
  </body>
</html>
