var GuideTestAppClass = (function(app) {
	app.basePath = "mgmt/guide_test/";
	app.init = function() {
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					d.role_id = $('#role_id').val();
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post',
				complete:function(data){
					
				}
			},

			columns : mCols,

			order : [[mOrderIdx, "desc"]],
			columnDefs : mColDefs,
			"footerCallback": function ( row, data, start, end, display ) {
          var api = this.api(), data;
					window.mApi = api;
      }

		}));

		// data table actions
		app.dtActions();

		// get year month list
		app.tableReload();


		app.doExportAll = function() {
			location.href = baseUrl + app.basePath + '/export_all';
		}

		// do submit
		app.doSubmit = function() {
			if(!$('#app-edit-form').data('bootstrapValidator').validate().isValid()) return;
			var url = baseUrl + app.basePath + 'insert'; // the script where you handle the form input.

			getLoopStoreStr();
			getRoundStoreStr();

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

		$('#role_id').on('change', function(){
			app.tableReload();
		});

		return app;
	};

	// return self
	return app.init();
});
