<?php $lang = $this -> session -> userdata('lang'); ?>
<?php require_once(APPPATH."views/lang/$lang.php"); ?>
<!DOCTYPE html>
<html lang="en-us" id="extr-page">
	<head>
		<meta charset="utf-8">
		<title>App version</title>
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
			body, html {
			    height: 100%;
			}

			label.error {
				color: red;
			}

			.blurry {
				cursor: pointer;
			}

			#main {
				background: none!important;
			}
		</style>
	</head>

	<body class="animated fadeInDown" >
		<div id="main" role="main" style="text-align:center;padding-top: 40%;">
			<!-- MAIN CONTENT -->
			<a class="btn btn-primary" href="itms-services://?action=download-manifest&url=<?= $plist_url ?>">Install App</a>
		</div>
	</body>
</html>
