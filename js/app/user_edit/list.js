var UserEditAppClass = (function(app) {
	app.basePath = "mgmt/user_edit/";

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
				data : 'account'
			}, {
				data : 'user_name'
			}, {
				data : 'role_name'
			}, {
				data : 'create_time'
			}],

			order : [[4, "desc"]],
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
					currentApp.doEdit($('#login_user_id').val());
				}
			});
		};

		app.upgradeMe = function() {
			if(!confirm('是否升級?')) {
				return;
			}
			var url = baseUrl + app.basePath + 'upgrade_me'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-edit-form").serialize(),
				success : function(data) {
					if(data.error_msg) {
						alert(data.error_msg);
						console.log(data);
						if(data.error_code == 98) {
							window.location.hash = '#mgmt/pay_records';
						}
					} else {
						currentApp.doEdit($('#login_user_id').val());
					}
				}
			});
		};

		// data table actions
		app.dtActions();

		// get year month list
		//app.tableReload();

		$('#role_id').on('change', function(){
			app.tableReload();
		});

		return app;
	};

	// return self
	return app.init();
});
