var ComTxAppClass = (function(app) {
	app.basePath = "mgmt/com_tx/";

	app.init = function() {
		app.enableFirstClickable = true;
		app.disableRowClick = true;

		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					d.user_id = $('#login_user_id').val();
					d.start_date = $('#start_date').val();
					d.end_date = $('#end_date').val();
					d.search_corp_id = $('#corp_id').val();
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			iDisplayLength : 100,

			columns : [{
				data : 'note',
				render: function(d,t,r) {
					return d;
				}
			}, {
				data : 'amt',
				render: function(d,t,r) {
					if(parseInt(d) >= 0) {
						return "<span style='color:green'>" + d + "</span>";
					} else {
						return "<span style='color:red'>" + d + "</span>";
					}
					return d;
				}
			}, {
				data : 'create_time'
			}],

			order : [[2, "desc"]],
			columnDefs : [{
				"targets" : [0,1,2],
				"orderable" : false
			}]
		}));

		// edit
		app.doEdit = function(id) {
		    var loading = $('<h1 class="ajax-loading-animation"><i class="fa fa-cog fa-spin"></i> Loading...</h1>')
		    	.appendTo($('#edit-modal-body').empty());
		    $("#btn-submit-edit").prop( "disabled", true);

			$('.tab-pane').removeClass('active'); $('#edit_page').addClass('active');

			$('#edit-modal-body').load(baseUrl + app.basePath + 'edit/' + id, function(){
	        	$("#btn-submit-edit").prop( "disabled", false);
	        	loading.remove();
			});
		};

		app.doSubmit = function() {
			if(!$('#app-edit-form').data('bootstrapValidator').validate().isValid()) return;

			if($('#amt').val() <= 0) {
				alert('數量需大於零');
				return;
			}
			var url = baseUrl + app.basePath + 'insert'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-edit-form").serialize(),
				success : function(data) {
					if(data.error_msg) {
						alert(data.error_msg);
					} else {
						app.mDtTable.ajax.reload(null, false);
						app.backTo();
					}
				}
			});
		};

		app.mDtTable.on('xhr', function(e, settings, json, xhr){
			$('#sum_amt_ntd').html(numberWithCommas(parseInt(json.sum_amt_ntd)));
			$('#sum_amt_range').html(numberWithCommas(parseInt(json.sum_amt_range)));

		});

		// data table actions
		app.dtActions();

		// get year month list
		app.tableReload();

		return app;
	};

	// return self
	return app.init();
});

function doPay() {
	alert('buying...');
}
