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
								合計：<span style="color: red;" id="sum_amt_range"></span>
							</div>
							<div class="widget-toolbar pull-left">
								總額：<span style="color: red;" id="sum_amt_ntd"></span>
							</div>
							<div class="widget-toolbar pull-left">
								<input type="text" class="dt_picker" id="start_date" value="<?= date('Y-m-d') ?>" />
								<input type="text" class="dt_picker" id="end_date" value="<?= date('Y-m-d') ?>" />
							</div>
							<div class="widget-toolbar pull-left">
								<?php if($login_user -> role_id == 99): ?>
									<select onchange="currentApp.tableReload()" id="corp_id">
										<option value="0">全部</option>
										<?php foreach($corp_list as $each): ?>
											<option value="<?= $each -> id ?>"><?= $each -> corp_name ?></option>
										<?php endforeach ?>
									</select>
								<?php endif ?>
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
								<input type="hidden" id="login_user_id" value="<?= $login_user_id ?>" />
								<table id="dt_list" class="table table-striped table-bordered table-hover" width="100%">
									<thead>
										<tr>
											<th class="min100">交易角色</th>
											<th class="min100">公司</th>
											<th class="min100">數量</th>
											<th class="min100">類型</th>
											<th class="min100">簡述</th>
											<th class="min150">建立時間</th>
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
		loadScript(baseUrl + "js/app/cash_tx/tx_list.js?v=<?= date("His") ?>", function(){
			currentApp = new CashTxAppClass(new BaseAppClass({}));
		});
	});


	$(".dt_picker").datetimepicker({
		format : 'YYYY-MM-DD'
	}).on('dp.change',function(event){
		currentApp.tableReload();
	});
</script>
