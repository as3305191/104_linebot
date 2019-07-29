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
		<?php if(isset($item)): ?>
			<?php if($item -> status == 0): ?>
			<div class="widget-toolbar pull-left">
				<a href="javascript:void(0);" id="back_parent" onclick="doStatus(1)" class="btn btn-default btn-danger">
					<i class="fa fa-save"></i>處理中
				</a>
			</div>
			<?php endif ?>
			<?php if($item -> status == 1): ?>
			<div class="widget-toolbar pull-left">
				<a href="javascript:void(0);" id="back_parent" onclick="doStatus(2)" class="btn btn-default btn-danger">
					<i class="fa fa-save"></i>已完成
				</a>
			</div>
			<?php endif ?>
			<?php if($item -> status != -1): ?>
			<div class="widget-toolbar pull-left">
				<a href="javascript:void(0);" id="back_parent" onclick="doStatus(-1)" class="btn btn-default btn-default">
					<i class="fa fa-close"></i>取消
				</a>
			</div>
			<?php endif ?>
		<?php endif ?>

		<div class="widget-toolbar pull-left">
			<?= $item -> user_account ?> DBC：<span style="color: red;"><?= sp_color(number_format($sum_amt, 8)) ?></span>
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

			<form id="app-edit-form" action="" method="post" class="form-horizontal">
				<input type="hidden" name="id" id="item_id" value="<?= isset($item) ? $item -> id : '' ?>" />
				<input type="hidden" name="pay_order_cate_main_id" id="pay_order_cate_main_id" value="<?= $main_id ?>" />
				<input type="hidden" name="pay_order_cate_sub_id" id="pay_order_cate_sub_id" value="<?= $sub_id ?>" />
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

				<?php if(
						$sub_id == 1
					|| $sub_id == 2
					|| $sub_id == 3
					|| $sub_id == 4
						):?>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">手機</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="mobile" value="<?= isset($item) ? $item -> mobile : '' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">身分證</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="uid" value="<?= isset($item) ? $item -> uid : '' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
						</div>
					</div>
				</fieldset>

				<?php endif ?>

				<?php if(
						$sub_id == 5

						):?>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">車牌</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="plate_no" value="<?= isset($item) ? $item -> plate_no : '' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">身分證</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="uid" value="<?= isset($item) ? $item -> uid : '' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
						</div>
					</div>
				</fieldset>


				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">儲值金額</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="amt_ntd_deposit" value="<?= isset($item) ? $item -> amt_ntd_deposit : '' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
						</div>
					</div>
				</fieldset>

				<?php endif ?>

				<?php if(
						$sub_id == 6
						):?>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">代收期限</label>
						<div class="col-md-6">
							<select name="due_y">
							<?php for($i = 106; $i < 120; $i++): ?>
									<option value="<?= $i ?>" <?= isset($item) && $item -> due_y == $i ? 'selected="selected"' : '' ?>><?= $i ?></option>
							<?php endfor ?>
							</select>
							<select name="due_m">
							<?php for($i = 1; $i <= 12; $i++): ?>
									<option value="<?= $i ?>" <?= isset($item) && $item -> due_m == $i ? 'selected="selected"' : '' ?>><?= $i ?></option>
							<?php endfor ?>
							</select>
							<select name="due_d">
							<?php for($i = 1; $i <= 31; $i++): ?>
									<option value="<?= $i ?>" <?= isset($item) && $item -> due_d == $i ? 'selected="selected"' : '' ?>><?= $i ?></option>
							<?php endfor ?>
							</select>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">銷帳編號</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="serial" value="<?= isset($item) ? $item -> serial : '' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">查核碼</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="check" value="<?= isset($item) ? $item -> check : '' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
						</div>
					</div>
				</fieldset>

				<?php endif ?>
				<?php if(
						$sub_id == 7
						):?>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">代收期限</label>
						<div class="col-md-6">
							<select name="due_y">
							<?php for($i = 106; $i < 120; $i++): ?>
									<option value="<?= $i ?>" <?= isset($item) && $item -> due_y == $i ? 'selected="selected"' : '' ?>><?= $i ?></option>
							<?php endfor ?>
							</select>
							<select name="due_m">
							<?php for($i = 1; $i <= 12; $i++): ?>
									<option value="<?= $i ?>" <?= isset($item) && $item -> due_m == $i ? 'selected="selected"' : '' ?>><?= $i ?></option>
							<?php endfor ?>
							</select>
							<select name="due_d">
							<?php for($i = 1; $i <= 31; $i++): ?>
									<option value="<?= $i ?>" <?= isset($item) && $item -> due_d == $i ? 'selected="selected"' : '' ?>><?= $i ?></option>
							<?php endfor ?>
							</select>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">電號</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="serial" value="<?= isset($item) ? $item -> serial : '' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">查核碼</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="check" value="<?= isset($item) ? $item -> check : '' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">繳款金額</label>
						<div class="col-md-6">
							<input type="text" class="form-control" min="" name="amt_ntd_deposit" value="<?= isset($item) ? $item -> amt_ntd_deposit : '' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
						</div>
					</div>
				</fieldset>

				<?php endif ?>

				<?php if(
						$sub_id == 8
						):?>


				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">核銷帳號</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="serial" value="<?= isset($item) ? $item -> serial : '' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">身分證或統編</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="uid" value="<?= isset($item) ? $item -> uid : '' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
						</div>
					</div>
				</fieldset>

				<?php endif ?>

				<?php if(
						$sub_id == 9
						):?>


						<fieldset>
							<div class="form-group">
								<label class="col-md-3 control-label">捐款金額</label>
								<div class="col-md-6">
									<input type="text" class="form-control" min="" name="amt_ntd_deposit" value="<?= isset($item) ? $item -> amt_ntd_deposit : '' ?>" <?= isset($item) ? 'readonly="readonly"' : '' ?> />
								</div>
							</div>
						</fieldset>

				<?php endif ?>



				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">金額NTD</label>
						<div class="col-md-6">
							<input type="number" class="form-control" id="amt_ntd" name="amt_ntd" value="<?= isset($item) ? $item -> amt_ntd : '0' ?>" <?= $item -> status != 0 ? 'readonly' : '' ?>  />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">金額DBC</label>
						<div class="col-md-6">
							<input type="number" step="0.1" class="form-control" id="amt_dbc" max="<?= $sum_amt  ?>" name="amt_dbc" value="<?= isset($item) ? $item -> amt_dbc : '0' ?>" readonly />
						</div>
					</div>
				</fieldset>

				<?php if(isset($item)):?>
					<fieldset>
						<div class="form-group">
							<label class="col-md-3 control-label">狀態</label>
							<div class="col-md-6">
								<input type="text" class="form-control" value="<?= isset($item) ? $item -> pay_order_status_name : '' ?>" readonly="readonly" />
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
			in_account: {
          validators: {
						required : {
							message: '請輸入帳號'
						},
            remote: {
            	message: '帳號不存在',
            	url: '<?= base_url('mgmt/transfer_out/check_account') ?>'
            }
          }
       }
    }
	})
	.bootstrapValidator('validate');

	$('#amt_ntd').on('change keyup', function(){
		var $val = parseFloat($(this).val());
		var dbcCoinSell = <?= $dbc_coin -> sell_price_twd ?>;
		var resultVal = $val / parseFloat(dbcCoinSell) * 1.2;
		$('#amt_dbc').val(resultVal);
		$('#app-edit-form')
            .bootstrapValidator('revalidateField', 'amt_dbc');
	});

	function doVerify() {
		var url = baseUrl + currentApp.basePath + 'confirm_insert'; // the script where you handle the form input.
		$.ajax({
			type : "POST",
			url : url,
			data : {
				'id' : $('#item_id').val(),
				'code' :  $('#reg_code').val()
			},
			success : function(data) {
				if(data.error_msg) {
					alert(data.error_msg);
				} else {
					location.reload();
				}
			}
		});
	}

	function doStatus(status) {
		if(status == 1) {
				if($('#amt_ntd').val() <= 0) {
					alert('請輸入金額');
					return;
				}
				if($('#amt_dbc').val() > <?= $sum_amt ?>) {
					alert('DBC不足');
					return;
				}
		}

		var url = baseUrl + currentApp.basePath + 'do_status'; // the script where you handle the form input.
		$.ajax({
			type : "POST",
			url : url,
			data : {
				'id' : $('#item_id').val(),
				'status' :  status,
				'amt_ntd' : $('#amt_ntd').val()
			},
			success : function(data) {
				if(data.error_msg) {
					alert(data.error_msg);
				} else {
					currentApp.tableReload();
					currentApp.backTo();
				}
			}
		});
	}
</script>
