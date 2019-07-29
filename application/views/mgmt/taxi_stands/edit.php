<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget" id="wid-id-7" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
	<header>
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="back_parent" onclick="currentApp.backTo()" class="btn btn-default ">
				<i class="fa fa-arrow-circle-left"></i>返回
			</a>
		</div>
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="back_parent" onclick="currentApp.doSubmit()" class="btn btn-default btn-danger">
				<i class="fa fa-save"></i>存檔
			</a>
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

			<form id="app-edit-form" method="post" class="form-horizontal">
				<input type="hidden" name="id" value="<?= $id ?>" />
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">車隊</label>
						<div class="col-md-6">
							<select name="fleet_id" id="fleet_id" class="form-control">
									<option value="0">無</option>
								<?php foreach($fleet_list as $each): ?>
									<option value="<?= $each -> id?>" <?= isset($item) && $item -> fleet_id == $each -> id ? 'selected' : '' ?> ><?=  $each -> fleet_name ?></option>
								<?php endforeach ?>
							</select>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">排班點</label>
						<div class="col-md-6">
							<input type="text" required class="form-control" id="stop_name" name="stop_name" value="<?= isset($item) ? $item -> stop_name : '' ?>" />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">縣市/鄉鎮[市]區</label>
						<div class="col-md-6 row">
							<select name="city" id="city" class="form-control min100 col-md-3" style="margin-left: 12px;" onchange="cityChange()">
								<?php foreach($city_list as $each): ?>
									<option value="<?= $each -> city?>" <?= isset($item) && $item -> city == $each -> city ? 'selected' : '' ?> ><?=  $each -> city ?></option>
								<?php endforeach ?>
							</select>

							<select name="district" id="district" class="form-control min100 col-md-3" style="margin-left: 12px;">
								<?php foreach($district_list as $each): ?>
									<option value="<?= $each -> district?>" <?= isset($item) && $item -> district == $each -> district ? 'selected' : '' ?> ><?=  $each -> district ?></option>
								<?php endforeach ?>
							</select>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">協助定位地址或地點（非必填）</label>
						<div class="col-md-4 row">
							<input type="text" class="form-control col-md-12" style="margin-left: 12px;" id="address" name="address" value="<?= isset($item) ? $item -> address : '' ?>" />
						</div>
						<div class="col-md-3">
							<span class="input-group-btn">
								<button type="button" class="btn btn-primary" onclick="geocode()" >
									<i class="fa fa-map-marker"></i>
									地址查詢經緯度
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
							<input id="lng" name="lng" type="text" class="form-control" size="30" placeholder="經度" value="<?= isset($item) ? $item -> lng : '' ?>" style="margin-bottom: 10px;" />
							<input id="lat" name="lat" type="text" class="form-control" size="30" placeholder="緯度" value="<?= isset($item) ? $item -> lat : '' ?>" style="margin-bottom: 10px;" />
							<style>
								.gm-style-cc span, .gm-style a {
									background-image: none !important;
								}
							</style>
							<div id="map_canvas" style="width:400px; height:300px"></div>
							<script>
								loadScript("https://maps.googleapis.com/maps/api/js?sensor=true&language=zh-TW&key=AIzaSyCnRtjB_PoKh0oauiaVeCcvLSkPlBGFz0A", function(){
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
								}

								function geocode() {
									var city = $('#city').val();
									var district = $('#district').val();
									var address = $('#address').val();
									var stopName = $('#stop_name').val();

									var sAddr = stopName;
									if(address.length > 0) {
										sAddr = address;
									}

									geocoder.geocode({
										'address' :sAddr
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
					</div>
				</fieldset>

			</form>

		</div>
		<!-- end widget content -->

	</div>
	<!-- end widget div -->

</div>
<!-- end widget -->
<style>
	.kv-file-zoom {
		display: none;
	}
</style>
<script>
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

	function cityChange() {
		$city = $('#city').val();
		$.ajax({
			url: '<?= base_url() ?>' + 'api/members/find_district_by_city',
			type: 'POST',
			data: {
				city: $city
			},
			dataType: 'json',
			success: function(d) {
				if(d) {
					$district = $('#district').empty();
					$.each(d.list, function(){
		        $('<option/>', {
		            'value': this.district,
		            'text': this.district
		        }).appendTo($district);
		    });
				}
			},
			failure:function(){
				alert('faialure');
			}
		});
	}

</script>
