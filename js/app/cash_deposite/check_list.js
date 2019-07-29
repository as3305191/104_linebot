var CashDepositeCheckAppClass = (function(app) {
	app.basePath = "mgmt/cash_deposite/";

	app.init = function() {
		app.enableFirstClickable = true;
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					d.status = $('#q_status').val();
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [{
				data : 'corp_id',
				render:function(d,t,r) {
					return r.corp_name;
				}
			},{
				data : 'sn'
			},{
				data : 'user_account'
			}, {
				data : 'amt'
			}, {
				data : 'status_name',
				render: function(d,t,r) {
					if(r.status == 0) {
						return "<font color='red'>" + d + "</font>";
					}
					if(r.status == 1) {
						return "<font color='green'>" + d + "</font>";
					}
					return d;
				}
			}, {
				data : 'create_time'
			}],

			order : [[5, "desc"]],
			columnDefs : [{
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
			}, {
				"targets" : 4,
				"orderable" : false
			}]
		}));

		app.doSubmit = function(status) {
			if(!confirm('是否確定')) return;

			if(!$('#app-edit-form').data('bootstrapValidator').validate().isValid()) return;

			$('#status').val(status);
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

		// data table actions
		app.dtActions();

		$('#s_corp_id').on('change', function() {
			var me = this;
			setTimeout(function(){
				app.mDtTable.column($(me).parent().index() + ':visible').search($(me).val()).draw();
			}, 100);
		});

		// get year month list
		app.tableReload();

		$('#q_status').on('change', function(){
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
