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
						<div class="col-md-12">
							<textarea id="val" name="val"><?= $val ?></textarea>
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
// ckeditor
var config = {
		customConfig : '',
		toolbarCanCollapse : false,
		colorButton_enableMore : false,
		removePlugins : 'list,indent,enterkey,showblocks,stylescombo,styles',
		extraPlugins : 'imagemaps,autogrow,uploadimage',
		filebrowserUploadUrl:baseUrl + 'mgmt/images/upload_terms/dm_image',
		autoGrow_onStartup : true,
		height:400,

		allowedContent: true
	}
	config.removeButtons = 'Save,NewPage,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Find,SelectAll,Scayt,About';

CKEDITOR.replace( "val", config);
CKEDITOR.instances['val'].on('change', function() { CKEDITOR.instances['val'].updateElement() });

// CKEditors
// $('#desc').ckeditor(config).editor.on('dialogShow',function(event){
// 	manualApp.imgDialog = event.data;
// });

function callbackImgUrl($imageUrl){
	//manualApp.imgDialog.setValueOf( 'info', 'txtUrl', $imageUrl );
}

</script>
