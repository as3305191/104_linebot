var membersAppClass = (function(app) {
	app.basePath = "mgmt/members/";

	app.enableFirstClickable = true;
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

			columns : [{
				data : 'mobile'
			}, {
				data : 'member_name'
			}, {
				data : 'fleet_name',
				render: function(data) {
					if(!data || data == 'null') {
						return "";
					}
					return '<div class="iffyTip min200" style="margin:0px!important;">' + data + '</div>';
				}
			}, {
				data : 'create_time'
			}],

			order : [[3, "desc"]],
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
