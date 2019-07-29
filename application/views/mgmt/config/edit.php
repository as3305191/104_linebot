<style>

	input[disabled] {
	  background-color: #DDD;
		color: #EEE;
	}
</style>
<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget" id="wid-id-7" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
	<header>
		<!-- <h2>編輯選單</h2> -->

		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="submit_btn" onclick="currentApp.doSubmit()" class="btn btn-default btn-danger">
				<i class="fa fa-save"></i>存檔
			</a>
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
				<input type="hidden" name="id" value="<?= $item -> id ?>" />

				<!-- <fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">贈禮公司抽佣百分比</label>
						<div class="col-md-6">
							<input type="number" required class="form-control" name="transfer_gift_pct" value="<?= isset($item) ? $item -> transfer_gift_pct : '' ?>" />
						</div>
					</div>
				</fieldset> -->

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">是否維護</label>
						<div class="col-md-1">
						<select  id="is_maintain" name="is_maintain" class="form-control">
							<option value="0" <?= $item -> is_maintain == 0 ? 'selected' : '' ?>>否</option>
							<option value="1" <?= $item -> is_maintain == 1 ? 'selected' : '' ?>>是</option>
						</select>
					</div>
					<div class="widget-toolbar pull-left" style="border-left:0px" >
							維修開始時間:<input id="maintain_start_time" name="maintain_start_time"  placeholder="請輸入日期" type="text" class="dt_picker" value="<?= $item -> maintain_start_time ?>" />
						</div>
						<div class="widget-toolbar pull-left" style="border-left:0px" >
							~ 維修開始時間:<input id="maintain_end_time" name="maintain_end_time"  placeholder="請輸入日期" type="text" class="dt_picker" value="<?= $item -> maintain_end_time ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">是否維護正式</label>
						<div class="col-md-1">
						<select  id="is_maintain_production" name="is_maintain_production" class="form-control">
							<option value="0" <?= $item -> is_maintain_production == 0 ? 'selected' : '' ?>>否</option>
							<option value="1" <?= $item -> is_maintain_production == 1 ? 'selected' : '' ?>>是</option>
						</select>
					</div>
					<div class="widget-toolbar pull-left" style="border-left:0px">
							正式維修開始時間: <input id="maintain_start_time_production" name="maintain_start_time_production" placeholder="請輸入日期" type="text" class="dt_picker" value="<?= $item -> maintain_start_time_production ?>" />
						</div>
						<div class="widget-toolbar pull-left" style="border-left:0px">
							~  維修開始時間: <input id="maintain_end_time_production" name="maintain_end_time_production" placeholder="請輸入日期" type="text" class="dt_picker" value="<?= $item -> maintain_end_time_production ?>" />
						</div>
					</div>
				</fieldset>
				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">註冊贈送金幣數量</label>
						<div class="col-md-6">
							<input type="number" required class="form-control"  name="register_reward_amt" value="<?= isset($item) ? $item -> register_reward_amt : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">贈禮抽成百分比</label>
						<div class="col-md-6">
							<input type="number" required class="form-control"  name="transfer_gift_pct" value="<?= isset($item) ? $item -> transfer_gift_pct : '0' ?>" />
						</div>
					</div>
				</fieldset>

				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">JP 門檻值</label>
						<div class="col-md-6">
							<input type="number" required class="form-control"  name="fish_jp_amt_thresh" value="<?= isset($item) ? $item -> fish_jp_amt_thresh : '' ?>" />
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
<!-- PAGE RELATED PLUGIN(S) -->
<script type="text/javascript">
$(".dt_picker").datetimepicker({
		format: 'YYYY-MM-DD HH:mm:ss'
	}).on('dp.change',function(event){
	});


</script>
