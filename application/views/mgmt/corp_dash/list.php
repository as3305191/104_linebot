

<div class="tab-content">
	<div class="tab-pane active" id="list_page">

		<!-- widget grid -->
		<section id="widget-grid" class="">
			<?php if($l_user -> role_id == 99
								|| $l_user -> role_id == 1
								|| $l_user -> role_id == 11
								): ?>



			<?php endif ?>
			<ul class="nav nav-tabs">
				<?php if($l_user -> role_id == 99): ?>
			  	<li <?= $l_user -> role_id == 99 ? 'class="active"' : '' ?>><a data-toggle="tab" href="#s1">收入列表</a></li>
				<?php endif ?>
				<?php if($l_user -> role_id == 1
									|| $l_user -> role_id == 2
									|| $l_user -> role_id == 3
									|| $l_user -> role_id == 11
									): ?>
			  <li <?= $l_user -> role_id == 99 ? '' : 'class="active"' ?>><a data-toggle="tab" href="#s2">收入列表</a></li>
				<?php endif ?>
				<?php if($l_user -> role_id == 1
									|| $l_user -> role_id == 2
									|| $l_user -> role_id == 3
									|| $l_user -> role_id == 11
									): ?>
			  <li><a data-toggle="tab" href="#s3">會員列表</a></li>
				<?php endif ?>
			</ul>

			<div class="tab-content">
				<div id="s1" class="tab-pane fade in <?= $l_user -> role_id == 99 ? 'active' : '' ?>">
					<?php if($corp -> id == 1): ?>
					<div class="row">
						<div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
							<div>
								<div id="sys_income" class="form-inline">
							      <label>日期</label><br>
										<?php
										$month_ini = new DateTime();
										?>
							      <input type="text" id="s_date" class="form-control dt_picker" value="<?= $month_ini->format('Y-m-d') ?>" />
								</div>
								<table id="dt_list" class="table table-striped table-bordered table-hover" width="100%">
									<thead>
										<tr>
											<th class="min200">項目</th>
											<th class="">金額</th>
										</tr>
									</thead>
									<tbody id="res_body">
									</tbody>
								</table>
							</div>

						<script>
							function reloadData() {
								$.ajax({
									type: "POST",
									url: '<?= base_url('mgmt/corp_dash/find_all_corp_tx') ?>',
									data: {
										s_date: $('#s_date').val(),
										e_date: $('#e_date').val()
									},
									success: function(data)
									{
											$body = $('#res_body').empty();
											if(data.cp_list) {
												$.each(data.cp_list, function(){
													var me = this;
													$tr = $('<tr></tr>').appendTo($body);
													$('<td></td>').appendTo($tr).html(me.type_name);
													var sAmt = me.sum_amt;
													sAmt = parseFloat(sAmt);
													sAmt = sAmt.toFixed(2);
													$('<td class="ta_right"></td>').appendTo($tr).html(numberWithCommas(sAmt));
												});
											}
									}
								});
							}

							$(document).ready(function(){
								$("#sys_income > .dt_picker").datetimepicker({
									format : 'YYYY-MM-DD'
								}).on('dp.change',function(event){
							    reloadData();
							  });
								reloadData();
							});
						</script>

						</div>
					</div>
					<?php endif ?>
				</div>
				<div id="s2" class="tab-pane fade in <?= $l_user -> role_id == 99 ? '' : 'active' ?>">
					<?php if($l_user -> role_id == 1
										|| $l_user -> role_id == 2
										|| $l_user -> role_id == 3
										|| $l_user -> role_id == 11
										): ?>
					<div class="row">
						<div class="col-xs-12 col-sm-5 col-md-4 col-lg-2">
							<div>
								<h1>收入列表</h1>
								<input type="hidden" id="l_user_id" value="<?= $l_user -> id ?>" />
								<div>總計<span id="p_sum" style="color:red;"></span></div>
								<div id="p_income" class="form-inline">
							      <label>起始日期</label><br>
										<?php
										$month_ini = new DateTime("first day of this month");
										$month_end = new DateTime("last day of this month");
										?>
							      <input type="text" id="p_s_date" class="form-control dt_picker" value="<?= $month_ini->format('Y-m-d') ?>" />
										～ <input type="text" id="p_e_date" class="form-control dt_picker" value="<?= $month_end->format('Y-m-d') ?>" />
								</div>
								<table id="p_dt_list" class="table table-striped table-bordered table-hover" width="100%">
									<thead>
										<tr>
											<th class="min100">點數</th>
											<th class="">說明</th>
										</tr>
									</thead>
									<tbody id="p_res_body">
									</tbody>
								</table>
							</div>

						<script>
							function pReloadData() {
								$.ajax({
									type: "POST",
									url: '<?= base_url('mgmt/corp_dash/find_all_p_tx') ?>',
									data: {
										s_date: $('#p_s_date').val(),
										e_date: $('#p_e_date').val(),
										user_id: $('#l_user_id').val()
									},
									success: function(data)
									{
											$body = $('#p_res_body').empty();
											if(data.cp_list) {
												$.each(data.cp_list, function(){
													var me = this;
													$tr = $('<tr></tr>').appendTo($body);
													$('<td></td>').appendTo($tr).html(numberWithCommas(me.amt));
													$('<td></td>').appendTo($tr).html(me.brief);
												});
											}

											$('#p_sum').html(numberWithCommas(data.sum));
									}
								});
							}

							$(document).ready(function(){
								$("#p_income > .dt_picker").datetimepicker({
									format : 'YYYY-MM-DD'
								}).on('dp.change',function(event){
							    pReloadData();
							  });
								pReloadData();
							});
						</script>

						</div>
					</div>
					<?php endif ?>
				</div>

				<div id="s3" class="tab-pane fade in">
					<?php if($l_user -> role_id == 1
										|| $l_user -> role_id == 2
										|| $l_user -> role_id == 3
										|| $l_user -> role_id == 11
										): ?>
					<div class="row">
						<div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
							<div>
								<h1>會員列表</h1>
								<input type="hidden" id="l_user_id" value="<?= $l_user -> id ?>" />
								<div>會員總數<span id="m_sum" style="color:red;"></span></div>
								<div id="m_income" class="form-inline">
							      <label>起始日期</label><br>
										<?php
										$month_ini = new DateTime("first day of this month");
										$month_end = new DateTime("last day of this month");
										?>
							      <input type="text" id="m_s_date" class="form-control dt_picker" value="<?= $month_ini->format('Y-m-d') ?>" />
										～ <input type="text" id="m_e_date" class="form-control dt_picker" value="<?= $month_end->format('Y-m-d') ?>" />
								</div>
								<table id="m_dt_list" class="table table-striped table-bordered table-hover" width="100%">
									<thead>
										<tr>
											<th class="min100">帳號</th>
											<th class="">加入時間</th>
										</tr>
									</thead>
									<tbody id="m_res_body">
									</tbody>
								</table>
							</div>

						<script>
							function mReloadData() {
								$.ajax({
									type: "POST",
									url: '<?= base_url('mgmt/corp_dash/find_all_m_user') ?>',
									data: {
										s_date: $('#m_s_date').val(),
										e_date: $('#m_e_date').val(),
										user_id: $('#l_user_id').val()
									},
									success: function(data)
									{
											$body = $('#m_res_body').empty();
											if(data.cp_list) {
												$.each(data.cp_list, function(){
													var me = this;
													$tr = $('<tr></tr>').appendTo($body);
													$('<td></td>').appendTo($tr).html(me.account);
													$('<td></td>').appendTo($tr).html(me.create_time);
												});
											}

											$('#m_sum').html(numberWithCommas(data.sum));
									}
								});
							}

							$(document).ready(function(){
								$("#m_income > .dt_picker").datetimepicker({
									format : 'YYYY-MM-DD'
								}).on('dp.change',function(event){
							    mReloadData();
							  });
								mReloadData();
							});
						</script>

						</div>
					</div>
					<?php endif ?>
				</div>

			</div>
			<!-- row -->
			<div class="row" style="display:none;">

				<!-- NEW WIDGET START -->
				<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

					<!-- Widget ID (each widget will need unique ID)-->
					<div class="jarviswidget">
						<header style="height:0px;">

						</header>

						<!-- widget div-->
						<div>

							<!-- widget edit box -->
							<div class="jarviswidget-editbox">
								<!-- This area used as dropdown edit box -->

							</div>
							<!-- end widget edit box -->

							<!-- widget content -->
							<div class="widget-body ">

								<h1><?= $corp -> sys_name ?> - 公司儀表</h1>


									<div class="widget-body-toolbar bg-color-white smart-form" id="rev-toggles">

										<div class="inline-group">

											<label for="gra-0" class="checkbox">
												<input type="checkbox" name="gra-0" id="gra-0" checked="checked">
												<i></i> 公司 </label>
											<label for="gra-1" class="checkbox">
												<input type="checkbox" name="gra-1" id="gra-1" checked="checked">
												<i></i> 股東 </label>
											<label for="gra-2" class="checkbox">
												<input type="checkbox" name="gra-2" id="gra-2" checked="checked">
												<i></i> 經理人 </label>
										</div>



									</div>

									<div class="padding-10">
										<div id="flotcontainer" class="chart-large has-legend-unique"></div>
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

<style>
.sparks-info {
	padding: 10px!important;
}
</style>
<script type="text/javascript">

	loadScript(baseUrl + "js/class/BaseAppClass.js", function(){
		loadScript(baseUrl + "js/app/corp_dash/list.js", function(){
			runAllCharts();
		});
	});

</script>
