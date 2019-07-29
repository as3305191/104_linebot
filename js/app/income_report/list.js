var IncomeReportAppClass = (function(app) {
	app.basePath = "mgmt/income_report/";
	app.disableRowClick = true;
	app.init = function() {
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					d.user_id = $('#login_user_id').val();
					d.c_date = $('#c_date').val();
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [{
				data : 'sn'
			}, {
				data : 'buy_user_account'
			}, {
				data : 'product_name'
			}, {
				data : 'total_price'
			}, {
				data : 'percent'
			}, {
				data : 'amt'
			}, {
				data : 'create_time'
			}],

			order : [[6, "desc"]],
			columnDefs : [{
				"targets" : 0,
				"orderable" : false
			},{
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
			}, {
				"targets" : 5,
				"orderable" : false
			}, {
				"targets" : 6,
				"orderable" : false
			}]
		}));

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
