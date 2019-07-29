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
				<input type="hidden" name="corp_id" id="corp_id" value="<?= isset($corp) ? $corp -> id : '' ?>" />

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">名稱</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="name" value="<?= isset($item) ? $item -> name : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">電話</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="phone" value="<?= isset($item) ? $item -> phone : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">LINE ID</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="line_id" value="<?= isset($item) ? $item -> line_id : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">轉帳末五碼</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="last_5" value="<?= isset($item) ? $item -> last_5 : '' ?>" />
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
			account: {
            validators: {
              remote: {
              	message: '已經存在',
              	url: baseUrl + 'mgmt/users/check_account/' + ($('#item_id').val().length > 0 ? $('#item_id').val() : '0')
              }
            }
         },
				 intro_code: {
           validators: {
             remote: {
             	message: '授權碼不存在',
             	url: baseUrl + 'mgmt/users/check_code/' + $('#code').val()
             }
           }
        }
      }

	})
	.bootstrapValidator('validate');

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
    }).on('fileselect', function(event, numFiles, label) {
    	$("#file-input").fileinput('upload');
	}).on('fileuploaded', function(event, data, previewId, index) {
	   var id = data.response.id;
		$('#image_id').val(id);
	}).on('fileuploaderror', function(event, data, previewId, index) {
		alert('upload error');
	}).on('filedeleted', function(event, key) {
		$('#image_id').val(0);
	});

	// select2
	$('#company_id').select2();
	$('#cooperative_id').select2();
	$('#fleet_id').select2();

	$('#group_id').select2();

	function copyToClipboard(element) {
	  var $temp = $("<input>");
	  $("body").append($temp);
	  $temp.val($(element).val()).select();
	  document.execCommand("copy");
	  $temp.remove();
	}


</script>
