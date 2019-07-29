<input type="hidden" id="detail_user_id" value="<?= $detail_user_id ?>" />

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
								<a href="javascript:void(0);" id="back_parent" onclick="currentApp.backTo()" class="btn btn-default ">
									<i class="fa fa-arrow-circle-left"></i>返回
								</a>
							</div>
							<div class="widget-toolbar pull-left">
								合計：<span style="color: red;" id="sum_amt_range"></span>
							</div>
							<div class="widget-toolbar pull-left">
								總額：<span style="color: red;" id="sum_amt_ntd"></span>
							</div>
							<div class="widget-toolbar pull-left">
								<input type="text" class="dt_picker" id="start_date" value="<?= date('Y-m-d') ?>" />
								<input type="text" class="dt_picker" id="end_date" value="<?= date('Y-m-d') ?>" />
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
								<input type="hidden" id="login_user_id" value="<?= $login_user_id ?>" />
								<table id="dt_list_tx" class="table table-striped table-bordered table-hover" width="100%">
									<thead>
										<tr>
											<th class="min100">遊戲名稱</th>
											<th class="min100">交易類型</th>
											<th class="min100">下注金額</th>
											<th class="min100">贈幣</th>
											<th class="min100">反幣%</th>
											<th class="min100">贈幣%</th>
											<th class="min100">獎金</th>
											<th class="min150">建立時間</th>
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

var AgentTxListAppClass = (function(app) {
	app.basePath = "mgmt/agent_tx/";

	app.init = function() {
		app.enableFirstClickable = true;
		app.disableRowClick = true;

		app.mDtTable = $('#dt_list_tx').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					d.user_id = $('#detail_user_id').val();
					d.start_date = $('#start_date').val();
					d.end_date = $('#end_date').val();
					d.search_corp_id = $('#corp_id').val();
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},
			columns : [{
				data : 'game_name',
				render: function(d,t,r) {
					return d;
				}
			}, {
				data : 'type_name'
			}, {
				data : 'bet_amt'
			}, {
				data : 'win_amt'
			}, {
				data : 'agent_bonus'
			}, {
				data : 'agent_win_loose_bonus'
			}, {
				data : 'amt',
				render: function(d,t,r) {
					if(parseInt(d) >= 0) {
						return "<span style='color:green'>" + d + "</span>";
					} else {
						return "<span style='color:red'>" + d + "</span>";
					}
					return d;
				}
			}, {
				data : 'create_time'
			}],

			order : [[7, "desc"]],
			columnDefs : [{
				"targets" : [0,1,2,3,4,5,6],
				"orderable" : false
			}]
		}));

		// edit
		app.doEdit = function(id) {
		    var loading = $('<h1 class="ajax-loading-animation"><i class="fa fa-cog fa-spin"></i> Loading...</h1>')
		    	.appendTo($('#edit-modal-body').empty());
		    $("#btn-submit-edit").prop( "disabled", true);

			$('.tab-pane').removeClass('active'); $('#edit_page').addClass('active');

			$('#edit-modal-body').load(baseUrl + app.basePath + 'edit/' + id, function(){
	        	$("#btn-submit-edit").prop( "disabled", false);
	        	loading.remove();
			});
		};



		app.mDtTable.on('xhr', function(e, settings, json, xhr){
			$('#sum_amt_ntd').html(numberWithCommas(parseInt(json.sum_amt_ntd)));
			$('#sum_amt_range').html(numberWithCommas(parseInt(json.sum_amt_range)));

		});

		// data table actions
		app.dtActions();

		// get year month list
		app.tableReload();

		return app;
	};

	// return self
	return app.init();
});

currentApp.txListApp = new AgentTxListAppClass(new BaseAppClass({}));

$(".dt_picker").datetimepicker({
	format : 'YYYY-MM-DD'
}).on('dp.change',function(event){
	currentApp.txListApp.tableReload();
});
</script>
