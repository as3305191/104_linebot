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
			<a href="javascript:void(0);" onclick="currentApp.doSubmit()" class="btn btn-default btn-danger">
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
				<input type="hidden" name="item_id" value="<?= !empty($item) ? $item -> id : 0 ?>" />
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">序號</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="sn" value="<?= !empty($item) ? $item -> sn : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">獎品名稱</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="lottery_name" value="<?= !empty($item) ? $item -> lottery_name : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">價格(金幣)</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="price" value="<?= !empty($item) ? $item -> price : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">自動替補(只會有一個)</label>
						<div class="col-md-6" >
							<select name="is_basic"  style="width: 100%">
									<option value="0" <?= !empty($item) ? ($item -> is_basic == 0 ? 'selected' : '') : '' ?>>否</option>
									<option value="1" <?= !empty($item) ? ($item -> is_basic == 1 ? 'selected' : '') : '' ?>>是</option>
							</select>
						</div>
					</div>
				</fieldset>
				<fieldset>
	        <div class="form-group">
						<label class="col-md-3 control-label">上傳獎品照片</label>
						<div class="col-md-6" >
		          <input id="image_id" name="image_id" type="hidden" value="<?= isset($item) ? $item -> image_id : '' ?>">
						  <img id="file-input-img" src="<?= isset($item) ? base_url("mgmt/images/get/{$item->image_id}") : '' ?>" style="max-width:80%;position: relative;z-index: 100;<?= isset($item) && !empty($item -> image_id) ? "" : 'display:none;' ?>" />
		          <input id="uid1-input" name="file" type="file" class="form-control" >
		          <div id="file-input-progress" class="progress" style="display:none">
		          <div class="progress-bar progress-bar-success"></div>
						 </div>
		        </div>
					</div>
				</fieldset>
				<fieldset>
	        <div class="form-group">
						<label class="col-md-3 control-label">上傳得獎照片</label>
						<div class="col-md-6" >
		          <input id="win_image_id" name="win_image_id" type="hidden" value="<?= isset($item) ? $item -> win_image_id : '' ?>">
						  <img id="file-input-win-img" src="<?= isset($item) ? base_url("mgmt/images/get/{$item->win_image_id}") : '' ?>" style="max-width:80%;position: relative;z-index: 100;<?= isset($item) && !empty($item -> win_image_id) ? "" : 'display:none;' ?>" />
		          <input id="win-img-input" name="file" type="file" class="form-control" >
		          <div id="file-input-progress-win-img" class="progress" style="display:none">
		          <div class="progress-bar progress-bar-success"></div>
						 </div>
		        </div>
					</div>
				</fieldset>

				<hr/>




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
var baseUrl = '<?=base_url()?>';

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


      $('#uid1-input').fileupload({

            url:'<?= base_url('mgmt/images/upload/user_img') ?>',
            dataType: 'json',
            done: function (e, data) {
                $('#file-input-img').prop('src', data.result.initialPreview[0]).show();
                $('#image_id').val(data.result.id).attr('uid', data.result.id);
                $('#file-input-progress').hide();
            },
            progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#file-input-progress').show();
                    $('#file-input-progress .progress-bar').show().css(
                      'width',
                      progress + '%'
                    );
            },
            success: function(data)
           {

           }
         }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');

      $('#win-img-input').fileupload({

            url:'<?= base_url('mgmt/images/upload/user_img') ?>',
            dataType: 'json',
            done: function (e, data) {
                $('#file-input-win-img').prop('src', data.result.initialPreview[0]).show();
                $('#win_image_id').val(data.result.id).attr('uid', data.result.id);
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
            success: function(data)
           {

           }
         }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');

</script>
