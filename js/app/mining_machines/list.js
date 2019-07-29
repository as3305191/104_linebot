var MiningMachinesAppClass = (function(app) {
	app.basePath = "mgmt/mining_machines/";

	app.init = function() {

		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					d.corp_id = $('#corp_sel').val();
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [null, {
				data : 'corp_name',
				render: function(d,t,r) {
					// return d + ' <font color="red">還有 ' + (parseInt(r.max_days) - parseInt(r.day_diff))  + '天</font>';
					return d;
				}
			}, {
				data : 'machine_name'
			}, {
				data : 'card'
			}, {
				data : 'ntd_price'
			}, {
				data : 'max_days',
				render: function(d,t,r) {
					var d = parseFloat(d);
					return d.toFixed(4);
				}
			}, {
				data : 'create_time'
			}],

			order : [[6, "desc"]],
			columnDefs : [{
				targets : 0,
				data : null,
				defaultContent : app.defaultContent,
				searchable : false,
				orderable : false,
				width : "5%",
				className : ''
			},{
				"targets" : [1,2,3,4,5],
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

		$('#corp_sel').on('change', function(){
			app.tableReload();
		});

		return app;
	};

	// return self
	return app.init();
});
