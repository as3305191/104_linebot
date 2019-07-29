<!-- #NAVIGATION -->
<!-- Left panel : Navigation area -->
<!-- Note: This width of the aside area can be adjusted through LESS/SASS variables -->
<aside id="left-panel">

	<!-- User info -->
	<div class="login-info">
		<span data-toggle="dropdown"> <!-- User image size is adjusted inside CSS, it should stay as is -->

			<a href="javascript:void(0);" id="show-shortcut" >
				<?php if(!empty($user_img_url)): ?>
					<img src="<?= $user_img_url . "/thumb" ?>" alt="me" class="online" />
				<?php endif ?>
				<span style="overflow:hidden;">
					<?= $login_user_name ?><br/><?= $login_user -> sys_name ?>
				</span>
				<i class="fa fa-angle-down"></i>
			</a>
		</span>


		<ul class="dropdown-menu" style="top:auto;">
			<?php if(!empty($group_list)): ?>
				<?php foreach($group_list as $each): ?>
					<li>
						<a href="javascript:void(0);" onclick="chgUser(<?= $each -> id ?>)">
							<?php if(empty($each -> image_id)): ?>
								<img height="30" src="<?= base_url('img/demo/login/noimage.png') ?>" />
							<?php else: ?>
								<img height="30" src="<?= base_url('mgmt/images/get/' . $each -> image_id) . '/thumb' ?>" />
							<?php endif ?>
							<?= $each -> user_name ?></a>
					</li>
				<?php endforeach ?>
			<?php endif ?>
		</ul>
	</div>
	<!-- end user info -->

	<!-- NAVIGATION : This navigation is also responsive

	To make this navigation dynamic please make sure to link the node
	(the reference to the nav > ul) after page load. Or the navigation
	will not initialize.
	-->
	<nav>
		<!--
		NOTE: Notice the gaps after each icon usage <i></i>..
		Please note that these links work a bit different than
		traditional href="" links. See documentation for details.
		-->

		<ul>
			<?php foreach($menu_list as $each): ?>
			<li>
				<a href="<?= !empty($each -> base_path) ? $each -> base_path : '#' ?>" title="<?= $each -> nav_name ?>"><i class="fa fa-lg fa-fw <?= $each -> icon ?>"></i> <span class="menu-item-parent"><?= $each -> nav_name ?></span></a>
				<?php if(!empty($each -> sub_list)): ?>
				<ul>
					<?php foreach($each -> sub_list as $s_each): ?>
						<li class="">
							<a href="<?= $s_each -> base_path ?>" title="<?= $s_each -> nav_name?>">
								<i class="fa <?= $s_each -> icon?>"></i>
								<span class="menu-item-parent"><?= $s_each -> nav_name ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</li>
			<?php endforeach; ?>
		</ul>
	</nav>

	<span class="minifyme" data-action="minifyMenu"> <i class="fa fa-arrow-circle-left hit"></i> </span>

</aside>
<!-- END NAVIGATION -->
