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
								<input type="hidden" id="login_user_id" value="<?= $login_user_id ?>" />
								<table id="dt_list" class="table table-striped table-bordered table-hover" width="100%">
									<thead>
										<tr>
											<th class="min100">公司名稱</th>
											<th class="min100">購買帳號</th>
											<th class="min200">序號</th>
											<th class="min100">機器</th>
											<th class="min100">價格台幣</th>
											<th class="min100">購買幣值</th>
											<th class="min100">購買價格(<?= $corp -> sys_name ?>)</th>
											<th class="min150">建立時間</th>
										</tr>
										<tr class="search_box">
									    <th><input class="form-control input-xs" type="text" /></th>
									    <th><input class="form-control input-xs" type="text" /></th>
									    <th></th>
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
		loadScript(baseUrl + "js/app/mining_buy/list.js", function(){
			currentApp = new MiningBuyAppClass(new BaseAppClass({}));
			if(getParam('buy')) {
				currentApp.doEdit(0);
			}
		});
	});
</script>
