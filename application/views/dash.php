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
			body, html {
			    height: 100%;
			}

			#extr-page #main {
				height:100%;
				position: absolute;
				background-size: cover;
				padding: 0px;
			}

			#main_body {
				background-image:url('<?= base_url('img/v/dash/btn-01.png') ?>');
				height:45px;
				color: white;
				font-size: 20px;
				background-size: cover;
			}

			.line_01_box {
				text-align: center;
			}

			.line_01 {
				background-image:url('<?= base_url('img/v/login/btn-account-01.png') ?>');
				width: 230px;
				height:45px;
				line-height: 45px;
				margin: 0 auto;
				color: #4f3019;
				font-size: 16px;
				background-size: contain;
			 	background-position: center center;
				background-repeat: no-repeat;
				text-align: center;
				text-shadow:
			    1px  1px 2px white,
			    1px -1px 2px white,
			   -1px  1px 2px white,
			   -1px -1px 2px white;
			}

			.line_04 {
				background-image:url('<?= base_url('img/v/dash/btn-04.png') ?>');
				width: 220px;
				max-width: 80%;
				height:30px;
				margin: 0 auto;
				background-size: contain;
			 	background-position: center center;
				background-repeat: no-repeat;
			}

			label.error {
				color: red;
			}

			.a_bg {
				position: relative;
				background-repeat: no-repeat;
				width: 100%;
			}
			.a_box {
				position: relative;
				height: 34px;
				width: 90%;
				max-width: 400px;
				margin: 0 auto;
			}
			.a_bg img {
				position: absolute;
				top: 0px;
				left: 0px;
				height: 34px;
				width: 100%;
				margin: 0 auto;
				border: 0px;
			}
			.a_bg input, .a_bg select {
				position: absolute;
				top: 0px;
				left: 0px;
				height: 34px;
				width: 100%;
				border: 0px;
				margin: 0 auto;
				padding-left: 10px;
				font-size: 16px;
				color: white;
				background: none;
			}

			.btn_box_outer {
				position: relative;
				height: 34px;
				width: 330px;
				margin:0px auto;
				max-width: 90%;
			}
			.btn_box {
				position: relative;
				height: 34px;
				width: 40px;
				max-width: 400px;
			}
			.btn_box img {
				position: absolute;
				top: 0px;
				left: 0px;
				height: 20px;
				width: 40px;
				margin: 10px auto;
				border: 0px;
			}

			.n_label {
				padding: 8px;
				background: -webkit-linear-gradient(#FFEC46, #EA8D2B);
			  -webkit-background-clip: text;
			  -webkit-text-fill-color: transparent;
			}

			.rt_label_1 {
				padding: 8px;
				background: -webkit-linear-gradient(#FFEC46, #EA8D2B);
			  -webkit-background-clip: text;
			  -webkit-text-fill-color: transparent;
				text-align: center;
			}

			.rt_label_3 {
				padding: 0px;
				font-size: 24px;
				background: -webkit-linear-gradient(#FFEC46, #ED5D16, #FFEC46);
			  -webkit-background-clip: text;
			  -webkit-text-fill-color: transparent;
				text-align: center;
			}

			.rt_label_2 {
				padding: 0px 0px 8px;
				color: white;
				text-align: center;
				font-size: 12px;
			}

			#r_top {
				background-image:url('<?= base_url('img/v/dash/btn-02.png') ?>');
			}
			#p1 {
				background-image:url('<?= base_url('img/v/dash/btn-08.png') ?>');
				min-height: 150px;
				width: 50%;
				float: left;
			}
			#p2 {
				background-image:url('<?= base_url('img/v/dash/btn-10.png') ?>');
				min-height: 150px;
				width: 50%;
				float: left;
			}
			#p3 {
				background-image:url('<?= base_url('img/v/dash/btn-12.png') ?>');
				min-height: 150px;
				width: 50%;
				float: left;
			}
			#p4 {
				background-image:url('<?= base_url('img/v/dash/btn-14.png') ?>');
				min-height: 150px;
				width: 50%;
				float: left;
			}
			#p5 {
				background-image:url('<?= base_url('img/v/dash/btn-16.png') ?>');
				min-height: 150px;
				width: 50%;
				float: left;
			}
			#p6 {
				background-image:url('<?= base_url('img/v/dash/btn-17.png') ?>');
				min-height: 150px;
				width: 50%;
				float: left;
				background-size: cover;
			 	background-position: center center;
				background-repeat: no-repeat;
				background-color: #F0C1BC;
			}
		</style>
	</head>

	<body class="animated fadeInDown" >
		<div id="main_body">
			<div class="btn_box_outer">
				<div class="btn_box pull-left">
					<img src="<?= base_url('img/v/dash/h_left.png') ?>" />
				</div>
				<div class="btn_box pull-right">
					<img src="<?= base_url('img/v/dash/h_right.png') ?>" />
				</div>
				<div style="text-align:center;">
					<div class="n_label"><?= $corp -> sys_name ?></div>
				</div>
			</div>
		</div>

		<div id="main" role="main">
			<div id="r_top">
				<div class="line_04"></div>
				<div class="rt_label_3">今日會員總贏金額</div>
				<div class="rt_label_1">
					<?php
						$total_amt_str = $this -> session -> userdata('total_amt_str');
						$n_str = "$param->total_amt_0-$param->total_amt_1";
						$need_refresh = FALSE;
						if(!empty($total_amt_str)) {
							if($total_amt_str != $n_str) {
								$this -> session -> set_userdata('total_amt_str', $n_str);
								$need_refresh = TRUE;
							}
						} else {
							$need_refresh = TRUE;
							$this -> session -> set_userdata('total_amt_str', $n_str);
						}

						$s_win = 0;
						if($need_refresh || empty($this -> session -> userdata('s_win'))) {
							$s_win = rand($param -> total_amt_0, $param -> total_amt_1);
						} else {
							$s_win = $this -> session -> userdata('s_win');
						}
						$s_win += rand(0, 1000);
						$this -> session -> set_userdata('s_win', $s_win);
						$win_num = number_format($s_win);
						$win_num_arr = str_split($win_num);

					?>
					<img src="<?= base_url('img/v/dash/dollar.png') ?>" height="34" />
					<?php foreach ($win_num_arr as $each): ?>
						<img src="<?= base_url("img/v/dash/$each.png") ?>" height="30" />
					<?php endforeach ?>
				</div>
				<div class="line_04"></div>
				<div class="rt_label_2">在線人數:
					<?php
						$s_online_str = $this -> session -> userdata('s_online_str');
						$n_str = "$param->online_amt_0-$param->online_amt_1";
						$need_refresh = FALSE;
						if(!empty($s_online_str)) {
							if($s_online_str != $n_str) {
								$this -> session -> set_userdata('s_online_str', $n_str);
								$need_refresh = TRUE;
							}
						} else {
							$need_refresh = TRUE;
							$this -> session -> set_userdata('s_online_str', $n_str);
						}

						$s_online = 0;
						if($need_refresh || empty($this -> session -> userdata('s_online'))) {
							$s_online = rand($param -> online_amt_0, $param -> online_amt_1);
						} else {
							$s_online = $this -> session -> userdata('s_online');
						}
						$s_online += rand(-10, 10);
						$s_online = ($s_online > 0 ? $s_online : 0);
						$this -> session -> set_userdata('s_online', $s_online);

						echo number_format($s_online);
					 ?>人
				</div>
			</div>
			<div id="r_row_1">
				<div id="p1">
					<a style="text-align:center;" href="javascript:void(0)" onclick="alert('hi..');">
						<div style="width: 150px; margin: 15px auto;">
							<img style="float: left;" src="<?= base_url("img/v/dash/btn-07.png") ?>" height="70" />
							<div style="float: left; color: white; font-size: 20px; margin: 10px 0 0 0;">馬上推廣<br/>成功使用</div>
						</div>
						<div style="clear:both; color: yellow; font-size: 24px;">送20%點數</div>
						<div style="clear:both; color: white; font-size: 12px;">立即推薦推廣網址 轉貼</div>
					</a>
				</div>
				<div id="p2">
					<a style="text-align:center;" href="javascript:void(0)" onclick="alert('hi..');">
						<div style="width: 150px; margin: 20px auto;">
							<img style="float: left;" src="<?= base_url("img/v/dash/btn-09.png") ?>" height="70" />
							<div style="float: left; color: white; font-size: 20px; margin: 10px 0 0 0;">電子錢包<br/>正式啟用</div>
						</div>
						<div style="padding-top: 10px;clear:both; color: white; font-size: 12px;">可提款  可交易</div>
						<div style="clear:both; color: white; font-size: 12px;">可購買  可賺點</div>
					</a>
				</div>
				<div id="p3">
					<a style="text-align:center;" href="javascript:void(0)" onclick="alert('hi..');">
						<div style="width: 150px; margin: 20px auto;">
							<img style="float: left;" src="<?= base_url("img/v/dash/btn-11.png") ?>" height="70" />
							<div style="float: left; color: white; font-size: 20px; margin: 10px 0 0 0; text-align:left;">火速繳費<br/><span style="font-size: 16px;">升級經理人</span></div>
						</div>
						<div style="padding-top: 10px;clear:both; color: white; font-size: 12px;">線下推廣無限領取10%點數</div>
					</a>
				</div>
				<div id="p4">
					<a style="text-align:center;" href="javascript:void(0)" onclick="alert('hi..');">
						<div style="width: 150px; margin: 20px auto;">
							<img style="float: left;" src="<?= base_url("img/v/dash/btn-13.png") ?>" height="60" />
							<div style="float: left; color: white; font-size: 20px; margin: 10px 0 0 0; text-align:left;">直營加盟<br/><span style="font-size: 16px;">馬上開店賺錢</span></div>
						</div>
						<div style="padding-top: 10px;clear:both; color: white; font-size: 12px;">快速・穩定．被動收入好選擇</div>
					</a>
				</div>
				<div id="p5">
					<a style="text-align:center;" href="javascript:void(0)" onclick="alert('hi..');">
						<div style="width: 160px; margin: 20px auto;">
							<img style="float: left;" src="<?= base_url("img/v/dash/btn-15.png") ?>" height="70" />
							<div style="float: left; color: white; font-size: 20px; margin: 10px 0 0 0; text-align:left;">優質娛樂城<br/><span style="font-size: 16px;">廣告推薦</span></div>
						</div>
						<div style="padding-top: 10px;clear:both; color: white; font-size: 12px;">博通娛樂城註冊送100點</div>
					</a>
				</div>
				<div id="p6"></div>
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
						},
						i_agree : {
							required : '請同意'
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
										alert('簡訊已寄出，請重新登入');
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
		</script>

	</body>
</html>
