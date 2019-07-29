var TransferInAppClass = (function(app) {
	app.basePath = "mgmt/transfer_in/";

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
				data : 'amt'
			}, {
				data : 'result_amt'
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

			order : [[4, "desc"]],
			columnDefs : [{
				"targets" : 0,
				"orderable" : false
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
			}]
		}));

		app.doSubmit = function() {
			if(!$('#app-edit-form').data('bootstrapValidator').validate().isValid()) return;

			if($('#result_amt').val() < 1) {
				alert('匯款金額不足');
				return;
			}
			var url = baseUrl + app.basePath + 'insert_in'; // the script where you handle the form input.
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
