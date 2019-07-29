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
								<div class="btn-group">
									<select id="corp_sel" class="form-control" onchange="currentApp.tableReload()">
										<?php foreach($corp_list as $each): ?>
											<option value="<?= $each -> id?>" ><?=  $each -> corp_name ?></option>
										<?php endforeach ?>
									</select>
								</div>
							</div>
							<div class="widget-toolbar pull-left">
								<span id="sum_pool_amt"></span>
							</div>
							<div class="widget-toolbar pull-left">
								<input type="number" class="form-control input-sm" style="float:left;" id="pool_diff_amt" value="" />
							</div>
							<div class="widget-toolbar pull-left">
								<button class="btn btn-sm btn-danger" onclick="currentApp.addPool()">增減彩池</button>
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

								<table id="dt_list" class="table table-striped table-bordered table-hover" width="100%">
									<thead>
										<tr>
											<th class="min100">期數</th>
											<th class="min100">狀態</th>
											<th class="min200">開始時間</th>
											<th class="min200">開獎時間</th>
											<th class="min200">排名</th>
											<th class="min100">下注總額</th>
											<th class="min100">獎金總額</th>
											<th class="min100">彩池增減</th>
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
		loadScript(baseUrl + "js/app/racing_game_list/list.js", function(){
			currentApp = new RacingGameListAppClass(new BaseAppClass({}));
		});
	});
</script>
