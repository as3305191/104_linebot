var ordersAppClass = (function(app) {
	app.basePath = "mgmt/orders/";
	app.enableFirstClickable = true;
	app.init = function() {
		app.mDtTable = $('#dt_list').DataTable($.extend(app.dtConfig,{
			ajax : {
				url : baseUrl + app.basePath + '/get_data',
				data : function(d) {
					d.status_filter = $('#status_filter input[name=options]:checked').val();
					d.pay_status_filter = $('#pay_status_filter input[name=pay_status]:checked').val();
					d.shipping_status_filter = $('#shipping_status_filter input[name=shipping_status]:checked').val();
					d.store_id = $('#store_id').val();
					return d;
				},
				dataSrc : 'items',
				dataType : 'json',
				type : 'post'
			},

			columns : [{
				data : 'sn'
			}, {
				data : 'order_status_name'
			}, {
				data : 'product_amt'
			}, {
				data : 'order_pay_status_name'
			}, {
				data : 'order_shipping_status_name'
			}, {
				data : 'create_time'
			}],

			order : [[5, "desc"]],
			columnDefs : [{
				targets : 0,
				data : null,
				defaultContent : app.defaultContent,
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
			}]
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
			if(val > 0) {
	    		$('#' + elId).parent().find('span').show().text(val);
	    	} else {
	    		$('#' + elId).parent().find('span').hide();
	    	}
		}

		app.mDtTable.on( 'xhr', function () {
		    var json = app.mDtTable.ajax.json();
		    if(json.status_cnt) {
		    	// var co = json.status_cnt, val = 0;
          //
					// // o_0
		    	// val = getCoVal(co, 0);
		    	// setSpanVal('o_0', val);
          //
		    	// // o_1
		    	// val = getCoVal(co, 1);
		    	// setSpanVal('o_1', val);
          //
		    	// // o_2
		    	// val = getCoVal(co, 2);
		    	// setSpanVal('o_2', val);
          //
		    	// // o_3
		    	// val = getCoVal(co, 3);
		    	// setSpanVal('o_3', val);
          //
		    	// // o_m10
		    	// val = getCoVal(co, -10);
		    	// setSpanVal('o_m10', val);
		    	// // o_m20
		    	// val = getCoVal(co, -20);
		    	// setSpanVal('o_m20', val);
		    	// // o_m30
		    	// val = getCoVal(co, -30);
		    	// setSpanVal('o_m30', val);
		    	// // o_m31
		    	// val = getCoVal(co, -31);
		    	// setSpanVal('o_m31', val);
		    }
		} );

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

		// set shipping status filter
		$('#shipping_status_filter label').on('click', function(){
			$(this).find('input').prop('checked', true);
			app.tableReload();
		});
		$('#shipping_status_filter > label > span').hide();

		return app;
	};

	// return self
	return app.init();
});
