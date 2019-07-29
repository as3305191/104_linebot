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
						<!-- <header>

						</header> -->

						<!-- widget div-->
						<div style="padding-bottom: 10px!important;">

							<!-- widget edit box -->
							<div class="jarviswidget-editbox">
								<!-- This area used as dropdown edit box -->

							</div>
							<!-- end widget edit box -->

							<!-- widget content -->

							<div class="widget-body" style="">

								<div id="main-frame">
									<h1>使用期限已到期，請繼續儲值！</h1>
								</div>
								<a href="<?= base_url('app/#mgmt/user_buy?buy=1') ?>" class="btn btn-info">點我儲值</a>
								<div style="font-size:20px;padding:10px 0 0 20px;;color: #AA0000; font-weight:bolder;">在線人數 :
									<?php
										$s_online_str = $this -> session -> userdata('s_online_str');
										$n_str = "$param->online_amt_0-$param->online_amt_1";
										$need_refresh = FALSE;
										if(!empty($s_online_str)) {
											if($s_online_str != $n_str) {
												$this -> session -> set_userdata('s_online_str', $n_str);
												$need_refresh = TRUE;
											}
										} else {
											$need_refresh = TRUE;
											$this -> session -> set_userdata('s_online_str', $n_str);
										}

										$s_online = 0;
										if($need_refresh || empty($this -> session -> userdata('s_online'))) {
											$s_online = rand($param -> online_amt_0, $param -> online_amt_1);
										} else {
											$s_online = $this -> session -> userdata('s_online');
										}
										$s_online += rand(-10, 10);
										$s_online = ($s_online > 0 ? $s_online : 0);
										$this -> session -> set_userdata('s_online', $s_online);

										echo $s_online;
									 ?>
								</div>
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

<!-- waitubg-->
<script type="text/javascript">


</script>
