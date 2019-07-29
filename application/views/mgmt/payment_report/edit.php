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
						<label class="col-md-3 control-label">商品</label>
						<div class="col-md-6">
							<select name="product_id" id="product_id" class="form-control" <?= isset($item) ? 'disabled="disabled"' : '' ?>>
								<?php foreach($product_list as $each): ?>
									<option value="<?= $each -> id?>" <?= isset($item) && $item -> product_id == $each -> id ? 'selected' : '' ?> ><?=  $each -> product_name . "(" . $each -> price . ")" ?></option>
								<?php endforeach ?>
							</select>
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
						<label class="col-md-3 control-label">繳款代碼</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="payment_no" value="<?= isset($item) ? $item -> payment_no : '' ?>" readonly="readonly" />
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
			account: {
            validators: {
              remote: {
              	message: '已經存在',
              	url: baseUrl + 'mgmt/users/check_account/' + ($('#item_id').val().length > 0 ? $('#item_id').val() : '0')
              }
            }
         }
      }
	})
	.bootstrapValidator('validate');



</script>
