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
.buttom{
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
	.buttom{
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

.buttom a:link {
	color: white;
}
.buttom {
	color: white;
}

.agree{
  margin: 0 auto;
  position:relative;
  width: 100%;
  left: 0%;
  top: 8%;
}
.wa{
  margin: 0 auto;
  position:relative;
  width: 100%;
  height: 150%;
  left: 0%;
  top: 0%;
}
</style>
	<?php if(empty($l_user)): ?>

			<?php
				$nonce = "";
				if(!empty($p_user)) {
					$nonce = $p_user -> gift_id;
				}

				$line_call_back_url = BASE_URL . "/line_callback";
				$line_login_url = BASE_URL . "/line_login";
				$line_cliend_id = LOGIN_CHANNEL_ID;
			?>
      <div class="font">
        <div class="col-xs-12 background" >
          <img src="<?=base_url('img/line/background/7.jpg')?>" style="width:100%" >
        </div >

        <div class="wa" >
          <img src="<?=base_url('img/coc_bot/logo_n.png')?>" style="width:18%" id="wa">
        </div >
				<?php if(!empty($p_user)): ?>
				<div class=" text1" >
					<span style="color:#000000">?????????:<?= $p_user -> nick_name ?></span></br>
				</div >
				<?php endif ?>
        <div class="text" >
          <span style="color:#000000">???????????????Line??????????????????COC???????????????????????????????????????</span></br>
          <span style="color:#000000">Line????????????????????????COC??????????????????????????????????????????</span>
        </div >
        <div class="agree" >
					<a id="btn-line-reg" class="" href="https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=<?= $line_cliend_id ?>&redirect_uri=<?= $line_call_back_url ?>&state=<?= $line_login_url ?>&bot_prompt=aggressive&scope=openid%20profile&nonce=<?= $nonce ?>">
						<img src="<?=base_url('img/line/agree/agree.png')?>" style="width:40%">
					</a>
        </div >
      </div >
		<?php else: ?>


			<div class="  font" >
				<div class=" background" >
					<img src="<?=base_url('img/line/background/7.jpg')?>" style="width:100%" >
        </div >

				<div class="circle" >
          <img src="<?= $l_user -> line_picture ?>" style="width:150px" id="circle">
        </div >
				<div class=" text1" >
					<span style="color:#000000"><?= $l_user -> line_name ?></span></br>
				</div >

				<div class=" text1" >
					<span style="color:#000000"><span>????????????: <?= number_format($sum_amt) ?></span></span></br>
				</div >
				<div class=" aline" >
					<img src="<?=base_url('img/line/aline/aline.png')?>" style="width:3px" id="aline">
				</div >
				<div class="buttom" >
					<a style="color:#000000;" href="<?=base_url('line_login/signout')?>">??????</a>
        </div >

      </div >


		<?php endif ?>
  </html >
