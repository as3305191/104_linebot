<div class="tab-content">
	<div class="tab-pane active" id="list_page">
		<input type="hidden" id="user_role" value="<?= $login_user -> role_id ?>" />
		<!-- widget grid -->
		<section id="widget-grid" class="">

			<!-- row -->
			<div class="row">

				<!-- NEW WIDGET START -->
				<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

					<!-- Widget ID (each widget will need unique ID)-->
					<div class="jarviswidget">
						<header>
							<?php if($login_user -> role_id == 99):?>
							<div class="widget-toolbar pull-left">
								<div class="btn-group">
									<button onclick="currentApp.doEdit(0)" class="btn dropdown-toggle btn-xs btn-success" data-toggle="dropdown">
										<i class="fa fa-plus"></i>新增
									</button>
								</div>
							</div>
							<?php else: ?>
							<div class="widget-toolbar pull-left">
								<div class="btn-group">
									<span id="s_total" style="color:#AA0000"></span>
								</div>
							</div>
							<?php endif ?>



							<!-- 只有總管理者有設定功能 -->
							<?php if($login_user -> role_id == 99):?>
							<!-- <div class="widget-toolbar pull-left">
								<a href="javascript:void(0);" id="" onclick="location.hash='#mgmt/slot_sun_config'" class="btn btn-default btn-danger">
									<i class="fa fa-save"></i>設定
								</a>
							</div> -->
							<?php endif ?>

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

								<table id="dt_list" class="table table-striped table-bordered table-hover" width="100%">
									<thead>
										<tr>
											<th class="min30"></th>
											<th class="min100">機台</th>
											<th class="min100">彩池(公司)</th>
											<th class="min100">彩池(一般)</th>
											<th class="min100">Free(1-150倍)</th>
											<th class="min100">Free(150-350倍)</th>
											<th class="min100">Free(350-600倍)</th>
											<th class="min100">Free(600-100倍)</th>
											<th class="min100">狀態</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>

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

<!-- Modal -->
<div id="addModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">

      <div class="modal-body">
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">加減彩池金額</label>
						<div class="col-md-6">
							<input type="number" id="diff_val" required class="form-control"  value="0" />
						</div>
					</div>
				</fieldset>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="sendOut();" class="btn btn-primary">送出</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
      </div>
    </div>

  </div>
</div>

<?php $this -> load -> view('general/delete_modal'); ?>

<input type="hidden" id="l_user_role" value="<?= $login_user -> role_id ?>" />
<input type="hidden" id="l_corp_id" value="<?= $corp -> id ?>" />
<script type="text/javascript">
	loadScript(baseUrl + "js/class/BaseAppClass.js", function(){
		loadScript(baseUrl + "js/app/slot_sun_tab/list.js", function(){
			currentApp = new SlotSunTabAppClass(new BaseAppClass({}));
			// if($('#l_user_role').val() != '99') {
			// 	currentApp.doEdit($('#l_corp_id').val());
			// }
		});
	});

	function sendOut() {
		var diffval = $('#diff_val').val();
		if(diffval == 0) {
			alert('至少一個輸入不等於0的數值');
			return;
		}

		var url = baseUrl + currentApp.basePath + 'add_diff'; // the script where you handle the form input.
		$.ajax({
			type : "POST",
			url : url,
			data : {
				tab_id : currentApp._lastPk,
				pool_type : currentApp._poolType,
				diff : diffval
			},
			success : function(data) {
				currentApp.tableReload();
				$('#diff_val').val(0);
				$('#addModal').modal('hide');
			}
		});
	}
</script>
