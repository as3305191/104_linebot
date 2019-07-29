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
			<?php
				$thresh = 3000000;
				if($sum_amt < $thresh): ?>
				餘額不足 <?= number_format($thresh) ?>
			<?php else: ?>
				<a href="javascript:void(0);" id="" onclick="currentApp.doSubmit()" class="btn btn-default btn-danger">
					<i class="fa fa-save"></i>存檔
				</a>
			<?php endif ?>
		</div>
		<div class="widget-toolbar pull-left">
			目前餘額：<span style="color: red;"><?= number_format($sum_amt) ?></span>
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
				<input type="hidden" name="corp_id" id="corp_id" value="<?= isset($item) ? $item -> corp_id : '' ?>" />



				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">代理人</label>
						<div class="col-md-6">
							<select name="agent_lv" onchange="">
								<option value="0" <?= isset($item) && $item -> agent_lv == '0' ? 'selected' : '' ?>>否</option>
								<option value="2"  <?= isset($item) && $item -> agent_lv == '2' ? 'selected' : '' ?>>是</option>
							</select>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">拆成百分比</label>
						<div class="col-md-6">
							<input type="number" required class="form-control" min="0" max="15" name="agent_pct" value="<?= isset($item) ? $item -> agent_pct : '' ?>" />
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
									+ '/' + ($('#corp_id').val().length > 0 ? $('#corp_id').val() : '0')
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

	$(".dt_picker").datetimepicker({
		format : 'YYYY-MM-DD'
	}).on('dp.change',function(event){

	});


	function doPay() {
		if(confirm('是否確認？')) {
			$.ajax({
				url: '<?= base_url() ?>' + 'mgmt/pay_records/sys_insert',
				type: 'POST',
				data: {
					user_id: $('#item_id').val(),
					amt: $('#pay_amt').val()
				},
				dataType: 'json',
				success: function(d) {
					alert('新增成功，等待審核確認中...');
					currentApp.doEdit($('#item_id').val());
				},
				failure:function(){
					alert('faialure');
				}
			});
		}
	}
	function doPayBtc() {
		if(confirm('是否購買BTC？')) {
			$.ajax({
				url: '<?= base_url() ?>' + 'mgmt/pay_records/sys_insert_coin',
				type: 'POST',
				data: {
					user_id: $('#item_id').val(),
					type: 'btc',
					amt: $('#pay_amt_btc').val()
				},
				dataType: 'json',
				success: function(d) {
					alert('購買成功');
					currentApp.doEdit($('#item_id').val());
				},
				failure:function(){
					alert('faialure');
				}
			});
		}
	}
	function doPayEth() {
		if(confirm('是否購買ETH？')) {
			$.ajax({
				url: '<?= base_url() ?>' + 'mgmt/pay_records/sys_insert_coin',
				type: 'POST',
				data: {
					user_id: $('#item_id').val(),
					type: 'eth',
					amt: $('#pay_amt_eth').val()
				},
				dataType: 'json',
				success: function(d) {
					alert('購買成功');
					currentApp.doEdit($('#item_id').val());
				},
				failure:function(){
					alert('faialure');
				}
			});
		}
	}
	function doPayNtd() {
		if(confirm('是否購買NTD？')) {
			$.ajax({
				url: '<?= base_url() ?>' + 'mgmt/pay_records/sys_insert_coin',
				type: 'POST',
				data: {
					user_id: $('#item_id').val(),
					type: 'ntd',
					amt: $('#pay_amt_ntd').val()
				},
				dataType: 'json',
				success: function(d) {
					alert('購買成功');
					currentApp.doEdit($('#item_id').val());
				},
				failure:function(){
					alert('faialure');
				}
			});
		}
	}

	function doPayBdc() {
		if(confirm('是否購買藍鑽幣？')) {
			$.ajax({
				url: '<?= base_url() ?>' + 'mgmt/pay_records/sys_insert_coin',
				type: 'POST',
				data: {
					user_id: $('#item_id').val(),
					type: 'bdc',
					amt: $('#pay_amt_bdc').val()
				},
				dataType: 'json',
				success: function(d) {
					alert('購買成功');
					currentApp.doEdit($('#item_id').val());
				},
				failure:function(){
					alert('faialure');
				}
			});
		}
	}

	function doClearWash() {
		if(confirm('是否清除？')) {
			$.ajax({
				url: '<?= base_url() ?>' + 'mgmt/users/clear_wash',
				type: 'POST',
				data: {
					user_id: $('#item_id').val()
				},
				dataType: 'json',
				success: function(d) {
					alert('已完成');
					currentApp.doEdit($('#item_id').val());
				},
				failure:function(){
					alert('faialure');
				}
			});
		}
	}
</script>
