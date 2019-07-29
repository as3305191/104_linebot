var TransferCoinAppClass = (function(app) {
	app.basePath = "mgmt/transfer_coin/";

	app.init = function() {
		app.enableFirstClickable = true;
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					d.in_user_id = $('#login_user_id').val();
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [{
				data : 'sn'
			}, {
				data : 'out_account'
			}, {
				data : 'in_account'
			}, {
				data : 'amt'
			}, {
				data : 'currency_name'
			}, {
				data : 'status_name',
				render: function(d,t,r) {
					if(r.status == 0) {
						return "<font color='red'>" + d + "</font>";
					}
					if(r.status == 1) {
						return "<font color='green'>" + d + "</font>";
					}
					return d;
				}
			}, {
				data : 'create_time'
			}],

			order : [[6, "desc"]],
			columnDefs : [{
				"targets" : [0,1,2,3,4,5],
				"orderable" : false
			}]
		}));

		app.doSubmit = function(status, type) {
			if(!$('#app-edit-form').data('bootstrapValidator').validate().isValid()) return;

			var typeUrl = '';
			if(type == 1) {
				typeUrl = 'insert_in';
			}
			if(type == 2) {
				typeUrl = 'insert_transfer';
			}
			var url = baseUrl + app.basePath + typeUrl + '/' + status; // the script where you handle the form input.
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

		return app;
	};

	// return self
	return app.init();
});

function doPay() {
	alert('buying...');
}
