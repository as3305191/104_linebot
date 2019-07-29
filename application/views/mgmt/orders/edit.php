<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget" id="wid-id-7" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
	<header>
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="back_parent" onclick="currentApp.backTo()" class="btn btn-default ">
				<i class="fa fa-arrow-circle-left"></i>返回
			</a>
		</div>
		<div class="widget-toolbar pull-left">

		</div>
		<ul class="nav nav-tabs pull-right in" id="myTab">
			<li class="active">
				<a data-toggle="tab" href="#s1"><i class="fa fa-file-text"></i> <span class="hidden-mobile hidden-tablet">商品資料</span></a>
			</li>

			<!-- <li>
				<a data-toggle="tab" href="#s5"><i class="fa fa-truck"></i> <span class="hidden-mobile hidden-tablet">運費規則</span></a>
			</li> -->
		</ul>
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

				<div id="myTabContent" class="tab-content">
					<div class="tab-pane fade active in padding-10 no-padding-bottom" id="s1">
						<form id="app-edit-form1" method="post" class="form-horizontal">
							<input type="hidden" name="id" value="<?= $id ?>" />
							<fieldset>
								<div class="form-group">
									<table class="table">
										<tr>
											<td class="min100">單號</td>
											<td><?= $item -> sn ?></td>
										</tr>
										<tr>
											<td>價格DBC</td>
											<td><?= sp_color(number_format($item -> product_amt, 8))?></td>
										</tr>
										<tr>
											<td>訂購商品</td>
											<td><?= $item -> product_name ?></td>
										</tr>
										<tr>
											<td>購買帳號</td>
											<td><?= $item -> user_account ?></td>
										</tr>
										<tr>
											<td>收件人名稱</td>
											<td><?= $item -> receive_name ?></td>
										</tr>
										<tr>
											<td>收件人電話</td>
											<td><?= $item -> receive_phone ?></td>
										</tr>
										<tr>
											<td>收件人地址</td>
											<td><?= $item -> receive_address ?></td>
										</tr>
										<tr>
											<td>付款狀態</td>
											<td><?= $item -> order_pay_status_name ?>

											</td>
										</tr>
										<tr>
											<td>運送狀態</td>
											<td><?= $item -> order_shipping_status_name ?>
												<?php if($item -> shipping_status == 0): ?>
													<a href="javascript:void(0);" id="" onclick="doShipping(1)" class="btn btn-default btn-danger">
														<i class="fa fa-save"></i>已出貨
													</a>
												<?php elseif($item -> shipping_status == 1): ?>
													<a href="javascript:void(0);" id="" onclick="doShipping(-1)" class="btn btn-default btn-danger">
														<i class="fa fa-save"></i>退貨
													</a>
												<?php elseif($item -> shipping_status == -1): ?>
													<a href="javascript:void(0);" id="" onclick="doShipping(1)" class="btn btn-default btn-danger">
														<i class="fa fa-save"></i>已出貨
													</a>
													<a href="javascript:void(0);" id="" onclick="doShipping(0)" class="btn btn-default btn-danger">
														<i class="fa fa-save"></i>未出貨
													</a>
												<?php endif ?>
											</td>
										</tr>
										<tr>
											<td>訂單狀態</td>
											<td><?= $item -> order_status_name ?>
												<?php if($item -> status == 0): ?>
													<a href="javascript:void(0);" id="" onclick="doCancel()" class="btn btn-default btn-danger">
														<i class="fa fa-save"></i>取消
													</a>
												<?php elseif($item -> status == 1): ?>
													<a href="javascript:void(0);" id="" onclick="doCancel()" class="btn btn-default btn-danger">
														<i class="fa fa-save"></i>取消
													</a>
												<?php endif ?>
											</td>
										</tr>
									</table>
								</div>
							</fieldset>

							<fieldset>
								<div class="form-group">
									<table class="table">
										<!-- <tr>
											<td class="min100">商品小計</td>
											<td><?= number_format($item -> product_amt) ?></td>
										</tr> -->
										<!-- <tr>
											<td>運費小計</td>
											<td><?= number_format($item -> shipping_amt) ?></td>
										</tr> -->
										<!-- <tr>
											<td>總計</td>
											<td><?= number_format($item -> total) ?></td>
										</tr> -->
									</table>
								</div>
							</fieldset>
						</form>
					</div>
					<!-- end of s1 -->
					<!-- start of s2 -->
					<!-- end of s2 -->
					<!-- start of s3 -->
					<!-- end of s3 -->
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
	.cke_skin_v2 input.cke_dialog_ui_input_text, .cke_skin_v2 input.cke_dialog_ui_input_password {
	    background-color: white;
	    border: none;
	    padding: 0;
	    width: 100%;
	    height: 14px;
	    /* new lines */
	    position: relative;
	    z-index: 9999;
	}

</style>
<!-- PAGE RELATED PLUGIN(S) -->

<script>
	currentApp.dtTableOrderEdit = $('#product_list').DataTable({
			processing : true,
			serverSide : true,
			responsive : true,
			deferLoading : 0, // don't reload on init
			iDisplayLength : 100,
			sDom: "<'dt-toolbar'<'col-sm-12 col-xs-12'>>"+
						"<'ted-box'"+
						"t"+
						">"+
						"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'><'col-xs-12 col-sm-6'>>",
			language : {
				url : baseUrl + "js/datatables-lang/zh-TW.json"
			},

			ajax : {
				url : baseUrl + currentApp.basePath + '/get_product_list',
				data : function(d) {
					d.order_id = '<?= $item -> id ?>';
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [null,{
				data : 'image_id',
				render: function(data, type, row) {
					if(data == 0) return "";
					return '<img src="' + row.image_url + '/yes" style="height: 120px;">';
				}
			}, {
				data : 'product_name'
			}, {
				data : 'num',
				render: function ( data, type, row ) {
	    			return data;
		    	}
			}, {
				data : 'price',
				render: function ( data, type, row ) {
	    			return data;
		    	}
			}],

			bSortCellsTop : true,
			columnDefs : [{
				targets : 0,
				data : null,
				defaultContent : '',
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
			}, {
				"targets" : 4,
				"orderable" : false
			}

			],
			fnRowCallback : function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// edit click
				$(nRow).find('td').not(':first').addClass('pointer').on('click', function(){
				});
			}
		});
	currentApp.dtTableOrderEdit.ajax.reload();

	$('#app-edit-form').bootstrapValidator({
		feedbackIcons : {
			valid : 'glyphicon glyphicon-ok',
			invalid : 'glyphicon glyphicon-remove',
			validating : 'glyphicon glyphicon-refresh'
		}
	});

	currentApp.dtTableStatusLog = $('#status_log_list').DataTable({
			processing : true,
			serverSide : true,
			responsive : true,
			deferLoading : 0, // don't reload on init
			iDisplayLength : 100,
			sDom: "<'dt-toolbar'<'col-sm-12 col-xs-12'>>"+
						"<'ted-box'"+
						"t"+
						">"+
						"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'><'col-xs-12 col-sm-6'>>",
			language : {
				url : baseUrl + "js/datatables-lang/zh-TW.json"
			},

			ajax : {
				url : baseUrl + currentApp.basePath + '/get_status_log',
				data : function(d) {
					d.order_id = '<?= $item -> id ?>';
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [{
				data : 'status_name',
				render: function ( data, type, row ) {
	    			return data;
		    	}
			}, {
				data : 'user_name',
				render: function ( data, type, row ) {
	    			return data;
		    	}
			}, {
				data : 'member_name',
				render: function ( data, type, row ) {
	    			return data;
		    	}
			}, {
				data : 'note'
			}, {
				data : 'create_time',
				render: function ( data, type, row ) {
	    			return data;
		    	}
			}],

			bSortCellsTop : true,
			order : [[4, "desc"]],
			columnDefs : [{
				targets : 0,
				data : null,
				defaultContent : '',
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
			}

			],
			fnRowCallback : function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// edit click
				$(nRow).find('td').not(':first').addClass('pointer').on('click', function(){
				});
			}
		});
	currentApp.dtTableStatusLog.ajax.reload();

	function doCancel() {
		$.ajax({
			type: "POST",
			url: '<?= base_url('mgmt/orders/do_cancel') ?>',
			data: {
				id: <?= isset($item) ? $item -> id : '0' ?>,
				status: -1
			},
			success: function(data)
			{
				currentApp.doEdit(data.id);
			}
		});
	}

	function doShipping(status) {
		$.ajax({
			type: "POST",
			url: '<?= base_url('mgmt/orders/do_shipping') ?>',
			data: {
				id: <?= isset($item) ? $item -> id : '0' ?>,
				status: status
			},
			success: function(data)
			{
				currentApp.doEdit(data.id);
			}
		});
	}
</script>
