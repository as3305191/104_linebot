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
		<?php if(!(isset($item) && $item -> agent_lv == '2')):?>
			<?php if($item -> agent_type_id > 0):?>
				<div class="widget-toolbar pull-left">
					<a href="javascript:void(0);" id="" onclick="currentApp.doSubmit()" class="btn btn-default btn-danger">
						<i class="fa fa-save"></i>存檔
					</a>
				</div>
			<?php else: ?>
				<div class="widget-toolbar pull-left">
					<a href="javascript:void(0);" id="" onclick="currentApp.doSubmit(1)" class="btn btn-default btn-danger">
						<i class="fa fa-save"></i>存檔
					</a>
				</div>
			<?php endif ?>
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
				<input type="hidden" name="corp_id" id="corp_id" value="<?= isset($item) ? $item -> corp_id : '' ?>" />


				<?php if($item -> agent_type_id == 0): ?>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">代理人</label>
						<div class="col-md-6">
							<select name="agent_lv" onchange="" class="form-control">
								<option value="0" <?= isset($item) && $item -> agent_lv == '0' ? 'selected' : '' ?>>否</option>
								<option value="1" <?= isset($item) && $item -> agent_lv == '1' ? 'selected' : '' ?>>是</option>
								<option value="2" disabled <?= isset($item) && $item -> agent_lv == '2' ? 'selected' : '' ?>>是（二級）</option>
							</select>
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">代理人分級</label>
						<div class="col-md-6">
							<select name="agent_type_id" onchange="" class="form-control">
								<option value="1" <?= isset($item) && $item -> agent_type_id == '1' ? 'selected' : '' ?>>A組</option>
								<option value="2" <?= isset($item) && $item -> agent_type_id == '2' ? 'selected' : '' ?>>B組</option>
								<option value="3" <?= isset($item) && $item -> agent_type_id == '3' ? 'selected' : '' ?>>C組</option>
							</select>
						</div>
					</div>
				</fieldset>
				<?php else: ?>
					<fieldset>
						<div class="form-group">
							<label class="col-md-3 control-label">代理人</label>
							<div class="col-md-6">
								<select name="agent_lv" onchange="" class="form-control" disabled readonly>
									<option value="0" <?= isset($item) && $item -> agent_lv == '0' ? 'selected' : '' ?>>否</option>
									<option value="1" <?= isset($item) && $item -> agent_lv == '1' ? 'selected' : '' ?>>是</option>
									<option value="2" disabled <?= isset($item) && $item -> agent_lv == '2' ? 'selected' : '' ?>>是（二級）</option>
								</select>
							</div>
						</div>
					</fieldset>
					<fieldset>
						<div class="form-group">
							<label class="col-md-3 control-label">代理人分級</label>
							<div class="col-md-6">
								<select name="agent_type_id" onchange="" class="form-control" disabled readonly>
									<option value="1" <?= isset($item) && $item -> agent_type_id == '1' ? 'selected' : '' ?>>A級</option>
									<option value="2" <?= isset($item) && $item -> agent_type_id == '2' ? 'selected' : '' ?>>B級</option>
									<option value="3" <?= isset($item) && $item -> agent_type_id == '3' ? 'selected' : '' ?>>C級</option>
								</select>
							</div>
						</div>
					</fieldset>


					<?php
						$bonus_min = 0;
						$bonus_max = 0;
						$win_loose_min = 0;
						$win_loose_max = 0;
						if($item -> agent_type_id == 1) { // A
							$win_loose_max = 50;
						}
						if($item -> agent_type_id == 2) { // B
							$bonus_max = 27;
						}
				 	?>

					<fieldset>
						<div class="form-group">
							<label class="col-md-3 control-label">反幣% (min:<?= $bonus_min ?> max:<?= $bonus_max ?> )</label>
							<div class="col-md-6">
								<input type="text" required class="form-control" min="<?= $bonus_min ?>" max="<?= $bonus_max ?>" name="agent_bonus" value="<?= isset($item) ? $item -> agent_bonus : '' ?>" />
							</div>
						</div>
					</fieldset>

					<fieldset>
						<div class="form-group">
							<label class="col-md-3 control-label">贈幣% (min:<?= $win_loose_min ?> max:<?= $win_loose_max ?> )</label>
							<div class="col-md-6">
								<input type="text" required class="form-control" min="<?= $win_loose_min ?>" max="<?= $win_loose_max ?>" name="agent_win_loose_bonus" value="<?= isset($item) ? $item -> agent_win_loose_bonus : '' ?>" />
							</div>
						</div>
					</fieldset>

					<fieldset>
						<div class="form-group">
							<label class="col-md-3 control-label">下線人數</label>
							<div class="col-md-6">
								<input type="text" readonly class="form-control" value="<?= count($agnet_child_list) ?>" />
							</div>
						</div>
					</fieldset>
					<fieldset>
						<div class="form-group">
							<label class="col-md-3 control-label">本月結算</label>
							<div class="col-md-6">
								<input type="text" readonly class="form-control" value="<?= $monthly_amt ?>" />
							</div>
						</div>
					</fieldset>
					<fieldset>
						<div class="form-group">
							<label class="col-md-3 control-label">年度報表</label>
							<div class="col-md-6">
								<table id="dt_list_year" class="table table-striped table-bordered table-hover" width="100%">
									<thead>
										<tr>
											<th class="min100">年月</th>
											<th class="min150">報酬</th>

										</tr>
										<tr class="search_box">
									    <th>
												<select class="form-control" id="s_y">
													<?php foreach($y_list as $y): ?>
														<option><?= $y ?></option>
													<?php endforeach ?>
												</select>
											</th>
									    <th></th>
										</tr>
									</thead>
									<tbody id="dt_list_year_body">
									</tbody>
								</table>
							</div>
						</div>
					</fieldset>



				<?php endif ?>
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

	function drawYearList(list) {
		$body = $("#dt_list_year_body").empty();
		$.each(list, function(){
			var me = this;
			var $tr = $("<tr/>").appendTo($body);
			$('<td />').append(me.ym).appendTo($tr);
			$('<td />').append(me.samt).appendTo($tr);
		});
	}

	function reloadYearList() {
		$.ajax({
			url: '<?= base_url() ?>' + 'mgmt/agent/y_list',
			type: 'POST',
			data: {
				year: $('#s_y').val(),
				user_id: $('#item_id').val(),
			},
			dataType: 'json',
			success: function(d) {
				drawYearList(d.list);
			},
			failure:function(){
				alert('faialure');
			}
		});
	}
	// do reload now
	reloadYearList();

	$('#sy').on("change", function(){
		reloadYearList();
	});
</script>
