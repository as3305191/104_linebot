
<?php foreach($list as $each): ?>
	<?php if($each -> send_user_id != $each -> user_id): ?>
	<div class="row msg_container base_sent">
			<div class="col-md-10 col-xs-10">
					<div class="messages msg_sent">
							<p><?= $each -> msg ?></p>
							<time datetime="2009-11-13T20:00"><?= $each -> create_time ?></time>
					</div>
			</div>
			<div class="col-md-2 col-xs-2 avatar">
					<img src="<?= $each -> user_image_url ?>" class=" img-responsive ">
			</div>
	</div>
<?php else: ?>
	<div class="row msg_container base_receive">
			<div class="col-md-2 col-xs-2 avatar">
					<img src="<?= $each -> user_image_url ?>" class=" img-responsive ">
			</div>
			<div class="col-xs-10 col-md-10">
					<div class="messages msg_receive">
							<p><?= $each -> msg ?></p>
							<time datetime="2009-11-13T20:00"><?= $each -> create_time ?></time>
					</div>
			</div>
	</div>
<?php endif ?>
<?php endforeach ?>
<script>
setTimeout(function(){
	var d = $('#chat_list');
	d.scrollTop(d.prop("scrollHeight"));
}, 500);
</script>
