var ProductsV3App = (function(app) {
	app.basePath = "mgmt/products_v3/";
	app.init = function() {
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			iDisplayLength : 25,

			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					d.parent_id = app.parentId;
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [null, {
				data : 'product_name'
			}, {
				data : 'price'
			},{
				data : 'online',
				render: function(d,t,r) {
					if(d == 0) {
						return "<font color='blue'>下架</font>";
					}
					if(d == 1) {
						return "<font color='green'>上架</font>";
					}
				}
			}],

			columnDefs : [{
				targets : 0,
				data : null,

				defaultContent : app.defaultContent,
				searchable : false,
				orderable : false,
				width : "5%",
				className : ''
			}, {
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

	// return seslf
	return app.init();
});
