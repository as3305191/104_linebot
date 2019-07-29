<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget" id="wid-id-7" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
	<header>
		<h2>編輯選單</h2>
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
				<input type="hidden" name="id" value="<?= $id ?>" />
				
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">Icon</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="icon" value="<?= isset($item) ? $item -> icon : '' ?>" />
						</div>
					</div>
				</fieldset>
				
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">NavName</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="nav_name" value="<?= isset($item) ? $item -> nav_name : '' ?>" />
						</div>
					</div>
				</fieldset>
				
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">Key</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="key" value="<?= isset($item) ? $item -> key : '' ?>" />
						</div>
					</div>
				</fieldset>
				
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">BasePath</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="base_path" value="<?= isset($item) ? $item -> base_path : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">Order</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="pos" value="<?= isset($item) ? $item -> pos : '' ?>" />
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
<link href="<?= base_url('js/plugin/fontawesome-iconpicker/dist/css/fontawesome-iconpicker.min.css') ?>" rel="stylesheet">
<script src="<?= base_url('js/plugin/fontawesome-iconpicker/dist/js/fontawesome-iconpicker.min.js') ?>"></script>
<style>
	
</style>
<script>
	$('input[name="icon"]').iconpicker();
    
</script>