<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url('css/guide/share.css') ?>">
<style>
.loader {
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 30px;
  height: 30px;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
}

@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>

	<div class="container-fluid">
		<div class="row">
			<h2 style="margin-left">
				<a href="javascript:void(0)" class="btn " onclick="goMenu()">遊戲選單</a> /
				<a href="javascript:void(0)" class="btn " onclick="goTabMenu(<?= $company -> id ?>)"><?= $company -> company_name ?></a> /
				<?= $tab_name ?>
			</h2>
		</div>
			<div class="row">
				<!-- <div class="btn-group">
					<?php for($i = 1; $i <= 16; $i++ ): ?>
						<a href="javascript:void(0);" class="col mcol" data-id="<?= $i ?>" >
							<img width="150" src="<?= base_url('img/guide/btn_casino.png') ?>" class="img-box" />
							<div class="caption">
								<?= $i ?>
							</div>
						</a>
					<?php endfor ?>
				</div> -->
				<input type="hidden" id="com_id" value="<?= $com_id ?>" />
				<input type="hidden" id="tab_id" value="<?= $tab_id ?>" />
				<div>剩餘時間:<span id="rem_time" style="color:red;">---</span></div>
				<form>
					<div class="form-horizontal">
							<fieldset>
								<div class="form-group">
									<label class="col-md-3 control-label">本金設置</label>
									<div class="col-md-6">
										<input type="number" id="base_amt" class="form-control" value="<?= isset($item) ? $item -> base_amt : '' ?>" <?= isset($item) ? 'readonly' : '' ?> placeholder="建議5000以上" />
									</div>
								</div>
							</fieldset>
							<fieldset>
								<div class="form-group">
									<label class="col-md-3 control-label">該桌勝率</label>
									<div class="col-md-6">
										<p class="form-control-static"><?= floatval(rand(550, 719) / 10.0)  ?>%</p>
									</div>
								</div>
							</fieldset>

							<?php if(!empty($item)): ?>
								<fieldset>
									<div class="form-group">
										<label class="col-md-3 control-label">目前輸贏</label>
										<div class="col-md-6">
											<input type="number" id="" class="form-control" value="<?= isset($item) ? $item -> balance : '' ?>" readonly />
										</div>
									</div>
								</fieldset>

								<fieldset>
									<div class="form-group">
										<label class="col-md-3 control-label">預測</label>
										<div class="col-md-6"><div class="loader" style=""></div></div>
										<div id="pred" class="col-md-6">

											<?php
											$rnd_str = "";
											$rnd_str1 = "";
											$rnd_str2 = "";
											$rnd = rand(1, 100);
											$rnd1 = rand(1, 100);
											$rnd2 = rand(1, 100);

                      $is_win_count = $this -> session -> userdata('is_win_count');
                  		$is_win_count = empty($is_win_count) ? 0 : $is_win_count;

                  		$banance_diff = $this -> session -> userdata('banance_diff');
                  		$banance_diff = empty($banance_diff) ? 0 : $banance_diff;

                      $bet_balance = 50;
                      $bet_balance += $banance_diff;

											if(1 <= $rnd1 && $rnd1 <= 15) {
												$rnd_str1 = "和";
											}
											if(1 <= $rnd2 && $rnd2 <= 10) {
												$rnd_str2 = "對子";
											}
											if(1 <= $rnd && $rnd <= $bet_balance) {
												$rnd_str = "莊";
											}
											if(($bet_balance + 1) <= $rnd && $rnd <= 100) {
												$rnd_str = "閒";
											}

                      // reverse
                      // if($is_win_count >= 4) {
                      //   if($rnd_str == "莊") {
                      //     $rnd_str = "閒";
                      //   } else {
                      //     $rnd_str = '莊';
                      //   }
                      // }

											?>
                      <input type="hidden" id="bet_balance" value="<?= $bet_balance ?>" />
											<?php if(!empty($rnd_str)):?>
												<p id="rnd_str" class="form-control-static btn btn-default btn-lg pull-left"><?= $rnd_str  ?></p>
											<?php endif ?>
											<?php if(!empty($rnd_str1)):?>
												<p class="form-control-static btn btn-default btn-lg pull-left"><?= $rnd_str1  ?></p>
											<?php endif ?>
											<?php if(!empty($rnd_str2)):?>
												<p class="form-control-static btn btn-default btn-lg pull-left"><?= $rnd_str2  ?></p>
											<?php endif ?>
											<?php if($item -> balance >= ($item -> base_amt * 1.1)): ?>
												<p style="margin: 5px 0 0 10px; color:#AA0000!important;" class="form-control-static pull-left">建議提領休息，結束任務</p>
												<?php if(empty($this -> session -> userdata('s_yn'))): ?>
													 <script>
													 window._yn_option = 0;
													 $('#ynModal').modal('show');
													 </script>
												<?php endif ?>
											<?php else: ?>
												<?php
													$this -> session -> set_userdata('s_yn', ''); // clear yn
												 ?>
											<?php endif ?>
											<?php if($item -> balance <= ($item -> base_amt * 0.4)): ?>
												<p style="margin: 5px 0 0 10px; color:#AA0000!important;" class="form-control-static pull-left">建議結束任務，修改本金換桌</p>
											<?php endif ?>
										</div>
									</div>
								</fieldset>
								<script>
								$('#pred').hide();
								$('.loader').fadeOut('slow', function(){
									$('#pred').fadeIn('fast');
								}); </script>
								<fieldset>
									<div class="form-group">
										<label class="col-md-3 control-label">下注金額</label>
										<div class="col-md-6">
											<input type="number" id="bet_amt" class="form-control" value="<?= $s_amt ?>" placeholder="" />
										</div>
									</div>
								</fieldset>

								<style>

								</style>
								<fieldset>
									<div class="form-group">
										<label class="col-md-3 control-label">輸贏</label>
										<div class="col-md-6">
											<div class="btn-group" data-toggle="buttons">
											  <label class="btn btn-default btn-lg ">
											    <input type="radio" name="is_win" value="1" autocomplete="off"> 贏
											  </label>
											  <label class="btn btn-default btn-lg ">
											    <input type="radio" name="is_win" value="-1" autocomplete="off"> 輸
											  </label>
												<label class="btn btn-default btn-lg ">
												 <input type="radio" name="is_win" value="0" autocomplete="off"> 和
											 	</label>
											</div>
										</div>
									</div>
								</fieldset>

								<fieldset>
									<div class="form-group">
										<label class="col-md-3 control-label"></label>
										<div class="col-md-6">
											<button class="btn btn-danger" type="button" onclick="doSendResult()">送出結果</button>
											<button class="btn btn-danger" type="button" onclick="doFinish()">任務結束</button>
										</div>
									</div>
								</fieldset>


							<?php endif ?>

							<?php if(!isset($item)): ?>
								<fieldset>
									<div class="form-group">
										<label class="col-md-3 control-label"></label>
										<div class="col-md-6">
											<button class="btn btn-danger" type="button" onclick="doStart()">開始</button>
										</div>
									</div>
								</fieldset>
							<?php endif ?>
						</div>
				</form>

			</div>
		</div>

		<script>
		function goTabMenu(comId) {
			$('#main-frame').load(baseUrl + 'mgmt/guide/table_select?com_id=' +  comId);
		}

		function doSendResult() {
			if($('#bet_amt').val().length == 0) {
				alert('請輸入金額');
				return;
			}

			if($('input[name=is_win]:checked').length == 0) {
				alert('請先選擇輸贏');
				return;
			}

			if(!confirm('是否確定送出結果？')) {
				return;
			}

			$.ajax({
		    type: 'POST',
		    url: '<?= base_url() ?>mgmt/guide/send_result',
		    data: {
					com_id: $('#com_id').val(),
					tab_id: $('#tab_id').val(),
					bet_amt: $('#bet_amt').val(),
					is_win: $('input[name=is_win]:checked').val(),
          rnd_str: $('#rnd_str').text(),
          bet_balance: $('#bet_balance').val()
				},
		    dataType: 'json',
		    success: function (data) {
						if(data && data.is_finish) {
							alert('任務結束');
							$('#main-frame').load(baseUrl + 'mgmt/guide/com_select');
							return;
						}

		        if(data && data.last_id) {
							$('#main-frame').load(baseUrl + 'mgmt/guide/main?com_id=' + $('#com_id').val()
							+ '&tab_id=' + $('#tab_id').val(), function(){

							});
						}
		    }
			});
		}

		function doFinish() {
			if($('#bet_amt').val().length == 0) {
				alert('請輸入金額');
				return;
			}

			if(!confirm('是否確定結束？')) {
				return;
			}

			$.ajax({
		    type: 'POST',
		    url: '<?= base_url() ?>mgmt/guide/send_finish',
		    data: {
					com_id: $('#com_id').val(),
					tab_id: $('#tab_id').val()
				},
		    dataType: 'json',
		    success: function (data) {
					$('#main-frame').load(baseUrl + 'mgmt/guide/com_select');
		    }
			});
		}

		function doCont() {
			window._yn_option = 1;

      $.ajax({
  			type: "GET",
  			url: '<?= base_url('mgmt/guide/set_yn_session') ?>',
  			success: function(data)
  			{
  				console.log(data);
          $('#ynModal').modal('hide');
  			}
  		});

		}

		function doStart() {
			if($('#base_amt').val().length == 0) {
				alert('請輸入金額');
				return;
			}
			$.ajax({
		    type: 'POST',
		    url: '<?= base_url() ?>mgmt/guide/start',
		    data: {
					com_id: $('#com_id').val(),
					tab_id: $('#tab_id').val(),
					base_amt: $('#base_amt').val()
				},
		    dataType: 'json',
		    success: function (data) {
		        if(data && data.last_id) {
							$('#main-frame').load(baseUrl + 'mgmt/guide/main?com_id=' + $('#com_id').val()
							+ '&tab_id=' + $('#tab_id').val(), function(){

							});
						}
		    }
			});
		}
		</script>
