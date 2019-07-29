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
		<ul class="nav nav-tabs pull-right in" id="myTab">
			<li class="active">
				<a data-toggle="tab" href="#s1"><i class="fa fa-list-alt"></i> <span class="hidden-mobile hidden-tablet">基本資料</span></a>
			</li>

			<li>
				<a data-toggle="tab" href="#s2"><i class="fa fa-image"></i> <span class="hidden-mobile hidden-tablet">圖片</span></a>
			</li>

			<li>
				<a data-toggle="tab" href="#s3"><i class="fa fa-file-text-o"></i> <span class="hidden-mobile hidden-tablet">商品描述</span></a>
			</li>

		</ul>
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

				<div id="myTabContent" class="tab-content">
					<div class="tab-pane fade active in padding-10 no-padding-bottom" id="s1">
						<form id="app-edit-form" method="post" class="form-horizontal">
							<input type="hidden" id="product_id" name="id" value="<?= isset($item) ? $item->id : '0' ?>" />

							<style>

							</style>
							<fieldset class="product-border">
								<legend class="product-border general-product">
									<!-- 一般商品 -->
									<span class="onoffswitch hidden" >
										<input type="checkbox"  class="onoffswitch-checkbox" <?=isset($item)&&$item->is_pro==1?'checked':''?> id="is_pro" >

											<label class="onoffswitch-label" for="is_pro">
											<span class="onoffswitch-inner" data-swchon-text="專案商品" data-swchoff-text="一般商品"></span>
											<span class="onoffswitch-switch"></span>
										</label>
									</span>
									<input type="hidden" name="is_pro" value="<?=isset($item)?$item->is_pro:0?>">
								</legend>
								<fieldset>
									<fieldset>
										<div class="form-group">
											<label class="col-md-3 control-label">商品序號</label>
											<div class="col-md-6">
												<input type="text" class="form-control" required name="serial" value="<?= isset($item) ? $item -> serial : '' ?>" />
											</div>
										</div>
									</fieldset>

									<fieldset>
										<div class="form-group">
											<label class="col-md-3 control-label">商品名稱</label>
											<div class="col-md-6">
												<input type="text" class="form-control" required name="product_name" value="<?= isset($item) ? $item -> product_name : '' ?>" />
											</div>
										</div>
									</fieldset>

									<fieldset>
										<div class="form-group">
											<label class="col-md-3 control-label">商品分類</label>
											<div class="col-md-6">
												<select id="main_cate" name="product_cate[]" class="form-control select2" multiple>
													<?php foreach($main_cates as $each): ?>
														<option value="<?= $each -> id ?>" <?= isset($mul_cates) && in_array($each->id,$mul_cates)  ? 'selected="selected"' : '' ?>>
															<?= $each -> cate_name ?>
														</option>
													<?php endforeach ?>
												</select>
											</div>
										</div>
									</fieldset>

									<fieldset>
										<div class="form-group hidden">
											<label class="col-md-3 control-label">禮卷</label>
											<div class="col-md-6">
												<span class="onoffswitch" >
													<input type="checkbox"  class="onoffswitch-checkbox" <?=isset($item)&&$item->is_voucher==1?'checked':''?> id="is_voucher" >

														<label class="onoffswitch-label" for="is_voucher">
														<span class="onoffswitch-inner" data-swchon-text="是" data-swchoff-text="否"></span>
														<span class="onoffswitch-switch"></span>
													</label>
												</span>
												<input type="hidden" name="is_voucher" value="<?=isset($item)?$item->is_voucher:0?>">
											</div>
										</div>
									</fieldset>

									<fieldset>
										<div class="form-group">
											<label class="col-md-3 control-label">永久檔期</label>
											<div class="col-md-6">
												<span class="onoffswitch" >
													<input type="checkbox"  class="onoffswitch-checkbox" <?=isset($item)&&$item->ever_time==1?'checked':''?> id="ever_time" >

														<label class="onoffswitch-label" for="ever_time">
														<span class="onoffswitch-inner" data-swchon-text="開" data-swchoff-text="關"></span>
														<span class="onoffswitch-switch"></span>
													</label>
												</span>
												<input type="hidden" name="ever_time" value="<?=isset($item)?$item->ever_time:0?>">
											</div>
										</div>
									</fieldset>

									<fieldset>
										<div class="form-group">
											<label class="col-md-3 control-label">上架時間</label>
											<div class="col-md-6">
												<input id="start_time" type="text" required class="form-control dt_picker" name="start_time" value="<?= isset($item) ? $item -> start_time : '' ?>" />
											</div>
										</div>
									</fieldset>

									<fieldset>
										<div class="form-group">
											<label class="col-md-3 control-label">下架時間</label>
											<div class="col-md-6">
												<input id="end_time" type="text" class="form-control dt_picker" name="end_time" value="<?= isset($item) ? $item -> end_time : '' ?>" />
											</div>
										</div>
									</fieldset>

									<fieldset>
										<div class="form-group hidden">
											<label class="col-md-3 control-label">建議售價</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="price_origin" value="<?= isset($item) ? $item -> price_origin : '' ?>" />
											</div>
										</div>
									</fieldset>

									<fieldset>
										<div class="form-group">
											<label class="col-md-3 control-label">售價NTD</label>
											<div class="col-md-6">
												<input type="text" required class="form-control" name="price" value="<?= isset($item) ? $item -> price : '' ?>" />
											</div>
										</div>
									</fieldset>

									<fieldset>
										<div class="form-group hidden">
											<label class="col-md-3 control-label">成本</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="cost" value="<?= isset($item) ? $item -> cost : '' ?>" />
											</div>
										</div>
									</fieldset>

									<fieldset>
										<div class="form-group hidden">
											<label class="col-md-3 control-label">單筆最小訂購數量</label>
											<div class="col-md-6">
												<input type="number" min="0" max="999" class="form-control" name="num_min"  value="<?= isset($item) ? $item -> num_min : '' ?>" />
											</div>
										</div>
									</fieldset>

									<fieldset>
										<div class="form-group hidden">
											<label class="col-md-3 control-label">單筆最大訂購數量</label>
											<div class="col-md-6">
												<input type="number" min="0" max="999" class="form-control" name="num_max" value="<?= isset($item) ? $item -> num_max : '' ?>" />
											</div>
										</div>
									</fieldset>

									<fieldset>
										<div class="form-group hidden">
											<label class="col-md-3 control-label">置頂商品</label>
											<div class="col-md-6">
												<span class="onoffswitch" >
													<input type="checkbox" class="onoffswitch-checkbox" <?=isset($item)&&$item->pos==1?'checked':''?> id="pos" >

														<label class="onoffswitch-label" for="pos">
														<span class="onoffswitch-inner" data-swchon-text="開" data-swchoff-text="關"></span>
														<span class="onoffswitch-switch"></span>
													</label>
												</span>
												<input type="hidden" name="pos" value="<?= isset($item)?$item->pos:2 ?>">
											</div>
										</div>
									</fieldset>

								</fieldset>
							</fieldset>

							<!-- 專案商品 -->
							<fieldset class="product-border" id="pro-product" style="display:<?=isset($item)&&$item->is_pro == 1?'inherit':'none'?>">
								<legend class="product-border pro-product">
									商品關聯
								</legend>
								<fieldset>
								<div class="form-group">
									<div class="col-md-2">
										<button type="button" class="btn btn-sm btn-primary" onclick="callPro()"><i class="fa fa-plus-circle fa-lg"></i></button>
									</div>
								</div>
							</fieldset>


							</fieldset>

						</form>
					</div>
					<!-- end of s1 -->
					<!-- start of s2 -->
					<div class="tab-pane fade in padding-10 no-padding-bottom" id="s2">
						<div class="row">
							<div class="col-md-12">
								<input id="file-input" name="file[]" multiple="true" type="file" class="file-loading form-control">
							</div>
						</div>
					</div>
					<!-- end of s2 -->
					<!-- start of s3 -->
					<div class="tab-pane fade in padding-10 no-padding-bottom" id="s3">
						<form id="app-edit-form-s3" method="post" class="form-horizontal">
						<fieldset>
							<div class="form-group">
								<div class="col-md-12">
									<textarea id="desc" name="desc"><?= isset($item) ? $item -> desc : '' ?></textarea>
								</div>
							</div>
						</fieldset>
						</form>
					</div>
					<!-- end of s3 -->

		</div>
		<!-- end widget content -->

	</div>
	<!-- end widget div -->

</div>
<!-- end widget -->


<script>

	$('#is_pro').change(function(){
		if($(this).prop('checked')){
			$('input[name="is_pro"]').val(1);
			$('#pro-product').show();
		}else{
			$('input[name="is_pro"]').val(0);
			$('#pro-product').hide();
		}

	})

	currentApp.tableReload();
	var proStore = [];

	function redrawPro() {
		var $sBody = $('#pro_body').empty();
		$.each(proStore, function(){
			var obj = this;
			if(obj.is_delete == 1) {
				// don't display deleted
				return;
			}

			var $tr = $('<tr></tr>').appendTo($sBody);
			var $td = $('<td></td>').appendTo($tr);
			var $del = $('<a href="javascript:(0)"><i class="fa fa-trash"></i></a>').on('click', function(){
				if(obj.id > 0) {
					// mark to be deleted
					obj.is_delete = 1;
				} else {
					// just remove
					for(var i = proStore.length - 1; i >= 0; i--) {
					    if(proStore[i] === obj) {
					       proStore.splice(i, 1);
					    }
					}
				}

				redrawPro();
			}).appendTo($td);

			$('<img />').attr('src',baseUrl + 'api/images/get/' + obj.image_id + '/thumb').css('width','50px')
				.appendTo($('<td></td>').appendTo($tr));

			$('<td>'+obj.serial+'</td>').appendTo($tr);
			$('<td>'+obj.product_name+'</td>').appendTo($tr);

			$('<input type="number">').val(obj.numbers).on('change keyup', function(){
				obj.numbers = $(this).val();
			}).appendTo($('<td></td>').appendTo($tr));
		});
	}

	function callPro(){
		$('#productModal').modal('show');
	}
	redrawPro();
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

	fieldset.product-border {
		border: 1px groove #ddd !important;
		padding: 0 1.4em 1.4em 1.4em !important;
		margin: 0 0 1.5em 0 !important;
		-webkit-box-shadow:  0px 0px 0px 0px #000;
		box-shadow:  0px 0px 0px 0px #000;
	}

	legend.product-border {
		font-size: 1.2em !important;
		font-weight: bold !important;
		text-align: left !important;
		width:auto;
		padding:0 10px;
		border-bottom:none;
	}

	.general-product .onoffswitch {
 		width: 70px;
	}

	.general-product .onoffswitch-switch {
 		right: 53px;
	}

</style>
<!-- PAGE RELATED PLUGIN(S) -->
<script src="<?= base_url('js/plugin/ckeditor/ckeditor.js') ?>"></script>
<script src="<?= base_url('js/plugin/ckeditor/adapters/jquery.js') ?>"></script>

<script>
	//call bootstrapValidator when change date
	$('#off_start_time').on('dp.change dp.show',function(){
		$('#app-edit-form-s4').bootstrapValidator('revalidateField', 'off_start_time');;
	});

	$('#off_end_time').on('dp.change dp.show',function(){
		$('#app-edit-form-s4').bootstrapValidator('revalidateField', 'off_end_time');;
	});

	// ckeditor
	var config = {
		plugins:'basicstyles,sourcearea,image,button,colorbutton,colordialog,contextmenu,toolbar,font,format,wysiwygarea,justify,menubutton,link,list',
		extraPlugins : 'filebrowser,autogrow',
		filebrowserBrowseUrl: baseUrl + 'mgmt/images/browser',
		startupFocus: true,
		autoGrow_onStartup: true,
		autoGrow_minHeight: 400,
		//autoGrow_maxHeight: 800,
		removePlugins: 'resize'
	};

	// CKEditors
	$('#desc').ckeditor(config).editor.on('dialogShow',function(event){
		currentApp.imgDialog = event.data;
	});

	function callbackImgUrl($imageUrl){
		currentApp.imgDialog.setValueOf( 'info', 'txtUrl', $imageUrl );
	}

	$('#myTab a').click(function (e) {
		  e.preventDefault()
		  $(this).tab('show');
		  if($(this).attr('href') == '#s3') {
		  		setTimeout(function(){
		  			CKEDITOR.instances.desc.execCommand('autogrow');
		  		}, 500);
		  }
	})

	currentApp.clearImgs();
	$("#file-input").fileinput({
					language: "zh-TW",
        <?php if(!empty($item -> images) && count($item -> images) > 0): ?>
        	initialPreview: [
        		<?php foreach($item -> images as $img): ?>
        			'<?=  base_url('mgmt/images/get/' . $img -> id) ?>',
        		<?php endforeach ?>
        	],
        	initialPreviewConfig: [
        	<?php foreach($item -> images as $img): ?>
    		{
	        		'caption' : '<?= $img -> image_name ?>',
	        		'size' : <?= $img -> image_size ?>,
	        		'width' : '120px',
	        		'url' : '<?= base_url('mgmt/images/delete/' . $img -> id)  ?>',
	        		'key' : <?= $img -> id ?>
	        },
    		<?php endforeach ?>

        	],
        <?php else: ?>
        	initialPreview: [],
        	initialPreviewConfig: [],
        <?php endif ?>
        initialPreviewAsData: true,
        overwriteInitial: false,
        maxFileCount: 8,
        uploadUrl: 'mgmt/images/upload_multiple/product_img',
				uploadAsync: false,
        uploadExtraData: {
        }
    }).on('fileuploaded', function(event, data, previewId, index) {
    	// upload image
	   var id = data.response.id;
	   $("#file-input").fileinput('reset');
	}).on('filebatchselected', function(event, numFiles, label) {
    	$("#file-input").fileinput('upload');
	}).on('filedeleted', function(event, key) {

	}).on('fileuploaderror', function(event, data, previewId, index) {
		alert('upload error');
	}).on('filedeleted', function(event, key) {
		$('#image_id').val(0);
	});

    $(".dt_picker").datetimepicker({
		format : 'YYYY-MM-DD HH:mm:ss'
	})

	$('#start_time')
	.on('dp.change dp.show', function(e) {
      // Revalidate the date when user change it
      $('#app-edit-form').bootstrapValidator('revalidateField', 'start_time');
	});


	// .on('changeDate', function (e) {
  //       $(this).datepicker('hide');
  //       $('#app-edit-form').bootstrapValidator('updateStatus', 'dt', 'VALID');
  // 	});

  	$('#cate_main').on('change', function(){
  		var _id = $(this).find('option:selected').val();
  		loadSubCate(_id);
  	});

  	function loadSubCate(id) {
  		$.ajax({
  			url: baseUrl + 'mgmt/products/cate_sub/' + id,
  			success: function(data) {
  				$('#cate_sub')
  							.find('option')
						    .remove()
						    .end()
						    .val(0);
				$('#cate_sub')
						    .append($('<option></option>').attr('value', 0).text('---'));

  				if(data && data.list.length > 0) {
  					$.each(data.list, function(){
  						$('#cate_sub')
						    .append($('<option></option>').attr('value', this.id).text(this.cate_name));
  					});
  				}

			},
			failure: function() {
			}
  		});
  	}

  	$('#app-edit-form').bootstrapValidator({
		feedbackIcons : {
			valid : 'glyphicon glyphicon-ok',
			invalid : 'glyphicon glyphicon-remove',
			validating : 'glyphicon glyphicon-refresh'
		}
	});

  	$('#app-edit-form-s4').bootstrapValidator({
		feedbackIcons : {
			valid : 'glyphicon glyphicon-ok',
			invalid : 'glyphicon glyphicon-remove',
			validating : 'glyphicon glyphicon-refresh'
		}
	});


	$('#cate_main').select2();


	//永久檔期
	$('#ever_time').change(function(){
		if($(this).prop('checked')){
			$('input[name="ever_time"]').val(1);
		}else{
			$('input[name="ever_time"]').val(0);
		}
	});

	//禮卷
	$('#is_voucher').change(function(){
		if($(this).prop('checked')){
			$('input[name="is_voucher"]').val(1);
		}else{
			$('input[name="is_voucher"]').val(0);
		}
	});

	//置頂
	$('#pos').change(function(){
		if($(this).prop('checked')){
			$('input[name="pos"]').val(1);
		}else{
			$('input[name="pos"]').val(2);
		}
	});

	// draw cate by select2
	$('#main_cate').select2();


	// spec
	var specStore = [];
	function addSpec() {
		var $specName = $('#spec_name');
		var specName = $specName.val();
		if(specName.length == 0) {
			alert('請輸入規格名稱');
			return;
		}

		$.ajax({
			url: baseUrl + 'store/products/add_spec',
			type: 'POST',
			data: {
				spec_name: specName,
				product_id: $('#product_id').val()
			},
			dataType: 'json',
			success: function(d) {
				if(d.spec) {
					$specName.val(''); // reset
					specStore.push({
						id: d.spec.id,
						product_id: d.spec.product_id,
						spec_name: d.spec.spec_name,
						pos: d.spec.pos,
						details: []
					});
					redrawSpec();
				}
			},
			failure:function(){
				alert('faialure');
			}
		});
	}

	$.fn.editable.defaults.mode = 'inline';
</script>
