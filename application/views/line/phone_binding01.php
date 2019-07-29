<!DOCTYPE html>
<html >
<meta charset="utf-8" name="viewport" content="width=device-width,initial-scale=1">
<title></title>
		<header >
		</header>
<style>
body{
	background-color: #1f0404;
}
.font{
  text-align:center;
  position:absolute;
  width: 100%;
  height: 48%;
  left: 0%;
  top: 0%;
}
.background{
  margin: 0 auto;
  position:relative;
  width: 100%;
  max-width: 800px;
  height: 60%;
  left: 0%;
  top: 0%;
}
.text2{
	font-size:40px;
  margin: 0 auto;
  position:relative;
  width: 100%;
	height: 0%;
  left: 0%;
  top: -40%;
	color:#fff;
}
.circle{
  margin: 0 auto;
  position:relative;
  width: 100%;
  height: 43%;
  left: 0%;
  top: 0%;
}
.text1{
	font-size:40px;
  margin: 0 auto;
  position:relative;
  width: 100%;
  height: 20%;
  left: 0%;
  top: 0%;
}
.aline{
  margin: 0 auto;
  position:relative;
  width: 100%;
  height: 110%;
  left: 0%;
  top: 0%;
}
.enter_phone{
  margin: 0 auto;
  position:relative;
  width: 100%;
  height: 25%;
  left: 0%;
  top: 0%;
	z-index: 0;
}
.text{
  margin: 0 auto;
  position:relative;
  width: 100%;
  height: 20%;
  left: 0%;
  top: 8%;
}
.sms{
  margin: 0 auto;
  position:relative;
  width: 100%;
  left: 0%;
  top: 0%;
}

#circle {
	border-radius: 50%;
}

#mobile {
	position: absolute;
	top: 9%;
  left: 39%;
  width: 25%;
	background-color: transparent;
	border: 0px;
	color: #c99e57;
	font-size: 16px;
}
@media only screen and (max-width: 750px) {
	.font{
		text-align:center;
		position:absolute;
		width: 100%;
		height: 20%;
		left: 0%;
		top: 0%;
	}
	.background{
		margin: 0 auto;
		position:relative;
		width: 100%;
		max-width: 100%;
		height: 60%;
		left: 0%;
		top: 0%;
	}
	.circle{
	  margin: 0 auto;
	  position:relative;
	  width: 100%;
	  height: 55%;
	  left: 0%;
	  top: 0%;

	}
	.text1{
		font-size:20px;
	  margin: 0 auto;
	  position:relative;
	  width: 100%;
	  height: 23%;
	  left: 0%;
	  top: 0%;
	}
	.aline{
	  margin: 0 auto;
	  position:relative;
	  width: 100%;
	  height: 150%;
	  left: 0%;
	  top: 0%;
	}
	.enter_phone{
	  margin: 0 auto;
	  position:relative;
	  width: 100%;
	  height:20%;
	  left: 0%;
	  top: 0%;
		z-index: 0;
	}
	.text{
		font-size:6px;
	  margin: 0 auto;
	  position:relative;
	  width: 100%;
	  height: 45%;
	  left: 0%;
	  top: 8%;
	}
	.sms{
	  margin: 0 auto;
	  position:relative;
	  width: 100%;
	  left: 0%;
	  top: 0%;
	}
	#circle{
		width : 70px  !important;
		border-radius: 50%;
	}
	#aline{
		width :2px  !important;
	}
	#sms{
		width :70px  !important;
	}

	#enter_phone{
		width :35%  !important;
	}

	.text2{
		font-size:20px;
		margin: 0 auto;
		position:relative;
		width: 100%;
		height: 0%;
		left: 0%;
		top: -60%;
		color:#fff;
	}

	#mobile {
		position: absolute;
		top: 10%;
		left: 34%;
		width: 35%;
		font-size: 12px;
	}
}
</style>


      <div class="font" >
				<div class=" background" >
          <img src="<?=base_url('img/line/background/5.png')?>" style="width:100%" >
        </div >
				<div class="text2" >
					<span  >STEP.1</span></br>
					<span >手機認證綁定</span></br>
				</div >
        <div class="circle" >
          <img src="<?= $l_user -> line_picture ?>" style="width:150px" id="circle">
        </div >
				<div class=" text1" >
					<span style="color:#fff"><?= $l_user -> line_name ?></span></br>
				</div >
				<div class=" aline" >
					<img src="<?=base_url('img/line/aline/aline.png')?>" style="width:4px" id="aline">
				</div >
        <div class=" enter_phone" >
          <img src="<?=base_url('img/line/enter_phone/76.png')?>" style="width:25%" id="enter_phone" >
					<form id="verify-from" method="post">
						<input id="mobile" type="text"  placeholder="請輸入手機號碼" />
						<button type="submit" style="display:none;">送出</button>
					</form>
        </div >
        <div class=" text" >
          <span style="color:#fff">公司將來會不定期的舉辦競賽與活動，如您在活動中獲得獎品</span></br>
          <span style="color:#fff">或獎金，公司人員將用您輸入的電話號碼與您聯繫獎品或獎金的</span></br>
					<span style="color:#fff">寄送事宜，故每位玩家的帳號必須以電話號碼作為認證標準。</span>
        </div >
        <div class=" sms" >
          <img src="<?=base_url('img/line/sms/54.png')?>" style="width:150px" id="sms" onclick='$("#verify-from").submit();'>

        </div >
      </div >

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

  </html >
