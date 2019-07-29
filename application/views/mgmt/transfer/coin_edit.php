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
		<?php if($item -> status == 0): ?>
			<?php if($item -> type == 1): // 接收 ?>
				<div class="widget-toolbar pull-left">
					<a href="javascript:void(0);" id="back_parent" onclick="currentApp.doSubmit(1, <?= $item -> type ?>)" class="btn btn-default btn-danger">
						<i class="fa fa-save"></i>審核通過
					</a>
				</div>
			<?php endif ?>
			<?php if($item -> type == 2): // 匯出 ?>
				<div class="widget-toolbar pull-left">
					<a href="javascript:void(0);" id="back_parent" onclick="currentApp.doSubmit(1, <?= $item -> type ?>)" class="btn btn-default btn-danger">
						<i class="fa fa-save"></i>審核通過
					</a>
				</div>
			<?php endif ?>
			<div class="widget-toolbar pull-left">
				<a href="javascript:void(0);" id="back_parent" onclick="currentApp.doSubmit(-1, <?= $item -> type ?>)" class="btn btn-default btn-info">
					<i class="fa fa-save"></i>審核不通過
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

			<form id="app-edit-form" action="<?= base_url('mgmt/pay_records/insert_and_pay')?>" method="post" class="form-horizontal">
				<input type="hidden" name="id" id="item_id" value="<?= isset($item) ? $item -> id : '' ?>" />
				<?php if(isset($item)): ?>
					<fieldset>
						<div class="form-group">
							<label class="col-md-3 control-label">序號</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="sn" value="<?= isset($item) ? $item -> sn : '' ?>" readonly="readonly" />
							</div>
						</div>
					</fieldset>
				<?php endif ?>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">轉出帳號</label>
						<div class="col-md-6">
							<input type="text" class="form-control" id="out_account" name="out_account" value="<?= isset($item) ? $item -> out_account : '' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">轉出錢包</label>
						<div class="col-md-6">
							<input type="text" class="form-control" id="out_code" name="out_code" value="<?= isset($item) ? $item -> out_code : '' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">轉入帳號</label>
						<div class="col-md-6">
							<input type="hidden" id="in_user_id" name="in_user_id" value="<?= isset($item) ? $item -> in_user_id : '' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
							<input type="text" class="form-control" name="in_account" value="<?= isset($item) ? $item -> in_account : '' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">轉入錢包</label>
						<div class="col-md-6">
							<input type="text" class="form-control" id="in_code" name="in_code" value="<?= isset($item) ? $item -> in_code : '' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">貨幣</label>
						<div class="col-md-6">
							<input type="text" class="form-control"  value="<?= isset($item) ? $item -> currency_name : '0' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">數量</label>
						<div class="col-md-6">
							<input type="number" step="0.1" class="form-control" id="amt" name="amt" value="<?= isset($item) ? $item -> amt : '0' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
						</div>
					</div>
				</fieldset>

				<?php if(isset($item)):?>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">狀態</label>
						<div class="col-md-6">
							<input type="text" class="form-control" value="<?= isset($item) ? $item -> status_name : '' ?>" readonly="readonly" />
						</div>
					</div>
				</fieldset>
				<?php endif ?>
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

	$('#amt').on('change keyup', function(){

	});
</script>
