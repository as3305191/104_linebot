<!DOCTYPE html>
<html lang="en-us">
	<head>
		<?php $this->load->view('layout/head'); ?>
		<script>
			var baseUrl = '<?= base_url(); ?>';
			var currentApp;
		</script>
	</head>
	<!-- #BODY -->
	<body class="smart-style-3">

		<?php $this->load->view('layout/header'); ?>
		<?php $this->load->view('layout/navigation'); ?>

		<!-- #MAIN PANEL -->
		<div id="main" role="main">

			<!-- RIBBON -->
			<div id="ribbon">

				<span class="ribbon-button-alignment">
					<span id="refresh" class="btn btn-ribbon" data-action="resetWidgets" data-title="refresh" rel="tooltip" data-placement="bottom" data-original-title="<i class='text-warning fa fa-warning'></i> Warning! This will reset all your widget settings." data-html="true" data-reset-msg="Would you like to RESET all your saved widgets and clear LocalStorage?"><i class="fa fa-refresh"></i></span>
				</span>

				<!-- breadcrumb -->
				<ol class="breadcrumb">
					<!-- This is auto generated -->
				</ol>
				<!-- end breadcrumb -->

				<!-- You can also add more buttons to the
				ribbon for further usability

				Example below:

				<span class="ribbon-button-alignment pull-right" style="margin-right:25px">
					<a href="#" id="search" class="btn btn-ribbon hidden-xs" data-title="search"><i class="fa fa-grid"></i> Change Grid</a>
					<span id="add" class="btn btn-ribbon hidden-xs" data-title="add"><i class="fa fa-plus"></i> Add</span>
					<button id="search" class="btn btn-ribbon" data-title="search"><i class="fa fa-search"></i> <span class="hidden-mobile">Search</span></button>
				</span> -->

			</div>
			<!-- END RIBBON -->

			<!-- #MAIN CONTENT -->
			<div id="content">

			</div>

			<!-- END #MAIN CONTENT -->

		</div>
		<!-- END #MAIN PANEL -->

		<?php $this->load->view('layout/footer'); ?>


		<!--================================================== -->

		<?php $this->load->view('layout/plugins'); ?>
		<!-- Scripts -->
		<script>
			var wOnResize = function(){
				var currentListElId = "#dt_list";
				if(currentApp && currentApp.dtListId) {
					currentListElId = currentApp.dtListId;
				}
				if($(window).height() < 700) {
					$('.t-box').css('height', ($(currentListElId).height()) + 'px');
				} else {
					$('.t-box').css('max-height', ($(window).height() - 320) + 'px');
				}
				$('#widget-grid').width($('#content').width() - 13);
			};
			$(window).on('resize', wOnResize);

			$(document).ajaxComplete(function( event, xhr, settings ) {
			    if(xhr.responseText.indexOf('<script>window.location') > -1) {
			    	window.location.reload();
			    }
			});
		</script>
	</body>
</html>
