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
							<!-- <div class="widget-toolbar pull-left">
								<div class="btn-group">
									<button onclick="currentApp.doEdit(0)" class="btn dropdown-toggle btn-xs btn-success" data-toggle="dropdown">
										<i class="fa fa-plus"></i>購買
									</button>
								</div>
							</div> -->

							<!-- <div class="widget-toolbar pull-left">
								目前餘額：<span style="color: red;"><?= number_format($sum_amt) ?></span>
							</div> -->
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
											<th class="min200">序號</th>
											<th class="min100">公司</th>
											<th class="min100">使用者帳號</th>
											<th class="min100">金額</th>
											<th class="min100">付款方式</th>
											<th class="min100">狀態</th>
											<th class="min150">建立時間</th>
										</tr>
										<tr class="search_box">
									    <th><input class="form-control input-xs" type="text" /></th>
									    <th>
												<?php if($login_user -> role_id == 99): ?>
													<select id="s_corp_id" class="form-control input-xs">
														<option value="0">-</option>
														<?php foreach($corp_list as $each): ?>
															<option value="<?= $each -> id ?>"><?= $each -> corp_name ?></option>
														<?php endforeach ?>
													</select>
												<?php endif ?>
											</th>
											<th><input class="form-control input-xs" type="text" /></th>
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
		loadScript(baseUrl + "js/app/pay_records/list.js", function(){
			loadScript(baseUrl + "js/libs/waiting-dialog.js", function(){
				currentApp = new PayRecordsAppClass(new BaseAppClass({}));
				if(getParam('buy')) {
					currentApp.doEdit(0);
				}
				currentApp.waitingDialog = new waitingDialogCalss(jQuery);
			});
		});
	});
</script>
