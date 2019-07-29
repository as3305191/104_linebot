var UsersAppClass = (function(app) {
	app.basePath = "mgmt/users/";
	app.basePath1 = "mgmt/images/get/";

	app.init = function() {
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					d.role_id = $('#role_id').val();
					d.is_valid_mobile = $('#is_valid_mobile').val();
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
		app.doSubmit = function() {
			// if(!$('#app-edit-form').data('bootstrapValidator').validate().isValid()) return;
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

		app.doExportAll = function() {
			location.href = baseUrl + app.basePath + '/export_all';
		}

		$('#role_id').on('change', function(){
			app.tableReload();
		});
		$('#is_valid_mobile').on('change', function(){
			app.tableReload();
		});

		return app;
	};

	// return self
	return app.init();
});

var FishtableLotteryAppClass = (function(app) {
	app.basePath = "mgmt/users/";
	app.disableRowClick = true;
	app.fnRowCallback1 = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// edit click
				if(!app.disableRowClick) {
					var _rtd = $(nRow).find('td');
					if(!app.enableFirstClickable) {
						_rtd = _rtd.not(':first').not(':last')
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

				if(app.fnRowCallbackExt) {
					app.fnRowCallbackExt(nRow, aData, iDisplayIndex, iDisplayIndexFull);
				}
		};

	app.dtConfig = {
		processing : true,
		serverSide : true,
		responsive : true,
		deferLoading : 0, // don't reload on init
		iDisplayLength : 10,
		sDom: app.sDom,
		language : {
			url : baseUrl + "js/datatables-lang/zh-TW.json"
		},
		bSortCellsTop : true,
		fnRowCallback : app.fnRowCallback1,
		footerCallback: function( tfoot, data, start, end, display ) {
			setTimeout(function(){ $(window).trigger('resize'); }, 300);
		}
	};


	app.init = function() {
		app.mDtTable = $('#lottery_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data_lottery',
				data : function(d) {
					d.user_id = $('#item_id').val();
					d.lottery_no = $('#lottery_select').val();

					return d;
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			pageLength: 50,

			columns : [{
				data : 'lottery_no'
			},{
				data : 'lottery_name'
			},{
				data : 'sn'
			}],
			ordering: false,
			order : [[0, "desc"]],
			columnDefs : [{
				"targets" : [0, 1,2],
				"orderable" : false
			}],

			footerCallback: function (row, data, start, end, display ) {
        var api = this.api();

      }

		}));

		// data table actions
		app.dtActions();

		function getCoVal(co, key) {
			if(co[key]) {
				return parseInt(co[key]);
			}
			return 0;
		}

		function setSpanVal(elId, val) {
			console.log("val: " + val);
			console.log("elId: " + elId);
			if(val > 0) {
	    		$('#' + elId).parent().find('span').show().text(val);
	    	} else {
	    		$('#' + elId).parent().find('span').hide();
	    	}
		}

		app.mDtTable.on( 'xhr', function () {
		    var json = app.mDtTable.ajax.json();
				$('#sum_orders').html(numberWithCommas(json.items.length));

				var sumWeight = 0;
				$.each(json.items, function(){
					sumWeight += parseFloat(this.sum_weight);
				});
				$('#sum_weight').html(numberWithCommas(sumWeight.toFixed(2)));
		});

		// get year month list
		app.tableReload();

		// set status filter
		$('#status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});

		$('#lottery_select').change(function(){
			app.tableReload();
		});

		$('#status_filter > label > span').hide();

		// set pay status filter
		$('#pay_status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});
		$('#pay_status_filter > label > span').hide();


		// do submit
		app.doSubmit = function() {
			// if(!$('#app-lottery-edit-form').data('bootstrapValidator').validate().isValid()) return;
			var url = baseUrl + app.basePath + 'insert_fish_tab_lottery'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-lottery-edit-form").serialize(),
				success : function(data) {
					if(data.error_msg) {
						layer.msg(data.error_msg);
					} else {
						app.mDtTable.ajax.reload(null, false);
					}
					// app.backTo();
				}
			});
		};

		app.doDelItem = function() {
			$.ajax({
				url : baseUrl + app.basePath  + 'delete_tab_lottery/' + app._delId,
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
		    	.appendTo($('#edit-modal-body').empty());
		    $("#btn-submit-edit").prop( "disabled", true);

			$('.tab-pane').removeClass('active'); $('#edit_page').addClass('active');

			$('#edit-modal-body').load(baseUrl + 'mgmt/fish_table/edit/' + id, function(){
	        	$("#btn-submit-edit").prop( "disabled", false);
	        	loading.remove();
			});
		};

		app.doFlow = function(id) {
			$('#edit_page_id').val(id);
			$('#edit_page111').modal('show');

		};

		app.fnRowCallbackExt = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {

			$(nRow).find("a").eq(1).click(function() {
				if(aData.is_current == 1) {
					layer.msg("當期無法開獎")
					return;
				}

				layer.prompt({
				  formType: 0,
				  title: '請輸入開獎號碼'
				}, function(value, index, elem){
					var url = baseUrl + app.basePath + 'do_open/' + aData.id; // the script where you handle the form input.
					var _mLoad = layer.load(0);
					$.ajax({
						type : "POST",
						url : url,
						data: {
							val: value
						},
						success : function(data) {
							if(data.error_msg) {
								layer.msg(data.error_msg);
							} else {
								app.mDtTable.ajax.reload(null, false);
								layer.close(index);
							}
							layer.close(_mLoad);
						}
					});

				});
			});
		}

		app.doExportAll = function() {
			location.href = baseUrl + app.basePath + '/export_all';
		}

		// station change
		$('#s_station_id').on('change', function(){
			app.tableReload();
		});
		$('#s_bypass_101').on('change', function(){
			app.tableReload();
		});

		$('#s_multiple').on('change', function(){
			if($('#s_multiple').prop("checked")) {
				// multiple
				$('#e_dt').prop("disabled", false)
			} else {
				$('#e_dt').prop("disabled", true)
			}

			app.tableReload();
		});

		$(".dt_picker").datetimepicker({
			format: 'YYYY-MM-DD'
		}).on('dp.change',function(event){
			currentApp.tableReload();
		});

		return app;
	};

	// return self
	return app.init();
});

var FishtableGiftAppClass = (function(app) {
	app.basePath = "mgmt/users/";
	app.disableRowClick = true;
	app.fnRowCallback1 = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// edit click
				if(!app.disableRowClick) {
					var _rtd = $(nRow).find('td');
					if(!app.enableFirstClickable) {
						_rtd = _rtd.not(':first').not(':last')
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

				if(app.fnRowCallbackExt) {
					app.fnRowCallbackExt(nRow, aData, iDisplayIndex, iDisplayIndexFull);
				}
		};

	app.dtConfig = {
		processing : true,
		serverSide : true,
		responsive : true,
		deferLoading : 0, // don't reload on init
		iDisplayLength : 10,
		sDom: app.sDom,
		language : {
			url : baseUrl + "js/datatables-lang/zh-TW.json"
		},
		bSortCellsTop : true,
		fnRowCallback : app.fnRowCallback1,
		footerCallback: function( tfoot, data, start, end, display ) {
			setTimeout(function(){ $(window).trigger('resize'); }, 300);
		}
	};


	app.init = function() {
		app.mDtTable = $('#gift_record_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data_gift',
				data : function(d) {
					d.user_id = $('#item_id').val();
					d.gift_select = $('#gift_select').val();

					return d;
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			pageLength: 50,

			columns : [{
				data : 'in_user_id',
				render: function(d,t,r) {
					if(d !== $('#item_id').val()) {
						return "<font color='blue'>贈禮</font>";
					} else {
						return "<font color='green'>收禮</font>";
					}
				}
			},{
				data : 'out_user_nick_name'
			},{
				data : 'in_user_nick_name'
			},{
				data : 'amt'
			},{
				data : 'status_name'
			},{
				data : 'create_time'
			}],
			ordering: false,
			order : [[0, "desc"]],
			columnDefs : [{
				"targets" : [0,1,2],
				"orderable" : false
			}],

			footerCallback: function (row, data, start, end, display ) {
        var api = this.api();

      }

		}));

		// data table actions
		app.dtActions();

		function getCoVal(co, key) {
			if(co[key]) {
				return parseInt(co[key]);
			}
			return 0;
		}

		function setSpanVal(elId, val) {
			console.log("val: " + val);
			console.log("elId: " + elId);
			if(val > 0) {
	    		$('#' + elId).parent().find('span').show().text(val);
	    	} else {
	    		$('#' + elId).parent().find('span').hide();
	    	}
		}

		app.mDtTable.on( 'xhr', function () {
		    var json = app.mDtTable.ajax.json();
				$('#sum_orders').html(numberWithCommas(json.items.length));

				var sumWeight = 0;
				$.each(json.items, function(){
					sumWeight += parseFloat(this.sum_weight);
				});
				$('#sum_weight').html(numberWithCommas(sumWeight.toFixed(2)));
		});

		// get year month list
		app.tableReload();

		// set status filter
		$('#status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});
		$('#status_filter > label > span').hide();

		$('#gift_select').change(function(){
			app.tableReload();
		});

		// set pay status filter
		$('#pay_status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});
		$('#pay_status_filter > label > span').hide();


		// do submit
		app.doSubmit = function() {
			// if(!$('#app-lottery-edit-form').data('bootstrapValidator').validate().isValid()) return;
			var url = baseUrl + app.basePath + 'insert_fish_tab_lottery'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-lottery-edit-form").serialize(),
				success : function(data) {
					if(data.error_msg) {
						layer.msg(data.error_msg);
					} else {
						app.mDtTable.ajax.reload(null, false);
					}
					// app.backTo();
				}
			});
		};

		app.doDelItem = function() {
			$.ajax({
				url : baseUrl + app.basePath  + 'delete_tab_lottery/' + app._delId,
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
		    	.appendTo($('#edit-modal-body').empty());
		    $("#btn-submit-edit").prop( "disabled", true);

			$('.tab-pane').removeClass('active'); $('#edit_page').addClass('active');

			$('#edit-modal-body').load(baseUrl + 'mgmt/fish_table/edit/' + id, function(){
	        	$("#btn-submit-edit").prop( "disabled", false);
	        	loading.remove();
			});
		};

		app.doFlow = function(id) {
			$('#edit_page_id').val(id);
			$('#edit_page111').modal('show');

		};

		app.fnRowCallbackExt = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {

			$(nRow).find("a").eq(1).click(function() {
				if(aData.is_current == 1) {
					layer.msg("當期無法開獎")
					return;
				}

				layer.prompt({
				  formType: 0,
				  title: '請輸入開獎號碼'
				}, function(value, index, elem){
					var url = baseUrl + app.basePath + 'do_open/' + aData.id; // the script where you handle the form input.
					var _mLoad = layer.load(0);
					$.ajax({
						type : "POST",
						url : url,
						data: {
							val: value
						},
						success : function(data) {
							if(data.error_msg) {
								layer.msg(data.error_msg);
							} else {
								app.mDtTable.ajax.reload(null, false);
								layer.close(index);
							}
							layer.close(_mLoad);
						}
					});

				});
			});
		}

		app.doExportAll = function() {
			location.href = baseUrl + app.basePath + '/export_all';
		}

		// station change
		$('#s_station_id').on('change', function(){
			app.tableReload();
		});
		$('#s_bypass_101').on('change', function(){
			app.tableReload();
		});

		$('#s_multiple').on('change', function(){
			if($('#s_multiple').prop("checked")) {
				// multiple
				$('#e_dt').prop("disabled", false)
			} else {
				$('#e_dt').prop("disabled", true)
			}

			app.tableReload();
		});

		$(".dt_picker").datetimepicker({
			format: 'YYYY-MM-DD'
		}).on('dp.change',function(event){
			currentApp.tableReload();
		});

		return app;
	};

	// return self
	return app.init();
});

var RechargerecordAppClass = (function(app) {
	app.basePath = "mgmt/users/";
	app.disableRowClick = true;
	app.fnRowCallback1 = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// edit click
				if(!app.disableRowClick) {
					var _rtd = $(nRow).find('td');
					if(!app.enableFirstClickable) {
						_rtd = _rtd.not(':first').not(':last')
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

				if(app.fnRowCallbackExt) {
					app.fnRowCallbackExt(nRow, aData, iDisplayIndex, iDisplayIndexFull);
				}
		};

	app.dtConfig = {
		processing : true,
		serverSide : true,
		responsive : true,
		deferLoading : 0, // don't reload on init
		iDisplayLength : 10,
		sDom: app.sDom,
		language : {
			url : baseUrl + "js/datatables-lang/zh-TW.json"
		},
		bSortCellsTop : true,
		fnRowCallback : app.fnRowCallback1,
		footerCallback: function( tfoot, data, start, end, display ) {
			setTimeout(function(){ $(window).trigger('resize'); }, 300);
		}
	};


	app.init = function() {
		app.mDtTable = $('#recharge_record_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_recharge_record',
				data : function(d) {
					d.user_id = $('#item_id').val();
					// d.gift_select = $('#gift_select').val();

					return d;
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			pageLength: 50,

			columns : [{
				data : 'name',
			},{
				data : 'number'
			},{
				data : 'total'
			},{
				data : 'status',
				render : function(d,t,r){
				if(d == 1){
							return '<span style="color:green">已付款</span>';
						} else if(d == 0){
							return '<span style="color:red">未付款</span>';
						}
					}
			}],
			ordering: false,
			order : [[0, "desc"]],
			columnDefs : [{
				"targets" : [0,1,2],
				"orderable" : false
			}],

			footerCallback: function (row, data, start, end, display ) {
        var api = this.api();

      }

		}));

		// data table actions
		app.dtActions();

		function getCoVal(co, key) {
			if(co[key]) {
				return parseInt(co[key]);
			}
			return 0;
		}

		function setSpanVal(elId, val) {
			console.log("val: " + val);
			console.log("elId: " + elId);
			if(val > 0) {
	    		$('#' + elId).parent().find('span').show().text(val);
	    	} else {
	    		$('#' + elId).parent().find('span').hide();
	    	}
		}

		app.mDtTable.on( 'xhr', function () {
		    var json = app.mDtTable.ajax.json();
				$('#sum_orders').html(numberWithCommas(json.items.length));

				var sumWeight = 0;
				$.each(json.items, function(){
					sumWeight += parseFloat(this.sum_weight);
				});
				$('#sum_weight').html(numberWithCommas(sumWeight.toFixed(2)));
		});

		// get year month list
		app.tableReload();

		// set status filter
		$('#status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});
		$('#status_filter > label > span').hide();

		$('#gift_select').change(function(){
			app.tableReload();
		});

		// set pay status filter
		$('#pay_status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});
		$('#pay_status_filter > label > span').hide();


		// do submit
		app.doSubmit = function() {
			// if(!$('#app-lottery-edit-form').data('bootstrapValidator').validate().isValid()) return;
			var url = baseUrl + app.basePath + 'insert_fish_tab_lottery'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-lottery-edit-form").serialize(),
				success : function(data) {
					if(data.error_msg) {
						layer.msg(data.error_msg);
					} else {
						app.mDtTable.ajax.reload(null, false);
					}
					// app.backTo();
				}
			});
		};

		app.doDelItem = function() {
			$.ajax({
				url : baseUrl + app.basePath  + 'delete_tab_lottery/' + app._delId,
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
		    	.appendTo($('#edit-modal-body').empty());
		    $("#btn-submit-edit").prop( "disabled", true);

			$('.tab-pane').removeClass('active'); $('#edit_page').addClass('active');

			$('#edit-modal-body').load(baseUrl + 'mgmt/fish_table/edit/' + id, function(){
	        	$("#btn-submit-edit").prop( "disabled", false);
	        	loading.remove();
			});
		};

		app.doFlow = function(id) {
			$('#edit_page_id').val(id);
			$('#edit_page111').modal('show');

		};

		app.fnRowCallbackExt = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {

			$(nRow).find("a").eq(1).click(function() {
				if(aData.is_current == 1) {
					layer.msg("當期無法開獎")
					return;
				}

				layer.prompt({
				  formType: 0,
				  title: '請輸入開獎號碼'
				}, function(value, index, elem){
					var url = baseUrl + app.basePath + 'do_open/' + aData.id; // the script where you handle the form input.
					var _mLoad = layer.load(0);
					$.ajax({
						type : "POST",
						url : url,
						data: {
							val: value
						},
						success : function(data) {
							if(data.error_msg) {
								layer.msg(data.error_msg);
							} else {
								app.mDtTable.ajax.reload(null, false);
								layer.close(index);
							}
							layer.close(_mLoad);
						}
					});

				});
			});
		}

		app.doExportAll = function() {
			location.href = baseUrl + app.basePath + '/export_all';
		}

		// station change
		$('#s_station_id').on('change', function(){
			app.tableReload();
		});
		$('#s_bypass_101').on('change', function(){
			app.tableReload();
		});

		$('#s_multiple').on('change', function(){
			if($('#s_multiple').prop("checked")) {
				// multiple
				$('#e_dt').prop("disabled", false)
			} else {
				$('#e_dt').prop("disabled", true)
			}

			app.tableReload();
		});

		$(".dt_picker").datetimepicker({
			format: 'YYYY-MM-DD'
		}).on('dp.change',function(event){
			currentApp.tableReload();
		});

		return app;
	};

	// return self
	return app.init();
});
var StoreAppClass = (function(app) {
	app.basePath = "mgmt/users/";
	app.disableRowClick = true;
	app.fnRowCallback1 = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// edit click
				if(!app.disableRowClick) {
					var _rtd = $(nRow).find('td');
					if(!app.enableFirstClickable) {
						_rtd = _rtd.not(':first').not(':last')
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

				if(app.fnRowCallbackExt) {
					app.fnRowCallbackExt(nRow, aData, iDisplayIndex, iDisplayIndexFull);
				}
		};

	app.dtConfig = {
		processing : true,
		serverSide : true,
		responsive : true,
		deferLoading : 0, // don't reload on init
		iDisplayLength : 10,
		sDom: app.sDom,
		language : {
			url : baseUrl + "js/datatables-lang/zh-TW.json"
		},
		bSortCellsTop : true,
		fnRowCallback : app.fnRowCallback1,
		footerCallback: function( tfoot, data, start, end, display ) {
			setTimeout(function(){ $(window).trigger('resize'); }, 300);
		}
	};


	app.init = function() {
		app.mDtTable = $('#store_list').DataTable($.extend(app.dtConfig,{
			ajax : {
			 	destroy:true,
				url : baseUrl + app.basePath + '/get_data_store',
				data : function(d) {
					d.user_id = $('#item_id').val();
					return d;
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			pageLength: 50,

			columns : [{
				data : 'name'
			},{
				data : 'amt'
			},{
				data : 'number'
			},{
				data : 'total'
			},{
				data : 'create_time'
			}],
			ordering: false,
			order : [[0, "desc"]],
			columnDefs : [{
				"targets" : [0, 1,2],
				"orderable" : false
			}],

			footerCallback: function (row, data, start, end, display ) {
        var api = this.api();

      }

		}));

		// data table actions
		app.dtActions();

		function getCoVal(co, key) {
			if(co[key]) {
				return parseInt(co[key]);
			}
			return 0;
		}

		function setSpanVal(elId, val) {
			console.log("val: " + val);
			console.log("elId: " + elId);
			if(val > 0) {
	    		$('#' + elId).parent().find('span').show().text(val);
	    	} else {
	    		$('#' + elId).parent().find('span').hide();
	    	}
		}

		app.mDtTable.on( 'xhr', function () {
		    var json = app.mDtTable.ajax.json();
				$('#sum_orders').html(numberWithCommas(json.items.length));

				var sumWeight = 0;
				$.each(json.items, function(){
					sumWeight += parseFloat(this.sum_weight);
				});
				$('#sum_weight').html(numberWithCommas(sumWeight.toFixed(2)));
		});

		// get year month list
		app.tableReload();

		// set status filter
		$('#status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});

		$('#lottery_select').change(function(){
			app.tableReload();
		});

		$('#status_filter > label > span').hide();

		// set pay status filter
		$('#pay_status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});
		$('#pay_status_filter > label > span').hide();


		// do submit
		app.doSubmit = function() {
			// if(!$('#app-lottery-edit-form').data('bootstrapValidator').validate().isValid()) return;
			var url = baseUrl + app.basePath + 'insert_fish_tab_lottery'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-lottery-edit-form").serialize(),
				success : function(data) {
					if(data.error_msg) {
						layer.msg(data.error_msg);
					} else {
						app.mDtTable.ajax.reload(null, false);
					}
					// app.backTo();
				}
			});
		};

		app.doDelItem = function() {
			$.ajax({
				url : baseUrl + app.basePath  + 'delete_tab_lottery/' + app._delId,
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
		    	.appendTo($('#edit-modal-body').empty());
		    $("#btn-submit-edit").prop( "disabled", true);

			$('.tab-pane').removeClass('active'); $('#edit_page').addClass('active');

			$('#edit-modal-body').load(baseUrl + 'mgmt/fish_table/edit/' + id, function(){
	        	$("#btn-submit-edit").prop( "disabled", false);
	        	loading.remove();
			});
		};

		app.doFlow = function(id) {
			$('#edit_page_id').val(id);
			$('#edit_page111').modal('show');

		};

		app.fnRowCallbackExt = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {

			$(nRow).find("a").eq(1).click(function() {
				if(aData.is_current == 1) {
					layer.msg("當期無法開獎")
					return;
				}

				layer.prompt({
				  formType: 0,
				  title: '請輸入開獎號碼'
				}, function(value, index, elem){
					var url = baseUrl + app.basePath + 'do_open/' + aData.id; // the script where you handle the form input.
					var _mLoad = layer.load(0);
					$.ajax({
						type : "POST",
						url : url,
						data: {
							val: value
						},
						success : function(data) {
							if(data.error_msg) {
								layer.msg(data.error_msg);
							} else {
								app.mDtTable.ajax.reload(null, false);
								layer.close(index);
							}
							layer.close(_mLoad);
						}
					});

				});
			});
		}

		app.doExportAll = function() {
			location.href = baseUrl + app.basePath + '/export_all';
		}

		// station change
		$('#s_station_id').on('change', function(){
			app.tableReload();
		});
		$('#s_bypass_101').on('change', function(){
			app.tableReload();
		});

		$('#s_multiple').on('change', function(){
			if($('#s_multiple').prop("checked")) {
				// multiple
				$('#e_dt').prop("disabled", false)
			} else {
				$('#e_dt').prop("disabled", true)
			}

			app.tableReload();
		});

		$(".dt_picker").datetimepicker({
			format: 'YYYY-MM-DD'
		}).on('dp.change',function(event){
			currentApp.tableReload();
		});

		return app;
	};

	// return self
	return app.init();
});
var BuyAppClass = (function(app) {
	app.basePath = "mgmt/users/";
	app.disableRowClick = true;
	app.fnRowCallback1 = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// edit click
				if(!app.disableRowClick) {
					var _rtd = $(nRow).find('td');
					if(!app.enableFirstClickable) {
						_rtd = _rtd.not(':first').not(':last')
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

				if(app.fnRowCallbackExt) {
					app.fnRowCallbackExt(nRow, aData, iDisplayIndex, iDisplayIndexFull);
				}
		};

	app.dtConfig = {
		processing : true,
		serverSide : true,
		responsive : true,
		deferLoading : 0, // don't reload on init
		iDisplayLength : 10,
		sDom: app.sDom,
		language : {
			url : baseUrl + "js/datatables-lang/zh-TW.json"
		},
		bSortCellsTop : true,
		fnRowCallback : app.fnRowCallback1,
		footerCallback: function( tfoot, data, start, end, display ) {
			setTimeout(function(){ $(window).trigger('resize'); }, 300);
		}
	};


	app.init = function() {
		app.mDtTable = $('#buy_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data_buy',
				data : function(d) {
					d.user_id = $('#item_id').val();
					d.lottery_no = $('#lottery_select').val();

					return d;
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			pageLength: 50,

			columns : [{
				data : 'name'
			},{
				data : 'shop_user_id',
				render: function(d,t,r) {
					if(d>0){
						if(d !== $('#item_id').val()) {
							return "<font color='blue'>買進</font>";
						} else {
							return "<font color='green'>賣出</font>";
						}
					}
				}
			},{
				data : 'amt'
			},{
				data : 'number'
			},{
				data : 'total'
			},{
				data : 'create_time'
			}],
			ordering: false,
			order : [[0, "desc"]],
			columnDefs : [{
				"targets" : [0, 1,2],
				"orderable" : false
			}],

			footerCallback: function (row, data, start, end, display ) {
        var api = this.api();

      }

		}));

		// data table actions
		app.dtActions();

		function getCoVal(co, key) {
			if(co[key]) {
				return parseInt(co[key]);
			}
			return 0;
		}

		function setSpanVal(elId, val) {
			console.log("val: " + val);
			console.log("elId: " + elId);
			if(val > 0) {
	    		$('#' + elId).parent().find('span').show().text(val);
	    	} else {
	    		$('#' + elId).parent().find('span').hide();
	    	}
		}

		app.mDtTable.on( 'xhr', function () {
		    var json = app.mDtTable.ajax.json();
				$('#sum_orders').html(numberWithCommas(json.items.length));

				var sumWeight = 0;
				$.each(json.items, function(){
					sumWeight += parseFloat(this.sum_weight);
				});
				$('#sum_weight').html(numberWithCommas(sumWeight.toFixed(2)));
		});

		// get year month list
		app.tableReload();

		// set status filter
		$('#status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});

		$('#lottery_select').change(function(){
			app.tableReload();
		});

		$('#status_filter > label > span').hide();

		// set pay status filter
		$('#pay_status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});
		$('#pay_status_filter > label > span').hide();


		// do submit
		app.doSubmit = function() {
			// if(!$('#app-lottery-edit-form').data('bootstrapValidator').validate().isValid()) return;
			var url = baseUrl + app.basePath + 'insert_fish_tab_lottery'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-lottery-edit-form").serialize(),
				success : function(data) {
					if(data.error_msg) {
						layer.msg(data.error_msg);
					} else {
						app.mDtTable.ajax.reload(null, false);
					}
					// app.backTo();
				}
			});
		};

		app.doDelItem = function() {
			$.ajax({
				url : baseUrl + app.basePath  + 'delete_tab_lottery/' + app._delId,
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
		    	.appendTo($('#edit-modal-body').empty());
		    $("#btn-submit-edit").prop( "disabled", true);

			$('.tab-pane').removeClass('active'); $('#edit_page').addClass('active');

			$('#edit-modal-body').load(baseUrl + 'mgmt/fish_table/edit/' + id, function(){
	        	$("#btn-submit-edit").prop( "disabled", false);
	        	loading.remove();
			});
		};

		app.doFlow = function(id) {
			$('#edit_page_id').val(id);
			$('#edit_page111').modal('show');

		};

		app.fnRowCallbackExt = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {

			$(nRow).find("a").eq(1).click(function() {
				if(aData.is_current == 1) {
					layer.msg("當期無法開獎")
					return;
				}

				layer.prompt({
				  formType: 0,
				  title: '請輸入開獎號碼'
				}, function(value, index, elem){
					var url = baseUrl + app.basePath + 'do_open/' + aData.id; // the script where you handle the form input.
					var _mLoad = layer.load(0);
					$.ajax({
						type : "POST",
						url : url,
						data: {
							val: value
						},
						success : function(data) {
							if(data.error_msg) {
								layer.msg(data.error_msg);
							} else {
								app.mDtTable.ajax.reload(null, false);
								layer.close(index);
							}
							layer.close(_mLoad);
						}
					});

				});
			});
		}

		app.doExportAll = function() {
			location.href = baseUrl + app.basePath + '/export_all';
		}

		// station change
		$('#s_station_id').on('change', function(){
			app.tableReload();
		});
		$('#s_bypass_101').on('change', function(){
			app.tableReload();
		});

		$('#s_multiple').on('change', function(){
			if($('#s_multiple').prop("checked")) {
				// multiple
				$('#e_dt').prop("disabled", false)
			} else {
				$('#e_dt').prop("disabled", true)
			}

			app.tableReload();
		});

		$(".dt_picker").datetimepicker({
			format: 'YYYY-MM-DD'
		}).on('dp.change',function(event){
			currentApp.tableReload();
		});

		return app;
	};

	// return self
	return app.init();
});

var FriendsAppClass = (function(app) {
	app.basePath = "mgmt/users/";
	app.disableRowClick = true;
	app.fnRowCallback1 = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// edit click
				if(!app.disableRowClick) {
					var _rtd = $(nRow).find('td');
					if(!app.enableFirstClickable) {
						_rtd = _rtd.not(':first').not(':last')
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

				if(app.fnRowCallbackExt) {
					app.fnRowCallbackExt(nRow, aData, iDisplayIndex, iDisplayIndexFull);
				}
		};

	app.dtConfig = {
		processing : true,
		serverSide : true,
		responsive : true,
		deferLoading : 0, // don't reload on init
		iDisplayLength : 10,
		sDom: app.sDom,
		language : {
			url : baseUrl + "js/datatables-lang/zh-TW.json"
		},
		bSortCellsTop : true,
		fnRowCallback : app.fnRowCallback1,
		footerCallback: function( tfoot, data, start, end, display ) {
			setTimeout(function(){ $(window).trigger('resize'); }, 300);
		}
	};


	app.init = function() {
		app.mDtTable = $('#friends_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data_friends',
				data : function(d) {
					d.user_id = $('#item_id').val();
					d.lottery_no = $('#lottery_select').val();

					return d;
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			pageLength: 50,

			columns : [{
				data : 'nick_name'
			},{
				data : 'is_block',
				render: function(d,t,r) {
					if(d >0) {
						return "<font color='red'>是</font>";
					} else {
						return "<font color='green'>否</font>";
					}
				}
			},{
				data : 'create_time'
			}],
			ordering: false,
			order : [[0, "desc"]],
			columnDefs : [{
				"targets" : [0, 1,2],
				"orderable" : false
			}],

			footerCallback: function (row, data, start, end, display ) {
        var api = this.api();

      }

		}));

		// data table actions
		app.dtActions();

		function getCoVal(co, key) {
			if(co[key]) {
				return parseInt(co[key]);
			}
			return 0;
		}

		function setSpanVal(elId, val) {
			console.log("val: " + val);
			console.log("elId: " + elId);
			if(val > 0) {
	    		$('#' + elId).parent().find('span').show().text(val);
	    	} else {
	    		$('#' + elId).parent().find('span').hide();
	    	}
		}

		app.mDtTable.on( 'xhr', function () {
		    var json = app.mDtTable.ajax.json();
				$('#sum_orders').html(numberWithCommas(json.items.length));

				var sumWeight = 0;
				$.each(json.items, function(){
					sumWeight += parseFloat(this.sum_weight);
				});
				$('#sum_weight').html(numberWithCommas(sumWeight.toFixed(2)));
		});

		// get year month list
		app.tableReload();

		// set status filter
		$('#status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});

		$('#lottery_select').change(function(){
			app.tableReload();
		});

		$('#status_filter > label > span').hide();

		// set pay status filter
		$('#pay_status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});
		$('#pay_status_filter > label > span').hide();


		// do submit
		app.doSubmit = function() {
			// if(!$('#app-lottery-edit-form').data('bootstrapValidator').validate().isValid()) return;
			var url = baseUrl + app.basePath + 'insert_fish_tab_lottery'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-lottery-edit-form").serialize(),
				success : function(data) {
					if(data.error_msg) {
						layer.msg(data.error_msg);
					} else {
						app.mDtTable.ajax.reload(null, false);
					}
					// app.backTo();
				}
			});
		};

		app.doDelItem = function() {
			$.ajax({
				url : baseUrl + app.basePath  + 'delete_tab_lottery/' + app._delId,
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
		    	.appendTo($('#edit-modal-body').empty());
		    $("#btn-submit-edit").prop( "disabled", true);

			$('.tab-pane').removeClass('active'); $('#edit_page').addClass('active');

			$('#edit-modal-body').load(baseUrl + 'mgmt/fish_table/edit/' + id, function(){
	        	$("#btn-submit-edit").prop( "disabled", false);
	        	loading.remove();
			});
		};

		app.doFlow = function(id) {
			$('#edit_page_id').val(id);
			$('#edit_page111').modal('show');

		};

		app.fnRowCallbackExt = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {

			$(nRow).find("a").eq(1).click(function() {
				if(aData.is_current == 1) {
					layer.msg("當期無法開獎")
					return;
				}

				layer.prompt({
				  formType: 0,
				  title: '請輸入開獎號碼'
				}, function(value, index, elem){
					var url = baseUrl + app.basePath + 'do_open/' + aData.id; // the script where you handle the form input.
					var _mLoad = layer.load(0);
					$.ajax({
						type : "POST",
						url : url,
						data: {
							val: value
						},
						success : function(data) {
							if(data.error_msg) {
								layer.msg(data.error_msg);
							} else {
								app.mDtTable.ajax.reload(null, false);
								layer.close(index);
							}
							layer.close(_mLoad);
						}
					});

				});
			});
		}

		app.doExportAll = function() {
			location.href = baseUrl + app.basePath + '/export_all';
		}

		// station change
		$('#s_station_id').on('change', function(){
			app.tableReload();
		});
		$('#s_bypass_101').on('change', function(){
			app.tableReload();
		});

		$('#s_multiple').on('change', function(){
			if($('#s_multiple').prop("checked")) {
				// multiple
				$('#e_dt').prop("disabled", false)
			} else {
				$('#e_dt').prop("disabled", true)
			}

			app.tableReload();
		});

		$(".dt_picker").datetimepicker({
			format: 'YYYY-MM-DD'
		}).on('dp.change',function(event){
			currentApp.tableReload();
		});

		return app;
	};

	// return self
	return app.init();
});

var TalkAppClass = (function(app) {
	app.basePath = "mgmt/users/";
	app.disableRowClick = true;
	app.fnRowCallback1 = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// edit click
				if(!app.disableRowClick) {
					var _rtd = $(nRow).find('td');
					if(!app.enableFirstClickable) {
						_rtd = _rtd.not(':first').not(':last')
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

				if(app.fnRowCallbackExt) {
					app.fnRowCallbackExt(nRow, aData, iDisplayIndex, iDisplayIndexFull);
				}
		};

	app.dtConfig = {
		processing : true,
		serverSide : true,
		responsive : true,
		deferLoading : 0, // don't reload on init
		iDisplayLength : 10,
		sDom: app.sDom,
		language : {
			url : baseUrl + "js/datatables-lang/zh-TW.json"
		},
		bSortCellsTop : true,
		fnRowCallback : app.fnRowCallback1,
		footerCallback: function( tfoot, data, start, end, display ) {
			setTimeout(function(){ $(window).trigger('resize'); }, 300);
		}
	};


	app.init = function() {
		app.mDtTable = $('#talk_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_talk_record',
				data : function(d) {
					d.user_id = $('#item_id').val();
					d.lottery_no = $('#lottery_select').val();

					return d;
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			pageLength: 50,

			columns : [{
				data : 'u_nick_name'
			},{
				data : 'nick_name',
				render: function(d,t,r) {
					if(d == null){
						return "<font color='red'>大廳</font>";
					} else{
						return d;
					}
				}
			},{
				data : 'message'
			},{
				data : 'create_time'
			}],
			ordering: false,
			order : [[0, "desc"]],
			columnDefs : [{
				"targets" : [0, 1,2],
				"orderable" : false
			}],

			footerCallback: function (row, data, start, end, display ) {
        var api = this.api();

      }

		}));

		// data table actions
		app.dtActions();

		function getCoVal(co, key) {
			if(co[key]) {
				return parseInt(co[key]);
			}
			return 0;
		}

		function setSpanVal(elId, val) {
			console.log("val: " + val);
			console.log("elId: " + elId);
			if(val > 0) {
	    		$('#' + elId).parent().find('span').show().text(val);
	    	} else {
	    		$('#' + elId).parent().find('span').hide();
	    	}
		}

		app.mDtTable.on( 'xhr', function () {
		    var json = app.mDtTable.ajax.json();
				$('#sum_orders').html(numberWithCommas(json.items.length));

				var sumWeight = 0;
				$.each(json.items, function(){
					sumWeight += parseFloat(this.sum_weight);
				});
				$('#sum_weight').html(numberWithCommas(sumWeight.toFixed(2)));
		});

		// get year month list
		app.tableReload();

		// set status filter
		$('#status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});

		$('#lottery_select').change(function(){
			app.tableReload();
		});

		$('#status_filter > label > span').hide();

		// set pay status filter
		$('#pay_status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});
		$('#pay_status_filter > label > span').hide();


		// do submit
		app.doSubmit = function() {
			// if(!$('#app-lottery-edit-form').data('bootstrapValidator').validate().isValid()) return;
			var url = baseUrl + app.basePath + 'insert_fish_tab_lottery'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-lottery-edit-form").serialize(),
				success : function(data) {
					if(data.error_msg) {
						layer.msg(data.error_msg);
					} else {
						app.mDtTable.ajax.reload(null, false);
					}
					// app.backTo();
				}
			});
		};

		app.doDelItem = function() {
			$.ajax({
				url : baseUrl + app.basePath  + 'delete_tab_lottery/' + app._delId,
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
		    	.appendTo($('#edit-modal-body').empty());
		    $("#btn-submit-edit").prop( "disabled", true);

			$('.tab-pane').removeClass('active'); $('#edit_page').addClass('active');

			$('#edit-modal-body').load(baseUrl + 'mgmt/fish_table/edit/' + id, function(){
	        	$("#btn-submit-edit").prop( "disabled", false);
	        	loading.remove();
			});
		};

		app.doFlow = function(id) {
			$('#edit_page_id').val(id);
			$('#edit_page111').modal('show');

		};

		app.fnRowCallbackExt = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {

			$(nRow).find("a").eq(1).click(function() {
				if(aData.is_current == 1) {
					layer.msg("當期無法開獎")
					return;
				}

				layer.prompt({
				  formType: 0,
				  title: '請輸入開獎號碼'
				}, function(value, index, elem){
					var url = baseUrl + app.basePath + 'do_open/' + aData.id; // the script where you handle the form input.
					var _mLoad = layer.load(0);
					$.ajax({
						type : "POST",
						url : url,
						data: {
							val: value
						},
						success : function(data) {
							if(data.error_msg) {
								layer.msg(data.error_msg);
							} else {
								app.mDtTable.ajax.reload(null, false);
								layer.close(index);
							}
							layer.close(_mLoad);
						}
					});

				});
			});
		}

		app.doExportAll = function() {
			location.href = baseUrl + app.basePath + '/export_all';
		}

		// station change
		$('#s_station_id').on('change', function(){
			app.tableReload();
		});
		$('#s_bypass_101').on('change', function(){
			app.tableReload();
		});

		$('#s_multiple').on('change', function(){
			if($('#s_multiple').prop("checked")) {
				// multiple
				$('#e_dt').prop("disabled", false)
			} else {
				$('#e_dt').prop("disabled", true)
			}

			app.tableReload();
		});

		$(".dt_picker").datetimepicker({
			format: 'YYYY-MM-DD'
		}).on('dp.change',function(event){
			currentApp.tableReload();
		});

		return app;
	};

	// return self
	return app.init();
});

var CaughtfishAppClass = (function(app) {
	app.basePath = "mgmt/users/";
	app.disableRowClick = true;
	app.fnRowCallback1 = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// edit click
				if(!app.disableRowClick) {
					var _rtd = $(nRow).find('td');
					if(!app.enableFirstClickable) {
						_rtd = _rtd.not(':first').not(':last')
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

				if(app.fnRowCallbackExt) {
					app.fnRowCallbackExt(nRow, aData, iDisplayIndex, iDisplayIndexFull);
				}
		};

	app.dtConfig = {
		processing : true,
		serverSide : true,
		responsive : true,
		deferLoading : 0, // don't reload on init
		iDisplayLength : 10,
		sDom: app.sDom,
		language : {
			url : baseUrl + "js/datatables-lang/zh-TW.json"
		},
		bSortCellsTop : true,
		fnRowCallback : app.fnRowCallback1,
		footerCallback: function( tfoot, data, start, end, display ) {
			setTimeout(function(){ $(window).trigger('resize'); }, 300);
		}
	};


	app.init = function() {
		app.mDtTable = $('#catch_fish_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_catch_fish_record',
				data : function(d) {
					d.user_id = $('#item_id').val();
					d.lottery_no = $('#lottery_select').val();

					return d;
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			pageLength: 50,

			columns : [{
				data : 'hall_id',
				render: function(d,t,r) {
					if(d == 0){
						return "<font color='blue'>高手</font>";
					}
					if(d == 1){
						return "<font color='green'>富貴</font>";
					}
					if(d == -1){
						return "<font color='orange'>體驗</font>";
					}
				}
			},{
				data : 'bet_type',
				render: function(d,t,r) {
					if(d == 1){
						return "<font color='blue'>打魚</font>";
					}
					if(d == 2){
						return "<font color='green'>打王</font>";
					}
					if(d == 3){
						return "<font color='orange'>打寶箱</font>";
					}
				}
			},{
				data : 'fish_amt'
			},{
				data : 'king_amt'
			},{
				data : 'jp_amt'
			},{
				data : 'accu_box'
			},{
				data : 'king_name'
			},{
				data : 'is_fatal'
			},{
				data : 'product_name'
			},{
				data : 'create_time'
			}],
			ordering: false,
			order : [[0, "desc"]],
			columnDefs : [{
				"targets" : [0, 1,2],
				"orderable" : false
			}],

			footerCallback: function (row, data, start, end, display ) {
        var api = this.api();

      }

		}));

		// data table actions
		app.dtActions();

		function getCoVal(co, key) {
			if(co[key]) {
				return parseInt(co[key]);
			}
			return 0;
		}

		function setSpanVal(elId, val) {
			console.log("val: " + val);
			console.log("elId: " + elId);
			if(val > 0) {
	    		$('#' + elId).parent().find('span').show().text(val);
	    	} else {
	    		$('#' + elId).parent().find('span').hide();
	    	}
		}

		app.mDtTable.on( 'xhr', function () {
		    var json = app.mDtTable.ajax.json();
				$('#sum_orders').html(numberWithCommas(json.items.length));

				var sumWeight = 0;
				$.each(json.items, function(){
					sumWeight += parseFloat(this.sum_weight);
				});
				$('#sum_weight').html(numberWithCommas(sumWeight.toFixed(2)));
		});

		// get year month list
		app.tableReload();

		// set status filter
		$('#status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});

		$('#lottery_select').change(function(){
			app.tableReload();
		});

		$('#status_filter > label > span').hide();

		// set pay status filter
		$('#pay_status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});
		$('#pay_status_filter > label > span').hide();


		// do submit
		app.doSubmit = function() {
			// if(!$('#app-lottery-edit-form').data('bootstrapValidator').validate().isValid()) return;
			var url = baseUrl + app.basePath + 'insert_fish_tab_lottery'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-lottery-edit-form").serialize(),
				success : function(data) {
					if(data.error_msg) {
						layer.msg(data.error_msg);
					} else {
						app.mDtTable.ajax.reload(null, false);
					}
					// app.backTo();
				}
			});
		};

		app.doDelItem = function() {
			$.ajax({
				url : baseUrl + app.basePath  + 'delete_tab_lottery/' + app._delId,
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
		    	.appendTo($('#edit-modal-body').empty());
		    $("#btn-submit-edit").prop( "disabled", true);

			$('.tab-pane').removeClass('active'); $('#edit_page').addClass('active');

			$('#edit-modal-body').load(baseUrl + 'mgmt/fish_table/edit/' + id, function(){
	        	$("#btn-submit-edit").prop( "disabled", false);
	        	loading.remove();
			});
		};

		app.doFlow = function(id) {
			$('#edit_page_id').val(id);
			$('#edit_page111').modal('show');

		};

		app.fnRowCallbackExt = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {

			$(nRow).find("a").eq(1).click(function() {
				if(aData.is_current == 1) {
					layer.msg("當期無法開獎")
					return;
				}

				layer.prompt({
				  formType: 0,
				  title: '請輸入開獎號碼'
				}, function(value, index, elem){
					var url = baseUrl + app.basePath + 'do_open/' + aData.id; // the script where you handle the form input.
					var _mLoad = layer.load(0);
					$.ajax({
						type : "POST",
						url : url,
						data: {
							val: value
						},
						success : function(data) {
							if(data.error_msg) {
								layer.msg(data.error_msg);
							} else {
								app.mDtTable.ajax.reload(null, false);
								layer.close(index);
							}
							layer.close(_mLoad);
						}
					});

				});
			});
		}

		app.doExportAll = function() {
			location.href = baseUrl + app.basePath + '/export_all';
		}

		// station change
		$('#s_station_id').on('change', function(){
			app.tableReload();
		});
		$('#s_bypass_101').on('change', function(){
			app.tableReload();
		});

		$('#s_multiple').on('change', function(){
			if($('#s_multiple').prop("checked")) {
				// multiple
				$('#e_dt').prop("disabled", false)
			} else {
				$('#e_dt').prop("disabled", true)
			}

			app.tableReload();
		});

		$(".dt_picker").datetimepicker({
			format: 'YYYY-MM-DD'
		}).on('dp.change',function(event){
			currentApp.tableReload();
		});

		return app;
	};

	// return self
	return app.init();
});

var LevelrecordAppClass = (function(app) {
	app.basePath = "mgmt/users/";
	app.disableRowClick = true;
	app.fnRowCallback1 = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// edit click
				if(!app.disableRowClick) {
					var _rtd = $(nRow).find('td');
					if(!app.enableFirstClickable) {
						_rtd = _rtd.not(':first').not(':last')
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

				if(app.fnRowCallbackExt) {
					app.fnRowCallbackExt(nRow, aData, iDisplayIndex, iDisplayIndexFull);
				}
		};

	app.dtConfig = {
		processing : true,
		serverSide : true,
		responsive : true,
		deferLoading : 0, // don't reload on init
		iDisplayLength : 10,
		sDom: app.sDom,
		language : {
			url : baseUrl + "js/datatables-lang/zh-TW.json"
		},
		bSortCellsTop : true,
		fnRowCallback : app.fnRowCallback1,
		footerCallback: function( tfoot, data, start, end, display ) {
			setTimeout(function(){ $(window).trigger('resize'); }, 300);
		}
	};


	app.init = function() {
		app.mDtTable = $('#level_record_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/level_record_list',
				data : function(d) {
					d.user_id = $('#item_id').val();
					d.lottery_no = $('#lottery_select').val();

					return d;
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			pageLength: 50,

			columns : [{
				data : 'name'
			},{
				data : 'level'
			},{
				data : 'status',
				render: function(d,t,r) {
					if(d == 0){
						return "<font color='red'>失敗</font>";
					}
					if(d == 1){
						return "<font color='green'>成功</font>";
					}
				}
			},{
				data : 'create_time'
			}],
			ordering: false,
			order : [[0, "desc"]],
			columnDefs : [{
				"targets" : [0, 1,2],
				"orderable" : false
			}],

			footerCallback: function (row, data, start, end, display ) {
        var api = this.api();

      }

		}));

		// data table actions
		app.dtActions();

		function getCoVal(co, key) {
			if(co[key]) {
				return parseInt(co[key]);
			}
			return 0;
		}

		function setSpanVal(elId, val) {
			console.log("val: " + val);
			console.log("elId: " + elId);
			if(val > 0) {
	    		$('#' + elId).parent().find('span').show().text(val);
	    	} else {
	    		$('#' + elId).parent().find('span').hide();
	    	}
		}

		app.mDtTable.on( 'xhr', function () {
		    var json = app.mDtTable.ajax.json();
				$('#sum_orders').html(numberWithCommas(json.items.length));

				var sumWeight = 0;
				$.each(json.items, function(){
					sumWeight += parseFloat(this.sum_weight);
				});
				$('#sum_weight').html(numberWithCommas(sumWeight.toFixed(2)));
		});

		// get year month list
		app.tableReload();

		// set status filter
		$('#status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});

		$('#lottery_select').change(function(){
			app.tableReload();
		});

		$('#status_filter > label > span').hide();

		// set pay status filter
		$('#pay_status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});
		$('#pay_status_filter > label > span').hide();


		// do submit
		app.doSubmit = function() {
			// if(!$('#app-lottery-edit-form').data('bootstrapValidator').validate().isValid()) return;
			var url = baseUrl + app.basePath + 'insert_fish_tab_lottery'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-lottery-edit-form").serialize(),
				success : function(data) {
					if(data.error_msg) {
						layer.msg(data.error_msg);
					} else {
						app.mDtTable.ajax.reload(null, false);
					}
					// app.backTo();
				}
			});
		};

		app.doDelItem = function() {
			$.ajax({
				url : baseUrl + app.basePath  + 'delete_tab_lottery/' + app._delId,
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
		    	.appendTo($('#edit-modal-body').empty());
		    $("#btn-submit-edit").prop( "disabled", true);

			$('.tab-pane').removeClass('active'); $('#edit_page').addClass('active');

			$('#edit-modal-body').load(baseUrl + 'mgmt/fish_table/edit/' + id, function(){
	        	$("#btn-submit-edit").prop( "disabled", false);
	        	loading.remove();
			});
		};

		app.doFlow = function(id) {
			$('#edit_page_id').val(id);
			$('#edit_page111').modal('show');

		};

		app.fnRowCallbackExt = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {

			$(nRow).find("a").eq(1).click(function() {
				if(aData.is_current == 1) {
					layer.msg("當期無法開獎")
					return;
				}

				layer.prompt({
				  formType: 0,
				  title: '請輸入開獎號碼'
				}, function(value, index, elem){
					var url = baseUrl + app.basePath + 'do_open/' + aData.id; // the script where you handle the form input.
					var _mLoad = layer.load(0);
					$.ajax({
						type : "POST",
						url : url,
						data: {
							val: value
						},
						success : function(data) {
							if(data.error_msg) {
								layer.msg(data.error_msg);
							} else {
								app.mDtTable.ajax.reload(null, false);
								layer.close(index);
							}
							layer.close(_mLoad);
						}
					});

				});
			});
		}

		app.doExportAll = function() {
			location.href = baseUrl + app.basePath + '/export_all';
		}

		// station change
		$('#s_station_id').on('change', function(){
			app.tableReload();
		});
		$('#s_bypass_101').on('change', function(){
			app.tableReload();
		});

		$('#s_multiple').on('change', function(){
			if($('#s_multiple').prop("checked")) {
				// multiple
				$('#e_dt').prop("disabled", false)
			} else {
				$('#e_dt').prop("disabled", true)
			}

			app.tableReload();
		});

		$(".dt_picker").datetimepicker({
			format: 'YYYY-MM-DD'
		}).on('dp.change',function(event){
			currentApp.tableReload();
		});

		return app;
	};

	// return self
	return app.init();
});
var LoginAppClass = (function(app) {
	app.basePath = "mgmt/users/";
	app.disableRowClick = true;
	app.fnRowCallback1 = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// edit click
				if(!app.disableRowClick) {
					var _rtd = $(nRow).find('td');
					if(!app.enableFirstClickable) {
						_rtd = _rtd.not(':first').not(':last')
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

				if(app.fnRowCallbackExt) {
					app.fnRowCallbackExt(nRow, aData, iDisplayIndex, iDisplayIndexFull);
				}
		};

	app.dtConfig = {
		processing : true,
		serverSide : true,
		responsive : true,
		deferLoading : 0, // don't reload on init
		iDisplayLength : 10,
		sDom: app.sDom,
		language : {
			url : baseUrl + "js/datatables-lang/zh-TW.json"
		},
		bSortCellsTop : true,
		fnRowCallback : app.fnRowCallback1,
		footerCallback: function( tfoot, data, start, end, display ) {
			setTimeout(function(){ $(window).trigger('resize'); }, 300);
		}
	};


	app.init = function() {
		app.mDtTable = $('#login_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data_user_login',
				data : function(d) {
					d.user_id = $('#item_id').val();
					d.lottery_no = $('#lottery_select').val();

					return d;
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			pageLength: 50,

			columns : [{
				data : 'log_type'
			},{
				data : 'ip'
			},{
				data : 'create_time'
			}],
			ordering: false,
			order : [[0, "desc"]],
			columnDefs : [{
				"targets" : [0,1],
				"orderable" : false
			}],

			footerCallback: function (row, data, start, end, display ) {
        var api = this.api();

      }

		}));

		// data table actions
		app.dtActions();

		function getCoVal(co, key) {
			if(co[key]) {
				return parseInt(co[key]);
			}
			return 0;
		}

		function setSpanVal(elId, val) {
			console.log("val: " + val);
			console.log("elId: " + elId);
			if(val > 0) {
	    		$('#' + elId).parent().find('span').show().text(val);
	    	} else {
	    		$('#' + elId).parent().find('span').hide();
	    	}
		}

		app.mDtTable.on( 'xhr', function () {
		    var json = app.mDtTable.ajax.json();
				$('#sum_orders').html(numberWithCommas(json.items.length));

				var sumWeight = 0;
				$.each(json.items, function(){
					sumWeight += parseFloat(this.sum_weight);
				});
				$('#sum_weight').html(numberWithCommas(sumWeight.toFixed(2)));
		});

		// get year month list
		app.tableReload();

		// set status filter
		$('#status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});

		$('#lottery_select').change(function(){
			app.tableReload();
		});

		$('#status_filter > label > span').hide();

		// set pay status filter
		$('#pay_status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});
		$('#pay_status_filter > label > span').hide();


		// do submit
		app.doSubmit = function() {
			// if(!$('#app-lottery-edit-form').data('bootstrapValidator').validate().isValid()) return;
			var url = baseUrl + app.basePath + 'insert_fish_tab_lottery'; // the script where you handle the form input.
			$.ajax({
				type : "POST",
				url : url,
				data : $("#app-lottery-edit-form").serialize(),
				success : function(data) {
					if(data.error_msg) {
						layer.msg(data.error_msg);
					} else {
						app.mDtTable.ajax.reload(null, false);
					}
					// app.backTo();
				}
			});
		};

		app.doDelItem = function() {
			$.ajax({
				url : baseUrl + app.basePath  + 'delete_tab_lottery/' + app._delId,
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
		    	.appendTo($('#edit-modal-body').empty());
		    $("#btn-submit-edit").prop( "disabled", true);

			$('.tab-pane').removeClass('active'); $('#edit_page').addClass('active');

			$('#edit-modal-body').load(baseUrl + 'mgmt/fish_table/edit/' + id, function(){
	        	$("#btn-submit-edit").prop( "disabled", false);
	        	loading.remove();
			});
		};

		app.doFlow = function(id) {
			$('#edit_page_id').val(id);
			$('#edit_page111').modal('show');

		};

		app.fnRowCallbackExt = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {

			$(nRow).find("a").eq(1).click(function() {
				if(aData.is_current == 1) {
					layer.msg("當期無法開獎")
					return;
				}

				layer.prompt({
				  formType: 0,
				  title: '請輸入開獎號碼'
				}, function(value, index, elem){
					var url = baseUrl + app.basePath + 'do_open/' + aData.id; // the script where you handle the form input.
					var _mLoad = layer.load(0);
					$.ajax({
						type : "POST",
						url : url,
						data: {
							val: value
						},
						success : function(data) {
							if(data.error_msg) {
								layer.msg(data.error_msg);
							} else {
								app.mDtTable.ajax.reload(null, false);
								layer.close(index);
							}
							layer.close(_mLoad);
						}
					});

				});
			});
		}

		app.doExportAll = function() {
			location.href = baseUrl + app.basePath + '/export_all';
		}

		// station change
		$('#s_station_id').on('change', function(){
			app.tableReload();
		});
		$('#s_bypass_101').on('change', function(){
			app.tableReload();
		});

		$('#s_multiple').on('change', function(){
			if($('#s_multiple').prop("checked")) {
				// multiple
				$('#e_dt').prop("disabled", false)
			} else {
				$('#e_dt').prop("disabled", true)
			}

			app.tableReload();
		});

		$(".dt_picker").datetimepicker({
			format: 'YYYY-MM-DD'
		}).on('dp.change',function(event){
			currentApp.tableReload();
		});

		return app;
	};

	// return self
	return app.init();
});
