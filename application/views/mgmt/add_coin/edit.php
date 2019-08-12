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
				<i class="fa fa-save"></i>新增
			</a>
		</div>
		<div class="widget-toolbar pull-left">
			貨幣：<span id="current_point"><?= $add_coin_daily -> current_point ?></span>
		</div>
		<div class="widget-toolbar pull-left">
			台幣：<span id="current_ntd"><?= $add_coin_daily -> current_ntd ?></span>
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
					console.log(d);
					$("#current_point").text(d.current_point);
					$("#current_ntd").text(d.current_ntd);
				}
				// failure:function(){
				// 	alert('faialure');
				// }
			});
		}


</script>
