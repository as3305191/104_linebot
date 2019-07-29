var BulletinAppClass = (function(app) {
	app.basePath = "mgmt/bulletin/";

	app.init = function() {
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					d.type_name = $('#bulletin_type').val();
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [null, {
				data : 'title'
			},{
				data : 'desc',
				render: function(d,t,r){
					return d;
				}
			},{
				data : 'pos',
				render: function(d,t,r){
					return d;
				}
			}, {
				data : 'status',
				render : function(d,t,r){
					if(d == 0) {
						return "正常"
					}
					if(d == 1) {
						return "<font color='red'>停用</font>"
					}
					return d;
				}
			}],

			order : [[4, "asc"]],

			columnDefs : [{
				targets : 0,
				data : null,
				defaultContent : '<a href="#deleteModal" role="button" data-toggle="modal" style="margin-right: 5px;"><i class="fa fa-trash fa-lg"></i></a>'
				,
				searchable : false,
				orderable : false,
				width : "5%",
				className : ''
			},{
				"targets" : [1,2,3,4],
				orderable : false
			}],

			fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
					 // edit click
					 if(!app.disableRowClick && $('#user_role').val() == '99') {
						 var _rtd = $(nRow).find('td');
						 if(!app.enableFirstClickable) {
							 _rtd = _rtd.not(':first').not(':last');
						 }
						 _rtd.addClass('pointer').on('click', function(){
							 app.doEdit(aData.id);

							 // remove all highlight first
							 $(this).parent().parent().find('tr').removeClass('active');

							 app._lastPk = aData.id;
							 app._tr = $(this).parent();
							 setTimeout(function(){
								 app._tr.addClass('active');
							 }, 100);
						 });
					 }

					 if(app._lastPk && aData.id && app._lastPk == aData.id) {
						 $(nRow).addClass('active');
					 }

					 // delete click
					 $(nRow).find("a").eq(0).click(function() {
						 app.setDelId(aData.id);

						 $('#modal_do_delete')
							 .prop('onclick',null)
							 .off('click')
							 .on('click', function(){
								 app.doDelItem();
							 });
					 });

					 // delete click
					 $(nRow).find("a").eq(1).click(function() {
						 app.setDelId(aData.id);
						 app._lastPk = aData.id;
						 $('#addModal').modal('show');
					 });
			 }

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

		$('#bulletin_type').on('change', function(){
			app.tableReload();
		});

		return app;
	};

	// return self
	return app.init();
});
