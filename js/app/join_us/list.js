var JoinUsAppClass = (function(app) {
	app.basePath = "mgmt/join_us/";

	app.init = function() {
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [null, {
				data : 'name'
			}, {
				data : 'phone'
			}, {
				data : 'line_id'
			}, {
				data : 'last_5'
			}, {
				data : 'create_time'
			}],

			order : [[5, "desc"]],
			columnDefs : [{
				targets : 0,
				data : null,

				defaultContent : app.defaultContent,
				searchable : false,
				orderable : false,
				width : "5%",
				className : ''
			}, {
				"targets" : [1,2,3,4],
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
