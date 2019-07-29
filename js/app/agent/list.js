var AgentAppClass = (function(app) {
	app.basePath = "mgmt/agent/";

	app.init = function() {
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					d.corp_id = $('#s_corp_id').val();
					d.agent_lv = $('#agent_lv').val();
					d.agent_type_id = $('#agent_type_id').val();
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post',
				complete:function(data){
					$('#s_total').html('推薦會員數:' + data.responseJSON.recordsTotal);
				}
			},

			columns : mCols,

			order : [[mOrderIdx, "desc"]],
			columnDefs : mColDefs,
			"footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
						window.mApi = api;
            // // Remove the formatting to get integer data for summation
            // var intVal = function ( i ) {
            //     return typeof i === 'string' ?
            //         i.replace(/[\$,]/g, '')*1 :
            //         typeof i === 'number' ?
            //             i : 0;
            // };
						//
            // // Total over all pages
            // total = api
            //     .column( 4 )
            //     .data()
            //     .reduce( function (a, b) {
            //         return intVal(a) + intVal(b);
            //     }, 0 );
						//
            // // Total over this page
            // pageTotal = api
            //     .column( 4, { page: 'current'} )
            //     .data()
            //     .reduce( function (a, b) {
            //         return intVal(a) + intVal(b);
            //     }, 0 );
						//
            // // Update footer
            // $( api.column( 4 ).footer() ).html(
            //     '$'+pageTotal +' ( $'+ total +' total)'
            // );
        }


		}));

		// data table actions
		app.dtActions();

		// get year month list
		app.tableReload();

		// do submit
		app.doSubmit = function(is_reload) {
			if(!$('#app-edit-form').data('bootstrapValidator').validate().isValid()) return;
			var url = baseUrl + app.basePath + 'insert'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-edit-form").serialize(),
				success : function(data) {
					app.mDtTable.ajax.reload(null, false);
					if(is_reload && is_reload == 1) {
						app.doEdit($('#item_id').val());
					} else {
						app.backTo();
					}

				}
			});
		};

		app.doFlow = function(id) {
			var loading = $('<h1 class="ajax-loading-animation"><i class="fa fa-cog fa-spin"></i> Loading...</h1>')
										.appendTo($('#edit-modal-body').empty());
			$('.tab-pane').removeClass('active');
			$('#edit_page').addClass('active');
			$('#edit-modal-body').load(baseUrl + app.basePath + 'agent_tx/' + id, function(){
        	loading.remove();
			});
		};

		app.fnRowCallbackExt = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
			// flow click
			$(nRow).find("a").eq(1).click(function() {
				app.doFlow(aData.id);
			});
		}

		app.doExportAll = function() {
			location.href = baseUrl + app.basePath + '/export_all';
		}

		$('#s_corp_id').on('change', function(){
			app.tableReload();
		});
		$('#agent_lv').on('change', function(){
			app.tableReload();
		});
		$('#agent_type_id').on('change', function(){
			app.tableReload();
		});

		return app;
	};

	// return self
	return app.init();
});
