<!-- widget grid -->
<section id="widget-grid" class="">

	<!-- row -->
	<div class="row">

		<!-- NEW WIDGET START -->
		<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

			<!-- Widget ID (each widget will need unique ID)-->
			<div class="jarviswidget ">
				<header>
					<div class="widget-toolbar pull-left">
						<div class="btn-group">
							<button onclick="navApp.doEdit(0)" class="btn dropdown-toggle btn-xs btn-default" data-toggle="dropdown">
								<i class="fa fa-plus"></i>新增
							</button>
						</div>
					</div>
					<div class="widget-toolbar pull-left">
						<a href="javascript:void(0);" id="back_parent" onclick="navApp.backParent()" class="btn btn-default ">
							<i class="fa fa-arrow-circle-left"></i>返回
						</a>
					</div>
				</header>

				<!-- widget div-->
				<div>

					<!-- widget edit box -->
					<div class="jarviswidget-editbox">
						<!-- This area used as dropdown edit box -->

					</div>
					<!-- end widget edit box -->

					<!-- widget content -->
					<div class="widget-body no-padding">

						<table id="dt_list" class="table table-striped table-bordered table-hover" width="100%">
							<thead>
								<tr>
									<th class="min50"></th>
									<th class="min150">Icon</th>
									<th class="min150">NavName</th>
									<th class="min150">Key</th>
									<th class="min150">BasePath</th>
									<th>Pos</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>

					</div>
					<!-- end widget content -->

				</div>
				<!-- end widget div -->

			</div>
			<!-- end widget -->

		</article>
		<!-- WIDGET END -->

	</div>

	<!-- end row -->

</section>
<!-- end widget grid -->

<?php $this -> load -> view('general/edit_modal'); ?>
<?php $this -> load -> view('general/delete_modal'); ?>

<script type="text/javascript">
	var baseUrl = '<?= base_url(); ?>';
	loadScript(baseUrl + "js/app/nav/list.js", function(){
		navApp.init();
	});
</script>
