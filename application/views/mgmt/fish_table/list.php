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
									<button onclick="currentApp.doEdit(0)" class="btn dropdown-toggle btn-xs btn-success" data-toggle="dropdown">
										<i class="fa fa-plus"></i>新增
									</button>
								</div>
							</div>
						</header>

						<!-- widget div-->
						<div>

							<!-- widget edit box -->

							<!-- widget content -->
							<div class="widget-body no-padding">

								<table id="dt_list" class="table table-striped table-bordered table-hover" width="100%">
									<thead>
										<tr>
											<th class="min50"></th>
											<th class="min100">桌名</th>
											<th class="min100">彩池100</th>
											<th class="min100">彩池100魚王</th>
											<th class="min100">彩池2000</th>
											<th class="min100">彩池2000魚王</th>
											<th class="min100">彩池20000</th>
											<th class="min100">彩池20000魚王</th>
											<th class="min100">彩池200000</th>
											<th class="min100">彩池200000魚王</th>
											<th class="min100">彩池1000000</th>
											<th class="min100">彩池1000000魚王</th>
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
					<div class="modal fade" id="edit_page111" role="dialog" aria-hidden="true">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header">
								</div>
								<div class="modal-body" id="">
									<form id="" class="">
										<input id="edit_page_id"  type="hidden" value="">

										<fieldset>
											<div class="form-group">
												<label class="col-md-3 control-label">彩池100</label>
												<div class="col-md-6">
													<input id="pool_100" type="text" required class="form-control"  name="ntd_price" value="" />
												</div>
											</div>
										</fieldset>
										<fieldset>
											<div class="form-group">
												<label class="col-md-3 control-label">彩池100魚王</label>
												<div class="col-md-6">
													<input id="pool_100_king" type="text" required class="form-control"  name="ntd_price" value="" />
												</div>
											</div>
										</fieldset>
										<fieldset>
												<div class="form-group">
													<label class="col-md-3 control-label">彩池2000</label>
													<div class="col-md-6">
														<input id="pool_2000" type="text" required class="form-control"  name="ntd_price" value="" />
													</div>
												</div>
											</fieldset>
											<fieldset>
													<div class="form-group">
														<label class="col-md-3 control-label">彩池2000魚王	</label>
														<div class="col-md-6">
															<input id="pool_2000_king" type="text" required class="form-control"  name="ntd_price" value="" />
														</div>
													</div>
												</fieldset>
												<fieldset>
														<div class="form-group">
															<label class="col-md-3 control-label">彩池20000	</label>
															<div class="col-md-6">
																<input id="pool_20000" type="text" required class="form-control"  name="ntd_price" value="" />
															</div>
														</div>
													</fieldset>
													<fieldset>
															<div class="form-group">
																<label class="col-md-3 control-label">彩池20000魚王	</label>
																<div class="col-md-6">
																	<input id="pool_20000_king" type="text" required class="form-control"  name="ntd_price" value="" />
																</div>
															</div>
													</fieldset>
													<fieldset>
														<div class="form-group">
															<label class="col-md-3 control-label">彩池200000	</label>
															<div class="col-md-6">
																<input id="pool_200000" type="text" required class="form-control"  name="ntd_price" value="" />
															</div>
														</div>
													</fieldset>
													<fieldset>
														<div class="form-group">
															<label class="col-md-3 control-label">彩池200000魚王	</label>
															<div class="col-md-6">
																<input id="pool_200000_king" type="text" required class="form-control"  name="ntd_price" value="" />
															</div>
														</div>
													</fieldset>
													<fieldset>
														<div class="form-group">
															<label class="col-md-3 control-label">彩池1000000	</label>
															<div class="col-md-6">
																<input id="pool_1000000" type="text" required class="form-control"  name="ntd_price" value="" />
															</div>
														</div>
													</fieldset>
													<fieldset>
														<div class="form-group">
															<label class="col-md-3 control-label">彩池1000000魚王	</label>
															<div class="col-md-6">
																<input id="pool_1000000_king" type="text" required class="form-control"  name="ntd_price" value="" />
															</div>
														</div>
													</fieldset>

										<div class="modal-footer">
											<button type="button" class="btn btn-danger btn-sm" onclick="save_edit_page111()">
												<i class="fa fa-save"></i> 存擋
											</button>
											<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
												<i class="fa fa-close"></i> 關閉
											</button>
										</div>
									</form>
								</div>

							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div>
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
		loadScript(baseUrl + "js/app/fish_table/list.js", function(){
			currentApp = new FishtableAppClass(new BaseAppClass({}));
		});
	});


	function save_edit_page111() {
			var url = '<?= base_url() ?>' + 'mgmt/fish_table/insert_edit_page';

			$.ajax({
				url : url,
				type: 'POST',
				data: {
					id: $('#edit_page_id').val(),
					pool_100: $('#pool_100').val(),
					pool_100_king: $('#pool_100_king').val(),
					pool_2000: $('#pool_2000').val(),
					pool_2000_king: $('#pool_2000_king').val(),
					pool_20000: $('#pool_20000').val(),
					pool_20000_king: $('#pool_20000_king').val(),
					pool_200000: $('#pool_200000').val(),
					pool_200000_king: $('#pool_200000_king').val(),
					pool_1000000: $('#pool_1000000').val(),
					pool_1000000_king: $('#pool_1000000_king').val()

				},
				dataType: 'json',
				success: function(d) {
					location.reload();
				},
				failure:function(){
					alert('faialure');
				}
			});
		}
</script>
