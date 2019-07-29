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
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="back_parent" onclick="currentApp.doSubmit()" class="btn btn-default btn-danger">
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
						<label class="col-md-3 control-label">主旨（說明用，不會顯示）</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="title" value="<?= isset($item) ? $item -> title : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">內容(推播的內容)</label>
						<div class="col-md-6">
							<textarea class="form-control" id="m_content" name="content"><?= isset($item) ? $item -> content : '' ?></textarea>
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">上傳照片</label>
						<div class="col-md-6" >
							<input id="image_id" name="image_id" type="hidden" value="<?= isset($item) ? $item -> image_id : '' ?>">
							<img id="file-input-win-img" src="<?= isset($item) ? base_url("mgmt/images/get/{$item->image_id}") : '' ?>" style="max-width:80%;position: relative;z-index: 100;<?= isset($item) && !empty($item -> image_id) ? "" : 'display:none;' ?>" />
							<input id="img-input" name="file" type="file" class="form-control" >
							<div id="file-input-progress-win-img" class="progress" style="display:none">
							<div class="progress-bar progress-bar-success"></div>
						 </div>
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
		}
	})
	.bootstrapValidator('validate');
</script>

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

<script type="text/javascript">
$('#img-input').fileupload({

			url:'<?= base_url('mgmt/images/upload/user_img') ?>',
			dataType: 'json',
			done: function (e, data) {
					$('#file-input-win-img').prop('src', data.result.initialPreview[0]).show();
					$('#image_id').val(data.result.id).attr('uid', data.result.id);
					$('#file-input-progress-win-img').hide();
			},
			progressall: function (e, data) {
							var progress = parseInt(data.loaded / data.total * 100, 10);
							$('#file-input-progress-win-img').show();
							$('#file-input-progress-win-img .progress-bar').show().css(
								'width',
								progress + '%'
							);
			},
			success: function(data){

		 }
	 }).prop('disabled', !$.support.fileInput)
			.parent().addClass($.support.fileInput ? undefined : 'disabled');

</script>
