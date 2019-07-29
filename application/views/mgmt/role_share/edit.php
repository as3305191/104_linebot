<style>
.file-drag-handle {
	display: none;
}
</style>
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
				<input type="hidden" name="id" id="item_id" value="<?= isset($item) ? $item -> id : '' ?>" />

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">角色</label>
						<div class="col-md-6">
							<input type="text" class="form-control"  name="share_name" value="<?= isset($item) ? $item -> share_name : '' ?>" readonly />
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">利潤百分比</label>
						<div class="col-md-6">
							<input type="number" min="0" max="100" required class="form-control"  name="share_val" value="<?= isset($item) ? $item -> share_val : '' ?>" />
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
			corp_code: {
            validators: {
              remote: {
              	message: '已經存在',
              	url: baseUrl + 'mgmt/corp/check_corp_code/' + ($('#item_id').val().length > 0 ? $('#item_id').val() : '0')
              }
            }
         }
      }

	})
	.bootstrapValidator('validate');


	function copyToClipboard(element) {
	  var $input = $("<input>");
	  $("body").append($input);
	  $input.val($(element).val());

		if (navigator.userAgent.match(/ipad|ipod|iphone/i)) {
		  var el = $input.get(0);
		  var editable = el.contentEditable;
		  var readOnly = el.readOnly;
		  el.contentEditable = true;
		  el.readOnly = false;
		  var range = document.createRange();
		  range.selectNodeContents(el);
		  var sel = window.getSelection();
		  sel.removeAllRanges();
		  sel.addRange(range);
		  el.setSelectionRange(0, 999999);
		  el.contentEditable = editable;
		  el.readOnly = readOnly;
		} else {
		  $input.select();
		}

	  document.execCommand("copy");
	  $input.remove();

		alert('複製成功');
	}


</script>
