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

							<div class="widget-toolbar pull-left">
								<div class="btn-group">
									<input id="s_account" class="form-control input-xs" placeholder="請輸入帳號開始搜尋" type="text" />

								</div>
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
							<div class="widget-body no-padding">

								<table id="dt_list" class="table table-striped table-bordered table-hover" width="100%">
									<thead>
										<tr>
											<th class="min100"></th>
											<th class="min150">公司</th>
											<th class="min200">帳號</th>
											<th class="min100">反幣%</th>
											<th class="min100">贈幣%</th>
											<th class="min100">代理人等級</th>
											<th class="min100">本月報酬</th>
											<th class="min150">建立時間</th>
										</tr>
										<tr class="search_box">
											    <th></th>
													<th>
														<select name="corp_id" id="s_corp_id" class="form-control">
														<?php foreach($corp_list as $each):?>
																<option value="<?= $each -> id ?>"><?= $each -> corp_name ?></option>
														<?php endforeach ?>
														</select>
													</th>
											    <th></th>
											    <th><input class="form-control input-xs" type="text" /></th>
													<th></th>
													<th></th>
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
	var mCols = [null,{
			data : 'corp_name'
		}, {
		data : 'account'
	}, {
		data : 'agent_bonus'
	}, {
		data : 'agent_win_loose_bonus'
	}, {
		data : 'agent_lv',
		render:function(d,t,r){
			return d;
		}
	}	, {
		data : 'create_time',
		render(d, t, r) {
			return r.monthly_amt;
		}
	}, {
		data : 'create_time'
	}];

	var mOrderIdx = 7;

	var defaultContent = '<a href="#deleteModal" role="button" data-toggle="modal" style="margin-right: 5px;"><i class="fa fa-trash fa-lg"></i></a>';
	defaultContent += $('<a href="javascript:void(0)" class="btn btn-primary btn-xs" style="margin-left:10px;"></a>').html("明細").prop("outerHTML");;

	var mColDefs = [{
		targets : 0,
		data : null,

		defaultContent : defaultContent,
		searchable : false,
		orderable : false,
		width : "5%",
		className : ''
	}, {
		"targets" : [1,2,3,4,5,6],
		"orderable" : false
	}];

	loadScript(baseUrl + "js/class/BaseAppClass.js", function(){
		loadScript(baseUrl + "js/app/agent_list/list.js", function(){
			currentApp = new AgentListAppClass(new BaseAppClass({}));
		});
	});
</script>
