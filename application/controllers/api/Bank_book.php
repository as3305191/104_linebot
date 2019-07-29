<?php
class Bank_book extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		// setup models
		$this -> load -> model('Bank_book_dao', 'bank_book_dao');

		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Com_tx_dao', 'ctx_dao');
		$this -> load -> model('Config_dao', 'config_dao');
	}

	public function list_all() {
		$res = array();
		$res['success'] = TRUE;
		$ope_pct = 1;

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$page = $this -> get_get_post('page');
		$page_size = $this -> get_get_post('page_size');
		$page_size = empty($page_size) ? 20 : $page_size;
		$page_size = $page_size <= 0 ? 20 : $page_size;

		if(empty($user_id) ) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$list = $this -> bank_book_dao -> list_all($user_id, $page, $page_size);
			$res['list'] = $list;
		}
		$this -> to_json($res);
	}

}
?>
