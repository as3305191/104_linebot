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
  height: 50%;
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
.text{
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
  height: 22%;
  left: 0%;
  top: -10%;
}
.text1{
	font-size:40px;
  margin: 0 auto;
  position:relative;
  width: 100%;
  height: 15%;
  left: 0%;
  top: 0%;
}
.aline{
  margin: 0 auto;
  position:relative;
  width: 100%;
  height: 79%;
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
.enter_phone1{
  margin: 0 auto;
  position:relative;
  width: 100%;
  height: 25%;
  left: 0%;
  top: 0%;
	z-index: 0;
}
.text2{
	font-size:20px;
	color:#FFFF8A;
}
.text3{
	font-size:20px;
	top: 0%;
	color:#FFFF8A;

}
.sms{
  margin: 0 auto;
  position:relative;
  width: 100%;
  height: 50%;
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
#reg_code {
	position: absolute;
	top: 9%;
  left: 39%;
  width: 25%;
	background-color: transparent;
	border: 0px;
	color: #c99e57;
	font-size: 16px;
}
#intro_code {
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
	  height: 10%;
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
	.text{
		font-size:20px;
	  margin: 0 auto;
	  position:relative;
	  width: 100%;
	  height: 10%;
	  left: 0%;
	  top: -60%;
		color:#fff;
	}
	.circle{
	  margin: 0 auto;
	  position:relative;
	  width: 100%;
	  height: 130%;
	  left: 0%;
	  top: 15%;
	}
	.text1{
		font-size:25px;
	  margin: 0 auto;
	  position:relative;
	  width: 100%;
	  height: 60%;
	  left: 0%;
	  top: 0%;
	}
	.aline{
	  margin: 0 auto;
	  position:relative;
	  width: 100%;
	  height: 240%;
	  left: 0%;
	  top: 0%;
	}
	.enter_phone{
	  margin: 0 auto;
	  position:relative;
	  width: 100%;
	  height: 70%;
	  left: 0%;
	  top: 0%;
		z-index: 0;
	}
	.enter_phone1{
	  margin: 0 auto;
	  position:relative;
	  width: 100%;
	  height: 70%;
	  left: 0%;
	  top: 0%;
		z-index: 0;
	}
	.text2{
		font-size:20px;
		color: #ffff93;
	}
	.text3{
		font-size:20px;
	  top: 0%;
		color: #ffff93;

	}
	.sms{
	  margin: 0 auto;
	  position:relative;
	  width: 100%;
	  height: 130%;
	  left: 0%;
	  top: 9%;
	}
	.text2{

		font-size:10px;
	}
	.text3{

		font-size:10px;
	}
	#circle{
		width :80px  !important;
		border-radius: 50%;
	}
	#aline{
		width :2px  !important;
	}
	#sms{
		width :75px  !important;
	}
	#cancel{
		width :100px  !important;
	}
	#complete{
		width :100px  !important;
	}
	#enter_phone{
		width :35%  !important;
	}
	#enter_phone1{
		width :35%   !important;
	}
	#reg_code {
		position: absolute;
		top: 8%;
		left: 34%;
		width: 35%;
		font-size: 12px;
	}
	#intro_code {
		position: absolute;
		top: 8%;
		left: 34%;
		width: 35%;
		font-size: 12px;
	}
}
</style>


      <div class="  font" >
				<div class=" background" >
          <img src="<?=base_url('img/line/background/5.png')?>" style="width:100%" >

        </div >
        <div class=" text" >
					<span  >STEP.2</span></br>
					<span >手機認證綁定</span></br>
        </div >
				<div class="circle" >
          <img src="<?= $l_user -> line_picture ?>" style="width:150px" id="circle">
        </div >
				<div class=" text1" >
					<span style="color:#fff"><?= $l_user -> line_name ?></span></br>
				</div >
				<div class=" aline" >
					<img src="<?=base_url('img/line/aline/aline.png')?>" style="width:3px" id="aline">
				</div >
				<form id="verify-from">
	        <div class=" enter_phone" >
	          <img src="<?=base_url('img/line/enter_phone/76.png')?>" style="width:25%" id="enter_phone">
						<input type="text" id="reg_code" placeholder="請輸入驗證碼" />

	        </div >
					<div class=" enter_phone1" >
	          <img src="<?=base_url('img/line/enter_phone/76.png')?>" style="width:25%" id="enter_phone1"></br>
						<span class="text3" >＊若您沒有好友推薦碼，可以略過此步驟</span>
						<input type="text" id="intro_code" placeholder="請輸入推薦碼(非必填)" />
	        </div >
				</form>
				<div class=" buttom" >
					<img src="<?=base_url('img/line/complete/79.png')?>" style="width:250px" id="complete" onclick='$("#verify-from").submit()'>

        </div >

      </div >

			<script src="<?= base_url('js/libs/jquery-2.1.1.min.js') ?>"></script>
      <script>
      $(document).ready(function(){
        $("#verify-from").submit(function(e){
					e.preventDefault();

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

        });
      });
      </script>

  </html >
