var CustomerServiceAppClass = (function(app) {
	app.basePath = "mgmt/customer_service/";
	app.enableFirstClickable = true;
	app.init = function() {
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post',
				complete:function(data){
					$('#s_total').html('推薦會員數:' + data.responseJSON.recordsTotal);
				}
			},

			columns : [{
					data : 'nick_name'
				}, {
					data : 'question'
				}, {
					data : 'answer'
				}
				, {
					data : 'answer_user_id',
					render: function(d,t,r) {
						if(d == 0) {
							return '<font color="red">未回覆</font>';

						}
						return '<font color="green">已回覆</font>';
					}
				}
				, {
					data : 'create_time'
				}],

			order : [[4, "desc"]],
			columnDefs : [ {
				"targets" : 0,
				"orderable" : false
			}, {
				"targets" : 1,
				"orderable" : false
			}, {
				"targets" : 2,
				"orderable" : false
			}, {
				"targets" : 3,
				"orderable" : false
			}],
			"footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
						window.mApi = api;

        }


		}));

		// data table actions
		app.dtActions();

		// get year month list
		app.tableReload();

		$('#role_id').on('change', function(){
			app.tableReload();
		});

		app.doSubmit = function() {
			var url = baseUrl + app.basePath + 'insert'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-edit-form").serialize(),
				success : function(data) {
					app.mDtTable.ajax.reload(null, false);
					app.backTo();
				}
			});
		};

		return app;
	};

	// return self
	return app.init();
});
