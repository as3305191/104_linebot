<!-- #HEADER -->
<header id="header">
	<div id="logo-group">

		<!-- PLACE YOUR LOGO HERE -->
		<span id="logo">
			<?php if(!empty($corp -> logo_image_id)): ?>
				<img src="<?= base_url('mgmt/images/get/' . $corp -> logo_image_id)  ?>" alt="SmartAdmin">
			<?php else: ?>
				<img src="<?= base_url() ?>img/demo/login/logo.png" alt="SmartAdmin">
			<?php endif ?>
		</span>
		<!-- END LOGO PLACEHOLDER -->
	</div>

	<style>
	.marquee {
		position:absolute;
		width: 100%;
		overflow: hidden;
		border: 1px solid #ccc;
		background: #ccc;
		height: 20px;
		overflow: hidden;
	}

	.marquee div {
		position:absolute;
		top: 30px;
	}

	#bank_alert {
		position: absolute;
		top:32px!important;
		left: 50px;
		right:100px;
		top:10px;
		max-width:400px;
		color:#AAFFFF;
		height:20px;
	}

	.blink_me {
  	animation: blinker 2s linear infinite;
	}

	@keyframes blinker {
	  50% { opacity: 0; }
	}
	</style>

	
	<!-- #TOGGLE LAYOUT BUTTONS -->
	<!-- pulled right: nav area -->
	<div class="pull-right">

		<!-- collapse menu button -->
		<div id="hide-menu" class="btn-header pull-right">
			<span> <a href="javascript:void(0);" data-action="toggleMenu" title="Collapse Menu"><i class="fa fa-reorder"></i></a> </span>
		</div>
		<!-- end collapse menu -->

		<!-- #MOBILE -->
		<!-- Top menu profile link : this shows only when top menu is active -->
		<ul id="mobile-profile-img" class="header-dropdown-list hidden-xs padding-5">
			<li class="">
				<a href="#" class="dropdown-toggle no-margin userdropdown" data-toggle="dropdown">
					<img src="<?= $user_img_url ?>"
						alt="<?= $login_user_name ?>" class="online" />
				</a>
				<ul class="dropdown-menu pull-right">
					<li>
						<a href="javascript:void(0);" class="padding-10 padding-top-0 padding-bottom-0"><i class="fa fa-cog"></i> Setting</a>
					</li>
					<li class="divider"></li>
					<li>
						<a href="#ajax/profile.html" class="padding-10 padding-top-0 padding-bottom-0"> <i class="fa fa-user"></i> <u>P</u>rofile</a>
					</li>
					<li class="divider"></li>
					<li>
						<a href="javascript:void(0);" class="padding-10 padding-top-0 padding-bottom-0" ><i class="fa fa-arrow-down"></i> <u>S</u>hortcut</a>
					</li>
					<li class="divider"></li>

					<li>
						<a href="javascript:void(0);" class="padding-10 padding-top-0 padding-bottom-0" data-action="launchFullscreen"><i class="fa fa-arrows-alt"></i> Full <u>S</u>creen</a>
					</li>
					<li class="divider"></li>
					<li>
						<a href="login/logout" class="padding-10 padding-top-5 padding-bottom-5" data-action="userLogout"><i class="fa fa-sign-out fa-lg"></i> <strong><u>L</u>ogout</strong></a>
					</li>
				</ul>
			</li>
		</ul>

		<!-- #Voice Command: Start Speech -->
		<!-- <div id="speech-btn" class="btn-header transparent pull-right hidden-sm hidden-xs">
			<div>
				<a href="javascript:void(0)" title="Voice Command" data-action="voiceCommand"><i class="fa fa-microphone"></i></a>
				<div class="popover bottom"><div class="arrow"></div>
					<div class="popover-content">
						<h4 class="vc-title">Voice command activated <br><small>Please speak clearly into the mic</small></h4>
						<h4 class="vc-title-error text-center">
							<i class="fa fa-microphone-slash"></i> Voice command failed
							<br><small class="txt-color-red">Must <strong>"Allow"</strong> Microphone</small>
							<br><small class="txt-color-red">Must have <strong>Internet Connection</strong></small>
						</h4>
						<a href="javascript:void(0);" class="btn btn-success" onclick="commands.help()">See Commands</a>
						<a href="javascript:void(0);" class="btn bg-color-purple txt-color-white" onclick="$('#speech-btn .popover').fadeOut(50);">Close Popup</a>
					</div>
				</div>
			</div>
		</div> -->
		<!-- end voice command -->

		<!-- logout button -->
		<div id="logout" class="btn-header transparent pull-right">
			<span> <a href="<?=isset($login_store_id)?'loginS/logout':'login/logout'?>" title="Sign Out" data-action="userLogout" data-logout-msg="You can improve your security further after logging out by closing this opened browser"><i class="fa fa-sign-out"></i></a> </span>
		</div>
		<!-- end logout button -->

		<!-- fullscreen button -->
		<div id="fullscreen" class="btn-header transparent pull-right">
			<span> <a href="javascript:void(0);" data-action="launchFullscreen" title="Full Screen"><i class="fa fa-arrows-alt"></i></a> </span>
		</div>
		<!-- end fullscreen button -->



		<!-- multiple lang dropdown : find all flags in the flags page -->
		<!-- <ul class="header-dropdown-list">
			<li>
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"> <img src="<?= base_url('img/blank.gif') ?>" class="flag flag-us" alt="United States"> <span> US</span> <i class="fa fa-angle-down"></i> </a>
				<ul class="dropdown-menu pull-right">
					<li class="active">
						<a href="javascript:void(0);"><img src="<?= base_url('img/blank.gif') ?>" class="flag flag-us" alt="United States"> English (US)</a>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="<?= base_url('img/blank.gif') ?>" class="flag flag-fr" alt="France"> Français</a>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="<?= base_url('img/blank.gif') ?>" class="flag flag-es" alt="Spanish"> Español</a>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="<?= base_url('img/blank.gif') ?>" class="flag flag-de" alt="German"> Deutsch</a>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="<?= base_url('img/blank.gif') ?>" class="flag flag-jp" alt="Japan"> 日本語</a>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="<?= base_url('img/blank.gif') ?>" class="flag flag-cn" alt="China"> 中文</a>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="<?= base_url('img/blank.gif') ?>" class="flag flag-it" alt="Italy"> Italiano</a>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="<?= base_url('img/blank.gif') ?>" class="flag flag-pt" alt="Portugal"> Portugal</a>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="<?= base_url('img/blank.gif') ?>" class="flag flag-ru" alt="Russia"> Русский язык</a>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="<?= base_url('img/blank.gif') ?>" class="flag flag-kr" alt="Korea"> 한국어</a>
					</li>

				</ul>
			</li>
		</ul> -->
		<!-- end multiple lang -->

	</div>
	<!-- end pulled right: nav area -->

</header>
<!-- END HEADER -->
