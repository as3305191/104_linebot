<!DOCTYPE html>
<html lang="en-us">	
	<head>
		<?php $this->load->view('layout/head'); ?>
	</head>
	<!-- #BODY -->
	<body>
		<table class="table table-hover">
			<?php foreach($items as $each): ?>
			<tr>
				<td class="min50"><?= $each -> id ?></td>
				<td><?= $each -> status_name ?></td>
			</tr>
			<?php endforeach ?>
		</table>
		
	</body>
</html>