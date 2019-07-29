var AwardcenterAppClass = (function(app) {
	app.basePath = "mgmt/award_center/";
	app.fnRowCallback1 = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// edit click
				if(!app.disableRowClick) {
					var _rtd = $(nRow).find('td');
					if(!app.enableFirstClickable) {
						_rtd = _rtd.not(':first').not(':last')
					}
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
			// setTimeout(function(){ $(window).trigger('resize'); }, 300);
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
				data : 'user_id'
			},{
				data : 'cate'
			},{
				data : 'detail'
			},{
				data : 'status',
				render: function(d,t,r) {
					if(d==0) {
						return '<input type="button" value="領獎">';
					} else{
						return "<font color='red'>已領獎</font>";

					}
				}
			},{
				data : 'date'
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
