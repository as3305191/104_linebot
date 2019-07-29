var MiningBuyAppClass = (function(app) {
	app.basePath = "mgmt/mining_buy/";

	app.init = function() {
		app.enableFirstClickable = true;
		app.disableRowClick = true;
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					d.user_id = $('#login_user_id').val();
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [{
				data : 'corp_name'
			},{
				data : 'user_account'
			},{
				data : 'sn',
				render: function(d,t,r) {
					return d + ' <font color="red">還有 ' + (parseInt(r.max_days) - parseInt(r.day_diff))  + '天</font>';
				}
			}, {
				data : 'machine_name'
			}, {
				data : 'buy_ntd_price'
			}, {
				data : 'buy_dbc_avg'
			}, {
				data : 'buy_dbc_amt',
				render: function(d,t,r) {
					var d = parseFloat(d);
					return d.toFixed(4);
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

		app.doSubmit = function() {
			if(!$('#app-edit-form').data('bootstrapValidator').validate().isValid()) return;

			if(!confirm('確認購買?')) {
				return;
			}

			var url = baseUrl + app.basePath + 'insert'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-edit-form").serialize(),
				success : function(data) {
					if(data.error_msg) {
						alert(data.error_msg);
					} else {
						app.mDtTable.ajax.reload(null, false);
						app.backTo();
					}
				}
			});
		};

		app.doPay = function() {
			doPay();
		}

		// data table actions
		app.dtActions();

		// get year month list
		app.tableReload();

		$('#role_id').on('change', function(){
			app.tableReload();
		});

		app.mDtTable.on('xhr', function(e, settings, json, xhr){
			$('#list_sum_amt').html(parseFloat(json.sum_amt).toFixed(8));
		});

		return app;
	};

	// return self
	return app.init();
});

function doPay() {
	alert('buying...');
}
