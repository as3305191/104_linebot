var BaccaratBetSumAppClass = (function(app) {
	app.basePath = "mgmt/baccarat_bet_sum/";

	app.init = function() {

		app.disableRowClick = true;
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					d.corp_id = $('#corp_id').val();
					d.tab_type = $('#tab_type').val();
					d.create_date = $('#c_dt').val();
					d.s_user_name = $('#s_user_name').val();
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			paging:false,
			ordering: false,

			columns : [{
				data : 'user_name',
				render: function(d,t,r) {
					var html = '';
					html += r.user_account + '<br/>';
					html += r.user_name + '<br/>';
					return html;
				}
			},{
				data : 's_total_bet'
			}],

			columnDefs : [{
				"targets" : [0,1],
				"orderable" : false
			}]
		}));

		app.doSubmit = function() {
			if(!$('#app-edit-form').data('bootstrapValidator').validate().isValid()) return;

			$('#m_corp_id').val($('#corp_id').val());
			$('#m_tab_type').val($('#tab_type').val());

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

		app.doPay = function() {
			doPay();
		}

		// data table actions
		app.dtActions();

		// get year month list
		app.tableReload();

		$('#corp_id').on('change', function(){
			app.tableReload();
		});
		$('#tab_type').on('change', function(){
			app.tableReload();
		});

		return app;
	};

	// return self
	return app.init();
});
