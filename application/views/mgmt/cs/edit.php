<style>
.file-drag-handle {
	display: none;
}
</style>
<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget" id="wid-id-7" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
	<header>
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="back_parent" onclick="currentApp.backTo()" class="btn btn-default ">
				<i class="fa fa-arrow-circle-left"></i>返回
			</a>
		</div>
		<?php if($login_user -> role_id == 1):?>
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="back_parent" onclick="currentApp.doSubmit()" class="btn btn-default btn-danger">
				<i class="fa fa-save"></i>存檔
			</a>
		</div>
		<?php endif ?>
	</header>

	<!-- widget div-->
	<div>
		<!-- widget edit box -->
		<div class="jarviswidget-editbox">
			<!-- This area used as dropdown edit box -->
			<input class="form-control" type="text">
		</div>
		<!-- end widget edit box -->

		<!-- widget content -->
		<div class="widget-body">

			<form id="app-edit-form" method="post" class="form-horizontal">
				<input type="hidden" name="id" id="item_id" value="<?= isset($item) ? $item -> id : '' ?>" />

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">推薦授權碼</label>
						<div class="col-md-6">
							<div class="input-group">
							   <input type="text" <?= isset($item) ? '' : 'id="intro_code" name="intro_code"' ?> required class="form-control" value="<?= isset($item) ? $item -> intro_code : '' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
							</div>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">授權碼</label>
						<div class="col-md-6">
							<div class="input-group">
							   <input type="text" id="code" name="code" class="form-control" value="<?= isset($item) ? $item -> code : '' ?>" readonly="readonly" />
							   <span class="input-group-btn">
						        <button type="button" class="btn" onclick="copyToClipboard('#code')" >複製</button>
							   </span>
							</div>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">推廣網址</label>
						<div class="col-md-6">
							<div class="input-group">
							   <input type="text" id="code_url" required class="form-control" value="<?= isset($item) ? base_url("login/register?code=") . $item -> code : '' ?>" readonly="readonly" />
							   <span class="input-group-btn">
						        <button type="button" class="btn" onclick="copyToClipboard('#code_url')" >複製</button>
							   </span>
							</div>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">目前時效</label>
						<div class="col-md-6">
							<div class="input-group">
							   <input type="text" id="go_buy" required class="form-control" value="<?= isset($item) ?  $item -> end_time : '--' ?>" readonly="readonly" />
								 <?php
								 	$r_hours = '-';
									$r_minutes = '-';
									if(isset($item) && !empty($item -> end_time)) {
										$now = time();
										$end_time = strtotime($item -> end_time);
										if($end_time > $now) {
											$r_hours = intval(($end_time - $now)/(60 * 60));
											$r_minutes = intval(($end_time - $now)%(60 * 60) / 60);
										}
									}
							   ?>
								 <input type="text" required class="form-control" value="剩下<?= $r_hours ?>小時<?= $r_minutes ?>分" readonly="readonly" />
							</div>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">帳號</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="account" value="<?= isset($item) ? $item -> account : '' ?>" />
						</div>
					</div>
				</fieldset>

				<?php if($login_user -> role_id == 1):?>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">密碼</label>
						<div class="col-md-6">
							<input type="text" required class="form-control" name="password" value="<?= isset($item) ? $item -> password : '' ?>" />
						</div>
					</div>
				</fieldset>
				<?php endif ?>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">名稱</label>
						<div class="col-md-6">
							<input type="text" required class="form-control" name="user_name" value="<?= isset($item) ? $item -> user_name : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">Email</label>
						<div class="col-md-6">
							<input type="email" class="form-control" name="email" value="<?= isset($item) ? $item -> email : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">LINE ID</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="line_id" value="<?= isset($item) ? $item -> line_id : '' ?>" />
						</div>
					</div>
				</fieldset>

				<?php if($login_user -> role_id == 1):?>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">權限角色</label>
						<div class="col-md-6">
							<select name="role_id" id="user_role_id" class="form-control">
								<option value="0" disabled>無</option>
								<?php foreach($role_list as $each): ?>
									<option value="<?= $each -> id?>" <?= isset($item) && $item -> role_id == $each -> id ? 'selected' : '' ?> ><?=  $each -> role_name ?></option>
								<?php endforeach ?>
							</select>
						</div>
					</div>
				</fieldset>
				<?php endif ?>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">個人照</label>
						<div class="col-md-6">
							<input id="image_id" name="image_id" type="hidden" value="<?= isset($item) ? $item -> image_id : '' ?>">
							<input id="file-input" name="file" type="file" class="file-loading form-control">
						</div>
					</div>
				</fieldset>

			</form>

		</div>
		<!-- end widget content -->

	</div>
	<!-- end widget div -->

</div>
<!-- end widget -->
<style>
	.kv-file-zoom {
		display: none;
	}
</style>
<script>


	
</script>
