<!DOCTYPE html>
<html >
  <head>
    <meta charset="utf-8" name="viewport" content="width=device-width,initial-scale=1">
    <title></title>
  <style>
  body{
    padding: 30px;
  }

  .upd_time {
    margin: 10px;
    color: gray;
  }
  </style>
  </head>
  <body>
    <div class="upd_time">
      <?php if(file_exists("../download/metaapp.apk")): ?>
        <?= "APK 更新時間： " . date("Y-m-d H:i:s", filectime("../download/metaapp.apk")) ?>
      <?php endif ?>
    </div>
    <form action="<?= base_url("deploy_meta/upload_apk") ?>" method="post" enctype="multipart/form-data">
      <input type="file" name="file" />
      <button type="submit">上傳apk</button>
    </form>
    <hr/>
    <div class="upd_time">
      <?php if(file_exists("../download/gameapp.ipa")): ?>
        <?= "IPA 更新時間： " . date("Y-m-d H:i:s", filectime("../download/gameapp.ipa")) ?>
      <?php endif ?>
    </div>
    <form action="<?= base_url("deploy_meta/upload_ipa") ?>" method="post" enctype="multipart/form-data">
      <input type="file" name="file" />
      <button type="submit">上傳ipa</button>
    </form>

    <script src="<?= base_url('js/libs/jquery-2.1.1.min.js') ?>"></script>
    <script>
    $(document).ready(function(){
      $("#verify-from").submit(function(e){
        e.preventDefault();

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

        return;
      });
    });
    </script>
  </body>

  </html >
