var RankingAppClass = (function(app) {
	app.basePath = "mgmt/ranking/";
	app.init = function() {
		// init add wrapper marker
		app.addDtWrapper = false;
		app.disableRowClick = true;

		app.mDtTable = $('#dt_list').DataTable({
			processing : true,
			serverSide : true,
			responsive : true,
			deferLoading : 0, // don't reload on init
			iDisplayLength : 100,
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
					d.dt = $('#m_date').val();
					return d;
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [{
				data : 'nick_name'
			}, {
				data : 'score'
			}],

			bSortCellsTop : true,
			order : [[1, "desc"]],
			columnDefs : [{
				"targets" : [0,1],
				"orderable" : false
			}],
			fnRowCallback : function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// disable row click
				return;
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

					$('#deleteModalBody').html(
						'<div class="alert alert-warning fade in">' +
							'<i class="fa fa-warning modal-icon"></i>' +
							'<strong>Warning</strong> 確定要刪除嗎? <br>' +
							'無法復原' +
						'</div>'
					);

					$('#deleteModalLabel').html(
						'刪除確認'
					);

					$('#modal_do_delete')
						.prop('onclick',null)
						.off('click')
						.on('click', function(){
							app.doDelItem();
						}).html('刪除');
				});

				$('#m_date').on("change", function(){
					app.tableReload();
				})

				// push click
				$(nRow).find("a").eq(1).click(function() {
					app.setDelId(aData.id);

					$('#deleteModalBody').html(
						'<div class="alert alert-warning fade in">' +
							'<i class="fa fa-warning modal-icon"></i>' +
							'<strong>Warning</strong> 確定要推播嗎? <br>' +
							'無法復原' +
						'</div>'
					);

					$('#deleteModalLabel').html(
						'推播確認'
					);

					$('#modal_do_delete')
						.prop('onclick',null)
						.off('click')
						.on('click', function(){
							app.doPushItem();
						}).html('推播');
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

		app.doSubmit = function() {
			if(!$('#app-edit-form').data('bootstrapValidator').validate().isValid()) return;
			var url = baseUrl + app.basePath + 'insert'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-edit-form").serialize(),
				success : function(data) {
					if(data.error_msg) {
						layer.msg(data.error_msg);
					} else {
						app.mDtTable.ajax.reload(null, false);
						app.backTo();
					}

				}
			});
		};

		app.doPushItem = function() {
			layer.load(1);
			$.ajax({
				url : baseUrl + app.basePath  + 'do_push/' + app._delId,
				success: function() {
					//alert('hi..');
					layer.close(layer.load(1));
					app.tableReload();
				},
				failure: function() {
					layer.close(layer.load(1));
					layer.msg('Network Error...');
				}
			});
		};



		return app;
	};

	// return self
	return app.init();
});
