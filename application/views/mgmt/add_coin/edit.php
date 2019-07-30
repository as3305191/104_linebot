<style>
.file-drag-handle {
	display: none;
}
</style>
<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget" id="wid-id-7" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
	<header>
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="back_parent" onclick="save_coin()" class="btn btn-default btn-danger">
				<i class="fa fa-save"></i>存檔
			</a>
		</div>
		<!-- <?php if(isset($item) && $item -> role_id == 3 && false) : ?>
			<?php if($corp -> disable_upgrade == 1): ?>
				<div class="widget-toolbar pull-left">
					<a href="javascript:void(0);" id="" class="btn btn-default btn-warning">
						<i class="fa fa-close"></i>經理人已經額滿，暫時無法升級
					</a>
				</div>
			<?php else: ?>
				<div class="widget-toolbar pull-left">
					<a href="javascript:void(0);" id="" onclick="currentApp.upgradeMe()" class="btn btn-default btn-warning">
						<i class="fa fa-shopping-cart"></i>升級經理人 $<?= number_format($config -> upgrade_amt) ?>
					</a>
				</div>
			<?php endif ?>
		<?php endif ?>
		<?php if(isset($item)): ?>
			<div class="widget-toolbar pull-left">
				目前餘額：<span style="color: red;"><?= number_format($sum_amt) ?></span>
			</div>
		<?php endif ?> -->
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

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">貨幣*</label>
						<div class="col-md-6">
							<input type="text" id="point"  class="form-control"  value="" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">台幣*</label>
						<div class="col-md-6">
							<input type="text" id="ntd"  class="form-control" value="" />
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


	function save_coin() {
			var url = '<?= base_url() ?>' + 'mgmt/add_coin/insert';

			$.ajax({
				url : url,
				type: 'POST',
				data: {
					point: $('#point').val(),
					ntd: $('#ntd').val()

				},
				dataType: 'json',
				success: function(d) {
					location.reload();
				},
				failure:function(){
					alert('faialure');
				}
			});
		}


</script>
