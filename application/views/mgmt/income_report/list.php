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
							<div class="widget-toolbar pull-left">
								日期
							</div>
							<div class="widget-toolbar pull-left">
								<input id="c_date" type="text" class="form-control input-sm dt_picker" value="<?= date('Y-m-d') ?>" placeholder="請選擇日期" />
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
											<th class="min150">購買序號</th>
											<th class="min150">購買帳號</th>
											<th class="min100">商品</th>
											<th class="min100">價格</th>
											<th class="min100">百分比</th>
											<th class="min100">獎金</th>
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
		loadScript(baseUrl + "js/app/income_report/list.js", function(){
			currentApp = new IncomeReportAppClass(new BaseAppClass({}));

			$(".dt_picker").datetimepicker({
				format : 'YYYY-MM-DD'
			}).on('dp.change',function(event){
		    currentApp.tableReload();
		  });

		});
	});
</script>
