var FishtableAppClass = (function(app) {
	app.basePath = "mgmt/fish_table/";
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
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					d.dt = $('#s_dt').val();
					d.e_dt = $('#e_dt').val();
					d.status_filter = $('input[name=options]:checked').val();
					d.pay_status_filter = $('#pay_status_filter input[name=pay_status]:checked').val();

					d.multiple = $('#s_multiple').prop("checked") ? 1 : 0;
					d.station_id = $('#s_station_id').val();
					d.bypass_101 = $('#s_bypass_101').is(':checked') ? 1 : 0;
					return d;
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			pageLength: 50,

			columns : [{
				data : 'id',
				render:function(){
					return '<a href="#deleteModal" role="button" data-toggle="modal" style="margin-right: 5px;"><i class="fa fa-trash fa-lg"></i></a>';
				}
			},{
				data : 'tab_name'
			},{
				data : 'pool_100'
			},{
				data : 'pool_100_king'
			},{
				data : 'pool_2000'
			},{
				data : 'pool_2000_king'
			},{
				data : 'pool_20000'
			},{
				data : 'pool_20000_king'
			},{
				data : 'pool_200000'
			},{
				data : 'pool_200000_king'
			},{
				data : 'pool_1000000'
			},{
				data : 'pool_1000000_king'
			}],
			order : [[0, "desc"]],
			columnDefs : [],

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

		// set pay status filter
		$('#pay_status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});
		$('#pay_status_filter > label > span').hide();


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
			app._lastPk = aData.id;
			$(nRow).parent().find('tr').removeClass('active');
			setTimeout(function(){
				$(nRow).addClass('active');
			}, 100);

			app.doFlow(aData.id);
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

var FishtableLotteryAppClass = (function(app) {
	app.basePath = "mgmt/fish_table/";
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
					d.tab_id = $('#item_id').val();

					return d;
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			pageLength: 50,

			columns : [{
				data: "id",
				render: function(d,t,r) {
					var html = '<a href="#deleteModal" role="button" data-toggle="modal" style="margin-right: 5px;"><i class="fa fa-trash fa-lg"></i></a>';
					if(r.is_open == 0 && r.is_end == 1) {
						html += '<a href="#openModal" role="button" data-toggle="modal" style="margin-right: 5px;"><i class="fa fa-slideshare fa-lg"></i></a>';
					}
					return html;
				}
			},{
				data : 'lottery_no'
			},{
				data : 'lottery_sn'
			},{
				data : 'lottery_name'
			},{
				data : 'total_num'
			},{
				data : 'is_current',
				render: function(d,t,r) {
					if(d == 1) {
						return "<font color='red'>是</font>";
					}
					return "否";
				}
			},{
				data : 'is_open',
				render: function(d,t,r) {
					if(d == 1) {
						return "<font color='green'>是</font>";
					}
					return "否";
				}
			},{
				data : 'open_lottery_tx_sn'
			},{
				data : 'is_received',
				render: function(d,t,r) {
					if(d == 1) {
						return "<font color='green'>是</font>";
					}
					return "<font color='red'>否</font>";
				}
			},{
				data : 'user_nick_name'
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
