<style>
	body, html {
			height: 100%;
	}

	#extr-page #main {
		height:100%;
		position: absolute;
		background-size: cover;
		padding: 0px;
	}

	#main_body {
		background-image:url('<?= base_url('img/v/dash/btn-01.png') ?>');
		height:45px;
		color: white;
		font-size: 20px;
		background-size: cover;
	}

	.line_01_box {
		text-align: center;
	}

	.line_01 {
		background-image:url('<?= base_url('img/v/login/btn-account-01.png') ?>');
		width: 230px;
		height:45px;
		line-height: 45px;
		margin: 0 auto;
		color: #4f3019;
		font-size: 16px;
		background-size: contain;
		background-position: center center;
		background-repeat: no-repeat;
		text-align: center;
		text-shadow:
			1px  1px 2px white,
			1px -1px 2px white,
		 -1px  1px 2px white,
		 -1px -1px 2px white;
	}

	.line_04 {
		background-image:url('<?= base_url('img/v/dash/btn-04.png') ?>');
		width: 220px;
		max-width: 80%;
		height:30px;
		margin: 0 auto;
		background-size: contain;
		background-position: center center;
		background-repeat: no-repeat;
	}

	label.error {
		color: red;
	}

	.a_bg {
		position: relative;
		background-repeat: no-repeat;
		width: 100%;
	}
	.a_box {
		position: relative;
		height: 34px;
		width: 90%;
		max-width: 400px;
		margin: 0 auto;
	}
	.a_bg img {
		position: absolute;
		top: 0px;
		left: 0px;
		height: 34px;
		width: 100%;
		margin: 0 auto;
		border: 0px;
	}
	.a_bg input, .a_bg select {
		position: absolute;
		top: 0px;
		left: 0px;
		height: 34px;
		width: 100%;
		border: 0px;
		margin: 0 auto;
		padding-left: 10px;
		font-size: 16px;
		color: white;
		background: none;
	}

	.btn_box_outer {
		position: relative;
		height: 34px;
		width: 330px;
		margin:0px auto;
		max-width: 90%;
	}
	.btn_box {
		position: relative;
		height: 34px;
		width: 40px;
		max-width: 400px;
	}
	.btn_box img {
		position: absolute;
		top: 0px;
		left: 0px;
		height: 20px;
		width: 40px;
		margin: 10px auto;
		border: 0px;
	}

	.n_label {
		padding: 8px;
		background: -webkit-linear-gradient(#FFEC46, #EA8D2B);
		-webkit-background-clip: text;
		-webkit-text-fill-color: transparent;
	}

	.rt_label_1 {
		padding: 8px;
		background: -webkit-linear-gradient(#FFEC46, #EA8D2B);
		-webkit-background-clip: text;
		-webkit-text-fill-color: transparent;
		text-align: center;
	}

	.rt_label_3 {
		padding: 0px;
		font-size: 24px;
		background: -webkit-linear-gradient(#FFEC46, #ED5D16, #FFEC46);
		-webkit-background-clip: text;
		-webkit-text-fill-color: transparent;
		text-align: center;
	}

	.rt_label_2 {
		padding: 0px 0px 8px;
		color: white;
		text-align: center;
		font-size: 12px;
	}

	#r_top {
		background-image:url('<?= base_url('img/v/dash/btn-02.png') ?>');
	}
	#p1 {
		background-image:url('<?= base_url('img/v/dash/btn-08.png') ?>');
		min-height: 150px;
		width: 50%;
		float: left;
	}
	#p2 {
		background-image:url('<?= base_url('img/v/dash/btn-10.png') ?>');
		min-height: 150px;
		width: 50%;
		float: left;
	}
	#p3 {
		background-image:url('<?= base_url('img/v/dash/btn-12.png') ?>');
		min-height: 150px;
		width: 50%;
		float: left;
	}
	#p4 {
		background-image:url('<?= base_url('img/v/dash/btn-14.png') ?>');
		min-height: 150px;
		width: 50%;
		float: left;
	}
	#p5 {
		background-image:url('<?= base_url('img/v/dash/btn-16.png') ?>');
		min-height: 150px;
		width: 50%;
		float: left;
	}
	#p6 {
		background-image:url('<?= base_url('img/v/dash/btn-17.png') ?>');
		min-height: 150px;
		width: 50%;
		float: left;
		background-size: contain;
		background-position: center center;
		background-repeat: no-repeat;
		background-color: #F0C1BC;
	}
	#p7 {
		background-image:url('<?= base_url('img/ad/20141024173054738.gif') ?>');
		min-height: 150px;
		width: 50%;
		float: left;
		background-size:contain;
		background-position: center center;
		background-repeat: no-repeat;
		background-color: #21426b;
	}
	#p8 {
		min-height: 150px;
		width: 50%;
		float: left;
		background-color: #8dc21f;
	}
</style>

<div class="tab-content">
	<div id="main_body">
		<div class="btn_box_outer">
			<div class="btn_box pull-left">
				<img src="<?= base_url('img/v/dash/h_left.png') ?>" />
			</div>
			<div class="btn_box pull-right">
				<img src="<?= base_url('img/v/dash/h_right.png') ?>" />
			</div>
			<div style="text-align:center;">
				<div class="n_label"><?= $corp -> sys_name ?></div>
			</div>
		</div>
	</div>

	<div id="main" role="main" style="margin-left:0px;">
		<div id="r_top">
			<div class="line_04"></div>
			<div class="rt_label_3">今日會員總贏金額</div>
			<div class="rt_label_1">
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
					$this -> session -> set_userdata('s_win', $s_win);
					$win_num = number_format($s_win);
					$win_num_arr = str_split($win_num);

				?>
				<img src="<?= base_url('img/v/dash/dollar.png') ?>" height="34" />
				<?php foreach ($win_num_arr as $each): ?>
					<img src="<?= base_url("img/v/dash/$each.png") ?>" height="30" />
				<?php endforeach ?>
			</div>
			<div class="line_04"></div>
			<div class="rt_label_2">在線人數:
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

					echo number_format($s_online);
				 ?>人
			</div>
		</div>
		<div id="r_row_1">
			<div id="p1">
				<a style="text-align:center;" href="#mgmt/user_edit" onclick="">
					<div style="width: 150px; margin: 15px auto;">
						<img style="float: left;" src="<?= base_url("img/v/dash/btn-07.png") ?>" height="70" />
						<div style="float: left; color: white; font-size: 20px; margin: 10px 0 0 0;">馬上推廣<br/>成功使用</div>
					</div>
					<div style="clear:both; color: yellow; font-size: 24px;">送20%點數</div>
					<div style="clear:both; color: white; font-size: 12px;">立即推薦推廣網址 轉貼</div>
				</a>
			</div>
			<div id="p2">
				<a style="text-align:center;" href="#mgmt/pay_records" >
					<div style="width: 150px; margin: 20px auto;">
						<img style="float: left;" src="<?= base_url("img/v/dash/btn-09.png") ?>" height="70" />
						<div style="float: left; color: white; font-size: 20px; margin: 10px 0 0 0;">電子錢包<br/>正式啟用</div>
					</div>
					<div style="padding-top: 10px;clear:both; color: white; font-size: 12px;">可提款  可交易</div>
					<div style="clear:both; color: white; font-size: 12px;">可購買  可賺點</div>
				</a>
			</div>
			<div id="p3">
				<a style="text-align:center;" href="#mgmt/user_edit" onclick="">
					<div style="width: 150px; margin: 20px auto;">
						<img style="float: left;" src="<?= base_url("img/v/dash/btn-11.png") ?>" height="70" />
						<div style="float: left; color: white; font-size: 20px; margin: 10px 0 0 0; text-align:left;">火速繳費<br/><span style="font-size: 16px;">升級經理人</span></div>
					</div>
					<div style="padding-top: 10px;clear:both; color: white; font-size: 12px;">線下推廣無限領取10%點數</div>
				</a>
			</div>
			<div id="p4">
				<a style="text-align:center;" href="http://line.me/ti/p/@jpd3896u" target="_blank">
					<div style="width: 150px; margin: 20px auto;">
						<img style="float: left;" src="<?= base_url("img/v/dash/btn-13.png") ?>" height="60" />
						<div style="float: left; color: white; font-size: 20px; margin: 10px 0 0 0; text-align:left;">直營加盟<br/><span style="font-size: 16px;">馬上開店賺錢</span></div>
					</div>
					<div style="padding-top: 10px;clear:both; color: white; font-size: 12px;">快速・穩定．被動收入好選擇</div>
				</a>
			</div>
			<div id="p5">
				<a style="text-align:center;" href="http://tb588.net/?p=GGWRX" target="_blank">
					<div style="width: 160px; margin: 20px auto;">
						<img style="float: left;" src="<?= base_url("img/v/dash/btn-15.png") ?>" height="70" />
						<div style="float: left; color: white; font-size: 20px; margin: 10px 0 0 0; text-align:left;">優質娛樂城<br/><span style="font-size: 16px;">廣告推薦</span></div>
					</div>
					<div style="padding-top: 10px;clear:both; color: white; font-size: 12px;">通博娛樂城註冊送100點</div>
				</a>
			</div>
			<div id="p6">
				<a style="text-align:center; display: block; width: 100%; min-height: 150px;" style="" href="http://tb588.net/?p=GGWRX" target="_blank">
				</a>
			</div>
			<div id="p7">
				<a style="text-align:center; display: block; width: 100%; min-height: 150px;" href="http://dd52956.ju11.net/" target="_blank">
					<div style="width: 160px; margin: 20px auto;">
						<div style="float: left; color: white; font-size: 20px; margin: 10px 0 0 10px; text-align:left;"><br/><span style="font-size: 16px;"></span></div>
					</div>
				</a>
			</div>
			<div id="p8">
				<a style="text-align:center; display: block; width: 100%; min-height: 150px;" href="http://line.me/ti/p/@jpd3896u" target="_blank">
					<div style="width: 160px; margin: 20px auto;">
						<div style="float: left; color: white; font-size: 20px; margin: 10px 0 0 10px; text-align:left;">娛樂城廣告招募<br/><span style="font-size: 16px;">馬上詢問</span></div>
					</div>
				</a>
			</div>
		</div>

	</div>
</div>
<?php $this -> load -> view('general/delete_modal'); ?>
<?php $this -> load -> view('general/speech_modal'); ?>
<script type="text/javascript">
	loadScript(baseUrl + "js/class/BaseAppClass.js", function(){
		loadScript(baseUrl + "js/app/dashboard/list.js", function(){

		});
	});
</script>
