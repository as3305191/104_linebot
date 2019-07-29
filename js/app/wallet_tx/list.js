var WalletTxAppClass = (function(app) {
	app.basePath = "mgmt/wallet_tx/";
	app.enableFirstClickable = true;

	app.init = function() {
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
				data : 'type_name'
			}, {
				data : 'amt'
			}, {
				data : 'brief'
			}, {
				data : 'note'
			}, {
				data : 'create_time'
			}],

			order : [[4, "desc"]],
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
