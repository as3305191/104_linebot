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
		<link rel="icon" type="image/png" href="<?= base_url('mgmt/images/get/' . $corp -> logo_image_id)  ?>" />

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

		<header id="header">

			<div id="logo-group">
				<span id="logo" style="width:300px;">
					<a href="<?= base_url($corp -> corp_code . '/login') ?>">
						<?php if(!empty($corp -> logo_image_id)): ?>
							<img src="<?= base_url('mgmt/images/get/' . $corp -> logo_image_id)  ?>" style="height: 50px!important" alt="SmartAdmin">
						<?php else: ?>
							<img src="<?= base_url() ?>img/demo/login/logo.png" style="height: 50px!important" alt="SmartAdmin">
						<?php endif ?>
						<span style="font-size:20px;"><?= $corp -> sys_name ?></span>
					</a>
				</span>
			</div>

			<?php if(empty($is_reg) && empty($is_forgot)):?>
				<span id="extr-page-header-space"><a href="<?= base_url($corp -> corp_code . '/login/register') ?>" class="btn btn-danger">??????</a> </span>
			<?php else: ?>
				<span id="extr-page-header-space"><a href="<?= base_url($corp -> corp_code . '/login') ?>" class="btn btn-danger">??????</a> </span>
			<?php endif ?>
		</header>

		<div id="main" role="main">

			<!-- MAIN CONTENT -->
			<div id="content" class="container">

				<div class="row">
					<div class="col-xs-12 col-sm-8 col-md-5 col-lg-4" style="margin: 0px auto!important; float: none!important;">
						<div class="well no-padding">
							<?php if(empty($is_reg) && empty($is_forgot)):?>
								<form action="" id="login-form" class="smart-form client-form" method="post">
									<header>
										??????
									</header>

									<fieldset>

										<section>
											<label class="label">??????</label>
											<label class="input"> <i class="icon-append fa fa-user"></i>
												<input type="text" required name="account">
												<input type="hidden" id="corp_id" name="corp_id" value="<?= $corp -> id ?>">
												<b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> ???????????????</b></label>
										</section>

										<section>
											<label class="label">??????</label>
											<label class="input"> <i class="icon-append fa fa-lock"></i>
												<input type="password" name="password">
												<b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> ???????????????</b> </label>
										</section>

										<section>
											<label class="label">?????????</label>
											<label class="input"> <i class="icon-append fa fa-lock"></i>
												<input type="text" required name="captcha">
												<b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> ??????????????????</b> </label>
												<div id="c_img"><?php echo $captcha['image']; ?></div>
												<a class="blurry" id="newPic" onclick="getPic();">????????????????????????</a>
										</section>

										<section>
												<label>??????????????????????????????????????????????????????????????????????????????????????????</label>
												<label style="font-weight:bolder;color:black;"><input name="i_agree" required type="checkbox" />?????????</label>
										</section>

									</fieldset>
									<footer>
										<button type="submit" class="btn btn-primary">
											??????
										</button>
										<a href="<?= base_url($corp -> corp_code . '/login/forgot') ?>" class="btn btn-warning pull-left">
											????????????
										</a>
									</footer>
								</form>
							<?php endif ?>
							<?php if(!empty($is_forgot)):?>
								<form action="" id="forgot-form" class="smart-form client-form" method="post">
									<header>
										????????????
									</header>

									<fieldset>

										<section>
											<label class="label">??????</label>
											<label class="input"> <i class="icon-append fa fa-user"></i>
												<input type="text" required name="account">
												<input type="hidden" id="corp_id" name="corp_id" value="<?= $corp -> id ?>">
												<b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> ???????????????</b></label>
										</section>

										<section>
											<label class="label">?????????</label>
											<label class="input"> <i class="icon-append fa fa-lock"></i>
												<input type="text" required name="captcha">
												<b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> ??????????????????</b> </label>
												<div id="c_img"><?php echo $captcha['image']; ?></div>
												<a class="blurry" id="newPic" onclick="getPic();">????????????????????????</a>
										</section>

									</fieldset>
									<footer>
										<button type="submit" class="btn btn-primary">
											??????????????????
										</button>
									</footer>
								</form>
							<?php endif ?>
							<?php if(!empty($is_reg)):?>
							<form action="" id="reg-form" class="smart-form client-form">
								<header>
									????????????
								</header>

								<fieldset>
									<section>
										<label class="input"> <i class="icon-append fa fa-user"></i>
											<input type="text" name="intro_code" placeholder="?????????" <?= !empty($code) ? 'readonly="readonly"' : '' ?> value="<?= !empty($code) ? $code : '' ?>">
											<b class="tooltip tooltip-bottom-right">??????????????????</b> </label>
									</section>
									<section>
										<label class="input"> <i class="icon-append fa fa-user"></i>
											<input type="text" name="account" placeholder="??????(????????????, ??????????????????86)">
											<input type="hidden" id="corp_id" name="corp_id" value="<?= $corp -> id ?>">
											<b class="tooltip tooltip-bottom-right">???????????????</b> </label>
									</section>

									<section>
										<label class="input"> <i class="icon-append fa fa-user"></i>
											<input type="text" name="user_name" placeholder="??????">
											<b class="tooltip tooltip-bottom-right">???????????????</b> </label>
									</section>

									<section>
										<label class="input"> <i class="icon-append fa fa-envelope"></i>
											<input type="email" name="email" placeholder="Email">
											<b class="tooltip tooltip-bottom-right">?????????Email</b> </label>
									</section>

									<section>
										<label class="input"> <i class="icon-append fa fa-envelope"></i>
											<input type="text" name="line_id" placeholder="?????????LINE ID">
											<b class="tooltip tooltip-bottom-right">?????????LINE ID</b> </label>
									</section>

									<section>
										<label class="input"> <i class="icon-append fa fa-lock"></i>
											<input type="password" name="password" placeholder="??????" id="password">
											<b class="tooltip tooltip-bottom-right">???????????????</b> </label>
									</section>

									<section>
										<label class="input"> <i class="icon-append fa fa-lock"></i>
											<input type="password" name="passwordConfirm" placeholder="????????????">
											<b class="tooltip tooltip-bottom-right">?????????????????????</b> </label>
									</section>

									<section>
										<label class="select"> <i class="icon-append fa fa-bank"></i>
											<select name="bank_id">
												<option value="" selected="" disabled="">???????????????</option>
												<?php if(!empty($bank_list)) : ?>
													<?php foreach($bank_list as $each) : ?>
														<option value="<?= $each -> bank_id ?>"><?= $each -> bank_name ?></option>
													<?php endforeach ?>
												<?php endif ?>
											</select>
											<b class="tooltip tooltip-bottom-right">????????????</b> </label>
									</section>

									<section>
										<label class="input"> <i class="icon-append fa fa-lock"></i>
											<input type="text" name="bank_account" placeholder="?????????????????????">
											<b class="tooltip tooltip-bottom-right">?????????????????????</b> </label>
									</section>
								</fieldset>


								<footer>
									<button type="submit" class="btn btn-primary">
										??????
									</button>
								</footer>

							</form>
						<?php endif ?>
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
						account : {
							required : true
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
							required : '???????????????'
						},
						password : {
							required : '???????????????'
						},
						captcha : {
							required : '??????????????????'
						},
						i_agree : {
							required : '?????????'
						}
					},

					// Ajax form submition
					submitHandler : function(form) {

						$.ajax({
							type: "POST",
							url: '<?= base_url('login/do_login') ?>',
							data: $("#login-form").serialize(), // serializes the form's elements.
							success: function(data)
							{
									if(data.msg) {
										alert(data.msg);
									} else {
										location.href = "<?= base_url() ?>" + 'app/#mgmt/dashboard' ;
									}
							}
						});
					},

					// Do not change code below
					errorPlacement : function(error, element) {
						error.insertAfter(element.parent());
					}
				});
				// Forgot
				$("#forgot-form").validate({
					// Rules for form validation
					rules : {
						account : {
							required : true
						}
					},

					// Messages for form validation
					messages : {
						account : {
							required : '???????????????'
						},
						captcha : {
							required : '??????????????????'
						}
					},

					// Ajax form submition
					submitHandler : function(form) {

						$.ajax({
							type: "POST",
							url: '<?= base_url('login/do_forgot') ?>',
							data: $("#forgot-form").serialize(), // serializes the form's elements.
							success: function(data)
							{
									if(data.msg) {
										alert(data.msg);
									} else {
										alert('?????????????????????????????????');
										location.href = $('#extr-page-header-space .btn').attr('href');
									}
							}
						});
					},

					// Do not change code below
					errorPlacement : function(error, element) {
						error.insertAfter(element.parent());
					}
				});

				$("#reg-form").validate({

					// Rules for form validation
					rules : {
						intro_code : {
							minlength : 8,
							required: true,
							remote: "<?= base_url('login/check_code') ?>"
						},
						account : {
							required : true,
							minlength : 10,
							digits: true,
							remote: "<?= base_url('login/check_account') ?>"
						},
						user_name : {
							required : true
						},
						email : {
							required : true,
							email : true
						},
						line_id : {
							required : true
						},
						password : {
							required : true,
							minlength : 3,
							maxlength : 20
						},
						passwordConfirm : {
							required : true,
							minlength : 3,
							maxlength : 20,
							equalTo : '#password'
						},
						firstname : {
							required : true
						},
						lastname : {
							required : true
						},
						gender : {
							required : true
						},
						terms : {
							required : true
						}
					},

					// Messages for form validation
					messages : {
						intro_code : {
							required : '??????????????????',
							remote : '??????????????????'
						},
						account : {
							required : '???????????????',
							remote : '????????????',
							minlength : '???????????????10?????????',
							digits : '???????????????'
						},
						email : {
							required : '?????????Email',
							email : '???????????????Email??????'
						},
						line_id : {
							required : '?????????LINE ID'
						},
						password : {
							required : '???????????????'
						},
						passwordConfirm : {
							required : '?????????????????????',
							equalTo : '??????????????????'
						}
					},

					// Ajax form submition
					submitHandler : function(form) {

						$.ajax({
							type: "POST",
							url: '<?= base_url('login/do_reg') ?>',
							data: $("#reg-form").serialize(), // serializes the form's elements.
							success: function(data)
							{
									if(data && data.last_id > 0) {
										alert('??????????????????????????????');
										location.href = "<?= base_url() ?>" + data.corp_code + '/login' ;
									} else {
										alert('??????????????????');
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

			function getPic(){
				$.ajax({
			    type: 'GET',
			    url: '<?= base_url('login/refresh_captcha') ?>',
			    data: { },
			    dataType: 'json',
			    success: function (data) {
						 $('#c_img').html(data.captcha.image);
			    }
				});
		};
		</script>

	</body>
</html>
