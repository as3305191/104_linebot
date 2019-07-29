<!-- Widget ID (each widget will need unique ID)-->
<input type="hidden" id="item_id" value="<?= isset($item) ? $item -> id : "" ?>">
<div class="jarviswidget" id="wid-id-7" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
	<header>
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="back_parent" onclick="currentApp.backTo()" class="btn btn-default ">
				<i class="fa fa-arrow-circle-left"></i>返回
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
				<input type="hidden" name="id" value="<?= $id ?>" />
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">乘客手機</label>
						<div class="col-md-6">
							<input type="text" required class="form-control" readonly value="<?= isset($item) ? $item -> member_mobile : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">乘客名稱</label>
						<div class="col-md-6">
							<input type="text" required class="form-control" readonly value="<?= isset($item) ? $item -> member_name : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">司機手機</label>
						<div class="col-md-6">
							<input type="text" required class="form-control" readonly value="<?= isset($item) ? $item -> driver_mobile : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">司機名稱</label>
						<div class="col-md-6">
							<input type="text" required class="form-control" readonly value="<?= isset($item) ? $item -> driver_name : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">司機手機</label>
						<div class="col-md-6">
							<input type="text" required class="form-control" readonly value="<?= isset($item) ? $item -> driver_mobile : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">車牌</label>
						<div class="col-md-6">
							<input type="text" required class="form-control" readonly value="<?= isset($item) ? $item -> plate : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">搭乘地址</label>
						<div class="col-md-6">
							<input type="text" required class="form-control" readonly value="<?= isset($item) ? $item -> address : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">搭乘座標</label>
						<div class="col-md-6">
							<input type="text" required class="form-control" style="width:150px;" readonly value="<?= isset($item) ? $item -> start_lng : '' ?>" />
							<input type="text" required class="form-control" style="width:150px;" readonly value="<?= isset($item) ? $item -> start_lat : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">分派時間</label>
						<div class="col-md-6">
							<input type="text" required class="form-control" readonly value="<?= isset($item) ? $item -> assign_time : '' ?>" />
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
        uploadUrl: 'mgmt/images/upload/member_img',
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


	$('#app-edit-form').bootstrapValidator({
		feedbackIcons : {
			valid : 'glyphicon glyphicon-ok',
			invalid : 'glyphicon glyphicon-remove',
			validating : 'glyphicon glyphicon-refresh'
		},
		fields: {
				mobile: {
	                validators: {
	                    remote: {
	                    	message: '已經存在',
	                    	url: baseUrl + 'mgmt/members/check_mobile/' + ($('#item_id').val().length > 0 ? $('#item_id').val() : '0')
	                    }
	                }
	           }
        }
	})
	.bootstrapValidator('validate');

</script>
