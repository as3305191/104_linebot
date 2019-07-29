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
				<input type="hidden" name="id" value="<?= isset($item) ? $item -> id : '0' ?>" />
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">群組名稱</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="group_name" value="<?= isset($item) ? $item -> group_name : '' ?>" />
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
