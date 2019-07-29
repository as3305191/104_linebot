<style>
	.main {
		background-color: #F0F0F0;
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
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="back_parent" onclick="currentApp.doSubmit()" class="btn btn-default btn-danger">
				<i class="fa fa-save"></i>存擋
			</a>
		</div>
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
				<input type="hidden" name="id" value="<?= isset($item) ? $item -> id : '' ?>" />
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">角色名稱</label>
						<div class="col-md-6">
							<input type="text" required class="form-control" name="role_name" value="<?= isset($item) ? $item -> role_name : '' ?>" />
						</div>
					</div>
				</fieldset>

				<div class="row">
					<table id="power-table" class="table table-hover">
						<thead>
							<tr>
								<th class="min50"><input type="checkbox" id="chk-all" /></th>
								<th class="min150">主選單</th>
								<th class="min150">次選單</th>
								<th>操作權限</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($menu_list as $each): ?>
								<tr class="main">
									<td><input name="nav_ids[]" value="<?= $each -> id ?>" <?= !empty($each -> rp) ? 'checked' : '' ?> id="mm_<?= $each -> id ?>" data-id="<?= $each -> id ?>" type="checkbox" class="menu-chk" /></td>
									<td><?= $each -> nav_name ?></td>
									<td></td>
									<td>
										<input type="hidden" name="nav_powers[]" value="0" />
									</td>
								</tr>
								<?php if(!empty($each -> sub_list)): ?>
									<?php foreach($each -> sub_list as $s_each): ?>
										<tr class="sub">
											<td><input name="nav_ids[]" value="<?= $s_each -> id ?>" <?= !empty($s_each -> rp) ? 'checked' : '' ?> type="checkbox" data-mm="<?= $each -> id ?>" class="menu-chk" /></td>
											<td></td>
											<td><?= $s_each -> nav_name ?></td>
											<td>
												<input type="hidden" name="nav_powers[]" value="0" />
											</td>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							<?php endforeach; ?>

						<tbody>
					</table>
				</div>
			</form>

		</div>
		<!-- end widget content -->

	</div>
	<!-- end widget div -->

</div>
<!-- end widget -->

<!-- PAGE RELATED PLUGIN(S) -->
<script>
$('#app-edit-form').bootstrapValidator({
	feedbackIcons : {
		valid : 'glyphicon glyphicon-ok',
		invalid : 'glyphicon glyphicon-remove',
		validating : 'glyphicon glyphicon-refresh'
	}
});

$('#chk-all').on('click', function(){
	$('#power-table .menu-chk').prop('checked', $(this).is(':checked'));
});

$('.menu-chk').on('click', function(){
	var me = $(this);
	if(me.data('mm')) {
		// sub
		$('#mm_' + me.data('mm')).prop('checked', true);
	} else {
		// main
		if(!me.is(':checked')) {
			// remove all sub
			console.log('main');
			$('#power-table .menu-chk[data-mm=' + me.data('id') + ']').prop('checked', false);
		}
	}

	isAllChecked();
});

function isAllChecked() {
	console.log('isAllChecked');
	$('#chk-all').prop('checked', $('.menu-chk:checked').length == $('.menu-chk').length);
}

isAllChecked();
</script>
