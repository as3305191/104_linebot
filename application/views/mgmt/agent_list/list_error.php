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

								<h1>並非一級代理人，無法操作此功能</h1>

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
	var mCols = [null
	<?php if($login_user -> role_id == 99):?>
	, {
		data : 'corp_name'
	}
	<?php endif ?>
		, {
		data : 'account'
	}, {
		data : 'user_name'
	}, {
		data : 'is_valid_mobile',render:function(d,t,r){
			if(d == 1) {
				return "<font color='green'>通過</font>";
			}
			if(d == 0) {
				return "<font color='red'>未通過</font>";
			}

			return "-";
		}
	}
	<?php if($login_user -> role_id == 1):?>
	, {
		data : 'role_name'
	}
	<?php endif ?>
	, {
		data : 'create_time'
	}];

	<?php if($login_user -> role_id == 1):?>
	var mOrderIdx = 5;
	<?php elseif($login_user -> role_id == 99): ?>
	var mOrderIdx = 5;
	<?php else: ?>
	var mOrderIdx = 4;
	<?php endif ?>

	var defaultContent = '<a href="#deleteModal" role="button" data-toggle="modal" style="margin-right: 5px;"><i class="fa fa-trash fa-lg"></i></a>';

	<?php if($login_user -> role_id == 1 || $login_user -> role_id == 99):?>
	var mColDefs = [{
		targets : 0,
		data : null,

		defaultContent : defaultContent,
		searchable : false,
		orderable : false,
		width : "5%",
		className : ''
	}, {
		"targets" : 1,
		"orderable" : false
	}, {
		"targets" : 2,
		"orderable" : false
	}, {
		"targets" : 3,
		"orderable" : false
	}, {
		"targets" : 4,
		"orderable" : false
	}];
	<?php else: ?>
	var mColDefs = [{
		targets : 0,
		data : null,

		defaultContent : defaultContent,
		searchable : false,
		orderable : false,
		width : "5%",
		className : ''
	}, {
		"targets" : 1,
		"orderable" : false
	}, {
		"targets" : 2,
		"orderable" : false
	}, {
		"targets" : 3,
		"orderable" : false
	}];
	<?php endif ?>
	loadScript(baseUrl + "js/class/BaseAppClass.js", function(){
		loadScript(baseUrl + "js/app/users/list.js", function(){
			currentApp = new UsersAppClass(new BaseAppClass({}));
		});
	});
</script>
