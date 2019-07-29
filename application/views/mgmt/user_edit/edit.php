<style>
.file-drag-handle {
	display: none;
}
</style>
<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget" id="wid-id-7" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
	<header>
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="back_parent" onclick="currentApp.doSubmit()" class="btn btn-default btn-danger">
				<i class="fa fa-save"></i>存檔
			</a>
		</div>
		<?php if(isset($item) && $item -> role_id == 3 && false) : ?>
			<?php if($corp -> disable_upgrade == 1): ?>
				<div class="widget-toolbar pull-left">
					<a href="javascript:void(0);" id="" class="btn btn-default btn-warning">
						<i class="fa fa-close"></i>經理人已經額滿，暫時無法升級
					</a>
				</div>
			<?php else: ?>
				<div class="widget-toolbar pull-left">
					<a href="javascript:void(0);" id="" onclick="currentApp.upgradeMe()" class="btn btn-default btn-warning">
						<i class="fa fa-shopping-cart"></i>升級經理人 $<?= number_format($config -> upgrade_amt) ?>
					</a>
				</div>
			<?php endif ?>
		<?php endif ?>
		<?php if(isset($item)): ?>
			<div class="widget-toolbar pull-left">
				目前餘額：<span style="color: red;"><?= number_format($sum_amt) ?></span>
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
				<!-- <fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">授權碼</label>
						<div class="col-md-6">
							<div class="input-group">
							   <input type="text" id="code" required class="form-control" value="<?= isset($item) ? $item -> code : '' ?>" readonly="readonly" />
							   <span class="input-group-btn">
						        <button type="button" class="btn" onclick="copyToClipboard('#code')" >複製</button>
							   </span>
							</div>
						</div>
					</div>
				</fieldset> -->
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">推廣網址</label>
						<div class="col-md-6">
							<div class="input-group">
							   <input type="text" id="code_url" required class="form-control" value="<?= isset($item) ? "http://sa888.tw/tgc/" . $corp->corp_code . "/go/#pg/register?code=" . $item -> code : '' ?>" readonly="readonly" />
							   <span class="input-group-btn">
						        <button type="button" class="btn" onclick="copyToClipboard('#code_url')" >複製</button>
							   </span>
							</div>
						</div>
					</div>
				</fieldset>

				<!-- <fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">目前時效</label>
						<div class="col-md-6">
							<div class="input-group">
							   <input type="text" id="go_buy" required class="form-control" value="<?= isset($item) ?  $item -> end_time : '--' ?>" readonly="readonly" />
							   <span class="input-group-btn">
						        <button type="button" class="btn btn-warning" onclick="goBuy()" >
											<i class="fa fa-shopping-cart"></i>
											購買
										</button>
							   </span>
							</div>
						</div>
					</div>
				</fieldset> -->

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">帳號</label>
						<div class="col-md-6">
							<input type="text" required class="form-control" readonly="readonly" disabled="disabled" name="account" value="<?= isset($item) ? $item -> account : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">密碼</label>
						<div class="col-md-6">
							<input type="text" required class="form-control" name="password" value="<?= isset($item) ? $item -> password : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">暱稱</label>
						<div class="col-md-6">
							<input type="text" required class="form-control" name="user_name" value="<?= isset($item) ? $item -> nick_name : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">Email</label>
						<div class="col-md-6">
							<input type="email" class="form-control" name="email" value="<?= isset($item) ? $item -> email : '' ?>" />
						</div>
					</div>
				</fieldset>

				<!-- <fieldset style="<?= $lang == 'cht' ? '' : 'display:none;' ?>">
					<div class="form-group">
						<label class="col-md-3 control-label">LINE ID</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="line_id" value="<?= isset($item) ? $item -> line_id : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset style="<?= $lang == 'chs' ? '' : 'display:none;' ?>">
					<div class="form-group">
						<label class="col-md-3 control-label">WECHAT ID</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="wechat_id" value="<?= isset($item) ? $item -> wechat_id : '' ?>" />
						</div>
					</div>
				</fieldset> -->

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">權限角色</label>
						<div class="col-md-6">
							<select name="role_id" id="user_role_id" class="form-control" readonly="readonly" disabled="disabled">
								<option value="0">無</option>
								<?php foreach($role_list as $each): ?>
									<option value="<?= $each -> id?>" <?= isset($item) && $item -> role_id == $each -> id ? 'selected' : '' ?> ><?=  $each -> role_name ?></option>
								<?php endforeach ?>
							</select>
						</div>
					</div>
				</fieldset>

				<!-- <fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">經理人帳號</label>
						<div class="col-md-6">
							<input type="text" class="form-control" value="<?= isset($item) ? $item -> manager_account : '' ?>" readonly="readonly" disabled="disabled" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">介紹人帳號</label>
						<div class="col-md-6">
							<input type="text" class="form-control" value="<?= isset($item) ? $item -> intro_account : '' ?>" readonly="readonly" disabled="disabled" />
						</div>
					</div>
				</fieldset> -->

				<!-- <fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">銀行</label>
						<div class="col-md-6">
							<select name="bank_id">
								<option value="" selected="" disabled="">請選擇銀行</option>
								<?php if(!empty($bank_list)) : ?>
									<?php foreach($bank_list as $each) : ?>
										<option value="<?= $each -> bank_id ?>" <?= isset($item) && $item->bank_id == $each->bank_id ? 'selected="selected"' : ''  ?> ><?= $each -> bank_name ?></option>
									<?php endforeach ?>
								<?php endif ?>
							</select>
						</div>
					</div>
				</fieldset> -->

				<!-- <fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">銀行帳號</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="bank_account" value="<?= isset($item) ? $item -> bank_account : '' ?>"  />
						</div>
					</div>
				</fieldset> -->


				<!-- <fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">個人照</label>
						<div class="col-md-6">
							<input id="image_id" name="image_id" type="hidden" value="<?= isset($item) ? $item -> image_id : '' ?>">
							<input id="file-input" name="file" type="file" class="file-loading form-control">
						</div>
					</div>
				</fieldset> -->

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

	function goBuy() {
		window.location.hash = '#mgmt/user_buy';
	}

</script>
