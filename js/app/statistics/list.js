var StatisticsAppClass = (function(app) {
	app.basePath = "mgmt/statistics/";
	app.init = function() {
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {

				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [{
				data : 'store_name'
			},{
				data : 'rank',
				render: function(data) {
					return 999;
				}
			},{
				data : 'rank'
			},{
				data : 'phone'
			}, {
				data : 'address'
			}, {
				data : 'email'
			},{
				data : 'create_time'
			}],

			order : [[5, "desc"]],
			columnDefs : [{
				"targets" : 0,
				"orderable" : false
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
			}, {
				"targets" : 5,
				"orderable" : false
			},{
				"targets" : 6,
				"orderable" : false
			}],

			fnRowCallback : function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
						// edit click
						if(!app.disableRowClick) {
							$(nRow).find('td').addClass('pointer').on('click', function(){
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
				}
		}));

		// data table actions
		app.dtActions();

		// get year month list
		app.tableReload();

		//edit
		// do submit
		app.doSubmit = function() {
			if(!$('#app-edit-form').data('bootstrapValidator').validate().isValid()) return;
			var url = baseUrl + app.basePath + 'insert'; // the script where you handle the form input.
			var imgIdList = app.getImgIdList();
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-edit-form").serialize()
				+ '&img_id_list=' + imgIdList.join(',')
				+ '&' +$('#app-edit-form-s3').serialize(),
				success : function(data) {
					app.tableReload();
					app.backTo();

				}
			});
		};

		app.getImgIdList = function() {
			var idList = [];
			$('.kv-file-content img').each(function() {
				if($(this).attr('src').indexOf('http') == 0) {
					var _id = $(this).attr('src').split('/').pop();
					idList.push(_id);
				}
			});
			return idList;
		};

		// image operations
		app.addImg = function(id) {
			if(!app.imgIds) {
				app.imgIds = [];
			}
			app.imgIds.push(id);
		};

		app.delImg = function(id) {
			if(app.imgIds && app.imgIds.length > 0) {
				for(var i = app.imgIds.length - 1; i >= 0; i--) {
				    if(app.imgIds[i] === id) {
				       app.imgIds.splice(i, 1);
				    }
				}
			}
		};

		app.getImgs = function() {
			if(!app.imgIds) {
				app.imgIds = [];
			}
			return app.imgIds;
		};

		app.clearImgs = function() {
			app.imgIds = [];
		};

		return app;
	};

	// return self
	return app.init();
});
