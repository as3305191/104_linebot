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
		<?php if(!isset($item)): ?>
			<div class="widget-toolbar pull-left">
				<a href="javascript:void(0);" id="back_parent" onclick="currentApp.doSubmit()" class="btn btn-default btn-danger">
					<i class="fa fa-save"></i>確定購買
				</a>
			</div>
		<?php endif ?>

		<?php if(isset($item) && $item -> status == 0): ?>
			<?php if($item -> pay_type_id == 5 || $item -> pay_type_id == 6): ?>
				<!-- <div class="widget-toolbar pull-left">
					<a target="_blank" href="javascript:void(0)" onclick="ajaxPay('<?= base_url('mgmt/pay_records/pay/' . $item -> id ) ?>')"  class="btn btn-default btn-warning">
						<i class="fa fa-save"></i>顯示QR CODE
					</a>
				</div> -->
			<?php elseif($item -> pay_type_id == 7): ?>

			<?php else: ?>
				<div class="widget-toolbar pull-left">
					<a target="_blank" href="<?= base_url('mgmt/pay_records/pay/' . $item -> id ) ?>" id="pay" class="btn btn-default btn-warning">
						<i class="fa fa-save"></i>前往付款
					</a>
				</div>
			<?php endif ?>
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
						<label class="col-md-3 control-label">金額</label>
						<div class="col-md-6">
							<input type="number" class="form-control" name="amt" value="<?= isset($item) ? $item -> amt : '' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">繳費方式</label>
						<div class="col-md-6">
							<select name="pay_type_id" id="pay_type_id" class="form-control" <?= isset($item) ? 'disabled="disabled"' : '' ?>>
								<?php foreach($pay_type_list as $each): ?>
									<option value="<?= $each -> id?>" <?= isset($item) && $item -> pay_type_id == $each -> id ? 'selected' : '' ?> ><?=  $each -> type_name ?></option>
								<?php endforeach ?>
							</select>
						</div>
					</div>
				</fieldset>

				<?php if(isset($item) && $item -> pay_type_id == 1): ?>
					<fieldset>
						<div class="form-group">
							<label class="col-md-3 control-label">銀行</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="bank_code" value="<?= isset($item) ? $item -> bank_code : '' ?>" readonly="readonly" />
							</div>
						</div>
					</fieldset>

					<fieldset>
						<div class="form-group">
							<label class="col-md-3 control-label">虛擬帳號</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="v_account" value="<?= isset($item) ? $item -> v_account : '' ?>" readonly="readonly" />
							</div>
						</div>
					</fieldset>

					<fieldset>
						<div class="form-group">
							<label class="col-md-3 control-label">ATM繳款期限</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="" value="<?= isset($item) ? $item -> expire_date : '' ?>" readonly="readonly" />
							</div>
						</div>
					</fieldset>
				<?php endif ?>

				<?php if(isset($item) && $item -> pay_type_id == 2): ?>
					<fieldset>
						<div class="form-group">
							<label class="col-md-3 control-label">便利商店繳款代碼</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="payment_no" value="<?= isset($item) ? $item -> payment_no : '' ?>" readonly="readonly" />
							</div>
						</div>
					</fieldset>

					<fieldset>
						<div class="form-group">
							<label class="col-md-3 control-label">便利商店繳款期限</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="" value="<?= isset($item) ? $item -> expire_time: '' ?>" readonly="readonly" />
							</div>
						</div>
					</fieldset>

				<?php endif ?>

				<?php if(isset($item) && $item -> pay_type_id == 7 && !empty($item -> pay_url)): ?>
					<fieldset>
						<div class="form-group">
							<label class="col-md-3 control-label">付款網址</label>
							<div class="col-md-6">
								<a class="btn btn-warning" href="<?= isset($item) ? $item -> pay_url : '' ?>" target="_blank">前往付款</a>
							</div>
						</div>
					</fieldset>

				<?php endif ?>
			</form>
			<?php if(!empty($item) && !empty($item -> qr_code)):?>
				<div id="qr_box"><a class="btn btn-danger" href="<?=  "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . $item -> qr_code ?>">顯示QR Code</a></div>
			<?php endif ?>
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


	function ajaxPay(url) {
		currentApp.waitingDialog.show();
		$.ajax({
			type: "POST",
			url: url,
			dataType: 'json',
			success: function(data)
			{
					if(data.Code == 0) {
						console.log(data.QrCode);

						$('<a class="btn btn-danger">顯示QR Code</a>')
							.attr('href', data.QrCode)
							.appendTo($('#qr_box').empty())
							;

						// $('<img>')
						// 	.attr('src', 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' + data.QrCode)
						// 	.on('load', function(){
						// 		acurrentApp.waitingDialog.hide();a
						// 	})
						// 	.appendTo($('#qr_box').empty());
					} else {
						currentApp.waitingDialog.hide();
						alert(data.Message);
					}
			},
			failure:function(){
				currentApp.waitingDialog.hide();
			}
		});
	}
</script>
