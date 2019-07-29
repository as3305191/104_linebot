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
							<?php if($login_user -> role_id == 99):?>

							<?php else: ?>

							<?php endif ?>
							<div class="widget-toolbar pull-left">
								<div class="btn-group">
									<input type="text" id="c_dt" class="dt_picker" value="<?= date("Y-m-d") ?>" />
								</div>
							</div>


							<div class="widget-toolbar pull-left">
								<div class="btn-group">
									<input type="text" id="s_user_name" class="" value="" placeholder="請輸入使用者名稱或帳號搜尋" onchange="currentApp.tableReload()" onkeyup="currentApp.tableReload()" />
								</div>
							</div>
							<div class="widget-toolbar pull-left">
								<select class="form-control" id="s_tab_id">
								<?php foreach($tab_list as $each): ?>
										<option value="<?= $each -> id ?>"><?= $each -> tab_name ?></option>
								<?php endforeach ?>
								</select>
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
											<th class="min100">下注帳號/名稱</th>
											<th class="min100">類型</th>
											<th class="min100">桌名</th>
											<th class="min100">FreeGame</th>
											<th class="min200">開牌</th>
											<th class="min100">下注</th>
											<th class="min100">贏分</th>
											<th class="min100">派彩</th>
											<th class="min100">建立時間</th>
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
<style>
.cs {
	width: 66px;
	height: 102px;
	display: block;
	float: left;
	font-size: 20px;
	zoom: 0.6;
}
<?php $m_cnt = 0;?>
<?php for($i = 0 ; $i < 4 ; $i++): ?>
	<?php for($j = 0 ; $j < 13 ; $j++): ?>

		<?= ".cs_{$m_cnt}" ?> {
			background: url('<?= base_url('img/baccarat/card_spritesheet.png') ?>') -<?= $j * 66 ?>px -<?= $i * 102 ?>px;
			width: 66px;
    	height: 102px;
			display: block;
			float: left;
			zoom: 0.6;
		}
		<?php $m_cnt++;?>
	<?php endfor ?>
<?php endfor ?>
</style>
<script type="text/javascript">
	loadScript(baseUrl + "js/class/BaseAppClass.js", function(){
		loadScript(baseUrl + "js/app/slot_sun_bet/list.js", function(){
			currentApp = new SlotSunBetAppClass(new BaseAppClass({}));
			// if($('#l_user_role').val() != '99') {
			// 	currentApp.doEdit($('#l_corp_id').val());
			// }

			$(".dt_picker").datetimepicker({
				format : 'YYYY-MM-DD'
			}).on('dp.change',function(event){
				currentApp.tableReload();
			});
		});
	});
</script>
