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
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="back_parent" onclick="currentApp.doSubmit()" class="btn btn-default btn-danger">
				<i class="fa fa-save"></i>存檔
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
				<input type="hidden" name="id" id="item_id" value="<?= isset($item) ? $item -> id : '' ?>" />
				<input type="hidden" name="corp_id" id="corp_id" value="" />

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">礦機名稱</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="machine_name" value="<?= isset($item) ? $item -> machine_name : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">卡數</label>
						<div class="col-md-6">
							<input type="number" required class="form-control"  name="card" value="<?= isset($item) ? $item -> card : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">購買價格(NTD)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control"  name="ntd_price" value="<?= isset($item) ? $item -> ntd_price : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">每月獲利(NTD)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control"  name="ntd_reward_monthly" value="<?= isset($item) ? $item -> ntd_reward_monthly : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">天數</label>
						<div class="col-md-6">
							<input type="number" required class="form-control"  name="max_days" value="<?= isset($item) ? $item -> max_days : '' ?>" />
						</div>
					</div>
				</fieldset>


				<hr/>




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

	// set corp id
	$('#corp_id').val($("#corp_sel").val());
</script>
