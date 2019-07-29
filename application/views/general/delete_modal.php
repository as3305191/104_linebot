<div class="modal small fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					×
				</button>
				<h3 id="deleteModalLabel">刪除確認</h3>
			</div>
			<div class="modal-body" id="deleteModalBody">
				<div class="alert alert-warning fade in">
					<i class="fa fa-warning modal-icon"></i>
					<strong>Warning</strong> 確定要刪除嗎? <br>
					無法復原
				</div>

			</div>
			<div class="modal-footer">
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
					取消
				</button>
				<button class="btn btn-danger" data-dismiss="modal" id="modal_do_delete">
					刪除
				</button>
			</div>
		</div>
	</div>
</div>
