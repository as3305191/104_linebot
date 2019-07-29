var proProductApp = (function(app) {
	var basePath = "store/products/";

	app.init = function() {
		// init add wrapper marker
		app.addDtWrapper = false;
		app.productsChoice = '';
		app.mDtTable = $('#dt_lists').DataTable($.extend(app.dtConfig,{
			iDisplayLength : 25,
			ajax : {
				url : baseUrl + basePath + '/get_data_pro',
				data : function(d) {
					d.product_pro_id = $('input[name="id"]').val();
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [{
				data : 'image_id',
				render: function ( data, type, row ) {
	    			return (data && data > 0 ? '<image src="' + baseUrl + 'api/images/get/' + data + '/thumb" style="width:90px" />' : "");
		    	}
			},{
				data : 'serial'
			},{
				data : 'product_name'
			},{
				data : 'mul_cate',
				render: function ( data, type, row ) {
	    			var html = '';
						$.each(data,function(){
							var me = this;
							html += ('<span class="badge bg-color-blue">'+me.cate_name+'</span>');
						});
	    			return html;
		    	}
			},{
				data : 'price'
			}],

			bSortCellsTop : true,
			// order : [[4, "desc"]],
			columnDefs : [{
				"targets" : 0,
				"orderable" : false
			},{
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
			}
			],
			pagingType : "full_numbers",
			fnRowCallback : function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// edit click
				if(!app.disableRowClick) {
					$(nRow).find('td').addClass('pointer').on('click', function(){
						var obj = {
							id: 0,
							product_ref_id:aData.id,
							product_pro_id:$('input[name="id"]').val(),
							is_delete: 0,
							numbers:0,
							//irrelevent
							image_id:aData.image_id,
							serial:aData.serial,
							product_name:aData.product_name,
						};
						proStore.push(obj);
						redrawPro();
						$('#productModal').modal('hide');
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
						 }
				   },
				   type: 'POST'
					})
				})
			}
		}));

		//**** select search *****//
		//store
		$("#search_store").on('keyup change', function() {
				app.mDtTable.column($(this).parent().index() + ':visible').search(this.value).draw();
		});

		// $('#search_cate_main').select2();
		$("#search_cate_main").on('keyup change', function() {
			var mulcate_val = ''
			if($(this).val() !== null){
				mulcate_val = $(this).val();
			}
			app.mDtTable.column($(this).parent().index() + ':visible').search(mulcate_val).draw();
		});

		$("#dt_lists thead th input[type=text]").on('keyup change', function() {
			app.mDtTable.column($(this).parent().index() + ':visible').search(this.value).draw();
		});

		// data table responsive
		$('#dt_lists').on('draw.dt', function() {
			if (!app.addDtWrapper) {
				app.addDtWrapper = true;
				$('#dt_lists').wrap($('<div></div>').addClass('table-responsive'));
			}
		});
		// get year month list
		app.tableReload();
		return app;
	};

	return app.init();
});
