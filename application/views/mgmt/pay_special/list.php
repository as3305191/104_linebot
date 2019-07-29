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
							<?php if($login_user -> role_id == 99 || $login_user -> role_id == 1):?>
							<div class="widget-toolbar pull-left">
								<div class="btn-group">
									<button onclick="currentApp.doEdit(0)" class="btn dropdown-toggle btn-xs btn-success" data-toggle="dropdown">
										<i class="fa fa-plus"></i>新增
									</button>
								</div>
							</div>
							<?php else: ?>
							<div class="widget-toolbar pull-left">
								<div class="btn-group">
									<span id="s_total" style="color:#AA0000"></span>
								</div>
							</div>
							<?php endif ?>

							<?php if(!empty($corp_list)): ?>
								<div class="widget-toolbar pull-left">
									<div class="btn-group">
										<select name="corp_id" id="corp_id" class="form-control">
											<option value="0" disabled>無</option>
											<?php foreach($corp_list as $each): ?>
												<option value="<?= $each -> id?>" <?= isset($item) && $item -> corp_id == $each -> id ? 'selected' : '' ?> ><?=  $each -> corp_name ?>(<?=  $each -> currency ?>)</option>
											<?php endforeach ?>
										</select>
									</div>
								</div>
							<?php endif ?>

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
											<th class="min30"></th>
											<th class="min100">公司</th>
											<th class="min100">專案名稱</th>
											<th class="min100">優惠紅利%</th>
											<th class="min100">投注倍數</th>
											<th class="min50">最低購買</th>
											<th class="min50">最高購買</th>
											<th class="">結束日期</th>
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

<input type="hidden" id="l_user_role" value="<?= $login_user -> role_id ?>" />
<input type="hidden" id="l_corp_id" value="<?= $corp -> id ?>" />
<script type="text/javascript">
	loadScript(baseUrl + "js/class/BaseAppClass.js", function(){
		loadScript(baseUrl + "js/app/pay_special/list.js", function(){
			currentApp = new PaySpecialAppClass(new BaseAppClass({}));


			// if($('#l_user_role').val() != '99') {
			// 	currentApp.doEdit($('#l_corp_id').val());
			// }
		});
	});


</script>
