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


            <!-- <div class="forget_icon"></div> -->
            <!-- <img class="forget_icon" src="<?=base_url('img/17play/WEB/info.png')?>"> -->
            <!-- <i class="icon ion-help"></i> -->
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
        <!-- <a href="<?= base_url() ?>store#go-main" class="btn btn-secondary btn-drop-menu">
            會員中心<span class="badge getUnread-js"></span>
        </a>
        <ul class="drop-down-menu">
            <li><a class="btn" href="<?= base_url() ?>member#go-main">設定</a></li>
            <li><a class="btn" href="<?= base_url("deposit") ?>">儲值</a></li>
            <li><a class="btn" href="<?= base_url() ?>transfer#go-main">轉移</a></li>
            <li><a class="btn" href="<?= base_url() ?>bank#go-main">提領</a></li>
            <li><a class="btn" href="<?= base_url() ?>record_store">帳務</a></li>
            <li><a class="btn" href="<?= base_url() ?>message#go-main">訊息</a></li>
        </ul> -->
    </div>
  <?php endif ?>
</div>
