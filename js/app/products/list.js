var ProductsApp = (function(app) {
	var basePath = "mgmt/store_products/";
	app.init = function() {
		// init add wrapper marker
		app.addDtWrapper = false;

		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			iDisplayLength : 25,

			ajax : {
				url : baseUrl + basePath + '/get_data',
				data : function(d) {

				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [null, {
				data : 'store_name',
				render: function ( data, type, row ) {
					var status = '<span style="color:green;">顯示中</span>';
					if(row.post_checked == 0 || !((moment().diff(moment(row.start_time)) > 0 && moment().diff(moment(row.end_time)) < 0) || row.ever_time == 1 )) {
						status = '隱藏中';
					} else if(row.pos == 1){
						status += '<span style="color:red;">置頂中</span>';
					}
					return status;
				}
			},{
				data : 'image_id',
				render: function ( data, type, row ) {
	    			return (data && data > 0 ? '<div class="img_con" style="width:50px;height:50px;background-image:url(' + baseUrl + 'api/images/get/' + data + '/thumb)" />' : "");
		    }
			}, {
				data : 'serial'
			},{
				data : 'product_name'
			}, {
				data : 'mul_cate',
				render: function ( data, type, row ) {
	    			var html = '';

						$.each(data,function(){
							var me = this;
							html += ('<span class="badge bg-color-blue">'+me.cate_name+'</span>');
						});

	    			return html;
		    	}
			}, {
				data : 'start_time'
			}, {
				data : 'end_time'
			}, {
				data : 'price'
			}, {
				data : 'create_time'
			}],

			bSortCellsTop : true,
			order : [[9, "desc"]],
			columnDefs : [{
				targets : 0,
				data : null,
				render:function ( data, type, row ) {
					var input = '';
					if(row.post_checked == 1){
						input = '<input type="checkbox" name="product_post" class="product-post onoffswitch-checkbox" checked id="id_'+row.id+'" >'
					}else{
						input = '<input type="checkbox" name="product_post" class="product-post onoffswitch-checkbox" id="id_'+row.id+'" >'
					}
					var html = '<span class="onoffswitch" style="margin-top: 10px;">'
						+input
						+'<label class="onoffswitch-label" for="id_'+row.id+'">'
							+'<span class="onoffswitch-inner" data-swchon-text="上架" data-swchoff-text="下架"></span>'
							+'<span class="onoffswitch-switch"></span>'
						+'</label>'
					+'</span>'
					+ '<span data-target="#deleteModal" data-toggle="modal"><a href="javascript:void(0)" title="刪除"  role="button" data-toggle="tooltip" style="margin-left: 10px;"><i class="fa fa-trash fa-lg"></i></a></span>'
					+ '<span data-target="#copyModal" data-toggle="modal"><a href="javascript:void(0)" data-toggle="tooltip" title="複製" role="button" style="margin-left: 10px;"><i class="fa fa-copy fa-lg"></i></a></span>';
					return html;
		    },
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
			}, {
				"targets" : 5,
				"orderable" : false
			}, {
				"targets" : 6,
				"orderable" : false
			}, {
				"targets" : 7,
				"orderable" : false
			}, {
				"targets" : 8,
				"orderable" : false
			}
			],
			pagingType : "full_numbers",

			fnRowCallback : function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// edit click
				if(!app.disableRowClick) {
					$(nRow).find('td').not(':first').addClass('pointer').on('click', function(){
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

				// copy click
				$(nRow).find("a").eq(1).click(function() {
					app.setDelId(aData.id);
					$('#modal_do_copy')
						.prop('onclick',null)
						.off('click')
						.on('click', function(){
							app.doCopyItem();
						});
				});


				//product post
				$(nRow).find('.product-post').eq(0).click(function(){
					var $me = $(this);
					$.ajax({
						url: baseUrl + basePath + '/product_post',
						data: {
						  'product_id': aData.id
						},
				   error: function() {
				      // $('#info').html('<p>An error has occurred</p>');
				   },
				   dataType: 'json',
				   success: function(data) {
						 if(data.error_msg == 'exceed'){
							//  $me.prop('checked') = !$me.prop('checked');
							$me.prop('checked',!$me.prop('checked'));
							$.smallBox({
					      title: "超過上架限數",
					      content: "<i class='fa fa-clock-o'></i> <i>1 seconds ago...</i>",
					      color: "#e74c3c",
					      iconSmall: "fa fa-times bounce animated",
					      timeout: 4000
					    });
							return;
						 }

						 $.smallBox({
		 			      title: data.success_msg,
		 			      content: "<i class='fa fa-clock-o'></i> <i>1 seconds ago...</i>",
		 			      color: "#5F895F",
		 			      iconSmall: "fa fa-check bounce animated",
		 			      timeout: 4000
		 			    });

							// table reload
							app.tableReload();
				   },
				   type: 'POST'
					})
				})
			}
		}));
		//product_cate select2(search_bar)

		// $('#search_cate_main').select2();
		$("#search_cate_main").on('keyup change', function() {
			var mulcate_val = ''
			if($(this).val() !== null){
				mulcate_val = $(this).val();
			}
				app.mDtTable.column($(this).parent().index() + ':visible').search(mulcate_val).draw();

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

			$('[data-toggle="tooltip"]').tooltip();
		});

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
				url : baseUrl + basePath  + 'delete/' + app._delId,
				success: function() {
					app.tableReload();
				},
				failure: function() {
					alert('Network Error...');
				}
			});
		};

		//copy
		app.doCopyItem = function(){
			$.ajax({
				url : baseUrl + basePath  + 'copy/' + app._delId,
				success: function() {
					app.tableReload();
				},
				failure: function() {
					alert('Network Error...');
				}
			});
		}

		// edit

		app.doEdit = function(id) {
		    var loading = $('<h1 class="ajax-loading-animation"><i class="fa fa-cog fa-spin"></i> Loading...</h1>')
		    	.appendTo($('#edit-modal-body').empty());
		    $("#btn-submit-edit").prop( "disabled", true);

			$('.tab-pane').removeClass('active'); $('#edit_page').addClass('active');

			$('#edit-modal-body').load(baseUrl + basePath + 'edit/' + id, function(){
	        	$("#btn-submit-edit").prop( "disabled", false);
	        	loading.remove();
			});
		};
		// do submit
		app.doSubmit = function() {
			if(!$('#app-edit-form').data('bootstrapValidator').validate().isValid()) return;

			// sync ckeditor first
			for ( instance in CKEDITOR.instances )
		        CKEDITOR.instances[instance].updateElement();


			var url = baseUrl + basePath + 'insert'; // the script where you handle the form input.
			var imgIdList = app.getImgIdList();
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-edit-form").serialize()
						+ '&' + $("#app-edit-form-s3").serialize()
						+ '&' + $("#app-edit-form-s4").serialize()

						+ '&img_id_list=' + imgIdList.join(','),
				success : function(data) {
					app.tableReload();
					app.backTo();
				}
			});
		};

		//**** select search *****//
		$("#search_product_cate").on('keyup change', function() {
			var mulcate_val = ''
			if($(this).val() !== null){
				mulcate_val = $(this).val();
			}
			app.mDtTable.column($(this).parent().index() + ':visible').search(mulcate_val).draw();

		});

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



		// get year month list
		app.tableReload();

		return app;
	};
	return app.init();
});
