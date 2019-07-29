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
		<?php if($login_user -> role_id == 99): ?>
			<div class="widget-toolbar pull-left">
				<a href="javascript:void(0);" id="back_parent" onclick="currentApp.doSubmit()" class="btn btn-default btn-danger">
					<i class="fa fa-save"></i>存檔
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

			<form id="app-edit-form" method="post" class="form-horizontal">
				<input type="hidden" name="id" id="item_id" value="<?= isset($item) ? $item -> id : '' ?>" />
				<input type="hidden" name="corp_id" id="m_corp_id" value="" />
				<input type="hidden" name="tab_type" id="m_tab_type" value="" />
				<input type="hidden" name="type_name" id="m_bulletin_type" value="" />

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">標題</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="title" value="<?= isset($item) ? $item -> title : '' ?>" />
						</div>
					</div>
				</fieldset>

				<!-- <fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">說明文字</label>
						<div class="col-md-6" rows="10" >
							<textarea class="form-control" id="desc" name="desc"><?= isset($item) ? $item -> desc : '' ?></textarea>
						</div>
					</div>
				</fieldset> -->

				<!-- <fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">順序（小至大）</label>
						<div class="col-md-6">
							<input type="number" class="form-control"  name="pos" value="<?= isset($item) ? $item -> pos : '' ?>" />
						</div>
					</div>
				</fieldset> -->

				<!-- <fieldset>
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
				</fieldset> -->
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
<script src="<?= base_url('js/plugin/ckeditor/ckeditor.js') ?>"></script>
<script src="<?= base_url('js/plugin/ckeditor/adapters/jquery.js') ?>"></script>
<script>
// ckeditor
// var config = {
// 		customConfig : '',
// 		toolbarCanCollapse : false,
// 		colorButton_enableMore : false,
// 		removePlugins : 'list,indent,enterkey,showblocks,stylescombo,styles',
// 		extraPlugins : 'imagemaps,autogrow,uploadimage',
// 		filebrowserUploadUrl:baseUrl + 'mgmt/images/upload_terms/dm_image',
// 		autoGrow_onStartup : true,
// 		height:400,
//
// 		allowedContent: true
// 	}
// 	config.removeButtons = 'Save,NewPage,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Find,SelectAll,Scayt,About';
//
// CKEDITOR.replace( "desc", config);
// CKEDITOR.instances['desc'].on('change', function() { CKEDITOR.instances['desc'].updateElement() });

	$('#app-edit-form').bootstrapValidator({
		feedbackIcons : {
			valid : 'glyphicon glyphicon-ok',
			invalid : 'glyphicon glyphicon-remove',
			validating : 'glyphicon glyphicon-refresh'
		},
		fields: {
			corp_code: {
            validators: {
              remote: {
              	message: '已經存在',
              	url: baseUrl + 'mgmt/corp/check_corp_code/' + ($('#item_id').val().length > 0 ? $('#item_id').val() : '0')
              }
            }
         }
      }

	})
	.bootstrapValidator('validate');

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
<script>
</script>
