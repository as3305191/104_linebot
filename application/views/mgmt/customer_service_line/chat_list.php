

<?php foreach($msg_list as $each): ?>
	<?php if($each -> is_cs == 1): ?>
	<div class="from_cs right">
			<div class="chat_txt"><?= $each -> msg ?><span class="chat_time"><?= $each -> create_time ?></span></div>
	</div>
	<?php else: ?>
		<div class="from_user">
			<div class="chat_txt"><?= $each -> msg ?><span class="chat_time"><?= $each -> create_time ?></span></div>
		</div>
	<?php endif ?>
<?php endforeach ?>
<script>
setTimeout(function(){
	var d = $('#chat_body');
	d.scrollTop(d.prop("scrollHeight"));
}, 500);
</script>
