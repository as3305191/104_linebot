var BuyRecordsAppClass = (function(app) {
	app.basePath = "mgmt/buy_records/";
	app.enableFirstClickable = true;

	app.init = function() {
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					//d.user_id = $('#login_user_id').val();
					d.status = $('#status').val();
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [{
				data : 'sn'
			}, {
				data : 'user_account'
			}, {
				data : 'product_name'
			}, {
				data : 'hours'
			}, {
				data : 'total_price'
			}, {
				data : 'pay_type_name'
			}, {
				data : 'status_name',
				render: function (d, t, r) {
					if(r.status == 0) {
						return '<font color="blue">' + d + '</font>';
					}
					if(r.status == 1) {
						return '<font color="green">' + d + '</font>';
					}
					if(r.status == 2) {
						return '<font color="red">' + d + '</font>';
					}
				}
			}, {
				data : 'create_time'
			}],

			order : [[7, "desc"]],
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

		$('#status').on('change', function(){
			app.tableReload();
		});

		return app;
	};

	// return self
	return app.init();
});
