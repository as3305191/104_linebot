<!-- widget grid -->
<section id="widget-grid" class="">

	<!-- row -->
	<div class="row">

		<!-- NEW WIDGET START -->
		<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

			<!-- Widget ID (each widget will need unique ID)-->
			<div class="jarviswidget">
				<header>
					<div class="widget-toolbar pull-left">
						<input id="file-input" name="file" type="file" class="file-loading form-control">
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
									<th class="min150">圖片</th>
									<th class="min250">描述</th>
									<th class="min200">檔名</th>
									<th>網址</th>
								</tr>
								<tr class="search_box">
									    <th></th>
									    <th></th>
									    <th><input class="form-control input-xs" type="text" /></th>
									    <th><input class="form-control input-xs" type="text" /></th>
									    <th></th>
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

