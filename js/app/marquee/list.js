var MarqueeAppClass = (function(app) {
	app.basePath = "mgmt/marquee/";

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

			columns : [null, {
				data : 'title'
			}, {
				data : 'show_time'
			}, {
				data : 'create_time'
			}],

			order : [[3, "desc"]],
			columnDefs : [{
				targets : 0,
				data : null,

				defaultContent : app.defaultContent,
				searchable : false,
				orderable : false,
				width : "5%",
				className : ''
			}, {
				"targets" : [1],
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
