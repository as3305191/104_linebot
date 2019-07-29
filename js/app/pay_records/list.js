var PayRecordsAppClass = (function(app) {
	app.basePath = "mgmt/pay_records/";

	app.init = function() {
		app.enableFirstClickable = true;
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					// d.user_id = $('#login_user_id').val();
					d.s_corp_id = $('#s_corp_id').val();
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [{
				data : 'sn'
			}, {
				data : 'corp_name'
			}, {
				data : 'user_account'
			}, {
				data : 'amt',render: function(d,t,r) {
					return numberWithCommas(d);
				}
			}, {
				data : 'pay_type_name'
			}, {
				data : 'pay_status_name',
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

			order : [[6, "desc"]],
			columnDefs : [{
				"targets" : [0,1,2,3,4,5,6],
				"orderable" : false
			}]
		}));



		app.doSubmit = function(payType) {
			if(!$('#app-edit-form').data('bootstrapValidator').validate().isValid()) return;
			var url = baseUrl + app.basePath + 'insert'; // the script where you handle the form input.
			currentApp.waitingDialog.show();
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-edit-form").serialize(),
				success : function(data) {
					app.mDtTable.ajax.reload(null, false);
					currentApp.waitingDialog.hide();
					// app.backTo();
					if(data.last_id) {
						app.doEdit(data.last_id);
						$('body').append(
							$('<a id="m_anchor"></a>').attr('href', baseUrl + 'mgmt/pay_records/pay/' + data.last_id)
							.attr('target', '_blank')
						);

						if(payType == 7) {
							console.log(data);
						} else if (payType == 5 || payType == 6){

						} else {
							// $('#m_anchor')[0].click();
						}

						if(data.message) {
							alert(data.message);
						}

						//$('#m_anchor').remove();
						// window.open(
						//   baseUrl + 'mgmt/pay_records/pay/' + data.last_id,
						//   '_blank'
						// );
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

		$('#role_id').on('change', function(){
			app.tableReload();
		});

		$('#s_corp_id').on('change', function() {
			var me = this;
			setTimeout(function(){
				app.tableReload();
			}, 100);
		});

		return app;
	};

	// return self
	return app.init();
});

function doPay() {
	alert('buying...');
}
