<style>
.file-drag-handle {
	display: none;
}
</style>
<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget" id="wid-id-7" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
	<header>
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="back_parent" onclick="currentApp.backTo()" class="btn btn-default ">
				<i class="fa fa-arrow-circle-left"></i>返回
			</a>
		</div>
		<?php if($login_user -> role_id == 99): ?>
			<div class="widget-toolbar pull-left">
				<a href="javascript:void(0);" id="back_parent" onclick="currentApp.doSubmit()" class="btn btn-default btn-danger">
					<i class="fa fa-save"></i>存檔
				</a>
			</div>
			<?php if(isset($item)): ?>
			<div class="widget-toolbar pull-left">
				<a href="javascript:void(0);" id="" onclick="genRoud();" class="btn btn-default btn-warning">
					<i class="fa fa-refresh"></i>更新牌組
				</a>
			</div>
			<?php endif ?>
		<?php endif ?>


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
				<input type="hidden" name="id" id="item_id" value="<?= isset($item) ? $item -> id : '' ?>" />
				<input type="hidden" name="corp_id" id="m_corp_id" value="" />
				<input type="hidden" name="tab_type" id="m_tab_type" value="" />

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">桌名</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="tab_name" value="<?= isset($item) ? $item -> tab_name : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">順序</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="pos" value="<?= isset($item) ? $item -> pos : '0' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">狀態</label>
						<div class="col-md-6">
							<select name="status" class="form-control">
								<option value="0">正常</option>
								<option value="1" <?= isset($item) && $item -> status == 1 ? 'selected' : '' ?>>停用</option>
							</select>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label" style="color: red;">無限制設定-1</label>

					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">和下注(最小 -> 最大)</label>
						<div class="col-md-3">
							<input type="number" class="form-control"  name="min_bet_1" value="<?= isset($item) ? $item -> min_bet_1 : '0' ?>" />
						</div>
						<div class="col-md-3">
							<input type="number" class="form-control"  name="max_bet_1" value="<?= isset($item) ? $item -> max_bet_1 : '0' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">莊下注(最小 -> 最大)</label>
						<div class="col-md-3">
							<input type="number" class="form-control"  name="min_bet_2" value="<?= isset($item) ? $item -> min_bet_2 : '0' ?>" />
						</div>
						<div class="col-md-3">
							<input type="number" class="form-control"  name="max_bet_2" value="<?= isset($item) ? $item -> max_bet_2 : '0' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">閒下注(最小 -> 最大)</label>
						<div class="col-md-3">
							<input type="number" class="form-control"  name="min_bet_3" value="<?= isset($item) ? $item -> min_bet_3 : '0' ?>" />
						</div>
						<div class="col-md-3">
							<input type="number" class="form-control"  name="max_bet_3" value="<?= isset($item) ? $item -> max_bet_3 : '0' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">荘對下注(最小 -> 最大)</label>
						<div class="col-md-3">
							<input type="number" class="form-control"  name="min_bet_4" value="<?= isset($item) ? $item -> min_bet_4 : '0' ?>" />
						</div>
						<div class="col-md-3">
							<input type="number" class="form-control"  name="max_bet_4" value="<?= isset($item) ? $item -> max_bet_4 : '0' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">閒對下注(最小 -> 最大)</label>
						<div class="col-md-3">
							<input type="number" class="form-control"  name="min_bet_5" value="<?= isset($item) ? $item -> min_bet_5 : '0' ?>" />
						</div>
						<div class="col-md-3">
							<input type="number" class="form-control"  name="max_bet_5" value="<?= isset($item) ? $item -> max_bet_5 : '0' ?>" />
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

<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget">
	<header>
		<div class="widget-toolbar pull-left">
			<h2>牌局</h2>
		</div>
	</header>
	<!-- widget div-->
	<div>
		<!-- widget content -->
		<div class="widget-body no-padding">

			<table id="round_list" class="table table-striped table-bordered table-hover" width="100%">
				<thead>
					<tr>
						<th class="min100">牌局號</th>
						<th class="min100">目前局數</th>
						<th class="min100">狀態</th>
						<th class="min100">開始時間</th>
						<th class="min100">結束時間</th>
						<th class="">建立時間</th>
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

<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget">
	<header>
		<div class="widget-toolbar pull-left">
			<h2>牌局明細</h2>
		</div>
	</header>
	<!-- widget div-->
	<div>
		<!-- widget content -->
		<div class="widget-body no-padding">

			<table id="round_detail_list" class="table table-striped table-bordered table-hover" width="100%">
				<thead>
					<tr>
						<th class="min100">局數</th>
						<th class="min100">莊閒輸贏</th>
						<th class="min100">牌局輸贏</th>
						<th class="min100">莊家牌</th>
						<th class="min100">閒家牌</th>
						<th class="min100">狀態</th>
						<th class="min150">開牌時間</th>
						<th class="min150">結束時間</th>
						<th class="">建立時間</th>
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

<style>
	.kv-file-zoom {
		display: none;
	}
</style>
<script>
	$('#app-edit-form').bootstrapValidator({
		feedbackIcons : {
			valid : 'glyphicon glyphicon-ok',
			invalid : 'glyphicon glyphicon-remove',
			validating : 'glyphicon glyphicon-refresh'
		},
		fields: {
			corp_code: {
            validators: {
              remote: {
              	message: '已經存在',
              	url: baseUrl + 'mgmt/corp/check_corp_code/' + ($('#item_id').val().length > 0 ? $('#item_id').val() : '0')
              }
            }
         }
      }

	})
	.bootstrapValidator('validate');

	function genRoud() {
		$.ajax({
			type: "POST",
			url: '<?= base_url('api/baccarat/gen_round') ?>',
			data: {
				corp_id: <?= isset($item) ? $item -> corp_id : '0' ?>,
				tab_id: <?= isset($item) ? $item -> id : '0' ?>
			},
			success: function(data)
			{
				currentApp.roundApp.tableReload();
				currentApp.roundApp._roundId = 0; // reset to empty detail list
				currentApp.roundDetailApp.tableReload();
			}
		});
	}


</script>
<script>
var BaccaratRoundAppClass = (function(app) {
	app.basePath = "mgmt/baccarat/";

	app.init = function() {
		app.mDtTable = $('#round_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_round_data',
				data : function(d) {
					d.tab_id = '<?= isset($item) ? $item -> id : 0 ?>';
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			ordering: false,

			columns : [{
				data : 'sn'
			},{
				data : 'current_detail_pos'
			}, {
				data : 'status',
				render : function(d,t,r){
					if(d == 0) {
						return "未開始"
					}
					if(d == 1) {
						return "<font color='green'>進行中</font>"
					}
					if(d == 2) {
						return "<font color='red'>已結束</font>"
					}
					return d;
				}
			},{
				data : 'start_time'
			},{
				data : 'finish_time'
			},{
				data : 'create_time'
			}],

			columnDefs : [{
				targets : 0,
				data : null,
				defaultContent : app.defaultContent,
				searchable : false,
				orderable : false,
				width : "5%",
				className : ''
			},{
				"targets" : [1,2,3,4],
				"orderable" : false
			}],

			fnRowCallback : function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
						// edit click
						if(!app.disableRowClick) {
							var _rtd = $(nRow).find('td');
							if(!app.enableFirstClickable) {
								_rtd = _rtd.not(':first')
							}
							_rtd.addClass('pointer').on('click', function(){
								app.doEdit(aData.id);

								// remove all highlight first
								$(this).parent().parent().find('tr').removeClass('active');

								app._lastPk = aData.id;
								app._tr = $(this).parent();
								setTimeout(function(){
									app._tr.addClass('active');
								}, 100);
							});
						}

						if(app._lastPk && aData.id && app._lastPk == aData.id) {
							$(nRow).addClass('active');
						}
				}

		}));

		// edit
		app.doEdit = function(id) {
		  app._roundId = id;
			if(currentApp.roundDetailApp) {
				currentApp.roundDetailApp.tableReload();
			}
		};

		// data table actions
		app.dtActions();

		// get year month list
		<?php if(isset($item)): ?>
		app.tableReload();
		<?php endif ?>

		return app;
	};

	// return self
	return app.init();
});

var BaccaratRoundDeatilAppClass = (function(app) {
	app.basePath = "mgmt/baccarat/";

	app.init = function() {
		app.mDtTable = $('#round_detail_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_round_detail_data',
				data : function(d) {
					d.round_id = currentApp.roundApp._roundId;
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			iDisplayLength : 100,
			ordering: false,

			columns : [{
				data : 'pos'
			},{
				data : 'winner',
				render : function(d,t,r){
					if(d == 0) {
						return "<font color='green'>和</font>"
					}
					if(d == 1) {
						return "<font color='red'>莊</font>"
					}
					if(d == 2) {
						return "<font color='blue'>閒</font>"
					}
					return d;
				}
			},{
				data : 'winner_type',
				render : function(d,t,r){
					if(d == 0) {
						return "<font color='green'>和</font>"
					}
					if(d == 1) {
						return "<font color='red'>莊</font>"
					}
					if(d == 2) {
						return "<font color='blue'>閒</font>"
					}
					if(d == 3) {
						return "<font color='red'>莊</font><font color='gray'>對</font>"
					}
					if(d == 4) {
						return "<font color='blue'>閒</font><font color='gray'>對</font>"
					}
					if(d == 6) {
						return "<font color='red'>莊</font><font color='gray'>對</font>" + "<font color='blue'>閒</font><font color='gray'>對</font>"
					}
					return d;
				}
			},{
				data : 'banker_c_1',
				render : function(d,t,r){
					var resArr = [];
					if(r.banker_c_0) {
						resArr.push(((r.banker_c_0 % 13) + 1));
					}
					if(r.banker_c_1) {
						resArr.push(((r.banker_c_1 % 13) + 1));
					}
					if(r.banker_c_2) {
						resArr.push(((r.banker_c_2 % 13) + 1));
					}
					return resArr.join(',') + ' = ' + r.banker_val;
				}
			},{
				data : 'player_c_1',
				render : function(d,t,r){
					var resArr = [];
					if(r.player_c_0) {
						resArr.push(((r.player_c_0 % 13) + 1));
					}
					if(r.player_c_1) {
						resArr.push(((r.player_c_1 % 13) + 1));
					}
					if(r.player_c_2) {
						resArr.push(((r.player_c_2 % 13) + 1));
					}
					return resArr.join(',') + ' = ' + r.player_val;
				}
			}, {
				data : 'status',
				render : function(d,t,r){
					if(d == 1) {
						return "<font color='green'>" + r.status_name + "</font>"
					}
					if(d == 2) {
						return "<font color='red'>" + r.status_name + "</font>"
					}
					if(d == 3) {
						return "<font color='gray'> " + r.status_name + " </font>"
					}
					return r.status_name;
				}
			},{
				data : 'open_time'
			},{
				data : 'finish_time'
			},{
				data : 'create_time'
			}],

			columnDefs : [{
				targets : 0,
				data : null,
				defaultContent : app.defaultContent,
				searchable : false,
				orderable : false,
				width : "5%",
				className : ''
			},{
				"targets" : [1,2,3,4],
				"orderable" : false
			}],

			fnRowCallback : function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
						// edit click
						if(!app.disableRowClick) {
							var _rtd = $(nRow).find('td');
							if(!app.enableFirstClickable) {
								_rtd = _rtd.not(':first')
							}
							_rtd.addClass('pointer').on('click', function(){
								app.doEdit(aData.id);

								// remove all highlight first
								$(this).parent().parent().find('tr').removeClass('active');

								app._lastPk = aData.id;
								app._tr = $(this).parent();
								setTimeout(function(){
									app._tr.addClass('active');
								}, 100);
							});
						}

						if(app._lastPk && aData.id && app._lastPk == aData.id) {
							$(nRow).addClass('active');
						}
				}

		}));

		// edit
		app.doEdit = function(id) {
		  alert(id);
		};

		// data table actions
		app.dtActions();

		return app;
	};

	// return self
	return app.init();
});

currentApp.roundApp =new BaccaratRoundAppClass(new BaseAppClass({}));
currentApp.roundDetailApp =new BaccaratRoundDeatilAppClass(new BaseAppClass({}));
</script>
