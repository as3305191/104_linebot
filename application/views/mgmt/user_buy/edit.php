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
				<a href="javascript:void(0);" id="back_parent" onclick="checkSubmit();" class="btn btn-default btn-danger">
					<i class="fa fa-save"></i>確定購買
				</a>
			</div>
		<?php endif ?>

		<div class="widget-toolbar pull-left">
			目前餘額：<span style="color: red;"><?= number_format($sum_amt) ?></span>
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

			<form id="app-edit-form" action="<?= base_url('mgmt/user_buy/insert_and_pay')?>" method="post" class="form-horizontal">
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
	var sumAmt = <?= !empty($sum_amt) ? $sum_amt : 0 ?>;
	var $product_list = JSON.parse('<?= json_encode($product_list) ?>');
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

	function checkSubmit() {
		var valOK = false;
		var pID = $('#product_id').val();
		$.each($product_list, function(){
			if(this.id == pID) {
				if(this.price <= sumAmt) {
					valOK = true;
				}
			}
		});

		if(valOK) {
			currentApp.doSubmit();
		} else {
			alert('餘額不足，前往購買');
			window.location.hash = '#mgmt/pay_records';
		}
	}

</script>
