<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget" id="wid-id-7" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
	<header>
		<!-- <h2>編輯選單</h2> -->
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="back_parent" onclick="currentApp.backTo()" class="btn btn-default ">
				<i class="fa fa-arrow-circle-left"></i>返回
			</a>
		</div>
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="submit_btn" onclick="currentApp.doSubmit()" class="btn btn-default btn-danger">
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

			<div id="chat_body">

			</div>


			<div>
				<form id="my_reply_form" method="post">
					<input id="my_reply" type="text" class="form-control" />
					<button type="submit" class="btn btn-primary">送出</button>
				</form>
			</div>
		</div>
		<!-- end widget content -->

	</div>
	<!-- end widget div -->

</div>

<style>
#chat_body {
	max-width: 800px;
	margin-bottom: 3em;
	max-height: 300px;
	overflow-y: scroll;
}
.chat_txt {
	font-size:20px;
}
.chat_time {
	font-size:12px;
	color:#ccc;
}

.right {
	text-align:right;
}
</style>
<!-- end widget -->
<!-- PAGE RELATED PLUGIN(S) -->
<script type="text/javascript">
	$("#my_reply_form").submit(function(e){
		e.preventDefault();
		var $msg = $("#my_reply").val();
		$("#my_reply").val('');
		$.ajax({
			type: "POST",
			url: '<?= base_url('mgmt/customer_service_line/add_msg') ?>',
			data: {
				user_id: '<?= $room -> user_id ?>',
				msg: $msg
			},
			success: function(data)
			{
					if(data.error_msg) {
						alert(data.error_msg);
					} else {
						reloadMsgList();
					}
			}
		});
		console.log($('#my_reply').val());
	});

	function reloadMsgList() {
		$("#chat_body").load('<?= base_url("mgmt/customer_service_line/chat_list/{$room->user_id}") ?>');
	}
	reloadMsgList();
</script>
