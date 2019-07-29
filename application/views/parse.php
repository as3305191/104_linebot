<!DOCTYPE html>
<html lang="en-us" id="extr-page">
	<head>
		<meta charset="utf-8">
		<title><?= $corp -> sys_name ?></title>
		<meta name="description" content="">
		<meta name="author" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

		<!-- #CSS Links -->
		<!-- Basic Styles -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url('css/bootstrap.min.css') ?>">
		<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url('css/font-awesome.min.css') ?>">

		<!-- SmartAdmin Styles : Caution! DO NOT change the order -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url() ?>css/smartadmin-production-plugins.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url() ?>css/smartadmin-production.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url() ?>css/smartadmin-skins.min.css">

		<!-- SmartAdmin RTL Support -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url() ?>css/smartadmin-rtl.min.css">

		<!-- We recommend you use "your_style.css" to override SmartAdmin
		     specific styles this will also ensure you retrain your customization with each SmartAdmin update.
		<link rel="stylesheet" type="text/css" media="screen" href="css/your_style.css"> -->

		<!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url() ?>css/demo.min.css">

		<!-- #FAVICONS -->
		<link rel="shortcut icon" href="<?= base_url() ?>img/favicon/favicon.ico" type="image/x-icon">
		<link rel="icon" href="<?= base_url() ?>img/favicon/favicon.ico" type="image/x-icon">

		<!-- #GOOGLE FONT -->
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

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
			#extr-page #main {
				<?php if(!empty($corp -> bg_image_id)): ?>
					background-image: url('<?= base_url('mgmt/images/get/' . $corp -> bg_image_id)  ?>');
				<?php else: ?>
					background-image: url('<?= base_url() ?>img/demo/login/login_bg.jpg');
				<?php endif ?>
			}
			label.error {
				color: red;
			}
		</style>
	</head>

	<body class="animated fadeInDown">

		<h1>分析</h1>
		<div id="c_result"></div>


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
		$.ajaxSetup({
			scriptCharset: "utf-8", //or "ISO-8859-1"
			contentType: "application/json; charset=utf-8"
		});
		function hello() {
			console.log('hello');
		}
		function doParseBtc() {
			$.getJSON('http://whateverorigin.org/get?url=' +
			    encodeURIComponent('https://www.maicoin.com/api/prices/btc-twd') + '&callback=?',
			    function (data) {
			        console.log(data);

			        //If the expected response is text/plain
			        //$("#viewer").html(data.contents);

			        //If the expected response is JSON
			        //var response = $.parseJSON(data.contents);
			});
		}

		setInterval(function(){
			// doParseBtc();
		}, 3000);
		</script>
	</body>
</html>
