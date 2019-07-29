<div class="header_info">
  <?php if(empty($l_user)): ?>
    <div class="header_info-right unauth ">
        <a class="btn login" href="<?= base_url() ?>#">登入</a>
        <a class="register btn" href="https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=1613148201&redirect_uri=https://www.17lineplay.com/line_callback&state=<?= $rand_str ?>&scope=openid%20profile%20email&nonce=aaa123">Line綁定註冊</a>
        <a href="" target="_blank" class="forget" title="忘記密碼">

            <!-- <div class="forget_icon"></div> -->
            <img class="forget_icon" src="<?=base_url('img/17play/WEB/info.png')?>">
            <!-- <i class="icon ion-help"></i> -->
        </a>
    </div>
  <?php else: ?>
    <div class="header_info-right auth">
        <div class="login_info">
            <img src="<?= $l_user -> line_picture ?>" style="height: 40px" />
            <span class="hidden-xxs">歡迎：</span>
            <span class="name"><?= $l_user -> line_name ?></span>
            <span>目前點數</span>
            <span class="wallet" name="wallet"></span>
        </div>
        <a href="<?=base_url('users/signout')?>" class="btn">登出</a>
        <a href="<?= base_url() ?>store#go-main" class="btn btn-secondary btn-drop-menu">
            會員中心<span class="badge getUnread-js"></span>
        </a>
        <ul class="drop-down-menu">
            <li><a class="btn" href="<?= base_url() ?>member#go-main">設定</a></li>
            <li><a class="btn" href="<?= base_url("deposit") ?>">儲值</a></li>
            <li><a class="btn" href="<?= base_url() ?>transfer#go-main">轉移</a></li>
            <li><a class="btn" href="<?= base_url() ?>bank#go-main">提領</a></li>
            <li><a class="btn" href="<?= base_url() ?>record_store">帳務</a></li>
            <li><a class="btn" href="<?= base_url() ?>message#go-main">訊息</a></li>
        </ul>
    </div>
  <?php endif ?>
</div>
