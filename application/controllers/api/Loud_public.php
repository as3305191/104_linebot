<?php
class Loud_public extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		// setup models
		$this -> load -> model('Lp_buy_records_dao', 'lp_buy_records_dao');
		$this -> load -> model('Lp_tx_dao', 'lp_tx_dao');

		$this -> load -> model('Wallet_tx_bdc_dao', 'wtx_bdc_dao');
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');
	}
	public function test() {
		echo "test";
	}

	public function do_buy() {
		$res = array();
		$res['success'] = TRUE;
		$ope_pct = 1;

		$corp_id = $this -> get_post('corp_id');
		$user_id = $this -> get_post('user_id');
		$amt = $this -> get_post('amt');
		$buy_type = $this -> get_post('buy_type');
		$num = $this -> get_post('num');

		if(empty($corp_id) || empty($user_id) || empty($amt) || !isset($buy_type) || !isset($num)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$samt =  $this -> wtx_bdc_dao -> get_sum_amt($user_id);
			if($amt > $samt) {
				$res['error_msg'] = "餘額不足";
				$res['samt'] = $samt;
			} else {
				$i = array();
				$i['corp_id'] = $corp_id;
				$i['user_id'] = $user_id;
				$i['amt'] = $amt;
				$i['buy_type'] = $buy_type;
				$i['num'] = $num;
				$last_id = $this -> lp_buy_records_dao ->  insert($i);
				$item = $this -> lp_buy_records_dao -> find_by_id($last_id);

				$u = array();
				$u['sn'] = "LP" . date("YmdHi") . $last_id;
				$this -> lp_buy_records_dao -> update($u, $last_id);

				// 㽪禮扣點
				$tx = array();
				$tx['lp_buy_record_id'] = $last_id;
				$tx['corp_id'] = $item -> corp_id; // corp id
				$tx['user_id'] = $item -> user_id;
				$tx['amt'] = -($item -> amt);

				$tx['type_id'] = 100; // 大聲公扣點
				$tx['brief'] = "購買大聲公扣 $item->amt 藍鑽";
				$this -> wtx_bdc_dao -> insert($tx);

				if($buy_type == 1) {
					// 包月 -> 增加包月due
					$user = $this -> users_dao -> find_by_id($user_id);
					$due_date = date('Y-m-d');
					if(empty($user -> lp_due_date)) {
						// 直接設定30天後
					} else {
						/// 日期加上30天
						$due_date = $user -> lp_due_date;
					}
					$due_date = date('Y-m-d',strtotime('+30 days',strtotime($due_date))); // plus 30 days
					// update record
					$u = array();
					$u['due_date'] = $due_date;
					$this -> lp_buy_records_dao -> update($u, $last_id);
					$res['due_date'] = $due_date;

					// update user
					$u = array();
					$u['lp_due_date'] = $due_date;
					$this -> users_dao -> update($u, $user_id);
				} else {
					// 增加次數
					$i = array();
					$i['corp_id'] = $corp_id;
					$i['user_id'] = $user_id;
					$i['lp_buy_record_id'] = $last_id;
					$i['num'] = $num;
					$i['brief'] = "購買大聲公花費藍鑽 $amt 次數 $num";
					$last_id = $this -> lp_tx_dao ->  insert($i);
					$res['last_id'] = $last_id;
				}
			}
		}
		$this -> to_json($res);
	}

	public function show_status() {
		$res = array();
		$res['success'] = TRUE;
		$ope_pct = 1;

		$user_id = $this -> get_post('user_id');

		if(empty($user_id)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$user = $this -> users_dao -> find_by_id($user_id);
			$corp = $this -> corp_dao -> find_by_id($user -> corp_id);
			if($user -> lp_always_on == 1) {
				$res['lp_always_on'] = 1;
				$res['lp_on'] = TRUE;
			} else if($corp -> is_lp_count == 0) {
				$res['is_lp_count'] = 0;
				$res['lp_on'] = TRUE;
			} else {
				if(!empty($user -> lp_due_date) && (strtotime(date('Y-m-d')) <= strtotime($user -> lp_due_date))) {
					// show due date
					$res['due_date'] = $user -> lp_due_date;
					$res['lp_on'] = TRUE;
				} else {
					$snum = $this -> lp_tx_dao -> sum_num_by_user($user_id);
					if($snum > 0) {
						$res['num'] = $snum;
						$res['lp_on'] = TRUE;
					} else {
						$res['num'] = $snum;
						$res['lp_on'] = FALSE;
					}
				}
			}
		}
		$this -> to_json($res);
	}
}
?>
