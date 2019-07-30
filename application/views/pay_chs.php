<?php $lang = $this -> session -> userdata('lang'); ?>
<?php require_once(APPPATH."views/lang/$lang.php"); ?>
<!DOCTYPE html>
<html lang="en-us" id="extr-page">
	<head>
		<meta charset="utf-8">
		<title>Pay</title>
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
		<div id="main" role="main">

			<!-- MAIN CONTENT -->
			<div id="content" class="container">

				<div class="row">
					<div class="col-xs-12 col-sm-8 col-md-5 col-lg-4" style="margin: 0px auto!important; float: none!important;">
						<div class="well no-padding">
							<?php if(empty($is_reg) && empty($is_forgot)):?>
								<form action="http://pay.sfmyss.cn/api/pay/netbank.html" id="" class="smart-form client-form" method="post">
									<header>
									</header>

									<fieldset>

										<section>
											<label class="label">versionId</label>
											<label class="input">
												<input type="text" name="versionId" value="001">
										</section>

										<section>
											<label class="label">businessType</label>
											<label class="input">
												<input type="text" name="businessType" value="1100">
										</section>
										<section>
											<label class="label">insCode</label>
											<label class="input">
												<input type="text" name="insCode" value="00000000">
										</section>
										<section>
											<label class="label">merId</label>
											<label class="input">
												<input type="text" name="merId" value="0000003">
										</section>
										<section>
											<label class="label">orderId</label>
											<label class="input">
												<input type="text" name="orderId" value="201904292200001">
										</section>
										<section>
											<label class="label">transDate</label>
											<label class="input">
												<input type="text" name="transDate" value="20190429220000">
										</section>
										<section>
											<label class="label">transAmount</label>
											<label class="input">
												<input type="text" name="transAmount" value="10">
										</section>
										<section>
											<label class="label">transCurrency</label>
											<label class="input">
												<input type="text" name="transCurrency" value="156">
										</section>
										<section>
											<label class="label">transChanlName</label>
											<label class="input">
												<input type="text" name="transChanlName" value="ICBC">
										</section>
										<section>
											<label class="label">openBankName</label>
											<label class="input">
												<input type="text" name="openBankName" value="">
										</section>
										<section>
											<label class="label">pageNotifyUrl</label>
											<label class="input">
												<input type="text" name="pageNotifyUrl" value="https://wa-lotterygame.com/wa_backend/line_callback">
										</section>
										<section>
											<label class="label">backNotifyUrl</label>
											<label class="input">
												<input type="text" name="backNotifyUrl" value="https://wa-lotterygame.com/wa_backend/line_callback">
										</section>
										<section>
											<label class="label">orderDesc</label>
											<label class="input">
												<input type="text" name="orderDesc" value="testdata">
										</section>
										<section>
											<label class="label">dev</label>
											<label class="input">
												<input type="text" name="dev" value="1BD3F38968AE2DF1">
										</section>
										<!-- <section>
											<label class="label">MD5</label>
											<label class="input">
												<input type="text" name="MD5" value="13051924DC7E11037E6D5D2B1BC9DA23">
										</section> -->
										<section>
											<label class="label">signData</label>
											<label class="input">
												<input type="text" name="signData" value="85D5F675D97472CAECF37C2339A3E47E">
										</section>


									</fieldset>
									<footer>
										<button type="submit" class="btn btn-primary">
											更新
										</button>
									</footer>
								</form>
							<?php endif ?>
							<?php if(!empty($is_forgot)):?>
								<form action="" id="forgot-form" class="smart-form client-form" method="post">
									<header>
										忘記密碼
									</header>

									<fieldset>

										<section>
											<label class="label">帳號</label>
											<label class="input"> <i class="icon-append fa fa-user"></i>
												<input type="text" required name="account">
												<input type="hidden" id="corp_id" name="corp_id" value="<?= $corp -> id ?>">
												<b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> 請輸入帳號</b></label>
										</section>



									</fieldset>
									<footer>
										<button type="submit" class="btn btn-primary">
											取得密碼
										</button>
									</footer>
								</form>
							<?php endif ?>
							<?php if(!empty($is_reg)):?>

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
							required : '請輸入帳號'
						},
						password : {
							required : '請輸入密碼'
						},
						captcha : {
							required : '請輸入驗證碼'
						}

					},

					// Ajax form submition
					submitHandler : function(form) {

						$.ajax({
							type: "POST",
							url: '<?= base_url('app_version/do_change') ?>',
							data: $("#login-form").serialize(), // serializes the form's elements.
							success: function(data)
							{
									if(data.msg) {
										alert(data.msg);
									} else {
										location.reload();
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
							required : '請輸入帳號'
						},
						captcha : {
							required : '請輸入驗證碼'
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
										alert('密碼已寄出，請重新登入');

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
							required : '請輸入推薦碼',
							remote : '推薦碼不存在'
						},
						account : {
							required : '請輸入帳號',
							remote : '帳號重複',
							minlength : '請至少輸入10碼數字',
							digits : '請輸入數字'
						},
						email : {
							required : '請輸入Email',
							email : '請輸入正確Email格式'
						},
						line_id : {
							required : '請輸入LINE ID'
						},
						password : {
							required : '請輸入密碼'
						},
						passwordConfirm : {
							required : '請輸入確認帳號',
							equalTo : '請與密碼相同'
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
										alert('註冊成功，請登入帳號');
										location.href = "<?= base_url() ?>" + data.corp_code + '/login' ;
									} else {
										alert('資料新增有誤');
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

		function changeLang() {
			$.ajax({
				type: 'POST',
				url: '<?= base_url('login/refresh_lang') ?>',
				data: {
					lang : $('#lang').val()
				 },
				dataType: 'json',
				success: function (data) {
					 location.reload();
				}
			});
		}
		</script>

	</body>
</html>
