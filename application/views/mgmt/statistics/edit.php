<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget" id="wid-id-7" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
	<header>


			<div class="widget-toolbar pull-left">
				<a href="javascript:void(0);" id="back_parent" onclick="currentApp.backTo()" class="btn btn-default ">
					<i class="fa fa-arrow-circle-left"></i>返回
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
				<a data-toggle="tab" href="#s3"><i class="fa fa-indent"></i> <span class="hidden-mobile hidden-tablet">商品樣板</span></a>
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
							<input type="hidden" id="item_id" name="id" value="<?= $id ?>" />
							<fieldset>
								<div class="form-group">
									<label class="col-md-3 control-label">帳號</label>
									<div class="col-md-6">
										<input type="text" required class="form-control" name="account" value="<?= isset($item) ? $item -> account : '' ?>" />
									</div>
								</div>
							</fieldset>

							<fieldset>
								<div class="form-group">
									<label class="col-md-3 control-label">密碼</label>
									<div class="col-md-6">
										<input type="password" required class="form-control" name="password" value="<?= isset($item) ? $item -> password : '' ?>" />
									</div>
								</div>
							</fieldset>

							<fieldset>
								<div class="form-group">
									<label class="col-md-3 control-label">商家名稱</label>
									<div class="col-md-6">
										<input type="text" required class="form-control" name="store_name" value="<?= isset($item) ? $item -> store_name : '' ?>" />
									</div>
								</div>
							</fieldset>

							<fieldset>
								<div class="form-group">
									<label class="col-md-3 control-label">商家等級</label>
									<div class="col-md-6">
										<select class="form-control" name="rank">
											<?php foreach($store_rank as $each):?>
												<option value="<?=$each->rank?>" <?=isset($item)&&$item->rank == $each->rank?'selected':''?>><?=$each->rank?></option>
											<?php endforeach;?>
										</select>
									</div>
								</div>
							</fieldset>

							<fieldset>
								<div class="form-group">
									<label class="col-md-3 control-label">電話</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="phone" value="<?= isset($item) ? $item -> phone : '' ?>" />
									</div>
								</div>
							</fieldset>

							<fieldset>
								<div class="form-group">
									<label class="col-md-3 control-label">地址</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="address" value="<?= isset($item) ? $item -> address : '' ?>" />
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



							<fieldset>
								<div class="form-group">
									<!-- <label class="col-md-3 control-label">Email</label> -->
									<div class="col-md-12 table-responsive">
										<table class="table table-striped table-bordered table-hover">
											<thead>
												<tr>
													<th  class="min100"></th>
													<?php for($i=0;$i<7;$i++){?>
														<th class="min150"><?='星期'.num_to_weekday($i);?></th>
														<?php }
														function num_to_weekday($j){
															$weekday = array(
																'日','一','二','三','四','五','六'
															);
															return $weekday[$j];
														}
														?>
												</tr>
											</thead>

											<tbody>
												<tr>
													<td>開店時間</td>
													<?php for($i=0;$i<7;$i++){?>
														<td><input class="input-xs timepicker" type="text" name="open_<?=$i?>" value="<?=isset($item)?$item->{'open_'.$i}:''?>"/></td>
														<?php }?>
												</tr>

												<tr>
													<td>打烊時間</td>
													<?php for($i=0;$i<7;$i++){?>
														<td><input class="input-xs timepicker" type="text" name="close_<?=$i?>" value="<?=isset($item)?$item->{'close_'.$i}:''?>"/></td>
														<?php }?>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</fieldset>
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

				<!-- start of s2 -->
					<div class="tab-pane fade in padding-10 no-padding-bottom" id="s2">
						<div class="row">
							<div class="col-md-12">
								<input id="file-input" name="file" type="file" class="file-loading form-control">
							</div>
						</div>
					</div>
					<!-- end of s2 -->

					<!-- start of s3 -->
						<div class="tab-pane fade in padding-10 no-padding-bottom" id="s3">
							<!-- <div class="row">
								<div class="col-md-12">
									<button type="button" data-action="toggleShortcut">btn</button>
								</div>
							</div> -->

							<!-- 大圖 -->

							<form id="app-edit-form-s3" method="post" class="form-horizontal">
								<label>首頁大圖</label>
								<div class="row">
									<!-- <div class="col-md-12">
										<div class="jumbotron" style="background-repeat;no-repeat;background-image:url('http://www.w3schools.com/css/trolltunga.jpg')">
										  <h1>...</h1>
										  <p>...</p>
										  <p><a class="btn btn-primary btn-lg" href="#" role="button">另選上傳</a></p>
										</div>
									</div> -->

									<div class="col-sm-12 col-md-12">
								    <div class="thumbnail" id="product_image">

								      <img class="product-img" src="<?=isset($product_image)?base_url().'api/images/get/'.$product_image[0]->id.'/thumb':$default_img?>" alt="...">
								      <div class="caption">

								        <p class="text-center"><a href="javascript:void(0)" onclick="defaultClick()" class="btn btn-primary" role="button">預設</a> <a href="javascript:void(0)" class="btn btn-danger" role="button" onclick="customClick()">自訂</a></p>
												<input id="product-image-upload" style="visibility:hidden"name="file" type="file" class="form-control">
												<input type="hidden" name="product_image_id" id="product_image_id" value="<?=isset($product_image)?$product_image[0]->id:0?>"/>
								      </div>
											<fieldset>
												<div class="form-group">
													<label class="col-md-3 control-label">優惠文字</label>
													<div class="col-md-6">
														<input type="text" class="form-control" name="subtitle" value="<?= isset($item) ? $item -> subtitle : '' ?>" />
													</div>
												</div>
											</fieldset>
								    </div>
								</div>

								<!-- 三樣商品 -->
								<label>三樣商品</label>
								<div class="row" id="s3-products">

									<!-- <input type="hidden" id="temp-list" value="<?=json_encode($temp_list)?>"/> -->
									<div class="col-sm-6 col-md-4">
								    <div class="thumbnail" id="product_1">
								      <img class="product-img" src="<?=isset($product_1)&&$product_1->image_id != 0?base_url().'api/images/get/'.$product_1->image_id.'/thumb':$default_img?>" alt="...">
								      <div class="caption">
								        <h3 class="text-center product-name"><?=isset($product_1)?$product_1->product_name:'商品名稱'?></h3>
								        <p class="text-center product-price"><?=isset($product_1)?$product_1->price:'價錢'?></p>
								        <p class="text-center"><a href="javascript:void(0)" onclick="productClick(1)" class="btn btn-primary" role="button">選取</a> <a href="javascript:void(0)" class="btn btn-danger" role="button" onclick="productDelete(1)">刪除</a></p>
												<input type="hidden" name="product_1" value="<?=isset($product_1)?$product_1->id:0?>"/>
								      </div>
								    </div>
								  </div>

									<div class="col-sm-6 col-md-4">
								    <div class="thumbnail" id="product_2">
								      <img class="product-img" src="<?=isset($product_2)&&$product_2->image_id != 0?base_url().'api/images/get/'.$product_2->image_id.'/thumb':$default_img?>" alt="...">
								      <div class="caption">
								        <h3 class="text-center product-name"><?=isset($product_2)?$product_2->product_name:'商品名稱'?></h3>
								        <p class="text-center product-price"><?=isset($product_2)?$product_2->price:'價錢'?></p>
								        <p class="text-center"><a href="javascript:void(0)" onclick="productClick(2)" class="btn btn-primary" role="button">選取</a> <a href="javascript:void(0)" class="btn btn-danger" role="button" onclick="productDelete(2)">刪除</a></p>
												<input type="hidden" name="product_2" value="<?=isset($product_2)?$product_2->id:0?>" />
								      </div>
								    </div>
								  </div>

									<div class="col-sm-6 col-md-4">
								    <div class="thumbnail" id="product_3">
								      <img class="product-img" src="<?=isset($product_3)&&$product_3->image_id != 0?base_url().'api/images/get/'.$product_3->image_id.'/thumb':$default_img?>" alt="...">
								      <div class="caption">
								        <h3 class="text-center product-name"><?=isset($product_3)?$product_3->product_name:'商品名稱'?></h3>
								        <p class="text-center product-price"><?=isset($product_3)?$product_3->price:'價錢'?></p>
								        <p class="text-center"><a href="javascript:void(0)" onclick="productClick(3)" class="btn btn-primary" role="button">選取</a> <a href="javascript:void(0)" class="btn btn-danger" role="button" onclick="productDelete(3)">刪除</a></p>
												<input type="hidden" name="product_3" value="<?=isset($product_3)?$product_3->id:0?>" />
								      </div>
								    </div>
								  </div>

								</form>
							</div>


							<!-- 商品表格 -->
							<!-- image Modal -->
							<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" data-backdrop="false">
							  <div class="modal-dialog modal-lg" role="document">
							    <div class="modal-content modal-lg">
							      <div class="modal-header">
							        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							        <h4 class="modal-title" id="imageModalLabel">新增商品</h4>
							      </div>
							      <div class="modal-body">
										<!-- <input type="hidden" id="imageMe" />
										<iframe src="<?=base_url('mgmt/products') ?>" id="image-iframe" width="100%" height="500" frameBorder="0"></iframe> -->
										<table id="dt_lists" class="table table-striped table-bordered table-hover" width="100%">
											<thead>
												<tr>
													<th class="min75"></th>
													<!-- <th class="min150">商家</th> -->
													<th class="min150">圖片</th>
													<th class="min150">商品序號</th>
													<th class="min250">商品名稱</th>
													<th class="min150">商品分類</th>
													<!-- <th class="min150">上架時間</th> -->
													<!-- <th class="min150">下架時間</th> -->
													<th class="min150">價錢</th>
													<th>創造時間</th>
												</tr>
												<tr class="search_box">
															<th></th>
															<!-- <th></th> -->
															<th></th>
															<th><input class="form-control input-xs" type="text" /></th>
															<th><input class="form-control input-xs" type="text" /></th>
															<!-- product_cate -->
															<th>
																<select id="search_cate_main"  class="form-control input-xs">
																		<option value="">全部</option>
																	<?php foreach($main_cates as $each): ?>
																		<option value="<?= $each -> id ?>">
																			<?= $each -> cate_name ?>
																		</option>
																	<?php endforeach ?>
																</select>
															</th>
															<!-- <th><input class="form-control input-xs" type="text" /></th> -->
															<!-- <th><input class="form-control input-xs" type="text" /></th> -->
															<th><input class="form-control input-xs" type="text" /></th>
															<th></th>
														</tr>
											</thead>
											<tbody>
											</tbody>
										</table>

							      </div>
							      <div class="modal-footer">
							        <button type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
							      </div>
							    </div>
							  </div>
							</div>
							<script type="text/javascript">
								var baseUrl = '<?= base_url(); ?>';
								loadScript(baseUrl + "js/app/store/products.js", function(){
									productApp.init();
								});
							</script>



						</div>
						<!-- end of s3 -->
			</div>
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
	$('.timepicker').datetimepicker({
		format: 'HH:mm',

	});
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
	                    	url: baseUrl + 'mgmt/store/check_account/' + ($('#item_id').val().length > 0 ? '?id=' + $('#item_id').val() : '')
	                    }
	                }
	           }
    }
	});

	$("#file-input").fileinput({
		<?php if(!empty($item -> images) && count($item -> images) > 0): ?>
			initialPreview: [
				<?php foreach($item -> images as $img): ?>
					'<?=  base_url('mgmt/images/get/' . $img -> id) ?>',
				<?php endforeach ?>
			],
        	initialPreviewConfig: [<?php foreach($item -> images as $img): ?>
    		{
	        		'caption' : '<?= $img -> image_name ?>',
	        		'size' : <?= $img -> image_size ?>,
	        		'width' : '120px',
	        		'url' : '<?= base_url('mgmt/images/delete/' . $img -> id)  ?>',
	        		'key' : <?= $img -> id ?>
	        },
    		<?php endforeach ?>],
        <?php else: ?>
        	initialPreview: [],
        	initialPreviewConfig: [],
        <?php endif ?>
        initialPreviewAsData: true,
        maxFileCount: 1,
        uploadUrl: 'mgmt/images/upload/store_img',
        uploadExtraData: {
        }
    }).on('fileselect', function(event, numFiles, label) {
    	$("#file-input").fileinput('upload');
	}).on('fileuploaded', function(event, data, previewId, index) {
	   var id = data.response.id;
		 $("#file-input").fileinput('reset');
	}).on('fileuploaderror', function(event, data, previewId, index) {
		alert('upload error');
	}).on('filedeleted', function(event, key) {
		$('#image_id').val(0);
	});

	//product template
	function productClick(order){
		productApp.productsChoice = order;
		// $('#imageModal').detach();
		// if(!$('#imageModal').length === 0){
			$('#imageModal').modal('show');
		// }else{
			// $('#imageModal').appendTo("body").modal('show');
		// }

	}

	function productDelete(order){
		var $main = $('#product_'+order);
		var defaultImg = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMjQyIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDI0MiAyMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzEwMCV4MjAwCkNyZWF0ZWQgd2l0aCBIb2xkZXIuanMgMi42LjAuCkxlYXJuIG1vcmUgYXQgaHR0cDovL2hvbGRlcmpzLmNvbQooYykgMjAxMi0yMDE1IEl2YW4gTWFsb3BpbnNreSAtIGh0dHA6Ly9pbXNreS5jbwotLT48ZGVmcz48c3R5bGUgdHlwZT0idGV4dC9jc3MiPjwhW0NEQVRBWyNob2xkZXJfMTU3NjljMjk0MmYgdGV4dCB7IGZpbGw6I0FBQUFBQTtmb250LXdlaWdodDpib2xkO2ZvbnQtZmFtaWx5OkFyaWFsLCBIZWx2ZXRpY2EsIE9wZW4gU2Fucywgc2Fucy1zZXJpZiwgbW9ub3NwYWNlO2ZvbnQtc2l6ZToxMnB0IH0gXV0+PC9zdHlsZT48L2RlZnM+PGcgaWQ9ImhvbGRlcl8xNTc2OWMyOTQyZiI+PHJlY3Qgd2lkdGg9IjI0MiIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNFRUVFRUUiLz48Zz48dGV4dCB4PSI4OS44NTkzNzUiIHk9IjEwNS4xIj4yNDJ4MjAwPC90ZXh0PjwvZz48L2c+PC9zdmc+';
		$main.find('.product-name').text('商品名稱');
		$main.find('.product-price').text('價錢');
		$main.find('.product-img').attr('src',defaultImg);
		$main.find('input[name="product_'+order+'"]').val(0);
	}

	function defaultClick(){
		var defaultImg = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMjQyIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDI0MiAyMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzEwMCV4MjAwCkNyZWF0ZWQgd2l0aCBIb2xkZXIuanMgMi42LjAuCkxlYXJuIG1vcmUgYXQgaHR0cDovL2hvbGRlcmpzLmNvbQooYykgMjAxMi0yMDE1IEl2YW4gTWFsb3BpbnNreSAtIGh0dHA6Ly9pbXNreS5jbwotLT48ZGVmcz48c3R5bGUgdHlwZT0idGV4dC9jc3MiPjwhW0NEQVRBWyNob2xkZXJfMTU3NjljMjk0MmYgdGV4dCB7IGZpbGw6I0FBQUFBQTtmb250LXdlaWdodDpib2xkO2ZvbnQtZmFtaWx5OkFyaWFsLCBIZWx2ZXRpY2EsIE9wZW4gU2Fucywgc2Fucy1zZXJpZiwgbW9ub3NwYWNlO2ZvbnQtc2l6ZToxMnB0IH0gXV0+PC9zdHlsZT48L2RlZnM+PGcgaWQ9ImhvbGRlcl8xNTc2OWMyOTQyZiI+PHJlY3Qgd2lkdGg9IjI0MiIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNFRUVFRUUiLz48Zz48dGV4dCB4PSI4OS44NTkzNzUiIHk9IjEwNS4xIj4yNDJ4MjAwPC90ZXh0PjwvZz48L2c+PC9zdmc+';
		<?php if(!empty($item -> images) && count($item -> images) > 0): $imgList = $item->images?>
			var url = '<?=base_url();?>' + 'api/images/get/' + '<?=$imgList[0]->id;?>' + '/thumb';
			$('#product_image .product-img').attr('src',url);
			$('#product_image_id').val('<?=$imgList[0]->id;?>');
		<?php endif;?>

	}

	function customClick(){
		// $('#product-image-upload').click();
		$('#product-image-upload').fileinput({
			<?php if(!empty($product_image) && count($product_image) > 0): ?>
				initialPreview: [
					<?php foreach($product_image as $img): ?>
						'<?=  base_url('mgmt/images/get/' . $img -> id) ?>',
					<?php endforeach ?>
				],
						initialPreviewConfig: [<?php foreach($product_image as $img): ?>
					{
								'caption' : '<?= $img -> image_name ?>',
								'size' : <?= $img -> image_size ?>,
								'width' : '120px',
								'url' : '<?= base_url('mgmt/images/delete/' . $img -> id)  ?>',
								'key' : <?= $img -> id ?>
						},
					<?php endforeach ?>],
					<?php else: ?>
						initialPreview: [],
						initialPreviewConfig: [],
					<?php endif ?>
					initialPreviewAsData: true,
					maxFileCount: 1,
					uploadUrl: 'mgmt/images/upload/store_product_image',
					uploadExtraData: {
					}
			}).on('fileselect', function(event, numFiles, label) {
				$("#product-image-upload").fileinput('upload');

		}).on('fileuploaded', function(event, data, previewId, index) {
			 var id = data.response.id;
			 $('#product_image_id').val(id);
			 $('#product_image .product-img').attr('src','<?=base_url();?>' + 'api/images/get/' + id + '/thumb');
			 $("#product-image-upload").fileinput('reset');

			 $('#product_image .file-input').hide();
		}).on('fileuploaderror', function(event, data, previewId, index) {
			alert('upload error');
		}).on('filedeleted', function(event, key) {
			$('#product_image_id').val(0);
		});

		$('#product-image-upload').click();
		// $('#product-image-upload').click(function(){
		// 	$('#product_image .file-input').hide();
		// });
	}



</script>
