<div class="modal small fade" id="speechModal" tabindex="-1" role="dialog" aria-labelledby="speechModalLabel" aria-hidden="true">
	<input type="hidden" id="currentReturnID" name="" value="">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					×
				</button>
				<h3 id="speechModalLabel">請開始講話</h3>
			</div>
			<div class="modal-body">
				<!-- <div class="alert alert-warning fade in">
					<i class="fa fa-warning modal-icon"></i>
					<strong>Warning</strong> 確定要刪除嗎? <br>
					無法復原
				</div> -->
				<canvas id="level" height="200" width="500" style="display:block"></canvas>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
					取消
				</button>
				<button class="btn btn-danger" data-dismiss="modal" id="modal_do_speech">
					結束
				</button>
			</div>
		</div>
	</div>
</div>
