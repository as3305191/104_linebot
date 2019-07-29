<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url('css/guide/share.css') ?>">
<style>

</style>
<div class="container-fluid">
	<div class="row">
		<h2 style="margin-left">
			<a href="javascript:void(0)" class="btn " onclick="goMenu()">遊戲選單</a> /
			<?= $company -> company_name ?>
		</h2>
		<h3>請選擇該遊戲系統的桌號</h3>
	</div>
	<div class="btn-group btn-matrix t-box">
		<?php foreach($tab_list as $each): ?>
			<a type="button" class="btn btn-default" data-id="<?= $each -> tab_id ?>"><?= $each -> tab_name ?></a>
		<?php endforeach ?>
	</div>
</div>

<script>
var cTableId;
$('.t-box a').on('click', function(){
	cTableId = $(this).data('id');
	currentApp.waitingDialog.show( '匯入資料中...');
	setTimeout(function(){
		currentApp.waitingDialog.hide();
		$('#main-frame').load(baseUrl + 'mgmt/guide/main?clear_yn=yes&com_id=' + cCompanyId
		+ '&tab_id=' + cTableId, function(){
			currentApp.waitingDialog.hide();
		});
	}, 8000);
});

function goMenu(comId) {
	$('#main-frame').load(baseUrl + 'mgmt/guide/com_select');
}
</script>
