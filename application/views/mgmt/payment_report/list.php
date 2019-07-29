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
								<div style="width:300px;">
									<div class="input-group" id="DateDemo">
										<input type='text' style="width: 250px;" class="form-control" id='weeklyDatePicker' placeholder="請選擇" />
										<span class="input-group-btn">
											<button class="btn btn-primary" type="button" onclick="currentApp.tableReload();">查詢</button>
										</span>
									</div>
								</div>

								<input type="hidden" id="login_user_id" value="<?= $login_user_id ?>" />
								<input type="hidden" id="s_date" value="" />
								<input type="hidden" id="e_date" value="" />
								<table id="dt_list" class="table table-striped table-bordered table-hover" width="100%">
									<thead>
										<tr>
											<th class="min150">帳號</th>
											<th class="min150">獎金</th>
											<th class="min150">銀行</th>
											<th class="">銀行帳號</th>
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

<style>
.bootstrap-datetimepicker-widget tr:hover {
    background-color: #808080;
}
</style>
<script type="text/javascript">
	loadScript(baseUrl + "js/class/BaseAppClass.js", function(){
		loadScript(baseUrl + "js/app/payment_report/list.js", function(){
			//Initialize the datePicker(I have taken format as mm-dd-yyyy, you can     //have your owh)
			$("#weeklyDatePicker").datetimepicker({
				format: 'YYYY-MM-DD'
			});

			//Get the value of Start and End of Week
			$('#weeklyDatePicker').on('dp.change', function (e) {
				var value = $("#weeklyDatePicker").val();
				var firstDate = moment(value, "YYYY-MM-DD'").day(0).format("YYYY-MM-DD");
				var lastDate =  moment(value, "YYYY-MM-DD'").day(6).format("YYYY-MM-DD");
				$("#weeklyDatePicker").val(firstDate + " ~ " + lastDate);
				$("#s_date").val(firstDate);
				$("#e_date").val(lastDate);
			});

			// dt tables
			currentApp = new PaymentReportAppClass(new BaseAppClass({}));


		});
	});
</script>
