var CorpAppClass = (function(app) {
	app.basePath = "mgmt/corp/";

	app.init = function() {
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					d.lang = $('#sys_lang').val();
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [null, {
				data : 'type_name'
			},{
				data : 'lang_name'
			},{
				data : 'corp_code'
			}, {
				data : 'corp_name'
			}, {
				data : 'sys_name'
			}, {
				data : 'currency'
			}, {
				data : 'status',
				render : function(d,t,r){
					if(d == 0) {
						return "正常"
					}
					if(d == 2) {
						return "<font color='red'>停用</font>"
					}
					return d;
				}
			}, {
				data : 'create_time'
			}],

			order : [[8, "desc"]],
			columnDefs : [{
				targets : 0,
				data : null,
				defaultContent : app.defaultContent,
				searchable : false,
				orderable : false,
				width : "5%",
				className : ''
			},{
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
			}]
		}));

		app.doSubmit = function() {
			if(!$('#app-edit-form').data('bootstrapValidator').validate().isValid()) return;

			$('#lang').val($('#sys_lang').val());

			var url = baseUrl + app.basePath + 'insert'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-edit-form").serialize(),
				success : function(data) {
					app.mDtTable.ajax.reload(null, false);
					app.backTo();

					if($('#l_user_role').val() != '99') {
						app.doEdit($('#l_corp_id').val());
					} else {
						app.backTo();
					}

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

		
		$('#sys_lang').on('change', function(){
			app.tableReload();
		});

		return app;
	};

	// return self
	return app.init();
});
