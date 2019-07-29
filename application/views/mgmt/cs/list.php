<style>
.col-md-2, .col-md-10{
    padding:0;
}
.panel{
    margin-bottom: 0px;
}
.chat-window{
    position:relative;
    margin-left:0px;
}
.chat-window > div > .panel{
    border-radius: 5px 5px 0 0;
}
.icon_minim{
    padding:2px 10px;
}
.msg_container_base{
  background: #e5e5e5;
  margin: 0;
  padding: 0 10px 10px;
  max-height:300px;
  overflow-x:hidden;
}
.top-bar {
  background: #666;
  color: white;
  padding: 10px;
  position: relative;
  overflow: hidden;
}
.msg_receive{
    padding-left:0;
    margin-left:0;
}
.msg_sent{
    padding-bottom:20px !important;
    margin-right:0;
}
.messages {
  background: white;
  padding: 10px;
  border-radius: 2px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
  max-width:100%;
}
.messages > p {
    font-size: 13px;
    margin: 0 0 0.2rem 0;
  }
.messages > time {
    font-size: 11px;
    color: #ccc;
}
.msg_container {
    padding: 10px;
    overflow: hidden;
    display: flex;
}
img {
    display: block;
    width: 100%;
}
.avatar {
    position: relative;
}
.base_receive > .avatar:after {
    content: "";
    position: absolute;
    top: 0;
    right: 0;
    width: 0;
    height: 0;
    border: 5px solid #FFF;
    border-left-color: rgba(0, 0, 0, 0);
    border-bottom-color: rgba(0, 0, 0, 0);
}

.base_sent {
  justify-content: flex-end;
  align-items: flex-end;
}
.base_sent > .avatar:after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 0;
    border: 5px solid white;
    border-right-color: transparent;
    border-top-color: transparent;
    box-shadow: 1px 1px 2px rgba(black, 0.2); // not quite perfect but close
}

.msg_sent > time{
    float: right;
}

.msg_container_base::-webkit-scrollbar-track
{
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
    background-color: #F5F5F5;
}

.msg_container_base::-webkit-scrollbar
{
    width: 12px;
    background-color: #F5F5F5;
}

.msg_container_base::-webkit-scrollbar-thumb
{
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
    background-color: #555;
}

.btn-group.dropup{
    position:fixed;
    left:0px;
    bottom:0;
}
</style>
<div class="tab-content">
	<div class="tab-pane active" id="list_page">

		<!-- widget grid -->
		<section id="widget-grid" class="">
			<input type="hidden" id="login_user_id" name="login_user_id" value="<?= $login_user_id ?>" />
			<!-- row -->
			<div class="row">
				<div class="chat_container">
					<div class="row chat-window col-xs-11 col-sm-8 col-md-6" id="chat_window_1" style="margin-left:10px;">
							<div class="col-xs-12 col-md-12" style="padding:0px;">
								<div class="panel panel-default">

											<div id="chat_list" class="panel-body msg_container_base">
													<div class="row msg_container base_sent">
															<div class="col-md-10 col-xs-10">
																	<div class="messages msg_sent">
																			<p>that mongodb thing looks good, huh?
																			tiny master db, and huge document store</p>
																			<time datetime="2009-11-13T20:00">Timothy • 51 min</time>
																	</div>
															</div>
															<div class="col-md-2 col-xs-2 avatar">
																	<img src="http://www.bitrebels.com/wp-content/uploads/2011/02/Original-Facebook-Geek-Profile-Avatar-1.jpg" class=" img-responsive ">
															</div>
													</div>
													<div class="row msg_container base_receive">
															<div class="col-md-2 col-xs-2 avatar">
																	<img src="http://www.bitrebels.com/wp-content/uploads/2011/02/Original-Facebook-Geek-Profile-Avatar-1.jpg" class=" img-responsive ">
															</div>
															<div class="col-md-10 col-xs-10">
																	<div class="messages msg_receive">
																			<p>that mongodb thing looks good, huh?
																			tiny master db, and huge document store</p>
																			<time datetime="2009-11-13T20:00">Timothy • 51 min</time>
																	</div>
															</div>
													</div>
													<div class="row msg_container base_receive">
															<div class="col-md-2 col-xs-2 avatar">
																	<img src="http://www.bitrebels.com/wp-content/uploads/2011/02/Original-Facebook-Geek-Profile-Avatar-1.jpg" class=" img-responsive ">
															</div>
															<div class="col-xs-10 col-md-10">
																	<div class="messages msg_receive">
																			<p>that mongodb thing looks good, huh?
																			tiny master db, and huge document store</p>
																			<time datetime="2009-11-13T20:00">Timothy • 51 min</time>
																	</div>
															</div>
													</div>
													<div class="row msg_container base_sent">
															<div class="col-xs-10 col-md-10">
																	<div class="messages msg_sent">
																			<p>that mongodb thing looks good, huh?
																			tiny master db, and huge document store</p>
																			<time datetime="2009-11-13T20:00">Timothy • 51 min</time>
																	</div>
															</div>
															<div class="col-md-2 col-xs-2 avatar">
																	<img src="http://www.bitrebels.com/wp-content/uploads/2011/02/Original-Facebook-Geek-Profile-Avatar-1.jpg" class=" img-responsive ">
															</div>
													</div>
													<div class="row msg_container base_receive">
															<div class="col-md-2 col-xs-2 avatar">
																	<img src="http://www.bitrebels.com/wp-content/uploads/2011/02/Original-Facebook-Geek-Profile-Avatar-1.jpg" class=" img-responsive ">
															</div>
															<div class="col-xs-10 col-md-10">
																	<div class="messages msg_receive">
																			<p>that mongodb thing looks good, huh?
																			tiny master db, and huge document store</p>
																			<time datetime="2009-11-13T20:00">Timothy • 51 min</time>
																	</div>
															</div>
													</div>
													<div class="row msg_container base_sent">
															<div class="col-md-10 col-xs-10 ">
																	<div class="messages msg_sent">
																			<p>that mongodb thing looks good, huh?
																			tiny master db, and huge document store</p>
																			<time datetime="2009-11-13T20:00">Timothy • 51 min</time>
																	</div>
															</div>
															<div class="col-md-2 col-xs-2 avatar">
																	<img src="http://www.bitrebels.com/wp-content/uploads/2011/02/Original-Facebook-Geek-Profile-Avatar-1.jpg" class=" img-responsive ">
															</div>
													</div>
											</div>
											<div class="panel-footer">
													<div class="input-group">
															<input id="send_message" type="text" class="form-control input-sm chat_input" placeholder="Write your message here..." />
															<span class="input-group-btn">
															<button class="btn btn-primary btn-sm" onclick="currentApp.doSend()">請輸入訊息</button>
															</span>
													</div>
											</div>
										</div>
									</div>
								</div>

					<div class="btn-group dropup">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
									<span class="glyphicon glyphicon-cog"></span>
									<span class="sr-only">Toggle Dropdown</span>
							</button>
							<ul class="dropdown-menu" role="menu">
									<li><a href="#" id="new_chat"><span class="glyphicon glyphicon-plus"></span> Novo</a></li>
									<li><a href="#"><span class="glyphicon glyphicon-list"></span> Ver outras</a></li>
									<li><a href="#"><span class="glyphicon glyphicon-remove"></span> Fechar Tudo</a></li>
									<li class="divider"></li>
									<li><a href="#"><span class="glyphicon glyphicon-eye-close"></span> Invisivel</a></li>
							</ul>
					</div>
			</div>

			</div>

			<!-- end row -->

		</section>
		<!-- end widget grid -->
	</div>
</div>
<?php $this -> load -> view('general/delete_modal'); ?>
<input type="hidden" id="is_cs" value="yes" />
<script type="text/javascript">
	loadScript(baseUrl + "js/class/BaseAppClass.js", function(){
		loadScript(baseUrl + "js/app/cs/list.js", function(){
			currentApp = new CsAppClass(new BaseAppClass({}));
			$('#chat_list').css('min-height', $(window).height() - 220 + 'px');

      window.mLastId = <?= $last_id ?>;
      currentApp.checkLastId = function() {
      	$.ajax({
      		type: "POST",
      		url: baseUrl + 'mgmt/cs/' + 'check_last_id',
      		data: {
      			user_id : $('#login_user_id').val()
      		},
      		dataType: 'json',
      		success: function(data)
      		{
      				if(data && data.last_id > 0) {
      					if(data.last_id > window.mLastId) {
      						window.mLastId = data.last_id;
      						currentApp.reloadData();
      					}
      				}
      		}
      	});
      }

      var intId = setInterval(function(){
      	if($('#is_cs').length == 0) {
      		clearInterval(intId);
      	} else {
      		currentApp.checkLastId();
      	}
      }, 1000);
		});
	});
</script>
