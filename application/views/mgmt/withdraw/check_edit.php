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

		<?php if($item -> status == 0 && ($sum_amt - $item -> amt) >= 0):?>
			<div class="widget-toolbar pull-left">
				<a href="javascript:void(0);" id="" onclick="currentApp.doSubmit(1)" class="btn btn-default btn-primary">
					<i class="fa fa-check-square-o"></i>處理中
				</a>
			</div>
		<?php endif ?>
		<?php if(($item -> status == 0 || $item -> status == 1)):?>
			<div class="widget-toolbar pull-left">
				<a href="javascript:void(0);" id="" onclick="currentApp.doSubmit(2)" class="btn btn-default btn-info">
					<i class="fa fa-check-square"></i>已完成
				</a>
			</div>
		<?php endif ?>
		<?php if($item -> status == 0 || $item -> status == 1):?>
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="" onclick="doCancel()" class="btn btn-default btn-danger">
				<i class="fa fa-cross"></i>取消
			</a>
		</div>
		<?php endif ?>
		<div class="widget-toolbar pull-left">
			目前餘額：<span style="color: red;"><?= number_format($sum_amt) ?></span>
		</div>
		<div class="widget-toolbar pull-left">
			提款手續費：<span style="color: red;"><?= $config -> withdraw_percent ?>％</span>
			轉帳費用：<span style="color: red;"><?= $config -> withdraw_amt ?></span>
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
					<fieldset>
						<div class="form-group">
							<label class="col-md-3 control-label">提款人帳號</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="user_account" value="<?= isset($item) ? $item -> user_account : '' ?>" readonly="readonly" />
							</div>
						</div>
					</fieldset>
				<?php endif ?>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">提領點數</label>
						<div class="col-md-6">
							<input type="number"
							 class="form-control" id="amt" max="" name="amt" value="<?= isset($item) ? $item -> amt : '0' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">提款手續費%</label>
						<div class="col-md-6">
							<input type="number" class="form-control" id="ope_percent" name="ope_percent" value="<?= isset($item) ? $item -> ope_percent : $config -> withdraw_percent ?>" readonly="readonly" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">提款手續費金額</label>
						<div class="col-md-6">
							<input type="number" class="form-control" id="ope_amt" name="ope_amt" value="<?= isset($item) ? $item -> ope_amt : '0' ?>" readonly="readonly" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">匯款費用</label>
						<div class="col-md-6">
							<input type="number" class="form-control" id="transfer_amt" name="transfer_amt" value="<?= isset($item) ? $item -> transfer_amt : $config -> withdraw_amt ?>" readonly="readonly" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">實際提領金額</label>
						<div class="col-md-6">
							<input type="number" class="form-control" min="1" id="result_amt" name="result_amt" value="<?= isset($item) ? $item -> result_amt : '0' ?>" readonly="readonly" />
						</div>
					</div>
				</fieldset>
				<?php if(($item -> status == 0 || $item -> status == 1) && ($sum_amt - $item -> amt) >= 0): ?>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">剩餘點數</label>
						<div class="col-md-6">
							<input type="number" class="form-control" min="0" id="remaining_amt" name="remaining_amt" value="<?= isset($item) ? floor($sum_amt - $item -> amt) : '0' ?>" readonly="readonly" />
						</div>
					</div>
				</fieldset>
				<?php endif ?>
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

				<?php if(isset($item)):?>
				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">銀行封面</label>
						<div class="col-md-6">
							<?php if(!empty($user -> bank_image_id)):?>
								<img src="<?=  base_url('mgmt/images/get/' . $user -> bank_image_id) ?>" width="80%" />
							<?php else: ?>
								<p class="form-control-static" style="color:red;">尚未上傳</p>
							<?php endif ?>
						</div>
					</div>
				</fieldset>

				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">銀行</label>
						<div class="col-md-6">
							<input type="text" class="form-control" value="<?= isset($item) ? $item -> bank_name : '' ?>" readonly="readonly" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">銀行代碼</label>
						<div class="col-md-6">
							<input type="text" class="form-control" value="<?= isset($item) ? $item -> bank_id : '' ?>" readonly="readonly" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">銀行帳號</label>
						<div class="col-md-6">
							<input type="text" class="form-control" value="<?= isset($item) ? $item -> bank_account : '' ?>" readonly="readonly" />
						</div>
					</div>
				</fieldset>


				<?php endif ?>
				<input type="hidden" id="status" name="status" value="" />
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
		var $val = parseFloat($(this).val());
		var $t_amt = parseFloat($('#transfer_amt').val());
		var $ope_percent = parseFloat($("#ope_percent").val());
		var $ope_amt = parseInt(($val * $ope_percent / 100.0));
		var $rAmt = parseInt($val - ($val * $ope_percent / 100.0) - $t_amt);
		$('#ope_amt').val($ope_amt);
		$('#result_amt').val($rAmt);

		$('#app-edit-form')
            .bootstrapValidator('revalidateField', 'result_amt');
	});

	function doCancel() {
		$('#status').val(-1);
		var url = baseUrl + currentApp.basePath + 'insert'; // the script where you handle the form input.
		$.ajax({
			type : "POST",
			url : url,
			data : $("#app-edit-form").serialize(),
			success : function(data) {
				currentApp.mDtTable.ajax.reload(null, false);
				currentApp.backTo();
			}
		});
	}
</script>
