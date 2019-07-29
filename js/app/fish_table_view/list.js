var FishtableviewAppClass = (function(app) {
	app.basePath = "mgmt/fish_table_view/";
	app.fnRowCallback1 = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// edit click
				if(!app.disableRowClick) {
					var _rtd = $(nRow).find('td');
					if(!app.enableFirstClickable) {
						_rtd = _rtd.not(':first').not(':last')
					}
					_rtd.addClass('pointer').on('click', function(){
						app.doFlow(aData.id);

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

				if(app.fnRowCallbackExt) {
					app.fnRowCallbackExt(nRow, aData, iDisplayIndex, iDisplayIndexFull);
				}
		};

	app.dtConfig = {
		// processing : true,
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
				data : 'tab_name'
			},{
				data : 'user_count'
			},{
				data : 'pool_100',
				render: function(d,t,r) {
					if(!currentApp.lastMap[r.id]) {
						currentApp.lastMap[r.id] = {};
					} else {
						if(!currentApp.lastMap[r.id].pool_100) {
							currentApp.lastMap[r.id].pool_100 = r.pool_100;
						} else {
							var lastR = currentApp.lastMap[r.id];
							if(d != lastR.pool_100) {
								currentApp.lastMap[r.id].pool_100 = r.pool_100;
								return '<font color="blue">'+d+'</font>';
							}
						}
					}
					currentApp.lastMap[r.id].pool_100 = r.pool_100;

					if(d <0) {
						return '<font color="red">'+d+'</font>';
					}else{}
					return d;
				}
			},{
				data : 'pool_100_king',
				render: function(d,t,r) {
					if(!currentApp.lastMap[r.id]) {
						currentApp.lastMap[r.id] = {};
					} else {
						if(!currentApp.lastMap[r.id].pool_100_king) {
							currentApp.lastMap[r.id].pool_100_king = r.pool_100_king;
						} else {
							var lastR = currentApp.lastMap[r.id];
							if(d != lastR.pool_100_king) {
								currentApp.lastMap[r.id].pool_100_king = r.pool_100_king;

								return '<font color="blue">'+d+'</font>';
							}
						}
					}
					currentApp.lastMap[r.id].pool_100_king = r.pool_100_king;

					if(d <0) {
						return '<font color="red">'+d+'</font>';
					}else{}
					return d;
				}
			},{
				data : 'pool_2000',
				render: function(d,t,r) {
					if(!currentApp.lastMap[r.id]) {
						currentApp.lastMap[r.id] = {};
					} else {
						if(!currentApp.lastMap[r.id].pool_2000) {
							currentApp.lastMap[r.id].pool_2000 = r.pool_2000;
						} else {
							var lastR = currentApp.lastMap[r.id];
							if(d != lastR.pool_2000) {
								currentApp.lastMap[r.id].pool_2000 = r.pool_2000;
								return '<font color="blue">'+d+'</font>';
							}
						}
					}
					currentApp.lastMap[r.id].pool_2000 = r.pool_2000;

					if(d <0) {
						return '<font color="red">'+d+'</font>';
					}else{}
					return d;
				}
			},{
				data : 'pool_2000_king',
				render: function(d,t,r) {
					if(!currentApp.lastMap[r.id]) {
						currentApp.lastMap[r.id] = {};
					} else {
						if(!currentApp.lastMap[r.id].pool_2000_king) {
							currentApp.lastMap[r.id].pool_2000_king = r.pool_2000_king;
						} else {
							var lastR = currentApp.lastMap[r.id];
							if(d != lastR.pool_2000_king) {
								currentApp.lastMap[r.id].pool_2000_king = r.pool_2000_king;
								return '<font color="blue">'+d+'</font>';
							}
						}
					}
					currentApp.lastMap[r.id].pool_2000_king = r.pool_2000_king;

					if(d <0) {
						return '<font color="red">'+d+'</font>';
					}else{}
					return d;
				}
			},{
				data : 'pool_20000',
				render: function(d,t,r) {
					if(!currentApp.lastMap[r.id]) {
						currentApp.lastMap[r.id] = {};
					} else {
						if(!currentApp.lastMap[r.id].pool_20000) {
							currentApp.lastMap[r.id].pool_20000 = r.pool_20000;
						} else {
							var lastR = currentApp.lastMap[r.id];
							if(d != currentApp.lastMap[r.id].pool_20000) {
								currentApp.lastMap[r.id].pool_20000 = r.pool_20000;
								return '<font color="blue">'+d+'</font>';
							}
						}
					}
					currentApp.lastMap[r.id].pool_20000 = r.pool_20000;

					if(d <0) {
						return '<font color="red">'+d+'</font>';
					}else{}
					return d;
				}
			},{
				data : 'pool_20000_king',
				render: function(d,t,r) {
					if(!currentApp.lastMap[r.id]) {
						currentApp.lastMap[r.id] = {};
					} else {
						if(!currentApp.lastMap[r.id].pool_20000_king) {
							currentApp.lastMap[r.id].pool_20000_king = r.pool_20000_king;
						} else {
							if(d != currentApp.lastMap[r.id].pool_20000_king) {
								currentApp.lastMap[r.id].pool_20000_king = r.pool_20000_king;
								return '<font color="blue">'+d+'</font>';
							}
						}
					}

					currentApp.lastMap[r.id].pool_20000_king = r.pool_20000_king;

					if(d <0) {
						return '<font color="red">'+d+'</font>';
					}else{}
					return d;
				}
			},{
				data : 'pool_200000',
				render: function(d,t,r) {
					if(!currentApp.lastMap[r.id]) {
						currentApp.lastMap[r.id] = {};
					} else {
						if(!currentApp.lastMap[r.id].pool_200000) {
							currentApp.lastMap[r.id].pool_200000 = r.pool_200000;
						} else {
							var lastR = currentApp.lastMap[r.id];
							if(d != lastR.pool_200000) {
								currentApp.lastMap[r.id].pool_200000 = r.pool_200000;
								return '<font color="blue">'+d+'</font>';
							}
						}
					}
					currentApp.lastMap[r.id].pool_200000 = r.pool_200000;

					if(d <0) {
						return '<font color="red">'+d+'</font>';
					}else{}
					return d;
				}
			},{
				data : 'pool_200000_king',
				render: function(d,t,r) {
					if(!currentApp.lastMap[r.id]) {
						currentApp.lastMap[r.id] = {};
					} else {
						if(!currentApp.lastMap[r.id].pool_200000_king) {
							currentApp.lastMap[r.id].pool_200000_king = r.pool_200000_king;
						} else {
							var lastR = currentApp.lastMap[r.id];
							if(d != lastR.pool_200000_king) {
								currentApp.lastMap[r.id].pool_200000_king = r.pool_200000_king;
								return '<font color="blue">'+d+'</font>';
							}
						}
					}
					currentApp.lastMap[r.id].pool_200000_king = r.pool_200000_king;

					if(d <0) {
						return '<font color="red">'+d+'</font>';
					}else{}
					return d;
				}
			},{
				data : 'pool_1000000',
				render: function(d,t,r) {
					if(!currentApp.lastMap[r.id]) {
						currentApp.lastMap[r.id] = {};
					} else {
						if(!currentApp.lastMap[r.id].pool_1000000) {
							currentApp.lastMap[r.id].pool_1000000 = r.pool_1000000;
						} else {
							var lastR = currentApp.lastMap[r.id];
							if(d != lastR.pool_1000000) {
								currentApp.lastMap[r.id].pool_1000000 = r.pool_1000000;
								return '<font color="blue">'+d+'</font>';
							}
						}
					}
					currentApp.lastMap[r.id].pool_1000000 = r.pool_1000000;

					if(d <0) {
						return '<font color="red">'+d+'</font>';
					}else{}
					return d;
				}
			},{
				data : 'pool_1000000_king',
				render: function(d,t,r) {
					if(!currentApp.lastMap[r.id]) {
						currentApp.lastMap[r.id] = {};
					} else {
						if(!currentApp.lastMap[r.id].pool_1000000_king) {
							currentApp.lastMap[r.id].pool_1000000_king = r.pool_1000000_king;
						} else {
							var lastR = currentApp.lastMap[r.id];
							if(d != lastR.pool_1000000_king) {
								currentApp.lastMap[r.id].pool_1000000_king = r.pool_1000000_king;
								return '<font color="blue">'+d+'</font>';
							}
						}
					}
					currentApp.lastMap[r.id].pool_1000000_king = r.pool_1000000_king;

					if(d < 0) {
						return '<font color="red">'+d+'</font>';
					}else{}
					return d;
				}
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

				$('#fish_jp_amt').html("JP:" + numberWithCommas(json.fish_jp_amt));
				var sumWeight = 0;
				$.each(json.items, function(){
					// var me = this;
					// currentApp.lastMap[me.id] = me;

					sumWeight += parseFloat(this.sum_weight);
				});
				$('#sum_weight').html(numberWithCommas(sumWeight.toFixed(2)));
				// console.log(json)

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
			app.type1 = new FishtableusersAppClass(new BaseAppClass({}));

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

		setInterval( function () {
			 app.tableReload();
		}, 1000 );

		return app;
	};

	// return self
	return app.init();
});

var FishtableusersAppClass = (function(app) {
	app.basePath = "mgmt/fish_table_view/";
	app.disableRowClick = true;
	app.sDom1 =
						"<'t-box'"+
						"t"+
						">"
						;
	app.dtConfig = {
		processing : true,
		serverSide : true,
		responsive : true,
		deferLoading : 0, // don't reload on init
		iDisplayLength : 10,
		sDom: app.sDom1,
		language : {
			url : baseUrl + "js/datatables-lang/zh-TW.json"
		},
		bSortCellsTop : true,
		fnRowCallback : app.fnRowCallback,
		footerCallback: function( tfoot, data, start, end, display ) {
			setTimeout(function(){ $(window).trigger('resize'); }, 300);
		}
	};
	app.init = function() {
		app.mDtTable = $('#nick_name_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data_users',
				data : function(d) {

					d.tab_id = 	$('#edit_page_id').val();;

					return d;
				},
				dataSrc : "items",
				dataType : 'json',
				type : 'post'
			},

			pageLength: 50,
			destroy:true,
			columns : [
				{
					data: "line_picture",
					render: function(d,t,r) {
						if(d !== null) {
							return '<img src="'+d+'" style="height:30px" />';
						} else {
							return "<font color='red'>無頭貼</font>";
						}
					}

				},{
				data: "nick_name"

			}],
			ordering: false,
			order : [[0, "desc"]],
			columnDefs : [{
				"targets" : [0],
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
