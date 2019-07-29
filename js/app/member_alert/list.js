var memberAlertAppClass = (function(app) {
	app.basePath = "mgmt/member_alert/";

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
				data : 'create_time'
			}, {
				data : 'deal_status_name',
				render:function(d,t,r){
					if(r.deal_status == 1) {
						return "<font color='green'>" + d + "</font>";
					}
					return "<font color='red'>" + d + "</font>";
				}
			}, {
				data : 'member_name'
			}, {
				data : 'response_text'
			}, {
				data : 'deal'
			},{
				data: 'lat'
			},{
				data: 'lng'
			}],

			order : [[0, "desc"]],
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

		return app;
	};

	// return self
	return app.init();
});
