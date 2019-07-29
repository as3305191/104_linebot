var BetRecordsAppClass = (function(app) {
	app.basePath = "mgmt/bet_records/";

	app.init = function() {
		app.enableFirstClickable = true;
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
				data : 'create_time'
			}, {
				data : 'sn'
			}, {
				data : 'is_open_name',
				render: function(d,t,r) {
					return d;
				}
			}],

			order : [[0, "desc"]],
			columnDefs : [ {
				"targets" : 0,
				"orderable" : false
			}, {
				"targets" : 1,
				"orderable" : false
			}, {
				"targets" : 2,
				"orderable" : false
			}]
		}));

		app.doSubmit = function() {
			if(!$('#app-edit-form').data('bootstrapValidator').validate().isValid()) return;
			var url = baseUrl + app.basePath + 'insert'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-edit-form").serialize(),
				success : function(data) {
					app.mDtTable.ajax.reload(null, false);
					app.backTo();

					// if(data.last_id) {
					// 	window.open(
					// 	  baseUrl + 'mgmt/user_buy/pay/' + data.last_id,
					// 	  '_blank'
					// 	);
					// }
				}
			});
		};

		app.doOpen = function() {
			if(!$('#app-edit-form').data('bootstrapValidator').validate().isValid()) return;
			var url = baseUrl + app.basePath + 'do_open'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-edit-form").serialize(),
				success : function(data) {
					app.mDtTable.ajax.reload(null, false);
					app.backTo();
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
