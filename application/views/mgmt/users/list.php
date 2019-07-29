<div class="tab-content">
	<div class="tab-pane active" id="list_page">

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
							<!-- <div class="widget-toolbar pull-left">
								<div class="btn-group">
									<button onclick="currentApp.doEdit(0)" class="btn dropdown-toggle btn-xs btn-success" data-toggle="dropdown">
										<i class="fa fa-plus"></i>新增
									</button>
								</div>
							</div> -->
								<?php if($login_user -> role_id == 99):?>
									<div class="widget-toolbar pull-left">
										<div class="btn-group">
											<button onclick="currentApp.doExportAll()" class="btn dropdown-toggle btn-xs btn-warning" data-toggle="dropdown">
												<i class="fa fa-save"></i>匯出
											</button>
										</div>
									</div>
								<?php endif ?>
							<?php else: ?>
							<div class="widget-toolbar pull-left">
								<div class="btn-group">
									<span id="s_total" style="color:#AA0000"></span>
								</div>
							</div>
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
											<th class="min50"></th>
											<?php if($login_user -> role_id == 99):?>
												<th class="min150">頭像</th>
											<?php endif ?>
											<!-- <th class="min200">帳號</th> -->
											<th class="min200">暱稱</th>
											<!-- <th class="min200">是否通過簡訊驗證</th> -->
											<?php if($login_user -> role_id == 1):?>
												<th class="min150">權限角色</th>
											<?php endif ?>
											<th class="min150">餘額</th>
											<th class="min150">建立時間</th>
										</tr>
										<tr class="search_box">
											    <th></th>
													<?php if($login_user -> role_id == 99):?>
														<th><input class="form-control input-xs" type="text" /></th>
													<?php endif ?>
											    <!-- <th><input class="form-control input-xs" type="text" /></th> -->
											    <th><input class="form-control input-xs" type="text" /></th>
											    <!-- <th>
														<select name="is_valid_mobile" id="is_valid_mobile" class="form-control">
															<option value="-1">-</option>
															<option value="1">通過</option>
															<option value="0">未通過</option>
														</select>
													</th> -->
													<?php if($login_user -> role_id == 1):?>
													<th>
														<select name="role_id" id="role_id" class="form-control">
															<option value="-1">無</option>
															<?php foreach($role_list as $each): ?>
																<option value="<?= $each -> id?>" ><?=  $each -> role_name ?></option>
															<?php endforeach ?>
														</select>
													</th>
													<?php endif ?>
													<th></th>

													<th></th>
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
<?php $this -> load -> view('general/delete_modal'); ?>

<script type="text/javascript">
	var mCols = [null
	<?php if($login_user -> role_id == 99):?>
	, {
		data : 'line_picture',
		render : function(d,t,r){
				return '<img src="'+d+'" style="height:30px" />';
		}
	}
	<?php endif ?>
	// 	, {
	// 	data : 'account'
	// }
	, {
		data : 'nick_name'
	}
	// , {
	// 	data : 'is_valid_mobile',render:function(d,t,r){
	// 		if(d == 1) {
	// 			return "<font color='green'>通過</font>";
	// 		}
	// 		if(d == 0) {
	// 			return "<font color='red'>未通過</font>";
	// 		}
	//
	// 		return "-";
	// 	}
	// }
	<?php if($login_user -> role_id == 1):?>
	, {
		data : 'role_name'
	}
	<?php endif ?>
	, {
		data : 'sum_amt'
	}
	, {
		data : 'create_time'
	}];

	mOrderIdx = 4;

	var defaultContent = '<a href="#deleteModal" role="button" data-toggle="modal" style="margin-right: 5px;"><i class="fa fa-trash fa-lg"></i></a>';

	<?php if($login_user -> role_id == 1 || $login_user -> role_id == 99):?>
	var mColDefs = [{
		targets : 0,
		data : null,

		defaultContent : defaultContent,
		searchable : false,
		orderable : false,
		width : "5%",
		className : ''
	}
	, {
		"targets" : 1,
		"orderable" : false
	}
	, {
		"targets" : 2,
		"orderable" : false
	}
	// , {
	// 	"targets" : 3,
	// 	"orderable" : false
	// }
	// , {
	// 	"targets" : 4,
	// 	"orderable" : false
	// }
];
	<?php else: ?>
	var mColDefs = [{
		targets : 0,
		data : null,

		defaultContent : defaultContent,
		searchable : false,
		orderable : false,
		width : "5%",
		className : ''
	}, {
		"targets" : 1,
		"orderable" : false
	}, {
		"targets" : 2,
		"orderable" : false
	}, {
		"targets" : 3,
		"orderable" : false
	}];
	<?php endif ?>
	loadScript(baseUrl + "js/class/BaseAppClass.js", function(){
		loadScript(baseUrl + "js/app/users/list.js", function(){
			currentApp = new UsersAppClass(new BaseAppClass({}));
		});
	});
</script>
