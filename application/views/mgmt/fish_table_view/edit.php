<style>
.file-drag-handle {
	display: none;
}
</style>
<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget" id="wid-id-7" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
	<header>
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);"  onclick="currentApp.backTo()" class="btn btn-default ">
				<i class="fa fa-arrow-circle-left"></i>返回
			</a>
		</div>
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" onclick="currentApp.doSubmit()" class="btn btn-default btn-danger">
				<i class="fa fa-save"></i>存檔
			</a>
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
		<div class="widget-body">
			<form id="app-edit-form" method="post" class="form-horizontal">
				<input type="hidden" id="item_id" name="item_id" value="<?= !empty($item) ? $item -> id : 0 ?>" />
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">桌名</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="tab_name" value="<?= !empty($item) ? $item -> tab_name : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">排序(小至大)</label>
						<div class="col-md-6">
							<input type="number" required class="form-control"  name="pos" value="<?= !empty($item) ? $item -> pos : '' ?>" />
						</div>
					</div>
				</fieldset>
				<hr/>
				<?php if(!empty($item)): ?>
				<h3>彩池增減<span>填寫正負變更</span></h3>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">彩池100</label>
						<div class="col-md-6">
							<input type="number"  class="form-control"  name="pool_100" value="0" />
							<input type="text"  class="form-control" readonly value="<?= !empty($item) ? (int)$item -> pool_100 : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">彩池100魚王</label>
						<div class="col-md-6">
							<input type="number"  class="form-control"  name="pool_100_king" value="0" />
							<input type="text"  class="form-control" readonly value="<?= !empty($item) ? (int)$item -> pool_100_king : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">彩池2000</label>
						<div class="col-md-6">
							<input type="number"  class="form-control"  name="pool_2000" value="0" />
							<input type="text"  class="form-control" readonly value="<?= !empty($item) ? (int)$item -> pool_2000 : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">彩池2000魚王</label>
						<div class="col-md-6">
							<input type="number"  class="form-control"  name="pool_2000_king" value="0" />
							<input type="text"  class="form-control" readonly value="<?= !empty($item) ? (int)$item -> pool_2000_king : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">彩池20000</label>
						<div class="col-md-6">
							<input type="number"  class="form-control"  name="pool_20000" value="0" />
							<input type="text"  class="form-control" readonly value="<?= !empty($item) ? (int)$item -> pool_20000 : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">彩池2000魚王</label>
						<div class="col-md-6">
							<input type="number"  class="form-control"  name="pool_20000_king" value="0" />
							<input type="text"  class="form-control" readonly value="<?= !empty($item) ? (int)$item -> pool_20000_king : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">彩池200000</label>
						<div class="col-md-6">
							<input type="number"  class="form-control"  name="pool_200000" value="0" />
							<input type="text"  class="form-control" readonly value="<?= !empty($item) ? (int)$item -> pool_200000 : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">彩池20000魚王</label>
						<div class="col-md-6">
							<input type="number"  class="form-control"  name="pool_200000_king" value="0" />
							<input type="text"  class="form-control" readonly value="<?= !empty($item) ? (int)$item -> pool_200000_king : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">彩池100000</label>
						<div class="col-md-6">
							<input type="number"  class="form-control"  name="pool_1000000" value="0" />
							<input type="text"  class="form-control" readonly value="<?= !empty($item) ? (int)$item -> pool_1000000 : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">彩池20000魚王</label>
						<div class="col-md-6">
							<input type="number"  class="form-control"  name="pool_1000000_king" value="0" />
							<input type="text"  class="form-control" readonly value="<?= !empty($item) ? intval($item -> pool_1000000_king) : '' ?>" />
						</div>
					</div>
				</fieldset>
			</form>

				<hr/>
				<h3>摸彩期數</h3>
				<header>
					<form id="app-lottery-edit-form" method="post" class="form-horizontal">
						<input type="hidden" name="tab_id" value="<?= !empty($item) ? $item -> id : '' ?>" />

						<div class="widget-toolbar pull-left">
							<fieldset>
								<div class="form-group">
									<label class="col-md-6 control-label">摸彩序號</label>
									<div class="col-md-6">
										<input type="text" required class="form-control" name="lottery_sn" value="" />
									</div>
								</div>
							</fieldset>
						</div>
						<div class="widget-toolbar pull-left">
							<fieldset>
								<div class="form-group">
									<label class="col-md-6 control-label">期數</label>
									<div class="col-md-6">
										<input type="text" required class="form-control" name="lottery_no" value="" />
									</div>
								</div>
							</fieldset>
						</div>
						<div class="widget-toolbar pull-left">
							<a href="javascript:void(0);" onclick="currentApp.lotteryList.doSubmit()" class="btn btn-default btn-danger">
								<i class="fa fa-save"></i>新增
							</a>
						</div>
					</form>

					<table id="lottery_list" class="table table-striped table-bordered table-hover" width="100%">
						<thead>
							<tr>
								<th class="min50"></th>
								<th class="min100">期數</th>
								<th class="min100">摸彩序號</th>
								<th class="min100">摸彩名稱</th>
								<th class="min100">摸彩卷總數</th>
								<th class="min100">是否當期</th>
								<th class="min100">是否開獎</th>
								<th class="min100">開獎號碼</th>
								<th class="min100">是否領獎</th>
								<th class="min100">領獎人暱稱</th>
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
				<?php endif ?>

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
var baseUrl = '<?=base_url()?>';

	$('#app-edit-form').bootstrapValidator({
		feedbackIcons : {
			valid : 'glyphicon glyphicon-ok',
			invalid : 'glyphicon glyphicon-remove',
			validating : 'glyphicon glyphicon-refresh'
		},
		fields: {

    }
	})
	.bootstrapValidator('validate');

</script>
