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
		<?php if($login_user -> role_id == 99 || $login_user -> role_id == 1): ?>
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
				<input type="hidden" name="corp_id" id="m_corp_id" value="" />

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">專案名稱</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="ps_name" value="<?= isset($item) ? $item -> ps_name : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">結束時間</label>
						<div class="col-md-6">
							<input type="text" required class="form-control dt_picker"  name="dead_line" value="<?= isset($item) ? $item -> dead_line : date('Y-m-d') ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">優惠紅利%</label>
						<div class="col-md-6">
							<input type="number" required class="form-control"  name="bonus_percent" value="<?= isset($item) ? $item -> bonus_percent : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">投注倍數</label>
						<div class="col-md-6">
							<input type="number" required class="form-control"  name="wash_times" value="<?= isset($item) ? $item -> wash_times : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">購買量(最低 -> 最高)</label>
						<div class="col-md-3">
							<input type="number" class="form-control"  name="min_pay" value="<?= isset($item) ? $item -> min_pay : '0' ?>" />
						</div>
						<div class="col-md-3">
							<input type="number" class="form-control"  name="max_pay" value="<?= isset($item) ? $item -> max_pay : '0' ?>" />
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

<!-- Widget ID (each widget will need unique ID)-->

<style>
	.kv-file-zoom {
		display: none;
	}
</style>
<script>
	$(".dt_picker").datetimepicker({
		format : 'YYYY-MM-DD'
	}).on('dp.change',function(event){

	});

	$('#app-edit-form').bootstrapValidator({
		feedbackIcons : {
			valid : 'glyphicon glyphicon-ok',
			invalid : 'glyphicon glyphicon-remove',
			validating : 'glyphicon glyphicon-refresh'
		},
		fields: {

			}
		})
		.bootstrapValidator('validate');

</script>
