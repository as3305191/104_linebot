<style>
.file-drag-handle {
	display: none;
}
</style>
<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget" id="wid-id-7" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
	<header>
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="back_parent" onclick="save_coin()" class="btn btn-default btn-danger">
				<i class="fa fa-save"></i>更新
			</a>
		</div>
		<div class="widget-toolbar pull-left">
			公司拆帳：<span id="config_com_pct"><?= !empty($config) ? $config -> com_pct : 0 ?></span>
		</div>
		<div class="widget-toolbar pull-left">
			一般拆帳：<span id="config_normal_pct"><?= !empty($config) ? $config -> normal_pct : 0 ?></span>
		</div>
		<div class="widget-toolbar pull-left">
			全盤拆帳：<span id="config_overall_pct"><?= !empty($config) ? $config -> overall_pct : 0 ?></span>
		</div>
		<div class="widget-toolbar pull-left">
			一般中獎：<span id="config_normal_winning"><?= !empty($config) ? $config -> normal_winning : 0 ?></span>
		</div>
		<div class="widget-toolbar pull-left">
			全盤中獎：<span id="config_overall_winning"><?= !empty($config) ? $config -> overall_winning-$config -> normal_winning : 0 ?></span>
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
		<div class="widget-body">

			<form id="app-edit-form" method="post" class="form-horizontal">


				<fieldset>
					<span id="" style="color:red">注意!!如要更改拆帳部分請同時填寫</span>
					<div class="form-group">
						<label class="col-md-3 control-label">公司拆帳：</label>
						<div class="col-md-6">
							<input type="text" id="com_pct"  class="form-control"  value="" placeholder="如果1%請輸入0.01以此類推"/>
							<span id="" style="color:red">COC版本公司拆帳 1/3介紹人拆分往上 1/3公司 1/3消滅</span>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">一般拆帳：</label>
						<div class="col-md-6">
							<input type="text" id="normal_pct"  class="form-control" value="" placeholder="如果1%請輸入0.01以此類推"/>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">全盤拆帳：</label>
						<div class="col-md-6">
							<input type="text" id="overall_pct"  class="form-control" value="" placeholder="如果1%請輸入0.01以此類推"/>
						</div>
					</div>
				</fieldset>
			<hr/>
				<fieldset>
					<span id="" style="color:red">注意!!如要更改中獎部分請同時填寫</span>
					<div class="form-group">
						<label class="col-md-3 control-label">一般中獎：</label>
						<div class="col-md-6">
							<input type="text" id="normal_winning"  class="form-control" value="" placeholder="如果1%請輸入1以此類推" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">全盤中獎：</label>
						<div class="col-md-6">
							<input type="text" id="overall_winning"  class="form-control" value="" placeholder="如果1%請輸入1以此類推"/>
						</div>
					</div>
				</fieldset>


			</form>

		</div>
		<!-- end widget content -->

	</div>
	<!-- end widget div -->

</div>
<!-- end widget -->
<style>
	.kv-file-zoom {
		display: none;
	}
</style>
<script>


	function save_coin() {
			var url = '<?= base_url() ?>' + 'mgmt/set_up_super8/update';

			$.ajax({
				url : url,
				type: 'POST',
				data: {
					com_pct: $('#com_pct').val(),
					normal_pct: $('#normal_pct').val(),
					overall_pct: $('#overall_pct').val(),
					normal_winning: $('#normal_winning').val(),
					overall_winning: $('#overall_winning').val()
				},
				dataType: 'json',
				success: function(d) {
					// console.log(d);
					if(d.success=="true") {
						location.reload();
					}
					if (d.err=="true") {
						alert('拆帳部分請同時填寫');
					}
				 	if (d.err1=="true") {
						alert('中獎部分請同時填寫');
					}
					if (d.success1=="true") {
						alert('拆帳部分設定錯誤');
					}
					if (d.success1=="true1") {
						alert('中獎部分設定錯誤');
					}

					// $("#current_point").text(d.current_point);
					// $("#current_ntd").text(d.current_ntd);
				}
				// failure:function(){
				// 	alert('faialure');
				// }
			});
		}


</script>
