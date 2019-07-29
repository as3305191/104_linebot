<style>
.file-drag-handle {
	display: none;
}
</style>
<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget" id="wid-id-7" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
	<header>
		<?php if($login_user -> role_id == 99): ?>
			<div class="widget-toolbar pull-left">
				<a href="javascript:void(0);" id="back_parent" onclick="currentApp.backTo()" class="btn btn-default ">
					<i class="fa fa-arrow-circle-left"></i>返回
				</a>
			</div>
		<?php endif ?>
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
				<input type="hidden" name="lang" id="lang" value="" />

				<?php if(isset($item)): ?>
					<fieldset>
						<div class="form-group">
							<label class="col-md-3 control-label">貨幣</label>
							<div class="col-md-6">
								<input type="text" required class="form-control"  value="<?= isset($item) ? $item -> currency : '' ?>" readonly />
							</div>
						</div>
					</fieldset>
				<?php endif ?>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">系統網址</label>
						<div class="col-md-6">
							<div class="input-group">
							   <input type="text" id="code_url" required class="form-control" value="<?= isset($item) ? base_url($item -> corp_code . "/login") : '' ?>" readonly="readonly" />
							   <span class="input-group-btn">
						        <button type="button" class="btn" onclick="copyToClipboard('#code_url')" >複製</button>
							   </span>
							</div>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">系統代碼</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="corp_code" value="<?= isset($item) ? $item -> corp_code : '' ?>" <?= $login_user -> role_id == 99 ? '' : 'readonly' ?> />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">公司名稱</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="corp_name" value="<?= isset($item) ? $item -> corp_name : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">公司網址</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="corp_url" value="<?= isset($item) ? $item -> corp_url : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">幣名</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="sys_name" value="<?= isset($item) ? $item -> sys_name : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">中文幣名</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="sys_name_cht" value="<?= isset($item) ? $item -> sys_name_cht : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">Slogan</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="slogan" value="<?= isset($item) ? $item -> slogan : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">宣傳影片連結</label>
						<div class="col-md-6">
							<input type="text" class="form-control"  name="video_link" value="<?= isset($item) ? $item -> video_link : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">LINE</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="line" value="<?= isset($item) ? $item -> line : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">LINE網址</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="line_url" value="<?= isset($item) ? $item -> line_url : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">微信</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="wechat" value="<?= isset($item) ? $item -> wechat : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">微信網址</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="wechat_url" value="<?= isset($item) ? $item -> wechat_url : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">狀態</label>
						<div class="col-md-6">
							<select name="status" class="form-control">
								<option value="0">正常</option>
								<option value="2" <?= isset($item) && $item -> status == 2 ? 'selected' : '' ?>>停用</option>
							</select>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">關閉經理人升級</label>
						<div class="col-md-6">
							<select name="disable_upgrade" class="form-control">
								<option value="0" <?= isset($item) && $item -> disable_upgrade == 0 ? 'selected' : '' ?>>否</option>
								<option style="color:red;" value="1" <?= isset($item) && $item -> disable_upgrade == 1 ? 'selected' : '' ?>>是</option>
							</select>
						</div>
					</div>
				</fieldset>

				<hr/>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">管理人簡訊通知電話<font color="red">(請用,號分隔)</font></label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="manager_sms" value="<?= isset($item) ? $item -> manager_sms : '' ?>" />
						</div>
					</div>
				</fieldset>

				<hr/>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">交易介紹人抽成(%)</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="intro_rate" value="<?= isset($item) ? $item -> intro_rate : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">交易公司抽成(%)</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="corp_rate" value="<?= isset($item) ? $item -> corp_rate : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">交易系統抽成(%)</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="sys_rate" value="<?= isset($item) ? $item -> sys_rate : '' ?>" />
						</div>
					</div>
				</fieldset>
				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">智慧交易使用者抽成(%)</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="smart_user_rate" value="<?= isset($item) ? $item -> smart_user_rate : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">智慧交易介紹人抽成(%)</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="smart_intro_rate" value="<?= isset($item) ? $item -> smart_intro_rate : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">智慧交易公司抽成(%)</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="smart_corp_rate" value="<?= isset($item) ? $item -> smart_corp_rate : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">智慧交易系統抽成(%)</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="smart_sys_rate" value="<?= isset($item) ? $item -> smart_sys_rate : '' ?>" />
						</div>
					</div>
				</fieldset>
				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">買價</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="price_buy" readonly value="<?= isset($item) ? $item -> price_buy : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">賣價</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="price_sell" readonly value="<?= isset($item) ? $item -> price_sell : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">均價</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="price_avg" value="<?= isset($item) ? $item -> price_avg : '' ?>" />
						</div>
					</div>
				</fieldset>

				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">每日挖礦金額</label>
						<div class="col-md-6">
							<input type="number"  class="form-control"  name="daily_dig" value="<?= isset($item) ? $item -> daily_dig : '' ?>" />
						</div>
					</div>
				</fieldset>
				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">介紹人每日點數</label>
						<div class="col-md-6">
							<input type="number" step="0.00001"  class="form-control"  name="intro_dig" value="<?= isset($item) ? $item -> intro_dig : '' ?>" />
						</div>
					</div>
				</fieldset>

				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">匯款銀行</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="bank_name" value="<?= isset($item) ? $item -> bank_name : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">匯款分行</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="branch_name" value="<?= isset($item) ? $item -> branch_name : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">匯款帳號</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="bank_account" value="<?= isset($item) ? $item -> bank_account : '' ?>" />
						</div>
					</div>
				</fieldset>

				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">綠界商店ID</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="merchant_id" value="<?= isset($item) ? $item -> merchant_id : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">HashKey</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="hash_key" value="<?= isset($item) ? $item -> hash_key : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">HashIV</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="hash_iv" value="<?= isset($item) ? $item -> hash_iv : '' ?>" />
						</div>
					</div>
				</fieldset>

				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">匯合商戶號</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="huihepay_appid" value="<?= isset($item) ? $item -> huihepay_appid : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">匯合Key</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="huihepay_key" value="<?= isset($item) ? $item -> huihepay_key : '' ?>" />
						</div>
					</div>
				</fieldset>

				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">翼支付商戶</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="best_pay_id" value="<?= isset($item) ? $item -> best_pay_id : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">翼支付Key</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="best_pay_key" value="<?= isset($item) ? $item -> best_pay_key : '' ?>" />
						</div>
					</div>
				</fieldset>

				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">三竹簡訊帳號</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="cht_sms_account" value="<?= isset($item) ? $item -> cht_sms_account : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">三竹簡訊密碼</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="cht_sms_password" value="<?= isset($item) ? $item -> cht_sms_password : '' ?>" />
						</div>
					</div>
				</fieldset>

				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">中國移動帳號</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="chs_sms_account" value="<?= isset($item) ? $item -> chs_sms_account : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">中國移動密碼</label>
						<div class="col-md-6">
							<input type="text"  class="form-control"  name="chs_sms_password" value="<?= isset($item) ? $item -> chs_sms_password : '' ?>" />
						</div>
					</div>
				</fieldset>

				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">logo圖片</label>
						<div class="col-md-6">
							<input id="logo_image_id" name="logo_image_id" type="hidden" value="<?= isset($item) ? $item -> logo_image_id : '' ?>">
							<input id="file-input-logo-image" name="file" type="file" class="file-loading form-control">
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">line logo圖片</label>
						<div class="col-md-6">
							<input id="line_logo_image_id" name="line_logo_image_id" type="hidden" value="<?= isset($item) ? $item -> line_logo_image_id : '' ?>">
							<input id="file-input-line-logo-image" name="file" type="file" class="file-loading form-control">
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">背景圖片</label>
						<div class="col-md-6">
							<input id="bg_image_id" name="bg_image_id" type="hidden" value="<?= isset($item) ? $item -> bg_image_id : '' ?>">
							<input id="file-input-bg-image" name="file" type="file" class="file-loading form-control">
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">背景影片</label>
						<div class="col-md-6">
							<input id="video_file_id" name="video_file_id" type="hidden" value="<?= isset($item) ? $item -> video_file_id : '' ?>">
							<input id="file-input-video-file" name="video_file" type="file" class="file-loading form-control">
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


	function copyToClipboard(element) {
	  var $input = $("<input>");
	  $("body").append($input);
	  $input.val($(element).val());

		if (navigator.userAgent.match(/ipad|ipod|iphone/i)) {
		  var el = $input.get(0);
		  var editable = el.contentEditable;
		  var readOnly = el.readOnly;
		  el.contentEditable = true;
		  el.readOnly = false;
		  var range = document.createRange();
		  range.selectNodeContents(el);
		  var sel = window.getSelection();
		  sel.removeAllRanges();
		  sel.addRange(range);
		  el.setSelectionRange(0, 999999);
		  el.contentEditable = editable;
		  el.readOnly = readOnly;
		} else {
		  $input.select();
		}

	  document.execCommand("copy");
	  $input.remove();

		alert('複製成功');
	}

	$("#file-input-logo-image").fileinput({
        <?php if(!empty($item -> logo_img)): ?>
        	initialPreview: [
        		'<?=  base_url('mgmt/images/get/' . $item -> logo_img -> id) ?>'
        	],
        	initialPreviewConfig: [{
        		'caption' : '<?= $item -> logo_img -> image_name ?>',
        		'size' : <?= $item -> logo_img -> image_size ?>,
        		'width' : '120px',
        		'url' : '<?= base_url('mgmt/images/delete/' . $item -> logo_img -> id)  ?>',
        		'key' : <?= $item -> logo_img -> id ?>
        	}],
        <?php else: ?>
        	initialPreview: [],
        	initialPreviewConfig: [],
        <?php endif ?>
        initialPreviewAsData: true,
        maxFileCount: 1,
        uploadUrl: 'mgmt/images/upload/logo_img',
        uploadExtraData: {
        }
    }).on('fileselect', function(event, numFiles, label) {
    	$("#file-input-logo-image").fileinput('upload');
	}).on('fileuploaded', function(event, data, previewId, index) {
	   var id = data.response.id;
		$('#logo_image_id').val(id);
	}).on('fileuploaderror', function(event, data, previewId, index) {
		alert('upload error');
	}).on('filedeleted', function(event, key) {
		$('#logo_image_id').val(0);
	});

	$("#file-input-line-logo-image").fileinput({
        <?php if(!empty($item -> line_logo_img)): ?>
        	initialPreview: [
        		'<?=  base_url('mgmt/images/get/' . $item -> line_logo_img -> id) ?>'
        	],
        	initialPreviewConfig: [{
        		'caption' : '<?= $item -> line_logo_img -> image_name ?>',
        		'size' : <?= $item -> line_logo_img -> image_size ?>,
        		'width' : '120px',
        		'url' : '<?= base_url('mgmt/images/delete/' . $item -> line_logo_img -> id)  ?>',
        		'key' : <?= $item -> line_logo_img -> id ?>
        	}],
        <?php else: ?>
        	initialPreview: [],
        	initialPreviewConfig: [],
        <?php endif ?>
        initialPreviewAsData: true,
        maxFileCount: 1,
        uploadUrl: 'mgmt/images/upload/logo_img',
        uploadExtraData: {
        }
    }).on('fileselect', function(event, numFiles, label) {
    	$("#file-input-line-logo-image").fileinput('upload');
	}).on('fileuploaded', function(event, data, previewId, index) {
	   var id = data.response.id;
		$('#line_logo_image_id').val(id);
	}).on('fileuploaderror', function(event, data, previewId, index) {
		alert('upload error');
	}).on('filedeleted', function(event, key) {
		$('#line_logo_image_id').val(0);
	});


	$("#file-input-bg-image").fileinput({
        <?php if(!empty($item -> bg_img)): ?>
        	initialPreview: [
        		'<?=  base_url('mgmt/images/get/' . $item -> bg_img -> id) ?>'
        	],
        	initialPreviewConfig: [{
        		'caption' : '<?= $item -> bg_img -> image_name ?>',
        		'size' : <?= $item -> bg_img -> image_size ?>,
        		'width' : '120px',
        		'url' : '<?= base_url('mgmt/images/delete/' . $item -> bg_img -> id)  ?>',
        		'key' : <?= $item -> bg_img -> id ?>
        	}],
        <?php else: ?>
        	initialPreview: [],
        	initialPreviewConfig: [],
        <?php endif ?>
        initialPreviewAsData: true,
        maxFileCount: 1,
        uploadUrl: 'mgmt/images/upload/bg_img',
        uploadExtraData: {
        }
    }).on('fileselect', function(event, numFiles, label) {
    	$("#file-input-bg-image").fileinput('upload');
	}).on('fileuploaded', function(event, data, previewId, index) {
	   var id = data.response.id;
		$('#bg_image_id').val(id);
	}).on('fileuploaderror', function(event, data, previewId, index) {
		alert('upload error');
	}).on('filedeleted', function(event, key) {
		$('#bg_image_id').val(0);
	});

	$("#file-input-video-file").fileinput({
        <?php if(!empty($item -> video_file_id)): ?>
        	initialPreview: [
        		'<?=  base_url('mgmt/images/get_file/' . $item -> video_file -> id) ?>'
        	],
        	initialPreviewConfig: [{
        		'type' : 'video',
        		'size' : '<?= $item -> video_file -> file_size ?>',
        		'filetype' : '<?= $item -> video_file -> mime ?>',
        		'caption' : '<?= $item -> video_file -> file_name ?>',
        		'url' : '<?= base_url('mgmt/images/delete_file/' . $item -> video_file -> id)  ?>',
        		'key' : <?= $item -> video_file -> id ?>
        	}],
        <?php else: ?>
        	initialPreview: [],
        	initialPreviewConfig: [],
        <?php endif ?>
        initialPreviewAsData: true,
				allowedFileTypes: ["video"],
        maxFileCount: 1,
        uploadUrl: 'mgmt/images/upload_file/video_file',
        uploadExtraData: {
        }
    }).on('fileselect', function(event, numFiles, label) {
    	$("#file-input-video-file").fileinput('upload');
	}).on('fileuploaded', function(event, data, previewId, index) {
	   var id = data.response.id;
		$('#video_file_id').val(id);
	}).on('fileuploaderror', function(event, data, previewId, index) {
		alert('upload error');
	}).on('filedeleted', function(event, key) {
		$('#video_file_id').val(0);
	});

</script>
