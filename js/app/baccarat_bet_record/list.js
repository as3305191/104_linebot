var BaccaratBetRecordAppClass = (function(app) {
	app.basePath = "mgmt/baccarat_bet_record/";

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

			ordering: false,

			columns : [{
				data : 'corp_name'
			},{
				data : 'user_name',
				render: function(d,t,r) {
					var html = '';
					html += r.user_account + '<br/>';
					html += r.user_name + '<br/>';
					return html;
				}
			},{
				data : 'tab_type',
				render: function(d,t,r){
					if(d == 1) {
						return "一般";
					}
					if(d == 2) {
						return "包廳";
					}
					return "---";
				}
			},{
				data : 'tab_name'
			},{
				data : 'round_sn'
			},{
				data : 'pos'
			}, {
				data : 'winner_type',
				render : function(d,t,r){
					var html = '';
					if(d == 0) {
						html+= "<font style='float:left' color='green'>和</font>"
					}
					if(d == 1) {
						html+= "<font style='float:left' color='red'>莊</font>"
					}
					if(d == 2) {
						html+= "<font style='float:left' color='blue'>閒</font>"
					}
					if(d == 3) {
						html+= "<font style='float:left' color='red'>莊</font><font color='gray'>對</font>"
					}
					if(d == 4) {
						html+= "<font v color='blue'>閒</font><font color='gray'>對</font>"
					}
					if(d == 6) {
						html+= "<font style='float:left' color='red'>莊</font><font style='float:left' color='gray'>對</font>" + "<font style='float:left' color='blue'>閒</font><font style='float:left' color='gray'>對</font>"
					}

					html += "<br/>";

					if(r.player_c_0) {
						html+= '<span class="cs_' + r.player_c_0 + '"></span>';
					}
					if(r.player_c_1) {
						html+= '<span class="cs_' + r.player_c_1 + '"></span>';
					}
					if(r.player_c_2) {
						html+= '<span class="cs_' + r.player_c_2 + '"></span>';
					}
					html += '<span class="cs"> = ' + r.player_val + '</span>';

					html += "<hr style='clear:both'>";

					if(r.banker_c_0) {
						html+= '<span class="cs_' + r.banker_c_0 + '"></span>';
					}
					if(r.banker_c_1) {
						html+= '<span class="cs_' + r.banker_c_1 + '"></span>';
					}
					if(r.banker_c_2) {
						html+= '<span class="cs_' + r.banker_c_2 + '"></span>';
					}
					html += '<span class="cs"> = ' + r.banker_val + '</span>';


					return html;
				}
			},{
				data : 'pos'
				,
				render : function(d,t,r){
					var html = '';
					html += '<div>和 : ' + numberWithCommas(r.bet_0 * 100,0) + '</div>';
					html += '<div>莊 : ' + numberWithCommas(r.bet_1 * 100,0) + '</div>';
					html += '<div>閒 : ' + numberWithCommas(r.bet_2 * 100,0) + '</div>';
					html += '<div>莊對 : ' + numberWithCommas(r.bet_3 * 100,0) + '</div>';
					html += '<div>閒對 : ' + numberWithCommas(r.bet_4 * 100,0) + '</div>';
					return html;
				}
			},{
				data : 'total_bet',
				render:function(d,t,r) {
					return numberWithCommas(d * 100,0);
				}
			},{
				data : 'result_bare',
				render: function(d,t,r){
					if(d >= 0 ) {
						return 0;
					}
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
