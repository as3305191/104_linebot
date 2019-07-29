
	<!-- Widget ID (each widget will need unique ID)-->
	<div class="row">
		<article class="col-xs-12 col-sm-7 col-md-7 col-lg-7 sortable-grid ui-sortable">
			<div class="jarviswidget" id="" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
				<header>
					<div class="widget-toolbar pull-left">
						<a href="javascript:void(0);" id="back_parent" onclick="currentApp.backTo()" class="btn btn-default ">
							<i class="fa fa-arrow-circle-left"></i>返回
						</a>
					</div>
					<div class="widget-toolbar pull-left">
						<button id="do_dispatch" onclick="doDispatch()" class="btn btn-default btn-danger">
							<i class="fa fa-search"></i>開始派車
						</button>
					</div>
					<div class="widget-toolbar pull-left">
						<button id="do_cancel" onclick="doCancel()" class="btn btn-default btn-info">
							<i class="fa fa-trash"></i>取消派車
						</button>
					</div>
				</header>

				<!-- widget div-->
				<div>
					<!-- widget edit box -->
					<div class="jarviswidget-editbox">
						<!-- This area used as dropdown edit box -->
						<input class="form-control" type="text">
					</div>
					<!-- end widget edit box -->

					<!-- widget content -->
					<div class="widget-body form-horizontal">
							<fieldset>
								<div class="form-group">
									<label class="col-md-3 control-label">手機</label>
									<div class="col-md-6">
										<input type="text" class="form-control" value="<?= isset($item) ? $item -> member_mobile : '' ?>" readonly="readonly" />
									</div>
								</div>
							</fieldset>

							<fieldset >
								<div class="form-group">
									<label class="col-md-3 control-label">乘客名稱</label>
									<div class="col-md-4 row">
										<input type="text" placeholder="請輸入乘客名稱" class="form-control col-md-12" style="margin-left: 12px;" id="member_name" name="member_name" value="<?= isset($item) ? $item -> member_name : '' ?>" />
									</div>
									<div class="col-md-3">
										<span class="input-group-btn">
											<button type="button" class="btn btn-primary" onclick="updateMemberName();" >
												<i class="fa fa-save"></i>
												變更名稱
											</button>
										</span>
										<span class="input-group-btn">
											<button type="button" class="btn btn-default" onclick="currentApp.speechClick('member_name')" >
												<i class="fa fa-microphone"></i>
												語音輸入
											</button>
										</span>
									</div>

								</div>
							</fieldset>

							<fieldset>
								<div class="form-group">
									<label class="col-md-3 control-label">派車編號</label>
									<div class="col-md-4 row">
										<input type="text" id="dispatch_id" readonly class="form-control col-md-12" style="margin-left: 12px;" value="<?= isset($item) ? $item -> dispatch_id : '0' ?>" />
									</div>
									<div class="col-md-3 row">
										<input type="hidden" id="take_status" readonly class="form-control col-md-12" style="margin-left: 12px;" value="<?= isset($item) ? $item -> take_status : '0' ?>" />
										<input type="text" id="take_status_name" readonly class="form-control col-md-12" style="margin-left: 12px;" value="<?= isset($item) ? $item -> take_status_name : '0' ?>" />
									</div>
								</div>
							</fieldset>
							<fieldset>
								<div class="form-group">
									<label class="col-md-3 control-label">開始派車時間</label>
									<div class="col-md-4 row">
										<input type="text" id="dispatch_time" readonly class="form-control col-md-12" style="margin-left: 12px;" value="<?= isset($item) ? $item -> dispatch_time : '0' ?>" />
									</div>
									<div class="col-md-3 row">
										<input type="text" id="dispatch_count" readonly class="form-control col-md-12" style="margin-left: 12px;" value="" placeholder="經過時間（秒）" />
									</div>
								</div>
							</fieldset>
							<fieldset>
								<div class="form-group">
									<label class="col-md-3 control-label">確認分派時間</label>
									<div class="col-md-6 row">
										<input type="text" id="assign_time" readonly class="form-control col-md-12" style="margin-left: 12px;" value="<?= isset($item) ? $item -> assign_time : '0' ?>" />
									</div>
								</div>
							</fieldset>

					</div>
					<!-- end widget content -->

				</div>
				<!-- end widget div -->

			</div>
			<!-- end widget -->

			<!-- 2nd row -->
			<form id="app-edit-form" method="post" class="form-horizontal">
			<input type="hidden" id="id" name="id" value="<?= isset($item) ? $item -> id : "" ?>">
			<div class="jarviswidget" id="" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
				<header>
					<div class="widget-toolbar pull-left">
					位置
					</div>
				</header>

				<!-- widget div-->
				<div>
					<!-- widget edit box -->
					<div class="jarviswidget-editbox">
						<!-- This area used as dropdown edit box -->
						<input class="form-control" type="text">
					</div>
					<!-- end widget edit box -->

					<!-- widget content -->
					<div class="widget-body">
							<fieldset>
								<div class="form-group">
									<label class="col-md-3 control-label">查詢關鍵字
										<br/>
										<font color="red">查詢專用，可輸入地址或附近地標查詢</font></label>
									<div class="col-md-4 row">
										<input type="text" placeholder="請輸入查詢地址或地標關鍵字" class="form-control col-md-12" style="margin-left: 12px;" id="address" name="address" value="<?= isset($item) ? $item -> address : '' ?>" />
									</div>
									<div class="col-md-3">
										<span class="input-group-btn">
											<button type="button" class="btn btn-primary" onclick="geocode()" >
												<i class="fa fa-map-marker"></i>
												地址查詢經緯度
											</button>
										</span>
										<span class="input-group-btn">
											<button type="button" class="btn btn-default" onclick="currentApp.speechClick('address')" >
												<i class="fa fa-microphone"></i>
												語音輸入
											</button>
										</span>
									</div>
								</div>
							</fieldset>

							<fieldset>
								<div class="form-group">
									<label class="col-md-3 control-label">實際地址或地標名稱
										<br/>
										<font color="red">正確的地標或地址給司機參考</font>
									</label>
									<div class="col-md-4 row">
										<input type="text" placeholder="若無填寫則顯示 查詢關鍵字" class="form-control col-md-12" style="margin-left: 12px;" id="address_note" name="address_note" value="<?= isset($item) ? $item -> address_note : '' ?>" />
									</div>
									<div class="col-md-3">

										<span class="input-group-btn">
											<button type="button" class="btn btn-default" onclick="currentApp.speechClick('address_note')" >
												<i class="fa fa-microphone"></i>
												語音輸入
											</button>
										</span>
									</div>
								</div>
							</fieldset>

							<fieldset>
								<div class="form-group">
									<label class="control-label col-md-3">坐標(經度, 緯度)
										<br/>
										<font color="red">拖曳地標可修正坐標</font></label>
									<div class="controls col-md-6">
										<input id="lng" name="start_lng" required type="text" class="form-control" size="30" placeholder="經度" value="<?= isset($item) ? $item -> start_lng : '' ?>" style="margin-bottom: 10px;" />
										<input id="lat" name="start_lat" required type="text" class="form-control" size="30" placeholder="緯度" value="<?= isset($item) ? $item -> start_lat : '' ?>" style="margin-bottom: 10px;" />
									</div>
									<style>
										.gm-style-cc span, .gm-style a {
											background-image: none !important;
										}
									</style>
									<div id="map_canvas" style="width:90%; height:400px; margin: 0 auto;"></div>
									<script>
										loadScript("https://maps.googleapis.com/maps/api/js?sensor=true&language=zh-TW&key=AIzaSyCW69EciWfJ3cn_UEZex0sEXYiQzxdNo38", function(){
											mapOptions = {
												center : new google.maps.LatLng($('#lat').val(), $('#lng').val()),
												scrollwheel : false,
												zoom : 15,
												mapTypeId : google.maps.MapTypeId.ROADMAP
											};
											map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
											map.marker = new google.maps.Marker({
												map : map,
												draggable : true,
												position : new google.maps.LatLng($('#lat').val(), $('#lng').val())
											});
											google.maps.event.addListener(map.marker, 'dragend', function() {
												updatePosition(map.marker.getPosition());
											});

											geocoder = new google.maps.Geocoder();
										});

										var mapOptions, map, geocoder;

										function updatePosition(ll) {
											$('#lng').val(ll.lng());
											$('#lat').val(ll.lat());

											$('#app-edit-form').bootstrapValidator('updateStatus', 'start_lng', 'VALID');
											$('#app-edit-form').bootstrapValidator('updateStatus', 'start_lat', 'VALID');
										}

										function geocode() {
											var address = $('#address').val();

											geocoder.geocode({
												'address' :address
											}, function(results, status) {
												if (status == google.maps.GeocoderStatus.OK) {
													map.setCenter(results[0].geometry.location);

													if (map.marker) {
														map.marker.setMap(null);
													}

													map.marker = new google.maps.Marker({
														map : map,
														draggable : true,
														position : results[0].geometry.location
													});

													google.maps.event.addListener(map.marker, 'dragend', function() {
														updatePosition(map.marker.getPosition());
													});

													updatePosition(results[0].geometry.location);

													// update city name
													var cmps = results[0].address_components;
													var cmp = cmps[cmps.length - 3];
													var cityName = cmp.long_name;

													$('#cities option').each(function() {
														var txt = $(this).text().trim();
														if (txt == cityName) {
															$('#cities').val($(this).val());
														}
													})
												} else {
													alert("地址查詢錯誤 : " + status);
												}
											});
										}
									</script>
								</div>
							</fieldset>

					</div>
					<!-- end widget content -->

				</div>
				<!-- end widget div -->

			</div>
			</form>
			<!-- end widget -->
		</article>


		<article class="col-xs-12 col-sm-5 col-md-5 col-lg-5 sortable-grid ui-sortable">
			<div class="jarviswidget" id="" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
					<header>
						<div class="widget-toolbar pull-left">
							司機/車輛資訊
						</div>
						<div class="widget-toolbar pull-left">
							<button id="do_assign" onclick="doAssign()" class="btn btn-default btn-danger">
								<i class="fa fa-taxi"></i>手動分派
							</button>
						</div>
					</header>

				<!-- widget div-->
				<div>
					<!-- widget edit box -->
					<div class="jarviswidget-editbox">
						<!-- This area used as dropdown edit box -->
						<input class="form-control" type="text">
					</div>
					<!-- end widget edit box -->

					<!-- widget content -->
					<div class="widget-body">
						<table class="table table-hover" id="d-table">
							<thead>
								<tr>
									<th class="min15"></th>
									<th>司機答覆</th>
									<th>車牌</th>
									<th>電話</th>
									<th>司機</th>
									<th>距離</th>
								</tr>
							</thead>
							<tbody id="d-list">
							<tbody>
						</table>
					</div>
					<!-- end widget content -->

				</div>
				<!-- end widget div -->

			</div>
			<!-- end widget -->
		</article>
	</div>



<style>
	.kv-file-zoom {
		display: none;
	}
</style>
<script>

	function updateMemberName() {
		if($('#member_name').val().length == 0) {
			alert('請輸入乘客名稱');
			return;
		}
		var url = baseUrl + currentApp.basePath + 'update_member_name'; // the script where you handle the form input.
		$.ajax({
			type : "POST",
			url : url,
			data : {
				id : $('#id').val(),
				member_name: $('#member_name').val()
			},
			success : function(data) {
				currentApp.tableReload();
				currentApp.doEdit($('#id').val());
				if(data && data.error_message) {
					alert(data.error_message);
				} else {
					alert('已更新乘客名稱');
				}
				listDispatch();
			}
		});
	}

	function doDispatch() {
		if(!$('#app-edit-form').data('bootstrapValidator').validate().isValid()) return;

		var url = baseUrl + currentApp.basePath + 'dispatch'; // the script where you handle the form input.
		$.ajax({
			type : "POST",
			url : url,
			data : $("#app-edit-form").serialize(),
			success : function(data) {
				currentApp.tableReload();
				currentApp.doEdit($('#id').val());
				if(data && data.error_message) {
					alert(data.error_message);
				} else {
					alert('等待司機確認中');
				}
				listDispatch();
			}
		});
	}

	function doAssign() {
		var url = baseUrl + currentApp.basePath + 'do_assign'; // the script where you handle the form input.
		$.ajax({
			type : "POST",
			url : url,
			data : {
				id : $('#id').val()
			},
			success : function(data) {
				currentApp.tableReload();
				currentApp.doEdit($('#id').val());
				if(data && data.error_message) {
					alert(data.error_message);
				} else {
					alert('已分派');
				}
				listDispatch();
			}
		});
	}

	function doCancel() {
		var url = baseUrl + currentApp.basePath + 'do_cancel'; // the script where you handle the form input.
		$.ajax({
			type : "POST",
			url : url,
			data : {
				id : $('#id').val()
			},
			success : function(data) {
				currentApp.tableReload();
				currentApp.doEdit($('#id').val());
				alert('已取消');
				listDispatch();
			}
		});
	}

	function doResponse(dispatchId, driverId) {
		var url = baseUrl + currentApp.basePath + 'do_response'; // the script where you handle the form input.
		$.ajax({
			type : "POST",
			url : url,
			data : {
				dispatch_id : dispatchId,
				driver_id : driverId
			},
			success : function(data) {

			}
		});
	}

	function listDispatch() {
		if(currentApp && currentApp.tOut) {
			clearTimeout(currentApp.tOut);
		}

		btnCheck();
		if($('#dispatch_id').length > 0) {
			currentApp.tOut = setTimeout(function(){
				listDispatch();
			}, 1000);
		} else {
			return;
		}

		// do count down


		var dList = $('<tbody id="d-list"></tbody>');
		var url = baseUrl + currentApp.basePath + 'dispatch_detail/' + $('#dispatch_id').val();
		$.ajax({
			type : "POST",
			url : url,
			data : {

			},
			success : function(data) {
					if(data && data.server_time && $('#dispatch_time').val().length > 0) {
						var tDiff = moment(data.server_time).diff(moment($('#dispatch_time').val()));
						$('#dispatch_count').val(tDiff / 1000);
					}

					if(data && data.drivers) {
						$.each(data.drivers, function(){
							var $tr = $('<tr></tr>');
							var me = this;
							$('<td></td>').html(
								$("<button class='btn btn-primary btn-xs'><i class='fa fa-check'></i></button>")
									.attr('onclick', "doResponse(" + me.dispatch_id + ", " + me.driver_id + ")")
							).appendTo($tr);
							if(me.assign == 1) {
									$('<td></td>').html("<i class='fa fa-check'></i>").appendTo($tr);
							} else {
								if(me.response == 1) {
									if(me.is_reject == 1) {
										$('<td></td>').html("已拒絕").appendTo($tr);
									} else {
										$('<td></td>').html("已接受").appendTo($tr);
									}
								} else {
									$('<td></td>').html("未答覆").appendTo($tr);
								}
							}

							$('<td></td>').html(me.plate).appendTo($tr);
							$('<td></td>').html(me.mobile).appendTo($tr);
							$('<td></td>').html(me.driver_name).appendTo($tr);
							$('<td></td>').html(parseInt(me.dis)).appendTo($tr);

							$tr.appendTo(dList)
						});
						$('#d-list').remove();
						dList.appendTo($('#d-table'));
					}
			}
		});
	}
	listDispatch();

	function btnCheck() {
		if($('#dispatch_id').val() == '0') {
			// enable
			$("#do_dispatch").prop('disabled', false);
			$("#do_assign").prop('disabled', true);
			//$("#do_cancel").prop('disabled', true);
		} else {
			// disable
			$("#do_dispatch").prop('disabled', true);

			if($('#take_status').val() == 1) {
				$("#do_assign").prop('disabled', false);
			} else {
				$("#do_assign").prop('disabled', true);
			}

			//$("#do_cancel").prop('disabled', false);
		}
	}
	btnCheck();

	$("#file-input").fileinput({
        <?php if(!empty($item -> img)): ?>
        	initialPreview: [
        		'<?=  base_url('mgmt/images/get/' . $item -> img -> id) ?>'
        	],
        	initialPreviewConfig: [{
        		'caption' : '<?= $item -> img -> image_name ?>',
        		'size' : <?= $item -> img -> image_size ?>,
        		'width' : '120px',
        		'url' : '<?= base_url('mgmt/images/delete/' . $item -> img -> id)  ?>',
        		'key' : <?= $item -> img -> id ?>
        	}],
        <?php else: ?>
        	initialPreview: [],
        	initialPreviewConfig: [],
        <?php endif ?>
        initialPreviewAsData: true,
        maxFileCount: 1,
        uploadUrl: 'mgmt/images/upload/member_img',
        uploadExtraData: {
        }
    }).on('fileuploaded', function(event, data, previewId, index) {
	   var id = data.response.id;
		$('#image_id').val(id);
	}).on('fileuploaderror', function(event, data, previewId, index) {
		alert('upload error');
	}).on('filedeleted', function(event, key) {
		$('#image_id').val(0);
	});

	$('#app-edit-form').submit(function(){return false;});
	$('#app-edit-form').bootstrapValidator({
		feedbackIcons : {
			valid : 'glyphicon glyphicon-ok',
			invalid : 'glyphicon glyphicon-remove',
			validating : 'glyphicon glyphicon-refresh'
		},
		fields: {
		}
	})
	.bootstrapValidator('validate');

</script>
