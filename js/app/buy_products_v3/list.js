var BuyProductsV3AppClass = (function(app) {
	app.basePath = "mgmt/buy_products_v3/";

	app.init = function() {
		app.enableFirstClickable = true;
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
				data : 'sn'
			}, {
				data : 'product_name'
			}, {
				data : 'total_price'
			}, {
				data : 'status_name',
				render: function(d,t,r) {
					if(r.status == 0) {
						return "<font color='red'>" + d + "</font>";
					}
					if(r.status == 1) {
						return "<font color='green'>" + d + "</font>";
					}
				}
			}, {
				data : 'create_time'
			}],

			order : [[4, "desc"]],
			columnDefs : [ {
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

		app.doSubmit = function() {
			if(!$('#app-edit-form').data('bootstrapValidator').validate().isValid()) return;
			var url = baseUrl + app.basePath + 'insert'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-edit-form").serialize(),
				success : function(data) {
					app.mDtTable.ajax.reload(null, false);
					app.backTo();

					// if(data.last_id) {
					// 	window.open(
					// 	  baseUrl + 'mgmt/user_buy/pay/' + data.last_id,
					// 	  '_blank'
					// 	);
					// }
				}
			});
		};

		app.doPay = function() {
			doPay();
		}

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

function doPay() {
	alert('buying...');
}
