<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url('css/guide/share.css') ?>">
<style>

</style>
<div class="container-fluid">
	<div class="row">
		<h2 style="margin-left">
			<a href="javascript:void(0)" class="btn " onclick="goMenu()">遊戲選單</a> /
			<?= $company -> company_name ?>
		</h2>
		<h3>請使用兩個帳號分別不同ＩＰ進行免水百家樂，閒家與莊家都下注同樣的金額，本系統僅預測莊家不開6點獲勝之學術研究，不附帶任何責任。</h3>
	</div>
	<div class="row">
		<button onclick="doSync()" class="btn btn-info"><i class="fa fa-user"></i>同步</button>
		<br/>
		<br/>
	</div>
	<div class="btn-group btn-matrix t-box">
		<?php foreach($tab_list as $each): ?>
			<a type="button" class="btn btn-default" data-id="<?= $each -> tab_id ?>">
				<table class="table table-striped">
					<tbody>
						<tr>
							<td><?= $each -> tab_name ?></td>
						</tr>
						<tr>
							<td class="ta_right"><?= $each -> cp_val / 100.0 ?>%</td>
						</tr>
						<tr>
							<td>
								<?php if($each -> cp_val > 1500): ?>
									<span class="t_red">不可下注</span>
								<?php else: ?>
									<span class="t_green">可下注</span>
								<?php endif ?>
							</td>
						</tr>
					</tbody>
				</table>
			</a>
		<?php endforeach ?>
	</div>
</div>

<script>
// var cTableId;
// $('.t-box a').on('click', function(){
// 	cTableId = $(this).data('id');
// 	currentApp.waitingDialog.show( '匯入資料中...');
// 	setTimeout(function(){
// 		currentApp.waitingDialog.hide();
// 		$('#main-frame').load(baseUrl + 'mgmt/water/main?clear_yn=yes&com_id=' + cCompanyId
// 		+ '&tab_id=' + cTableId, function(){
// 			currentApp.waitingDialog.hide();
// 		});
// 	}, 8000);
// });

function goMenu(comId) {
	$('#main-frame').load(baseUrl + 'mgmt/water/com_select', function(){
		console.log('goMenu');
	});
}

function doSync() {
	var url = baseUrl + 'mgmt/water/do_sync?com_id=' + <?= $company -> id ?>;
	$.ajax({
		url: url,
		type: 'GET',
		dataType: 'json',
		success: function(data){
			console.log(data);
			if(data.error_message) {
				alert(data.error_message);
			}
		}
	});
}
</script>
