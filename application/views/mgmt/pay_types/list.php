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
					<div class="widget-body">
						<form class="form-horizontal" id="updaet-form">
							<fieldset>
								<legend>付款方式</legend>
								<?php foreach($pay_types as $each): ?>
									<div class="form-group">
										<div class="col-xs-3 col-sm-2 col-md-2 col-lg-2">
											<label class="form-control-static">
												<input type="checkbox" name="enabled[]" value="<?= $each -> id ?>" <?= $each -> enabled == 1 ? 'checked="checked"' : ''  ?> />
												<?= $each -> type_name  ?>
											</label>
										</div>
										<div class="col-xs-8 col-sm-5 col-md-4 col-lg-3">
											<?php if($each -> need_flow): ?>
												<select class="form-control" name="flow[]">
													<!-- <option value="<?= $each -> id ?>__0">---</option> -->
													<?php foreach($cash_flows as $cf): ?>
														<option value="<?= $each -> id ?>__<?= $cf -> id ?>" <?= $cf -> id == $each -> flow_id ? 'selected="selected"' : ''  ?>><?= $cf -> name ?></option>
													<?php endforeach ?>
												</select>
											<?php else: ?>
												<p class="form-control-static">&nbsp;</p>
											<?php endif ?>
										</div>
									</div>
								<?php endforeach ?>
								
								<div class="form-group">
									<div class="col-md-6" id="setting_content">
										<a class="btn btn-warning" onclick="doSubmit();"><i class="fa fa-save"></i>存檔</a>
									</div>
								</div>
							</fieldset>
						</form>

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
	$('#updaet-form').on('submit', function(e){
			e.preventDefault();
			$.ajax({
				type : "POST",
				url : baseUrl + 'mgmt/pay_types/update',
				data : $( this ).serialize(),
				success : function(data) {
					$.notify({
						title: '<strong>訊息</strong>',
						message: '更新成功!'
					},{
						type: 'success',
						delay: 3000,
					});
				}
			}); 
		});
		
	function doSubmit() {
		$('#updaet-form').submit();
	}
</script>
