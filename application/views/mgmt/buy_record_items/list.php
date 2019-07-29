<style>
	#dt_list_wrapper {
		border-top: 1px solid #CCCCCC;
	}
	input[disabled] {
	  background-color: #DDD;
		color: #EEE;
	}
</style>
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
						<header>
						<div class="widget-toolbar pull-left">
							<label>範圍查詢 <input id="s_multiple" type="checkbox" class="" value="" /></label>
						</div>
						<div class="widget-toolbar pull-left">
							<input id="s_dt" placeholder="請輸入日期" type="text" class="dt_picker" value="<?= date('Y-m-d') ?>" />
						</div>
						<div class="widget-toolbar pull-left" disabled>
							~ <input id="e_dt" disabled placeholder="請輸入日期" type="text" class="dt_picker" value="<?= date('Y-m-d') ?>" />
						</div>


					</header>

						<!-- widget div-->
						<div>

							<!-- widget edit box -->
							<div class="jarviswidget-editbox">

							</div>

							<div class="widget-body no-padding">
								<table id="dt_list" class="table table-striped table-bordered table-hover" width="100%">
									<thead>
										<tr>
											<th class="min100">購買人員</th>
											<th class="min100">總花費金額</th>
											<th class="min150">商品</th>
											<th class="min150">數量</th>
											<th class="min150">時間</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</article>
			</div>
		</section>
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
#dt_list_wrapper .table-responsive {
	width: 100%;
}
</style>
<script type="text/javascript">
	loadScript(baseUrl + "js/class/BaseAppClass.js", function(){
		loadScript(baseUrl + "js/app/buy_record_items/list.js", function(){
			currentApp = new BuyrecorditemsAppClass(new BaseAppClass({}));
		});
	});
</script>
