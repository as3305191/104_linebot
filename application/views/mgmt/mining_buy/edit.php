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
			<?php if($l_user -> is_valid_mobile == 1
			&& $l_user -> is_valid_bank == 1
			&& $l_user -> is_valid_uid == 1
			): ?>
			<div class="widget-toolbar pull-left" id="btn_buy">
				<a href="javascript:void(0);" id="back_parent" onclick="currentApp.doSubmit()" class="btn btn-default btn-danger">
					<i class="fa fa-save"></i>確定購買
				</a>
			</div>
			<?php endif ?>
		<?php endif ?>
		<div class="widget-toolbar pull-left">
			<?= $corp -> sys_name_cht ?>：<span style="color: red;"><?= number_format($sum_amt) ?></span>
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
				<?php endif ?>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">機器</label>
						<div class="col-md-6">
							<select name="mining_machine_id" onchange="getMachinePrice();" id="mining_machine_id" class="form-control" <?= isset($item) ? 'disabled="disabled"' : '' ?>>
								<?php foreach($mm_list as $each): ?>
									<option value="<?= $each -> id?>" <?= isset($item) && $item -> mining_machine_id == $each -> id ? 'selected' : '' ?> ><?=  $each -> machine_name ?></option>
								<?php endforeach ?>
							</select>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">台幣價格</label>
						<div class="col-md-6">
							<input class="form-control" name="buy_ntd_price" id="buy_ntd_price" value="<?= isset($item) ? $item -> buy_ntd_price : '' ?>" readonly="readonly" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label"><?= $corp -> sys_name ?>賣價</label>
						<div class="col-md-6">
							<input  class="form-control" name="price_sell" id="buy_dbc_avg" value="<?= isset($item) ? $item -> buy_dbc_avg : '' ?>" readonly="readonly"/>
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label"><?= $corp -> sys_name ?>價格</label>
						<div class="col-md-6">
							<input class="form-control" name="buy_dbc_amt" id="buy_dbc_amt"  value="<?= isset($item) ? $item -> buy_dbc_amt : '' ?>" readonly="readonly" />
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

	function getMachinePrice() {
		var mId = $('#mining_machine_id').val();
		$.ajax({
			type: "POST",
			url: baseUrl + currentApp.basePath + 'get_machine/',
			data: {
				machine_id : mId
			}, // serializes the form's elements.
			success: function(res)
			{
					$('#buy_ntd_price').val(res.item.ntd_price);
					$('#buy_dbc_avg').val(res.dbc_avg);
					var dbcAmt = parseFloat(res.dbc_amt);
					var sumAmt = parseFloat(res.sum_amt);
					$('#buy_dbc_amt').val(dbcAmt.toFixed(4));

					if(sumAmt >= dbcAmt) {
						$('#btn_buy').show();
					} else {
						$('#btn_buy').hide();
					}
			}
		});
	}

	getMachinePrice();
</script>
