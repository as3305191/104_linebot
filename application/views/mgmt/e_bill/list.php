<style>
	#dt_list_wrapper {
		border-top: 1px solid #CCCCCC;
	}
</style>
<input type="hidden" id="corp_id" value="<?= $corp -> id ?>" />
<div class="tab-content">
	<div class="tab-pane active" id="list_page">
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
								<div class="btn-group">

								</div>
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
							<div class="widget-body no-padding ">

								<table id="dt_list" class="table table-striped table-bordered table-hover" width="100%">
									<thead>
										<tr>
											<th class="min150">序號</th>
											<th class="min150">狀態</th>
											<th class="min150">項目</th>
											<th class="min150">金額NTD</th>
											<th class="min150">DBC點數</th>
											<th class="">建立時間</th>
										</tr>
										<tr class="search_box">
									    <th><input class="form-control input-xs" type="text" /></th>
									    <th></th>
									    <th></th>
									    <th></th>
									    <th></th>
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
	</div>

	<div class="tab-pane animated fadeIn" id="edit_page">
		<section class="">
			<!-- row -->
			<div class="row">
				<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="edit-modal-body">

				</article>
			</div>
		</section>
	</div>

</div>
<?php $this -> load -> view('general/delete_modal'); ?>

<script type="text/javascript">
	loadScript(baseUrl + "js/class/BaseAppClass.js", function(){
		loadScript(baseUrl + "js/app/e_bill/list.js", function(){
			currentApp = new PayOrdersAppClass(new BaseAppClass({}));
		});
	});
</script>
