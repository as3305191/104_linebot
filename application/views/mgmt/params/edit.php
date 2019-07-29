<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget" id="wid-id-7" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
	<header>
		<!-- <h2>編輯選單</h2> -->

		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="submit_btn" onclick="currentApp.doSubmit()" class="btn btn-default btn-danger">
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
				<input type="hidden" name="id" value="<?= $item -> id ?>" />
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">今日會員總贏金額</label>
						<div class="col-md-6">
							<input type="text" required class="form-control" name="total_amt_0" value="<?= isset($item) ? $item -> total_amt_0 : '' ?>" />
							~<input type="text" required class="form-control" name="total_amt_1" value="<?= isset($item) ? $item -> total_amt_1 : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">在線人數</label>
						<div class="col-md-6">
							<input type="text" required class="form-control" name="online_amt_0" value="<?= isset($item) ? $item -> online_amt_0 : '' ?>" />
							<input type="text" required class="form-control" name="online_amt_1" value="<?= isset($item) ? $item -> online_amt_1 : '' ?>" />
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
	.cke_skin_v2 input.cke_dialog_ui_input_text, .cke_skin_v2 input.cke_dialog_ui_input_password {
	    background-color: white;
	    border: none;
	    padding: 0;
	    width: 100%;
	    height: 14px;
	    /* new lines */
	    position: relative;
	    z-index: 9999;
	}

</style>
<!-- PAGE RELATED PLUGIN(S) -->
<script src="<?= base_url('js/plugin/ckeditor/ckeditor.js') ?>"></script>
<script src="<?= base_url('js/plugin/ckeditor/adapters/jquery.js') ?>"></script>

<link href="<?= base_url('js/plugin/fontawesome-iconpicker/dist/css/fontawesome-iconpicker.min.css') ?>" rel="stylesheet">
<script src="<?= base_url('js/plugin/fontawesome-iconpicker/dist/js/fontawesome-iconpicker.min.js') ?>"></script>

<script type="text/javascript">

</script>
