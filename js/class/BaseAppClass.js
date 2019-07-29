var BaseAppClass = (function(app) {
	app.basePath = '';
	if(!app.dtListId) {
		app.dtListId = "#dt_list";
	}

	app.sDom =  "<'dt-toolbar'<'col-sm-12 col-xs-12'p>r>"+
						"<'t-box'"+
						"t"+
						">"+
						"<'dt-toolbar-footer'<'col-sm-6 col-xs-12'i><'col-xs-12 col-sm-6 hidden-xs'l>>";
	app.defaultContent = '<a href="#deleteModal" role="button" data-toggle="modal" style="margin-right: 5px;"><i class="fa fa-trash fa-lg"></i></a>';

	app.fnRowCallback = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// edit click
				if(!app.disableRowClick) {
					var _rtd = $(nRow).find('td');
					if(!app.enableFirstClickable) {
						_rtd = _rtd.not(':first')
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
		fnRowCallback : app.fnRowCallback,
		footerCallback: function( tfoot, data, start, end, display ) {
			setTimeout(function(){ $(window).trigger('resize'); }, 300);
		}
	};

	app.dtActions = function() {
		// search box
		$(app.dtListId + " thead th input[type=text]").on('keyup change', function() {
			var me = this;
			setTimeout(function(){
				app.mDtTable.column($(me).parent().index() + ':visible').search(me.value).draw();
			}, 100);
		});

		// trigger on resize when draw datatable
		$(app.dtListId).on('draw.dt', function(){
			wOnResize();
		});
	};

	app.init = function() {
		// return self
		return app;
	};

	// table reload
	app.tableReload = function() {
		app.mDtTable.ajax.reload(function(){
			if(typeof wOnResize != undefined) {
				wOnResize();
			}
		}, false);
	};

	$(app.dtListId).on( 'draw.dt', function () {
		if(typeof wOnResize != undefined) {
			wOnResize();
		}
	});

	// delete
	app.setDelId = function(delId) {
		app._delId = delId;
	};

	app.doDelItem = function() {
		$.ajax({
			url : baseUrl + app.basePath  + 'delete/' + app._delId,
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

		$('#edit-modal-body').load(baseUrl + app.basePath + 'edit/' + id, function(){
        	$("#btn-submit-edit").prop( "disabled", false);
        	loading.remove();
		});
	};

	// do submit
	app.doSubmit = function() {
		if(!$('#app-edit-form').data('bootstrapValidator').validate().isValid()) return;
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

	app.backTo = function(target) {
		if(!target) {
			$('#edit-modal-body').empty();

			$('.tab-pane').removeClass('active');
			$('#list_page').addClass('active');

			// prevent datable height zero
			$(window).trigger('resize');
		}
	};

	//speech

	$('#speechModal').on('show.bs.modal',function(){//modal打開就是啟動錄音
		app.speechStart();
	});

	$('#speechModal').on('hidden.bs.modal',function(){//modal關掉就結束錄音
		app.speechRestore();
		// $('#currentReturnID').val('');
	});

	$('#modal_do_speech').click(function(){//錄音結束
		app.speechUpload();
	});

	app.speechClick = function(id){
		$('#speechModal').modal();
		$('#currentReturnID').val(id);
	};

	app.speechRestore = function(){//錄音重置
		Fr.voice.stop();
	};

	app.speechStart = function(){//開始錄音
		Fr.voice.record(false, function(){
      app.makeWaveform();
    });
	};

	app.speechUpload = function(){
		Fr.voice.export(function(url){
			var base64 = url.split(',')[1];
			var formData = new FormData();
			var id = $('#currentReturnID').val();
			// $('#'+id).text('處理中...');
			$.smallBox({
				title : "處理中...",
				content : "<i class='fa fa-clock-o'></i> <i>2 seconds ago...</i>",
				color : "#296191",
				iconSmall : "fa fa-thumbs-up bounce animated",
				timeout : 4000
			});
			formData.append('file', base64);
					$.ajax({
						url: baseUrl + 'api/speech/upload',
						type: 'POST',
						data: formData,
						contentType: false,
						processData: false,
						success: function(res) {
							//go to google speech api
							console.log(res);

							if(res.speech_success){
								$.smallBox({
									title : "辨識成功",
									content : "<i class='fa fa-clock-o'></i> <i>2 seconds ago...</i>",
									color : "#739E73",
									iconSmall : "fa fa-thumbs-up bounce animated",
									timeout : 4000
								});
								if($('#'+id).is("input")){
										$('#'+id).val(res.reply);
								}else if($('#'+id).is("textarea")){
										$('#'+id).text(res.reply);
								}else{
									console.log('none of input and textarea');
								}
							}else{
								// $('#'+id).text('');
								$.smallBox({
									title : res.reply,
									content : "<i class='fa fa-clock-o'></i> <i>2 seconds ago...</i>",
									color : "#C46A69",
									iconSmall : "fa fa-thumbs-up bounce animated",
									timeout : 4000
								});
							}

						}
					});
			}, "base64");

	app.speechRestore();
};

	app.makeWaveform = function(){//產生波紋
		var analyser = Fr.voice.recorder.analyser;

  var bufferLength = analyser.frequencyBinCount;
  var dataArray = new Uint8Array(bufferLength);

  /**
   * The Waveform canvas
   */
  var WIDTH = 500,
      HEIGHT = 200;

  var canvasCtx = $("#level")[0].getContext("2d");
  canvasCtx.clearRect(0, 0, WIDTH, HEIGHT);

  	function draw() {
	    var drawVisual = requestAnimationFrame(draw);

	    analyser.getByteTimeDomainData(dataArray);

	    canvasCtx.fillStyle = 'rgb(200, 200, 200)';
	    canvasCtx.fillRect(0, 0, WIDTH, HEIGHT);
	    canvasCtx.lineWidth = 2;
	    canvasCtx.strokeStyle = 'rgb(0, 0, 0)';

	    canvasCtx.beginPath();

	    var sliceWidth = WIDTH * 1.0 / bufferLength;
	    var x = 0;
	    for(var i = 0; i < bufferLength; i++) {
	      var v = dataArray[i] / 128.0;
	      var y = v * HEIGHT/2;

	      if(i === 0) {
	        canvasCtx.moveTo(x, y);
	      } else {
	        canvasCtx.lineTo(x, y);
	      }

	      x += sliceWidth;
	    }
	    canvasCtx.lineTo(WIDTH, HEIGHT/2);
	    canvasCtx.stroke();
	  }
	  draw();
	};

	return app.init();
});
