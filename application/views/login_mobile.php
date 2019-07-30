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
			#extr-page #main {
				background-image: url('<?= base_url() ?>img/demo/login/login_bg.jpg');
			}
			label.error {
				color: red;
			}
		</style>
	</head>

	<body class="animated fadeInDown">

		<header id="header">

			<div id="logo-group">
				<span id="logo" style="width:300px;">
					<a href="<?= base_url($corp -> corp_code . '/login') ?>">
						<img src="<?= base_url() ?>img/demo/login/logo.png" style="height: 50px!important" alt="SmartAdmin">
						<span style="font-size:20px;"><?= $corp -> sys_name ?></span>
					</a>
				</span>
			</div>

			<span id="extr-page-header-space"><a href="<?= base_url('/login/logout') ?>" class="btn btn-danger">登出</a> </span>

		</header>

		<div id="main" role="main">

			<!-- MAIN CONTENT -->
			<div id="content" class="container">

				<div class="row">
					<div class="col-xs-12 col-sm-8 col-md-5 col-lg-4" style="margin: 0px auto!important; float: none!important;">
						<div class="well no-padding">
								<form action="" id="login-form" class="smart-form client-form" method="post">
									<header>
										手機驗證
									</header>

									<fieldset>

										<section>
											<label class="label">手機號碼</label>
											<label class="input"> <i class="icon-append fa fa-user"></i>
												<?php
												 	$show_mobile = "";
													if(!empty($login_user -> mobile)) {
														$show_mobile = $login_user -> mobile;
													} else if(is_numeric($login_user -> account)){
														$show_mobile = $login_user -> account;
													}
												 ?>
												<input type="text" required id="mobile" name="mobile" value="<?= $show_mobile ?>" <?= !empty($show_mobile) ? 'readonly' : '' ?> placeholder="請輸入手機號碼">
												<input type="hidden" id="corp_id" name="corp_id" value="<?= $corp -> id ?>">
												<b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> 請輸入手機號碼</b></label>
										</section>

										<section id="sec_reg_code" style="display:none;">
											<label class="label">驗證碼</label>
											<label class="input"> <i class="icon-append fa fa-lock"></i>
												<input type="text" name="reg_code" id="reg_code" placeholder="請輸入驗證碼">
												<b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> 請輸入驗證碼</b> </label>
										</section>

									</fieldset>
									<footer>
										<button type="submit" id="btn_submit" class="btn btn-primary">
											取得驗證碼
										</button>
										<button type="button" id="btn_validate" style="display:none;" class="btn btn-danger" onclick="doValidate()">
											確認驗證碼
										</button>
									</footer>
								</form>

						</div>

					</div>
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
			runAllForms();

			$(function() {
				// Validation
				$("#login-form").validate({
					// Rules for form validation
					rules : {
						mobile : {
							required : true,
							digits:true
						},
						password : {
							required : true,
							minlength : 3,
							maxlength : 20
						}
					},

					// Messages for form validation
					messages : {
						account : {
							required : '請輸入帳號',
							digits:'請輸入數字'
						}
					},

					// Ajax form submition
					submitHandler : function(form) {

						$.ajax({
							type: "POST",
							url: '<?= base_url('login/get_mobile_reg_code') ?>',
							data: {
								user_id: '<?= $login_user_id ?>',
								mobile: $('#mobile').val()
							},
							success: function(data)
							{
									if(data.msg) {
										alert(data.msg);
									} else {
										$('#btn_submit').hide();
										$('#btn_validate').show();
										$('#sec_reg_code').show();
									}
							}
						});
					},

					// Do not change code below
					errorPlacement : function(error, element) {
						error.insertAfter(element.parent());
					}
				});

			});

			function doValidate() {
				if($('#reg_code').val().length == 0) {
					alert('請輸入驗證碼');
					return;
				}
				$.ajax({
					type: "POST",
					url: '<?= base_url('login/check_mobile_reg_code') ?>',
					data: {
						user_id: '<?= $login_user_id ?>',
						reg_code: $('#reg_code').val()
					},
					success: function(data)
					{
							if(data.is_valid == 1) {
								alert('驗證成功');
								location.href = "<?= base_url() ?>" + 'app/#mgmt/dashboard' ;
							} else {
								alert('驗證碼有誤');
							}
					}
				});
			}
		</script>

	</body>
</html>
