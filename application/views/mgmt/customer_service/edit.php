<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget" id="wid-id-7" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
	<header>
		<!-- <h2>編輯選單</h2> -->
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="back_parent" onclick="currentApp.backTo()" class="btn btn-default ">
				<i class="fa fa-arrow-circle-left"></i>返回
			</a>
		</div>
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
				<input type="hidden" name="id" value="<?= $id ?>" />

				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">問題</label>
						<div class="col-md-6">
							<input type="text" required class="form-control" name="" value="<?= isset($item) ? $item -> question : '' ?>" />
						</div>
					</div>
				</fieldset>
				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">回覆</label>
						<div class="col-md-6">
							<textarea class="form-control" name="answer" cols="6"><?= isset($item) ? $item -> answer : '' ?></textarea>
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

</script>
