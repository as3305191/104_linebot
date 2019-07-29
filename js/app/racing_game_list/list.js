var RacingGameListAppClass = (function(app) {
	app.basePath = "mgmt/racing_game_list/";

	app.init = function() {
		app.enableFirstClickable = true;
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

			columns : [{
				data : 'sn'
			}, {
				data : 'status',
				render: function(d,t,r) {
					switch (d) {
						case '0':
							return "等待";
						case '1':
							return "下注";
						case '2':
							return "賽車";
						case '3':
							return "開盤";
						case '4':
							return "結束";
						default:
							return '-';
					}
				}
			},{
				data : 'start_time'
			},{
				data : 'open_time'
			},{
				data : 'ranking'
			},{
				data : 'bet_amt'
			},{
				data : 'bet_win_amt'
			},{
				data : 'pool_amt'
			}],

			ordering: false,
			columnDefs : [{
				"targets" : [0,1],
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

		app.mDtTable.on('xhr', function(e, settings, json, xhr){
			$('#sum_pool_amt').html(numberWithCommas(parseInt(json.pool_amt)));
		});

		app.addPool = function() {

			var $pool_amt = $('#pool_diff_amt').val();

			var url = baseUrl + app.basePath + 'add_pool'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : {
					pool_amt: $pool_amt,
					corp_id: $('#corp_sel').val()
				},
				success : function(data) {
					app.mDtTable.ajax.reload(null, false);
					$('#pool_diff_amt').val(0);
				}
			});
		};

		return app;
	};

	// return self
	return app.init();
});
