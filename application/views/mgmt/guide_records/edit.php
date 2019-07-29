
	<!-- Widget ID (each widget will need unique ID)-->
	<div class="row">
		<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="jarviswidget" id="" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
				<header>
					<div class="widget-toolbar pull-left">
						<a href="javascript:void(0);" id="back_parent" onclick="currentApp.backTo()" class="btn btn-default ">
							<i class="fa fa-arrow-circle-left"></i>返回
						</a>
					</div>
				</header>

				<!-- widget div-->
				<div>
					<!-- widget edit box -->
					<div class="jarviswidget-editbox">
						<!-- This area used as dropdown edit box -->
						<input class="form-control" type="text">
					</div>
					<!-- end widget edit box -->

					<!-- widget content -->
					<div class="widget-body form-horizontal">
						<div class="table-responsive">
							<table id="dt_list" class="table table-striped table-bordered table-hover" width="100%">
								<thead>
									<tr>
										<th class="min50">項次</th>
										<th class="min100">預測</th>
										<?php if($login_user -> role_id == 99): ?>
											<th class="min100">莊機率</th>
											<th class="min100">閒機率</th>
										<?php endif ?>
										<th class="min100">輸贏</th>
										<th class="min100">下注金額</th>
										<th class="min100">餘額</th>
										<th class="">建立時間</th>
									</tr>

								</thead>
								<tbody>
									<?php
											$cnt = 1;
											foreach($list as $each): ?>
										<tr>
											<td><?= $cnt++ ?></td>
											<td><?= $each -> 	guess ?></td>
											<?php if($login_user -> role_id == 99): ?>
												<td><?= $each -> 	p_banker ?></td>
												<td><?= $each -> 	p_player ?></td>
											<?php endif ?>
											<td><?= is_win_str($each -> is_win) ?></td>
											<td><?= $each -> 	bet_amt ?></td>
											<td><?= $each -> 	result_amt ?></td>
											<td><?= $each -> 	create_time ?></td>
										</tr>
									<?php endforeach ?>
								</tbody>
							</table>
						</div>

					</div>
					<!-- end widget content -->

				</div>
				<!-- end widget div -->

			</div>
			<!-- end widget -->
		</article>
	</div>
