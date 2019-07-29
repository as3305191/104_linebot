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
				<a href="javascript:void(0);" id="back_parent" onclick="currentApp.doSubmit();" class="btn btn-default btn-danger">
					<i class="fa fa-save"></i>確定
				</a>
			</div>
		<?php endif ?>
		<?php if(isset($item) && $item -> is_open == 0): ?>
			<div class="widget-toolbar pull-left">
				<a href="javascript:void(0);" id="back_parent" onclick="currentApp.doOpen();" class="btn btn-default btn-danger">
					<i class="fa fa-save"></i>發放獎金
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

			<form id="app-edit-form" action="<?= base_url('mgmt/buy_products_v3/insert_and_pay')?>" method="post" class="form-horizontal">
				<input type="hidden" name="id" id="item_id" value="<?= isset($item) ? $item -> id : '' ?>" />
				<?php if(isset($item)): ?>
					<fieldset>
						<div class="form-group">
							<label class="col-md-3 control-label">發放時間</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="" value="<?= isset($item) ? $item -> create_time : '' ?>" readonly="readonly" />
							</div>
						</div>
					</fieldset>
				<?php endif ?>


				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">序號</label>
						<div class="col-md-6">
							<input type="text" name="sn" class="form-control" readonly value="<?= isset($item) ? $item -> sn : '' ?>" />
						</div>
					</div>
				</fieldset>

				<?php if(isset($item)): ?>
					<fieldset>
						<div class="form-group">
							<label class="col-md-3 control-label">價格</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="" value="<?= isset($item) ? $item -> price : '' ?>" readonly="readonly" />
							</div>
						</div>
					</fieldset>
				<?php endif ?>
				<?php if(isset($item)): ?>
					<fieldset>
						<div class="form-group">
							<label class="col-md-3 control-label">累積金額</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="" value="<?= isset($current_sum) ? $current_sum : '' ?>" readonly="readonly" />
							</div>
						</div>
					</fieldset>
				<?php endif ?>
			</form>

			<?php if(isset($item) && $item -> is_open == 1): ?>
			<table id="dt_list" class="table table-striped table-bordered table-hover" width="100%">
				<thead>
					<tr>
						<th class="min200">使用者</th>
						<th class="min100">發放金額</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($brd_list as $each): ?>
						<tr>
							<td><?= $each -> user_account ?></td>
							<td><?= $each -> reward_amt ?></td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
			<? endif ?>
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

</script>
