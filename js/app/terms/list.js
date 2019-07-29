var TermsAppClass = (function(app) {
	app.basePath = "mgmt/terms/";

	app.init = function() {
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					d.role_id = $('#role_id').val();
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [{
				data : 'id'
			}, {
				data : 'title'
			}, {
				data : 'content'
			}],

			ordering: false,

			order : [[0, "asc"]],
			columnDefs : [ {
				"targets" : [0,1,2],
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
