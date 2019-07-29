<style>
.file-drag-handle {
	display: none;
}
</style>
<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget" id="wid-id-7" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
	<header>
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="" onclick="currentApp.doSubmit()" class="btn btn-default btn-danger">
				<i class="fa fa-save"></i>存檔
			</a>
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
				<input type="hidden" name="id" id="item_id" value="<?= isset($item) ? $item -> id : '' ?>" />

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">彩池分配-公司(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control pool" name="com_pct" value="<?= isset($item) ? $item -> com_pct : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">彩池分配-一般(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control pool" name="pool_pct" value="<?= isset($item) ? $item -> pool_pct : '' ?>" />
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
$("#app-edit-form").validate({
	// Rules for form validation
	rules : {

	},

	// Messages for form validation
	messages : {

	},

	// Ajax form submition
	submitHandler : function(form) {
		var $poolVal = 0;
		$('.pool').each(function(){
			$poolVal += parseFloat($(this).val());
		})
		var $error_msg = '';
		if($poolVal != 100) {
			$error_msg += "彩池分配總合需100<br/>";
		}

		if($error_msg.length > 0) {
			layui.layer.msg($error_msg);
			return;
		}

		$.ajax({
			type: "POST",
			url: '<?= base_url('mgmt/racing_config/insert') ?>',
			data: $("#app-edit-form").serialize(), // serializes the form's elements.
			success: function(data)
			{
					if(data.error_msg) {
						alert(data.error_msg);
					} else {
						$(window).trigger("hashchange");
					}
			}
		});
	},

	// Do not change code below
	errorPlacement : function(error, element) {
		error.insertAfter(element.parent());
	}
});
</script>
