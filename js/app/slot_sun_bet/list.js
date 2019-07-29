var SlotSunBetAppClass = (function(app) {
	app.basePath = "mgmt/slot_sun_bet/";

	app.init = function() {

		app.disableRowClick = true;
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					d.corp_id = $('#corp_id').val();
					d.create_date = $('#c_dt').val();
					d.s_user_name = $('#s_user_name').val();
					d.s_tab_id = $('#s_tab_id').val();
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			ordering: false,

			columns : [{
				data : 'user_name',
				render: function(d,t,r) {
					var html = '';
					html += r.user_account + '<br/>';
					// html += r.user_name + '<br/>';
					return html;
				}
			},{
				data : 'parent_id',
				render:function(d,t,r) {
					var html = '';
					if(d == 0) {
						html+= "<font style='float:left' color='green'>一般</font>"
					}
					if(d > 0) {
						html+= "<font style='float:left' color='red'>FreeGame</font>"
					}

					return html;
				}
			},{
				data : 'tab_name'
			},{
				data : 'is_sp',
				render:function(d,t,r) {
					var html = '';
					if(d == 0) {
						html+= "<font style='float:left' color='green'>否</font>"
					}
					if(d == 1) {
						html+= "<font style='float:left' color='red'>是</font>"
					}

					return html;
				}
			}, {
				data : 'json',
				render : function(d,t,r){
					var html = '';
					var obj = d.length > 0 ? JSON.parse(d) : {};
					console.log(obj);
					if(obj.match_arr) {
						html += "<div>立柱數：" + obj.match_arr.length + "</div>";
						// html += "<div>倍率：" + obj.multiply_arr + "</div>";
					}
					return html;
				}
			},{
				data : 'bet_amt',
				render:function(d,t,r) {
					return numberWithCommas(d * 100,0);
				}
			},{
				data : 'win_amt',
				render:function(d,t,r) {
					return numberWithCommas(d * 100,0);
				}
			},{
				data : 'total_amt',
				render: function(d,t,r){
					return numberWithCommas(-d * 100,0);
				}
			},{
				data : 'create_time'
			}],

			columnDefs : [{
				"targets" : [0,1,2,3,4],
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
