<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget" id="wid-id-7" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
	<header>
		<h2>編輯個人資料</h2>
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
						<label class="col-md-3 control-label">title</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="title" value="<?= isset($item) ? $item -> title : '' ?>" />
						</div>
					</div>
				</fieldset>
				
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">個人照</label>
						<div class="col-md-6">
							<input id="image_id" name="image_id" type="hidden" value="<?= isset($item) ? $item -> image_id : '' ?>">
							<input id="file-input" name="file" type="file" class="file-loading form-control">
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
	$("#file-input").fileinput({
        <?php if(!empty($item -> img)): ?>
        	initialPreview: [
        		'<?=  base_url('mgmt/images/get/' . $item -> img -> id) ?>'
        	],
        	initialPreviewConfig: [{
        		'caption' : '<?= $item -> img -> image_name ?>', 
        		'size' : <?= $item -> img -> image_size ?>, 
        		'width' : '120px', 
        		'url' : '<?= base_url('mgmt/images/delete/' . $item -> img -> id)  ?>', 
        		'key' : <?= $item -> img -> id ?>
        	}],
        <?php else: ?>
        	initialPreview: [],
        	initialPreviewConfig: [],
        <?php endif ?>
        initialPreviewAsData: true,
        maxFileCount: 1,
        uploadUrl: 'mgmt/images/upload/user_img',
        uploadExtraData: {
        }
    }).on('fileuploaded', function(event, data, previewId, index) {
	   var id = data.response.id;
		$('#image_id').val(id);
	}).on('fileuploaderror', function(event, data, previewId, index) {
		alert('upload error');		
	}).on('filedeleted', function(event, key) {
		$('#image_id').val(0);
	});
    
</script>