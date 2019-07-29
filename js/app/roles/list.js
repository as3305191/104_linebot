var RolesAppClass = (function(app) {
	app.basePath = "mgmt/roles/";
	app.init = function() {
		// init add wrapper marker
		app.addDtWrapper = false;

		app.mDtTable = $('#dt_list').DataTable({
			processing : true,
			serverSide : true,
			responsive : true,
			deferLoading : 0, // don't reload on init
			iDisplayLength : 10,
			sDom: "<'dt-toolbar'<'col-sm-12 col-xs-12'p>r>"+
						"<'t-box'"+
						"t"+
						">"+
						"<'dt-toolbar-footer'<'col-sm-6 col-xs-12'i><'col-xs-12 col-sm-6 hidden-xs'l>>",
			language : {
				url : baseUrl + "js/datatables-lang/zh-TW.json"
			},

			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					d.status_filter = $('input[name=options]:checked').val();
					return d;
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [{
				data : 'id'
			}, {
				data : 'role_name'
			}],

			bSortCellsTop : true,
			order : [[0, "desc"]],
			columnDefs : [{
				"targets" : 0,
				"orderable" : false
			}, {
				"targets" : 1,
				"orderable" : false
			}
			],
			fnRowCallback : function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// edit click
				$(nRow).find('td').not(':first').addClass('pointer').on('click', function(){
					app.doEdit(aData.id);
					if(app._tr) {
						app._tr.toggleClass('active');
					}

					app._tr = $(this).parent();
					app._tr.toggleClass('active');
				});

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
			}
		});

		// search box
		$("#dt_list thead th input[type=text]").on('keyup change', function() {
			setTimeout(function(){
				app.mDtTable.column($(this).parent().index() + ':visible').search(this.value).draw();
			}, 500);
		});

		// trigger on resize when draw datatable
		$('#dt_list').on('draw.dt', function(){
			wOnResize();
		});

		// get year month list
		app.tableReload();

		// set status filter
		$('#status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});

		return app;
	};

	// return self
	return app.init();
});
