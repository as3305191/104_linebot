<!DOCTYPE html>
<html >
<meta charset="utf-8" name="viewport" content="width=device-width,initial-scale=1">
<title></title>
		<header >
		</header>
<style>
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
.phone{
  margin: 0 auto;
  position:relative;
  width: 100%;
  height: 40%;
  left: 0%;
  top: 0%;
}

.android{
  margin: 0 auto;
  position:relative;
  left: -3%;
  top: 0%;
	width:15%;
}
.apple{
  margin: 0 auto;
  position:relative;
  left: 3%;
  top: 0%;
	width:15%;
}
.apk{
  margin: 0 auto;
  position:relative;
  width: 15%;
  left: 0%;
  top: 0%;
}

@media only screen and (max-width: 750px) {
	.font{
	  text-align:center;
	  position:absolute;
	  width: 100%;
	  height: 32%;
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
	.phone{
	  margin: 0 auto;
	  position:relative;
	  width: 100%;
	  height: 60%;
	  left: 0%;
	  top: 0%;
	}

	.android{
	  margin: 0 auto;
	  position:relative;
	  left: -3%;
	  top: 0%;
		width:20%;
	}
	.apple{
	  margin: 0 auto;
	  position:relative;
	  left: 3%;
	  top: 0%;
		width:20%;
	}
	.apk{
		margin: 0 auto;
		position:relative;
		left: 0%;
		top: 0%;
		width:20%;
	}
}
</style>
      <div class="font" >
        <div class=" background" >
          <img src="<?=base_url('img/line/blackbackground/1.png')?>" style="width:100%" >
        </div >
				<div class="phone" >
					<img src="<?=base_url('img/line/phone/phone-512.png')?>" style="width:60px"></br>
					<span class="text2" style="color:#fff;font-size:20px;">請選擇您要下載的系統</span>
				 </div >
	        <div class="phone1" >
						<img class="android" src="<?=base_url('img/line/android/4.png')?>" onclick="alert('敬請期待')">
						<img class="apple" src="<?=base_url('img/line/apple/3.png')?>" onclick="location.href='itms-services://?action=download-manifest&url=https://wa-lotterygame.com/download/manifest.plist'">
						<!-- <?php $url = "https://wa-lotterygame.com/wa_backend/data/manifest.plist"; ?>  -->
					 </div >
					 <div class="phone2" >
						<img class="apk" src="<?=base_url('img/line/apk/1.png')?>" onclick="location.href='https://wa-lotterygame.com/download/waapp.apk'">
					 </div >


			 </div >



  </html >
