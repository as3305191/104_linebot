<!DOCTYPE html>
<html lang="en-us" id="extr-page">
	<head>
		<meta charset="utf-8">
		<title>---</title>
		<meta name="description" content="">
		<meta name="author" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

		<!-- #CSS Links -->
		<!-- Basic Styles -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url('css/bootstrap.min.css') ?>">
		<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url('css/font-awesome.min.css') ?>">

		<!-- We recommend you use "your_style.css" to override SmartAdmin
		     specific styles this will also ensure you retrain your customization with each SmartAdmin update.
		<link rel="stylesheet" type="text/css" media="screen" href="css/your_style.css"> -->

		<!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url() ?>css/demo.min.css">


		<!-- #GOOGLE FONT -->
		<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

		<!-- #APP SCREEN / ICONS -->
		<!-- Specifying a Webpage Icon for Web Clip
			 Ref: https://developer.apple.com/library/ios/documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html -->
		<link rel="apple-touch-icon" href="<?= base_url() ?>img/splash/sptouch-icon-iphone.png">
		<link rel="apple-touch-icon" sizes="76x76" href="<?= base_url() ?>img/splash/touch-icon-ipad.png">
		<link rel="apple-touch-icon" sizes="120x120" href="<?= base_url() ?>img/splash/touch-icon-iphone-retina.png">
		<link rel="apple-touch-icon" sizes="152x152" href="<?= base_url() ?>img/splash/touch-icon-ipad-retina.png">


		<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url() ?>css/my.css">

		<!-- iOS web-app metas : hides Safari UI Components and Changes Status Bar Appearance -->
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">

		<!-- Startup image for web apps -->
		<link rel="apple-touch-startup-image" href="<?= base_url() ?>img/splash/ipad-landscape.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)">
		<link rel="apple-touch-startup-image" href="<?= base_url() ?>img/splash/ipad-portrait.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)">
		<link rel="apple-touch-startup-image" href="<?= base_url() ?>img/splash/iphone.png" media="screen and (max-device-width: 320px)">
		<style>
			label.error {
				color: red;
			}
		</style>
	</head>

	<body class="animated fadeInDown">

		<h1 style="text-align:center; padding: 10px;">回報紀錄</h1>

		<div>
			<ul class="nav nav-tabs nav-justified succss">
			  <li class="active"><a data-toggle="tab" href="#home">總共 (<?= count($all_list) ?>)</a></li>
			  <li><a data-toggle="tab" href="#menu1">未處理 (<?= count($yet_answer_list) ?>)</a></li>
			  <li><a data-toggle="tab" href="#menu2">已回覆 (<?= count($answer_list) ?>)</a></li>
			</ul>

			<div class="tab-content">
			  <div id="home" class="tab-pane fade in active">
					<?php foreach($all_list as $each): ?>
						<div class="panel panel-default">
						  <div class="panel-heading"><strong>問題:</strong><?= $each -> question ?>
								<?php if(!empty($each -> image_id)): ?>

									<br/>
									<img width="100" src="<?= IMG_URL . $each -> image_id . "/thumb" ?>" />
								<?php endif ?>
								<br/>
								<span style="font-size:10px; color: #AAA"><?= $each -> create_time ?></span>
							</div>
						  <div class="panel-body"><?= $each -> status == 0 ? '未回覆' : $each -> answer ?>
								<br/>
								<span style="font-size:10px; color: #AAA"><?= $each -> answer_time ?></span>
							</div>

						</div>
						<br/>
					<?php endforeach ?>
			  </div>
			  <div id="menu1" class="tab-pane fade">
					<?php foreach($yet_answer_list as $each): ?>
						<div class="panel panel-default">
						  <div class="panel-heading"><strong>問題:</strong><?= $each -> question ?>
								<?php if(!empty($each -> image_id)): ?>

									<br/>
									<img width="100" src="<?= IMG_URL . $each -> image_id . "/thumb" ?>" />
								<?php endif ?>
								<br/>
								<span style="font-size:10px; color: #AAA"><?= $each -> create_time ?></span>
							</div>
						  <div class="panel-body"><?= $each -> status == 0 ? '未回覆' : $each -> answer ?>
								<br/>
								<span style="font-size:10px; color: #AAA"><?= $each -> answer_time ?></span>
							</div>

						</div>
						<br/>
					<?php endforeach ?>
			  </div>
			  <div id="menu2" class="tab-pane fade">
					<?php foreach($answer_list as $each): ?>
						<div class="panel panel-default">
						  <div class="panel-heading"><strong>問題:</strong><?= $each -> question ?>
								<?php if(!empty($each -> image_id)): ?>

									<br/>
									<img width="100" src="<?= IMG_URL . $each -> image_id . "/thumb" ?>" />
								<?php endif ?>
								<br/>
								<span style="font-size:10px; color: #AAA"><?= $each -> create_time ?></span>
							</div>
						  <div class="panel-body"><?= $each -> status == 0 ? '未回覆' : $each -> answer ?>
								<br/>
								<span style="font-size:10px; color: #AAA"><?= $each -> answer_time ?></span>
							</div>

						</div>
						<br/>
					<?php endforeach ?>
			  </div>
			</div>
		</div>

		<!--================================================== -->

		<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
		<script src="<?= base_url() ?>js/plugin/pace/pace.min.js"></script>

	    <!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
	    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script> if (!window.jQuery) { document.write('<script src="<?= base_url() ?>js/libs/jquery-2.1.1.min.js"><\/script>');} </script>

	    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<script> if (!window.jQuery.ui) { document.write('<script src="<?= base_url() ?>js/libs/jquery-ui-1.10.3.min.js"><\/script>');} </script>

		<!-- IMPORTANT: APP CONFIG -->
		<script src="<?= base_url() ?>js/app.config.js"></script>

		<!-- JS TOUCH : include this plugin for mobile drag / drop touch events
		<script src="js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script> -->

		<!-- BOOTSTRAP JS -->
		<script src="<?= base_url() ?>js/bootstrap/bootstrap.min.js"></script>

		<!-- JQUERY VALIDATE -->
		<script src="<?= base_url() ?>js/plugin/jquery-validate/jquery.validate.min.js"></script>

		<!-- JQUERY MASKED INPUT -->
		<script src="<?= base_url() ?>js/plugin/masked-input/jquery.maskedinput.min.js"></script>

		<!--[if IE 8]>

			<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>

		<![endif]-->

		<!-- MAIN APP JS FILE -->
		<script src="<?= base_url() ?>js/app.min.js"></script>
		<script src="<?= base_url() ?>js/app/login.js"></script>

		<script type="text/javascript">

		</script>
	</body>
</html>
