
var PaymentReportAppClass = (function(app) {
	app.basePath = "mgmt/payment_report/";
	app.disableRowClick = true;
  app.enableFirstClickable = true;
  app.dtConfig.sDom =  "<'dt-toolbar'<'col-sm-12 col-xs-12'>r>"+
						"<'t-box'"+
						"t"+
						">"+
						"<'dt-toolbar-footer'<'col-sm-6 col-xs-12'><'col-xs-12 col-sm-6 hidden-xs'>>";

	app.init = function() {
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					d.user_id = $('#login_user_id').val();
					d.s_date = $('#s_date').val();
					d.e_date = $('#e_date').val();
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [{
				data : 'user_account'
			}, {
				data : 'amt'
			}, {
				data : 'bank_id'
			}, {
				data : 'bank_account'
			}],

			columnDefs : [{
				"targets" : 0,
				"orderable" : false
			},{
				"targets" : 1,
				"orderable" : false
			},{
				"targets" : 2,
				"orderable" : false
			},{
				"targets" : 3,
				"orderable" : false
			}]
		}));

		// data table actions
		app.dtActions();

		// get year month list
		app.tableReload();

		return app;
	};

	// return self
	return app.init();
});
