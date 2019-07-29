<?php
class Bd_buy_record extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Bd_buy_record_dao', 'bd_buy_record_dao');
		$this -> load -> model('Wallet_tx_bdc_dao', 'wtx_bdc_dao');
	}

	public function do_buy() {
		$res = array();
		$res['success'] = TRUE;

		$hash_key = 'bdhashkey';

		$user_id = $this -> get_post('user_id');
		$sn = $this -> get_post('sn');
		$product_id = $this -> get_post('product_id');
		$cash = $this -> get_post('cash');
		$amt = $this -> get_post('amt');
		$check = $this -> get_post('check');

		if(empty($user_id) || empty($sn) || empty($product_id) || empty($cash) || empty($amt) || empty($check)) {
			$res['error_msg'] = "缺少必填欄位";
		} else {
			$user = $this -> users_dao -> find_by_id($user_id);
			if(empty($user)) {
				$res['error_msg'] = "查無使用者";
			} else {
				$m_check = md5("{$sn}{$amt}{$hash_key}");

				$bbr = $this -> bd_buy_record_dao -> find_by('sn', $sn);
				if(empty($bbr)) {
					// create
					$last_id = $this -> bd_buy_record_dao -> insert(array(
						'corp_id' => $user -> corp_id,
						'user_id' => $user -> id,
						'sn' => $sn,
						'cash' => $cash,
						'amt' => $amt,
						'check' => $m_check
					));
					$bbr = $this -> bd_buy_record_dao -> find_by_id($last_id);
				}

				if($bbr -> is_ok == 0) {
					// 未付
					if($m_check == $check) {
						$tx = array();
						$tx['corp_id'] = $user -> corp_id; // corp id
						$tx['user_id'] = $user -> id;

						// $amt = 100;
						$tx['amt'] = $amt;
						$tx['type_id'] = 150;
						$tx['brief'] = "會員 $user->account 購買藍鑽 $amt ";
						$this -> wtx_bdc_dao -> insert($tx);

						$this -> bd_buy_record_dao -> update(array(
							'is_ok' => 1
						), $bbr -> id);

					} else {
						$res['error_msg'] = "確認碼錯誤";
					}
				} else {
					$res['error_msg'] = "已兌換過了";
				}

				$res['check'] = $m_check;
			}
		}
		$this -> to_json($res);
	}

}
?>
