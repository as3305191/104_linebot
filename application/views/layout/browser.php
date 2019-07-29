<!DOCTYPE html>
<html lang="en-us">
	<head>
		<?php $this->load->view('layout/head'); ?>
	</head>
	<!-- #BODY -->
	<body class="smart-style-0">
		<!-- #MAIN PANEL -->
		<div id="main" role="main" style="margin-left: 0px;">

			<!-- #MAIN CONTENT -->
			<div id="content">
				<?php $this->load->view('mgmt/images/list'); ?>
			</div>
			<!-- END #MAIN CONTENT -->

		</div>
		<!-- END #MAIN PANEL -->
		<?php $this->load->view('layout/plugins'); ?>

		<!-- x-editable -->
		<script src="<?= base_url('js/plugin/x-editable/x-editable.min.js')?>"></script>

		<script type="text/javascript">
			var storeId = '<?= $login_store_id ?>';
			var baseUrl = '<?= base_url(); ?>';
			loadScript(baseUrl + "js/app/images/list.js", function(){
				imagesApp.init();
			});
		</script>
	</body>
</html>
