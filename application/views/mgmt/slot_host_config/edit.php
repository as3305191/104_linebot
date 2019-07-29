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
							<input type="number" required class="form-control pool" name="pool_1_pct" value="<?= isset($item) ? $item -> pool_1_pct : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">彩池分配-一般(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control pool" name="pool_2_pct" value="<?= isset($item) ? $item -> pool_2_pct : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">freegame 彩池 1-100倍(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control pool" name="pool_3_pct" value="<?= isset($item) ? $item -> pool_3_pct : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">freegame 彩池 150-300倍(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control pool" name="pool_4_pct" value="<?= isset($item) ? $item -> pool_4_pct : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">freegame 彩池 350-500倍(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control pool" name="pool_5_pct" value="<?= isset($item) ? $item -> pool_5_pct : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">freegame 彩池 600-1000倍(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control pool" name="pool_6_pct" value="<?= isset($item) ? $item -> pool_6_pct : '' ?>" />
						</div>
					</div>
				</fieldset>

				<hr/>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">一般彩池分配-0.1倍-0.5倍(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control pool_general" name="pool_7_pct" value="<?= isset($item) ? $item -> pool_7_pct : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">一般彩池分配-0.6-1.2(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control pool_general" name="pool_8_pct" value="<?= isset($item) ? $item -> pool_8_pct : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">一般彩池分配-1.3-5倍(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control pool_general" name="pool_9_pct" value="<?= isset($item) ? $item -> pool_9_pct : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">一般彩池分配-6-30倍(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control pool_general" name="pool_10_pct" value="<?= isset($item) ? $item -> pool_10_pct : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">一般彩池分配-40-80倍(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control pool_general" name="pool_11_pct" value="<?= isset($item) ? $item -> pool_11_pct : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">一般彩池分配-100-300倍(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control pool_general" name="pool_12_pct" value="<?= isset($item) ? $item -> pool_12_pct : '' ?>" />
						</div>
					</div>
				</fieldset>


				<hr/>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">freegame 進入3倍(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control in_sp" name="sp_3_pct" value="<?= isset($item) ? $item -> sp_3_pct : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">freegame 進入6倍(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control in_sp" name="sp_6_pct" value="<?= isset($item) ? $item -> sp_6_pct : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">freegame 進入9倍(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control in_sp" name="sp_9_pct" value="<?= isset($item) ? $item -> sp_9_pct : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">freegame 進入12倍(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control in_sp" name="sp_12_pct" value="<?= isset($item) ? $item -> sp_12_pct : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">freegame 進入18倍(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control in_sp" name="sp_18_pct" value="<?= isset($item) ? $item -> sp_18_pct : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">freegame 進入24倍(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control in_sp" name="sp_24_pct" value="<?= isset($item) ? $item -> sp_24_pct : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">freegame 進入36倍(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control in_sp" name="sp_36_pct" value="<?= isset($item) ? $item -> sp_36_pct : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">freegame 進入48倍(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control in_sp" name="sp_48_pct" value="<?= isset($item) ? $item -> sp_48_pct : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">freegame 進入72倍(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control in_sp" name="sp_72_pct" value="<?= isset($item) ? $item -> sp_72_pct : '' ?>" />
						</div>
					</div>
				</fieldset>

				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">轉進free game機率(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control" max="100" name="is_sp_pct" value="<?= isset($item) ? $item -> is_sp_pct : '' ?>" />
						</div>
					</div>
				</fieldset>

				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">一般遊戲得獎機率(%)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control" max="100" name="win_pct" value="<?= isset($item) ? $item -> win_pct : '' ?>" />
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

		var $inSpVal = 0;
		$('.in_sp').each(function(){
			$inSpVal += parseFloat($(this).val());
		})
		if($inSpVal != 100) {
			$error_msg += "freegame 進入總合需100<br/>";
		}

		var $inSpVal = 0;
		$('.pool_general').each(function(){
			$inSpVal += parseFloat($(this).val());
		})
		if($inSpVal != 100) {
			$error_msg += "一般分配總合需100<br/>";
		}

		if($error_msg.length > 0) {
			layui.layer.msg($error_msg);
			return;
		}

		$.ajax({
			type: "POST",
			url: '<?= base_url('mgmt/slot_host_config/insert') ?>',
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
