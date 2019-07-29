var navBdApp = (function(app) {
	var basePath = "mgmt/nav_bd/";
	app.init = function() {
		// init add wrapper marker
		app.addDtWrapper = false;

		app.mDtTable = $('#dt_list').DataTable({
			processing : true,
			serverSide : true,
			responsive : true,
			deferLoading : 0, // don't reload on init
			iDisplayLength : 100,
			"sDom": "<'dt-toolbar'>"+
						"t"+
						"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'>>",
			language : {
				url : baseUrl + "js/datatables-lang/zh-TW.json"
			},

			ajax : {
				url : baseUrl + basePath + '/get_data',
				data : function(d) {
					d.parent_id = app.parentId;
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},


			columns : [null, {
				data : 'icon',
				render: function ( data, type, row ) {
					var html = data;
					if(data && data.length > 0) {
						html = "<i class='fa fa-lg " + data + "'>" + "</i>";
					}
					return html;
		    	}
			}, {
				data : 'nav_name',
				render: function ( data, type, row ) {
					if(app.parentId && app.parentId > 0) {
						return data;
					}
	    			return '<a href="javascript:void(0)">' + data + '</a>';
		    	}
			}, {
				data : 'key'
			}, {
				data : 'base_path'
			}, {
				data : 'pos'
			}],

			bSortCellsTop : true,
			order : [[5, "asc"]],
			columnDefs : [{
				targets : 0,
				data : null,
				defaultContent : '<a href="javascript:void(0);" style="margin-right: 5px;"><i class="fa fa-pencil fa-lg"></i></a>'
							   + '<a href="#deleteModal" role="button" data-toggle="modal"><i class="fa fa-trash fa-lg"></i></a>',
				searchable : false,
				orderable : false,
				width : "5%",
				className : ''
			}, {
				"targets" : 1,
				"orderable" : false
			}, {
				"targets" : 2,
				"orderable" : false
			}, {
				"targets" : 3,
				"orderable" : false
			}, {
				"targets" : 4,
				"orderable" : false
			}],
			pagingType : "full_numbers",
			fnRowCallback : function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// edit click
				$(nRow).find("a").eq(0).click(function() {
					app.doEdit(aData.id);
				});
				// delete click
				$(nRow).find("a").eq(1).click(function() {
					app.setDelId(aData.id);
					$('#modal_do_delete')
						.prop('onclick',null)
						.off('click')
						.on('click', function(){
							app.doDelItem();
						});
				});

				$(nRow).find("a").eq(2).click(function() {
					app.parentId = aData.id;
					app.tableReload();
				});
			}
		});

		$("#dt_list thead th input[type=text]").on('keyup change', function() {
			app.mDtTable.column($(this).parent().index() + ':visible').search(this.value).draw();
		});

		// data table responsive
		$('#dt_list').on('draw.dt', function() {
			if (!app.addDtWrapper) {
				app.addDtWrapper = true;
				$('#dt_list').wrap($('<div></div>').addClass('table-responsive'));
			}
		});

		app.tableReload();
	};

	app.backParent = function() {
		app.parentId = 0;
		app.tableReload();
	};

	app.checkParentId = function() {
		if(app.parentId && app.parentId > 0) {
			$('#back_parent').show();
		} else {
			$('#back_parent').hide();
		}
 	};

	// table reload
	app.tableReload = function() {
		app.mDtTable.ajax.reload(null, false);

		// check parent id
		app.checkParentId();
	};

	// delete
	app.setDelId = function(delId) {
		app._delId = delId;
	};

	app.doDelItem = function() {
		$.ajax({
			url : baseUrl + basePath  + 'delete/' + app._delId,
			success: function() {
				app.mDtTable.ajax.reload();
			},
			failure: function() {
				alert('Network Error...');
			}
		});
	};

	// edit
	app.doEdit = function(id) {
	    var loading = $('<h1 class="ajax-loading-animation"><i class="fa fa-cog fa-spin"></i> Loading...</h1>')
	    	.appendTo($('#editModal .modal-body'));
	    $("#btn-submit-edit").prop( "disabled", true);
		$('#editModal').modal('show');
		$('#edit-modal-body').empty().load(baseUrl + basePath + 'edit/' + id, function(){
        	$("#btn-submit-edit").prop( "disabled", false);
        	loading.remove();

        	$('#app-edit-form').submit(function(e) {
				app.doSubmit();
			    e.preventDefault(); // avoid to execute the actual submit of the form.
			});
		});
	};

	// do submit
	app.doSubmit = function() {
		var url = baseUrl + basePath + 'insert'; // the script where you handle the form input.
		$.ajax({
			type : "POST",
			url : url,
			data : $("#app-edit-form").serialize() + '&parent_id=' + (app.parentId ? app.parentId : 0),
			success : function(data) {
				app.mDtTable.ajax.reload();
				$('#editModal').modal('hide');
			}
		});
	};

	return app;
})({});
