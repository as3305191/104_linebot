<style>
.mtable td{
	text-align: center;
}
.mtable td.active{
	background-color: yellow!important;
	color: red;
}
table.mtable {
}

.c88 {
		color: white;
		background-color: blue;
}
.co88 {
		color: white;
		background-color: red;
}
.sp {
	color: red;
	font-weight: 400;
}
</style>
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
								<h1 style="margin-left:10px; line-height:50px;">老虎機演算法測試</h1>
								<table class="table table-bordered mtable" style="width: 400px;">
									<tr>
										<td>(wild) 百搭</td>
										<td>(free) 特殊遊戲</td>
									</tr>
								</table>
								<button onclick="window.location.hash = window.location.hash.split('?')[0] + '?' + (new Date()).getTime()">重新產生結果</button>
								<div><?= $slot_bet_id ?></div>

								<!-- draw rs -->
								<h3>此局結果</h3>

								<div class="">
									公司<?= $corp_id ?><br/>
									廳別<?= $hall_id ?><br/>
									桌號<?= $tab_id ?><br/>
									下注<?= $bet_amt ?><br/>
									贏得<?= $win_amt ?><br/>
									派彩<?= $total_amt ?>
									<hr/>
									SUM_FREE_GAME <?= $sum_free_game ?><br/>
									<hr/>
									彩池（前）<?= $pool_before ?> | <?= $pool_before_sp ?><br/>
									彩池（後）<?= $pool_after ?> | <?= $pool_after_sp ?><br/>
									重算次數 <?= $l_cnt ?>
								</div>
								<div class="sp">
									<?php if($is_special): ?>
										-----> 進入特殊遊戲<br/>
										<button onclick="enterFree(<?= $slot_bet_id ?>)">進入特殊遊戲</button>
										總倍數<?= $multiply_count ?><br/>
										總局數<?= $round_count ?><br/>
										<?php foreach($result_arr as $each): ?>
											-- <?= $each -> type ?> : <?= $each -> value ?><br/>
										<?php endforeach ?>
										贏局數<?= $rs_round_count ?><br/>
										贏金(預計)<?= $tmp_pool ?><br/>
										贏金額<?= $rs_win_amt ?><br/>
										輸局數<?= $append_round_count ?><br/>
										總局數<?= count($sp_rs_arr) ?><br/>
									<?php endif ?>
								</div>
								<div>
									<table class="table table-bordered mtable" style="width: 200px;">
										<?php for($j=0; $j < 3; $j++):?>
											<tr>
											<?php for($i=0; $i < 5; $i++):?>
												<td class="<?= $rs[$i][$j] == 'free' ? ($i % 2 == 0 ? 'co88' : 'c88') : '' ?>"><?= $rs[$i][$j] ?></td>
											<?php endfor ?>
											</tr>
										<?php endfor ?>
									</table>
								</div>

								<hr/>
								<h3>中獎結果 : <?= count($match_arr) ?></h3>

								<?php for($idx = 0 ; $idx < count($match_arr) ; $idx++):?>
									<? $match_coord = $match_arr[$idx];  ?>
									<? $origin_coord = $origin_arr[$idx];  ?>
									<div class="float: left;">
										<h5>中獎組合</h5>
										<table class="table table-bordered mtable" style="width: 200px;">
											<?php for($j=0; $j < 3; $j++):?>
												<tr>
												<?php for($i=0; $i < 5; $i++):?>
													<td class="<?= count($origin_coord) > $i && $origin_coord[$i] == $j ? 'active' : '' ?>"><?= $rs[$i][$j] ?></td>
												<?php endfor ?>
												</tr>
											<?php endfor ?>
										</table>
									</div>
									<div class="float: left;">
										<h5>已中獎部分: 倍數= <?= $multiply_arr[$idx] ?></h5>
										<table class="table table-bordered mtable" style="width: 200px; float: left;">
											<?php for($j=0; $j < 3; $j++):?>
												<tr>
												<?php for($i=0; $i < 5; $i++):?>
													<td class="<?= count($match_coord) > $i && $match_coord[$i] == $j ? 'active' : '' ?>"><?= $rs[$i][$j] ?></td>
												<?php endfor ?>
												</tr>
											<?php endfor ?>
										</table>
									</div>
									<div style="clear:both;"></div>
									<hr/>
								<?php endfor ?>

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
<script type="text/javascript">


	function enterFree(slotBetId) {
		$.ajax({
			type: "POST",
			url: '<?= base_url('api/slot_game/enter_free_game') ?>',
			data: {
				'slot_bet_id' : slotBetId
			}, // serializes the form's elements.
			success: function(data)
			{
					console.log(data);
					location.reload();
			}
		});
	}
</script>
