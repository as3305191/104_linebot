<style>
	#dt_list_wrapper {
		border-top: 1px solid #CCCCCC;
	}
</style>
<input type="hidden" id="corp_id" value="<?= $corp -> id ?>" />
<div class="tab-content">
	<div class="tab-pane active" id="list_page">
		<!-- widget grid -->
		<section id="widget-grid" class="">

			<!-- row -->
			<div class="row">

				<!-- NEW WIDGET START -->
				<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

					<!-- Widget ID (each widget will need unique ID)-->
					<div class="jarviswidget">

						<!-- widget div-->
						<div>

							<!-- widget edit box -->
							<div class="jarviswidget-editbox">
								<!-- This area used as dropdown edit box -->

							</div>
							<!-- end widget edit box -->


							<!-- widget content -->
							<div class="widget-body no-padding ">
								<div id="status_filter" class="btn-group" data-toggle="buttons" style="padding: 3px 0 3px 9px;">
								  <label class="btn btn-default btn-xs active">
								    <input type="radio" name="options" id="o_all" autocomplete="off" value="all" checked="checked"> 全部
								  </label>
								  <label class="btn btn-default btn-xs">
								    <input type="radio" name="options" id="o_0" autocomplete="off" value="0"> 訂單已建立
								    <span class="badge bg-color-red bounceIn animated">19</span>
								  </label>
								  <label class="btn btn-default btn-xs">
								    <input type="radio" name="options" id="o_1" autocomplete="off" value="1"> 訂單處理中
								    <span class="badge bg-color-red bounceIn animated">29</span>
								  </label>
								  <label class="btn btn-default btn-xs">
								    <input type="radio" name="options" id="o_2" autocomplete="off" value="2"> 訂單已完成
								    <span class="badge bg-color-red bounceIn animated">99</span>
								  </label>

								  <label class="btn btn-default btn-xs">
								    <input type="radio" name="options" id="o_m10" autocomplete="off" value="-1"> 訂單取消
								    <span class="badge bg-color-yellow bounceIn animated">19</span>
								  </label>

								</div>
								<br/>
								<div id="pay_status_filter" class="btn-group" data-toggle="buttons" style="padding: 3px 0 3px 9px;">
								  <label class="btn btn-default btn-xs active">
								    <input type="radio" name="pay_status" id="ps_all" autocomplete="off" value="all" checked="checked"> 全部
								  </label>
								  <label class="btn btn-default btn-xs">
								    <input type="radio" name="pay_status" id="ps_0" autocomplete="off" value="0"> 未付款
								    <span class="badge bg-color-red bounceIn animated">19</span>
								  </label>
								  <label class="btn btn-default btn-xs">
								    <input type="radio" name="pay_status" id="ps_1" autocomplete="off" value="1"> 已付款
								    <span class="badge bg-color-red bounceIn animated">29</span>
								  </label>

								  <label class="btn btn-default btn-xs">
								    <input type="radio" name="pay_status" id="ps_m1" autocomplete="off" value="-1"> 已退款
								    <span class="badge bg-color-yellow bounceIn animated">19</span>
								  </label>

								</div>
								<div id="shipping_status_filter" class="btn-group" data-toggle="buttons" style="padding: 3px 0 3px 9px;">
								  <label class="btn btn-default btn-xs active">
								    <input type="radio" name="shipping_status" id="ss_all" autocomplete="off" value="all" checked="checked"> 全部
								  </label>
								  <label class="btn btn-default btn-xs">
								    <input type="radio" name="shipping_status" id="ss_0" autocomplete="off" value="0"> 未運送
								    <span class="badge bg-color-red bounceIn animated">19</span>
								  </label>
								  <label class="btn btn-default btn-xs">
								    <input type="radio" name="shipping_status" id="ss_1" autocomplete="off" value="1"> 已運送
								    <span class="badge bg-color-red bounceIn animated">29</span>
								  </label>

								</div>
							
								<table id="dt_list" class="table table-striped table-bordered table-hover" width="100%">
									<thead>
										<tr>
											<th class="min150">訂單序號</th>
											<th class="min150">訂單狀態</th>
											<th class="min150">DBC點數</th>
											<th class="min150">付款狀態</th>
											<th class="min150">運送狀態</th>
											<th class="">建立時間</th>
										</tr>
										<tr class="search_box">
											    <th><input class="form-control input-xs" type="text" /></th>
											    <th></th>
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
<?php $this -> load -> view('general/delete_modal'); ?>

<script type="text/javascript">
	loadScript(baseUrl + "js/class/BaseAppClass.js", function(){
		loadScript(baseUrl + "js/app/orders/list.js", function(){
			currentApp = new ordersAppClass(new BaseAppClass({}));
		});
	});
</script>
