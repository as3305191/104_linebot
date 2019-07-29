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
							<!-- <div class="widget-toolbar pull-left">
								<div class="btn-group">
									<button onclick="currentApp.doEdit(0)" class="btn dropdown-toggle btn-xs btn-success" data-toggle="dropdown">
										<i class="fa fa-plus"></i>新增
									</button>
								</div>
							</div> -->
							<?php else: ?>

							<?php endif ?>
							<div class="widget-toolbar pull-left">
								<div class="btn-group">
									<input type="text" id="c_dt" class="dt_picker" value="<?= date("Y-m-d") ?>" />
								</div>
							</div>


							<?php if(!empty($corp_list)): ?>
								<div class="widget-toolbar pull-left">
									<div class="btn-group">
										<select name="corp_id" id="corp_id" class="form-control">
											<option value="0" disabled>無</option>
											<?php foreach($corp_list as $each): ?>
												<option value="<?= $each -> id?>" <?= isset($item) && $item -> corp_id == $each -> id ? 'selected' : '' ?> ><?=  $each -> corp_name ?></option>
											<?php endforeach ?>
										</select>
									</div>
								</div>
							<?php endif ?>

							<div class="widget-toolbar pull-left">
								<div class="btn-group">
									<select name="tab_type" id="tab_type" class="form-control">
										<option value="1">一般</option>
										<option value="2">包廳</option>
									</select>
								</div>
							</div>

							<div class="widget-toolbar pull-left">
								<div class="btn-group">
									<input type="text" id="s_user_name" class="" value="" placeholder="請輸入使用者名稱或帳號搜尋" onchange="currentApp.tableReload()" onkeyup="currentApp.tableReload()" />
								</div>
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
											<th class="min100">公司</th>
											<th class="min100">下注帳號/名稱</th>
											<th class="min100">類別</th>
											<th class="min100">桌名</th>
											<th class="min100">局號</th>
											<th class="min50">順序</th>
											<th class="min200">開牌</th>
											<th class="min100">下注</th>
											<th class="min100">下注總額</th>
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
		loadScript(baseUrl + "js/app/baccarat_bet_record/list.js", function(){
			currentApp = new BaccaratBetRecordAppClass(new BaseAppClass({}));
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
