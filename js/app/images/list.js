var imagesApp = (function(app) {
	var basePath = "mgmt/images/";

	app.init = function() {
		// init add wrapper marker
		app.addDtWrapper = false;

		app.mDtTable = $('#dt_list').DataTable({
			processing : true,
			serverSide : true,
			responsive : true,
			deferLoading : 0, // don't reload on init
			iDisplayLength : 10,
			"sDom": "<'dt-toolbar'<'col-sm-12 col-xs-12'l>r>"+
						"t"+
						"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
			language : {
				url : baseUrl + "js/datatables-lang/zh-TW.json"
			},

			ajax : {
				url : baseUrl + basePath + '/get_data',
				data : function(d) {
					d.store_id = storeId;
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [null, {
				data : 'id',
				render: function ( data, type, row ) {
	    			var html = '<a href="javascript:void(0)"><image src="' + baseUrl + 'api/images/get/' + data + '/thumb" style="width:120px" /></a>';
	    			return html;
		    	}
			}, {
				data : 'title',
				render: function ( data, type, row ) {
	    			var html = '<a href="javascript:void(0)" data-type="text"></a>';
	    			return html;
		    	}

			}, {
				data : 'image_name'
			}, {
				data : 'image_url'
			}],

			bSortCellsTop : true,
			order : [[1, "desc"]],
			columnDefs : [{
				targets : 0,
				data : null,
				defaultContent : '<a href="#deleteModal" role="button" data-toggle="modal"><i class="fa fa-trash fa-lg"></i></a>',
				searchable : false,
				orderable : false,
				width : "5%",
				className : ''
			}, {
				"targets" : 2,
				"orderable" : false
			}, {
				"targets" : 3,
				"orderable" : false
			}],
			pagingType : "full_numbers",
			fnRowCallback : function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
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

				// image click
				$(nRow).find("a").eq(1).click(function() {
					var imgUrl = baseUrl + 'api/images/get/' + aData.id;
					window.opener.callbackImgUrl(imgUrl);
					close();
				});

				// title click
				$(nRow).find("a").eq(2).editable({
					type: 'text',
					pk: aData.id,
				    url: 'update_title',
				    value: aData.title
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

		// get year month list
		app.tableReload();
	};

	// table reload
	app.tableReload = function() {
		app.mDtTable.ajax.reload(null, false);
	};

	// delete
	app.setDelId = function(delId) {
		app._delId = delId;
	};

	app.doDelItem = function() {
		$.ajax({
			url : baseUrl + basePath  + 'delete_status/' + app._delId,
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
			data : $("#app-edit-form").serialize(),
			success : function(data) {
				app.mDtTable.ajax.reload();
				$('#editModal').modal('hide');
			}
		});
	};

	return app;
})({});


$("#file-input").fileinput({
		language: "zh-TW",
	  initialPreview: [],
		initialPreviewConfig: [],
	  initialPreviewAsData: true,
	  maxFileCount: 1,
	  showPreview: false,
	  allowedFileExtensions: ["jpg", "jpeg", "png", "gif"],
	  uploadUrl: 'upload/general_img',
	  layoutTemplates: {
	      main1: "{preview}\n" +
	      "<div class=\'input-group {class}\'>\n" +
	      "   <div class=\'input-group-btn\'>\n" +
	      "       {browse}\n" +
	      "       {upload}\n" +
	      "       {remove}\n" +
	      "   </div>\n" +
	      "   {caption}\n" +
	      "</div>"
	  },
	  uploadExtraData: {
			store_id: storeId
	  }
  }).on('fileuploaded', function(event, data, previewId, index) {
    	// reload data
	   imagesApp.tableReload();

	   setTimeout(function(){ $('#file-input').fileinput('clear');}, 500);
	}).on('fileuploaderror', function(event, data, previewId, index) {
		alert('upload error');
	}).on('filedeleted', function(event, key) {

	});
