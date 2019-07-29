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
								<h1 style="margin-left:10px; line-height:50px;">Welcome!</h1>
								<div class="jarviswidget col-xs-6" id="wid-id-6" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-togglebutton="false" data-widget-deletebutton="false" data-widget-fullscreenbutton="false" data-widget-custombutton="false" data-widget-collapsed="false" data-widget-sortable="false" role="widget">
								<header role="heading">
									<span class="widget-icon"> <i class="fa fa-linkedin text-info"></i> </span>
									<h2>Welcome</h2>

								<span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>

								<!-- widget div-->
								<div role="content">

									<!-- widget edit box -->
									<div class="jarviswidget-editbox">
										<!-- This area used as dropdown edit box -->
										<input class="form-control" type="text">
									</div>
									<!-- end widget edit box -->

									<!-- widget content -->
									<div class="widget-body">
										<div class="input-group col-xs-12">
											<input class="form-control dt_picker" id="date" type="text" placeholder="YYYY-MM-DD" value="<?= date('Y-m-d') ?>"/>
											<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										</div>
										<!-- this is what the user will see -->
										<h2 class="text">
											<div class="input-group col-xs-12">
												<label class="col-md-12 control-label"><i class="fa fa-usd icon-color-good"></i>今日台幣收入：</label>
												<div class="input-group col-xs-6">
													<input type="text" class="form-control" id="sum_coin" style = "background-color: transparent; border: 0;"  value="<?=$samt_coin->coin?>" readonly="readonly" />
												</div>
											</div>

											<div class="input-group col-xs-12">
												<label class="col-md-12 control-label"><i class="fa fa-circle-o icon-color-good"></i>今日公司彩池收入：</label>
												<div class="input-group col-xs-6">
													<input type="text" class="form-control" id="sum_fish_tab" style = "background-color: transparent; border: 0;"  value="<?=$samt_fish_tab->amt?>" readonly="readonly" />
												</div>
											</div>

										<div class="input-group col-xs-12">
											<label class="col-md-12 control-label"><i class="fa fa-user icon-color-good"></i>今日註冊人數：</label>
												<div class="input-group col-xs-6">
													<input type="text" class="form-control" id="sum_user" style = "background-color: transparent; border: 0;"  value="<?=$samt_users->users?>" readonly="readonly" />
												</div>
										</div>

										<div class="sparkline" data-sparkline-type="line" data-fill-color="#9ad2ec" data-sparkline-line-color="#007bb6" data-sparkline-spotradius="5" data-sparkline-width="100%" data-sparkline-height="107px"><canvas width="511" height="107" style="display: inline-block; width: 511.984px; height: 107px; vertical-align: top;"></canvas></div>
										<h5 class="air air-bottom-left padding-10 font-md text-danger"><small class="ultra-light text-danger"></small></h5>

									</div>
									<!-- end widget content -->

								</div>
								<!-- end widget div -->

							</div>
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
	function relaodDashList() {
		if(currentApp && currentApp.dashListTimeOut) {
			currentApp.dashListTimeOut = setTimeout(function(){
					if(currentApp.dashListTimeOut) {
						if(!currentApp.dashListTimeOutCount) {
							currentApp.dashListTimeOutCount = 6;
						}
						$('#refresh-contdown').text((currentApp.dashListTimeOutCount - 1) + "秒後更新");
						if(currentApp.dashListTimeOutCount-- <= 1) {
							currentApp.tableReload();
						}
						relaodDashList();
					}
			}, 1000);
		}
	};


	loadScript(baseUrl + "js/class/BaseAppClass.js", function(){
		loadScript(baseUrl + "js/app/dashboard/list.js", function(){
			currentApp = new DashboardAppClass(new BaseAppClass({}));
		});
	});

	$(".dt_picker").datetimepicker({
		format: 'YYYY-MM-DD'
	}).on('dp.change',function(event){
		find_list();
	});

	function find_list() {
		var __load = layer.load(0);
		$.ajax({
			url: '<?= base_url() ?>' + 'mgmt/welcome/get_data',
			type: 'POST',
			data: {
				date: $('#date').val(),
			},
			dataType: 'json',
			success: function(d) {
				layer.close(__load);
				$('#sum_coin').val(d.samt_coin.coin);
				$('#sum_fish_tab').val(d.samt_fish_tab.amt);
				$('#sum_user').val(d.samt_users.users);
			},
			failure:function(){
				alert('faialure');
			}
		});
	}

</script>
