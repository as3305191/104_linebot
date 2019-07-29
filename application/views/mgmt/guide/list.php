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
						<!-- <header>

						</header> -->

						<!-- widget div-->
						<div style="padding-bottom: 10px!important;">

							<!-- widget edit box -->
							<div class="jarviswidget-editbox">
								<!-- This area used as dropdown edit box -->

							</div>
							<!-- end widget edit box -->

							<!-- widget content -->

							<div class="widget-body" style="">
								<!--
								<style>
								.marquee {
									position:relative;
								  width: 300px;
								  overflow: hidden;
								  border: 1px solid #ccc;
								  background: #ccc;
									height: 20px;
									overflow: hidden;
								}

								.marquee div {
									position:absolute;
									top: 30px;
								}
								</style>
								<div class="marquee" >
									<?php foreach($marquee_list as $each): ?>
										<div><?= $each -> title ?></div>
									<?php endforeach ?>
								</div>
							-->
								<div id="main-frame"></div>
								<?php
									$total_amt_str = $this -> session -> userdata('total_amt_str');
									$n_str = "$param->total_amt_0-$param->total_amt_1";
									$need_refresh = FALSE;
									if(!empty($total_amt_str)) {
										if($total_amt_str != $n_str) {
											$this -> session -> set_userdata('total_amt_str', $n_str);
											$need_refresh = TRUE;
										}
									} else {
										$need_refresh = TRUE;
										$this -> session -> set_userdata('total_amt_str', $n_str);
									}

									$s_win = 0;
									if($need_refresh || empty($this -> session -> userdata('s_win'))) {
										$s_win = rand($param -> total_amt_0, $param -> total_amt_1);
									} else {
										$s_win = $this -> session -> userdata('s_win');
									}
									$s_win += rand(0, 1000);
									$s_win = ($s_win > 0 ? $s_win : 0);
									$this -> session -> set_userdata('s_win', $s_win);
								 ?>
								<h3 style="color:gray;padding-left: 20px;">今日會員總贏金額：<span style="color: #aa0000"><?= number_format($s_win) ?></span>元</h3>

								<!-- <a target="_blank" href="http://tb588.net/?&agCode=A2A312C2-4E53-D3E4-A2B9-1DDB935A020D&agCodeId=ggwrx" style="padding-left: 12px;"><img src="<?= base_url('img/guide/bt.gif') ?>" /></a> -->
								<div style="font-size:20px;padding:10px 0 0 20px;;color: #AA0000; font-weight:bolder;">在線人數 :
								<?php
									$s_online_str = $this -> session -> userdata('s_online_str');
									$n_str = "$param->online_amt_0-$param->online_amt_1";
									$need_refresh = FALSE;
									if(!empty($s_online_str)) {
										if($s_online_str != $n_str) {
											$this -> session -> set_userdata('s_online_str', $n_str);
											$need_refresh = TRUE;
										}
									} else {
										$need_refresh = TRUE;
										$this -> session -> set_userdata('s_online_str', $n_str);
									}

									$s_online = 0;
									if($need_refresh || empty($this -> session -> userdata('s_online'))) {
										$s_online = rand($param -> online_amt_0, $param -> online_amt_1);
									} else {
										$s_online = $this -> session -> userdata('s_online');
									}
									$s_online += rand(-10, 10);
									$s_online = ($s_online > 0 ? $s_online : 0);
									$this -> session -> set_userdata('s_online', $s_online);

									echo $s_online;
								 ?>
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
<div class="modal small fade" id="ynModal" tabindex="-1" role="dialog" aria-labelledby="ynModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					×
				</button>
				<h3 id="ynModalLabel">訊息</h3>
			</div>
			<div class="modal-body" id="ynModalBody">
				<div class="alert alert-warning fade in" style="font-size: 20px;">
					<i class="fa fa-warning modal-icon"></i>
					<strong>獲利已達10%</strong> 是否要結束任務?
				</div>

			</div>
			<div class="modal-footer">
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true" onclick="doCont()">
					我同意繼續操作不給負評
				</button>
				<button class="btn btn-danger" data-dismiss="modal" onclick="doFinish()">
					結束任務離場休息
				</button>
			</div>
		</div>
	</div>
</div>
<?= $l_user -> end_time ?>
<input type="hidden" id="et" value="<?= !empty($l_user -> end_time) ? $l_user -> end_time : '' ?>" />
<!-- waitubg-->
<script type="text/javascript">

$('#ynModal').on('hidden.bs.modal', function () {
	if(window._yn_option && window._yn_option == 1) {
		// $('#main-frame').load(baseUrl + 'mgmt/guide/main?set_yn=yes&com_id=' + $('#com_id').val() + '&tab_id=' + $('#tab_id').val() );

		
	}
});

	loadScript(baseUrl + "js/class/BaseAppClass.js", function(){
		loadScript(baseUrl + "js/app/guide/list.js", function(){
			loadScript(baseUrl + "js/libs/waiting-dialog.js", function(){
			loadScript("//cdn.jsdelivr.net/jquery.marquee/1.4.0/jquery.marquee.min.js", function(){
					currentApp = new GuideAppClass(new BaseAppClass({}));
					currentApp.waitingDialog = new waitingDialogCalss(jQuery);
					$('#main-frame').load(baseUrl + 'mgmt/guide/com_select');

					//$('.marquee').marquee();
					// var ms = $('.marquee').find('div');
					// var cnt = 0;
					//
					// showMarquee();
					// function showMarquee() {
					// 	if(cnt > 0) {
					// 		$(ms[cnt - 1]).animate({'top': '-30px'},500).animate({'top': '30px'},0);
					// 	}
					// 	$(ms[cnt++]).animate({'top': '0px'},500);
					// 	setTimeout(function(){
					// 		cnt = (cnt % ms.length);
					// 		if(cnt == 0) {
					// 			$(ms[ms.length - 1]).animate({'top': '-30px'},500).animate({'top': '30px'},0);
					// 		}
					// 		showMarquee();
					// 	}, 4000);
					// }

				});
			});
		});
	});

if(window._guideInt) {
	clearInterval(window._guideInt);
}

<?php if($l_user -> role_id != 1 && $l_user -> role_id != 99): ?>
var mTs = moment('<?= date('Y-m-d H:i:s') ?>');

var jTs = moment(new Date());

window._guideInt = setInterval(function(){
	if(location.hash != "#mgmt/guide" || $('#et').val().length == 0) {
		clearInterval(window._guideInt);
		return;
	}

	$.ajax({
		type: "GET",
		url: '<?= base_url('mgmt/guide/sys_time') ?>',
		success: function(data)
		{
			var end = moment($('#et').val());
			var now = moment(data.ts);
			var duration = moment.duration(end.diff(now));
			console.log(duration.asMilliseconds());
			if(duration.asMilliseconds() < 0 ) {
				clearInterval(window._guideInt);
				location.reload();
				return;
			}
			var rem = parseInt(duration.asHours()) + "小時" + moment.utc(duration.asMilliseconds()).format("m分s秒");
			$('#rem_time').text(rem);
		}
	});


}, 1000);
<?php endif ?>
</script>
