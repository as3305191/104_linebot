<div class="tab-content">
	<div class="tab-pane active" id="list_page">

		<!-- widget grid -->
		<section id="widget-grid" class="">

			<!-- row -->
			<div class="row">

				<!-- NEW WIDGET START -->
				<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

					<!-- Widget ID (each widget will need unique ID)-->
					<div class="jarviswidget jarviswidget-color">
						<header>
							<div class="widget-toolbar pull-left">
								<div class="btn-group">
									<button onclick="currentApp.doEdit(0)" class="btn dropdown-toggle btn-xs btn-default" data-toggle="dropdown">
										<i class="fa fa-plus"></i>新增
									</button>
								</div>
							</div>
						</header>

						<!-- widget div-->
						<div>

							<!-- widget edit box -->
							<div class="jarviswidget-editbox">
								<!-- This area used as dropdown edit box -->

							</div>
							<!-- end widget edit box -->

							<!-- widget content -->
							<div class="widget-body no-padding">

								<table id="dt_list" class="table table-striped table-bordered table-hover" width="100%">
									<thead>
										<tr>
											<th class="min125"></th>
											<th class="min50">狀態</th>
											<th class="min50">圖片</th>
											<th class="min150">商品序號</th>
											<th class="min250">商品名稱</th>
											<th class="min150">店家分類</th>
											<th class="min150">上架時間</th>
											<th class="min150">下架時間</th>
											<th class="min150">價錢</th>
											<th class="min150">建立時間</th>
										</tr>
										<tr class="search_box">
											    <th></th>
											    <th></th>
											    <th></th>
											    <th><input class="form-control input-xs" type="text" /></th>
											    <th><input class="form-control input-xs" type="text" /></th>
													<th>
														<select id="search_product_cate"  class="form-control input-xs">
															<option value="">全部</option>
															<?php foreach($main_cates as $each): ?>
																<option value="<?= $each -> id ?>">
																	<?= $each -> cate_name ?>
																</option>
															<?php endforeach ?>
														</select>
													</th>
											    <th></th>
											    <th></th>
											    <th></th>
											    <th></th>
										    </tr>
									</thead>
									<tbody>
									</tbody>
								</table>

							</div>
							<!-- end widget content -->

						</div>
						<!-- end widget div -->

					</div>
					<!-- end widget -->

				</article>
				<!-- WIDGET END -->

			</div>

			<!-- end row -->

		</section>
		<!-- end widget grid -->
	</div>

	<div class="tab-pane animated fadeIn" id="edit_page">
		<section class="">
			<!-- row -->
			<div class="row">
				<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="edit-modal-body">

				</article>
			</div>
		</section>
	</div>
</div>

<style>
	.modal-lg {
    width: 90%; /* respsonsive width */
}
</style>


<!-- 商品表格 -->
<!-- product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" >
		<div class="modal-content modal-lg">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="productModalLabel">挑選欲展示商品</h4>
			</div>
			<div class="modal-body">
			<!-- <input type="hidden" id="imageMe" />
			<iframe src="<?=base_url('mgmt/products') ?>" id="image-iframe" width="100%" height="500" frameBorder="0"></iframe> -->
			<table id="dt_lists" class="table table-striped table-bordered table-hover" width="100%">
				<thead>
					<tr>
						<!-- <th class="min150">商家</th> -->
						<th class="min150">圖片</th>
						<th class="min150">商品序號</th>
						<th class="min250">商品名稱</th>
						<th class="min150">商品分類</th>
						<!-- <th class="min150">上架時間</th> -->
						<!-- <th class="min150">下架時間</th> -->
						<th class="min150">價錢</th>

					</tr>
					<tr class="search_box">

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

</script>


<?php $this -> load -> view('general/delete_modal'); ?>
<?php $this -> load -> view('general/copy_modal'); ?>

<script type="text/javascript">
	var baseUrl = '<?= base_url(); ?>';
	loadScript(baseUrl + "js/class/BaseAppClass.js", function(){
		loadScript(baseUrl + "js/app/products/list.js", function(){
			currentApp = new ProductsApp(new BaseAppClass({}));
		});
	});
	// loadScript(baseUrl + "js/store/products/list.js", function(){
	// 	productsApp.init();
	// });
</script>
