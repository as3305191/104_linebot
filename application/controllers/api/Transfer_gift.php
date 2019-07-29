<?php
class Transfer_gift extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		// setup models
		$this -> load -> model('Transfer_gift_dao', 'tsg_dao');
		$this -> load -> model('Transfer_gift_friends_dao', 'tsgf_dao');

		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Wallet_tx_bdc_dao', 'wtx_bdc_dao');
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');
		$this -> load -> model('Com_tx_dao', 'ctx_dao');

		$this -> load -> model('Config_dao', 'config_dao');
	}

	public function test() {
		echo "test";
	}

	public function list_all() {
		$res = array();
		$res['success'] = TRUE;

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];
		$page = $this -> get_get_post('page');
		$page_size = $this -> get_get_post('page_size');
		$page_size = empty($page_size) ? 20 : $page_size;
		$page_size = $page_size <= 0 ? 20 : $page_size;

		if(empty($user_id) ) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$list = $this -> tsg_dao -> list_all($user_id, $page, $page_size);
			foreach($list as $each) {
				unset($each -> gift_code);
				$diff = time() - strtotime($each -> create_time);
				$each -> diff = $diff;
				$each -> is_expired = (((60 * 60 * 72 - $diff) < 0) && $each -> status < 2) ? 1 : 0;
			}
			$res['list'] = $list;
			$res['user_id'] = $user_id;
			$res['status_list'] = $this -> tsg_dao -> find_all_status();
		}
		$this -> to_json($res);
	}

	public function agree_or_not() {
		$res = array();
		$res['success'] = TRUE;

		$transfer_gift_id = $this -> get_post('transfer_gift_id');
		$is_agree = $this -> get_post('is_agree');

		if(empty($transfer_gift_id)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$item = $this -> tsg_dao -> find_by_id($transfer_gift_id);
			if($item -> status == 0) {
				if($is_agree == 1) {
					// 同意 -> 變更狀態
					$this -> tsg_dao -> update(array(
						'status' => 1
					), $item -> id);

					// 傳送簡訊
					$corp = $this -> corp_dao -> find_by_id($item -> corp_id);
					$out_user = $this -> users_dao -> find_by_id($item -> out_user_id);
					$code = $this -> get_reg_code();

					$this -> tsg_dao -> update(array(
						'gift_code' => $code
					), $item -> id);

					$p = array();
					$p['to'] = $out_user -> line_sub;
					$p['messages'][] = array(
						"type" => "text",
						"text" => "贈禮單號 $item->sn 您的確認密碼為 $code"
					);
					$ret = call_line_api("POST", "https://api.line.me/v2/bot/message/push", json_encode($p), CHANNEL_ACCESS_TOKEN);

					$res['code'] = $code;

				} else {
					// 不同意
					// 變更狀態'
					if($item -> status == 0) {
						$this -> tsg_dao -> update(array(
							'status' => -1 // 接收者取消
						), $item -> id);

						// 退回預扣數量
						// 㽪禮扣點
						$tx = array();
						$tx['tx_id'] = $item -> id;
						$tx['tx_type'] = "gift_transfer_reject"; // 收取贈禮
						$tx['corp_id'] = $item -> corp_id; // corp id
						$tx['user_id'] = $item -> out_user_id;
						$tx['amt'] = ($item -> transfer_amt);

						$tx['brief'] = "$item->sn 返回金幣 $item->transfer_amt ";
						$this -> wtx_dao -> insert($tx);
					} else {
						$res['error_msg'] = "狀態錯誤";
					}

				}
			} else {
				$res['error_msg'] = "狀態錯誤";
			}
		}
		$this -> to_json($res);
	}

	public function deny() {
		$res = array();
		$res['success'] = TRUE;

		$transfer_gift_id = $this -> get_post('transfer_gift_id');

		if(empty($transfer_gift_id)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$item = $this -> tsg_dao -> find_by_id($transfer_gift_id);
			if($item -> status < 2) { // 未完成前都可以取消
				$this -> tsg_dao -> update(array(
					'status' => -2 // 發送者取消
				), $item -> id);

				// 退回預扣數量
				// 㽪禮扣點
				$tx = array();
				$tx['tx_id'] = $item -> id;
				$tx['tx_type'] = "gift_transfer_reject_by_out_user"; // 收取贈禮
				$tx['corp_id'] = $item -> corp_id; // corp id
				$tx['user_id'] = $item -> out_user_id;
				$tx['amt'] = ($item -> transfer_amt);

				$tx['brief'] = "$item->sn 取消返回金幣 $item->transfer_amt ";
				$this -> wtx_dao -> insert($tx);
			} else {
				$res['error_msg'] = "狀態錯誤";
			}
		}
		$this -> to_json($res);
	}

	public function confirm_code() {
		$res = array();
		$res['success'] = TRUE;

		$transfer_gift_id = $this -> get_post('transfer_gift_id');
		$code = $this -> get_post('code');

		if(empty($transfer_gift_id)||empty($code)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$item = $this -> tsg_dao -> find_by_id($transfer_gift_id);
			if($item -> status == 1) {
				if($item -> gift_code == $code) {
					// 正確 -> 變更狀態
					$this -> tsg_dao -> update(array(
						'status' => 2
					), $item -> id);

					// 確認匯款
					$tx = array();
					$tx['corp_id'] = $item -> corp_id; // corp id
					$tx['user_id'] = $item -> in_user_id;
					$tx['amt'] = ($item -> amt);

					$tx['tx_type'] = "gift_transfer_receive"; // 收取贈禮
					$tx['tx_id'] = $item -> id; // 收取贈禮
					$tx['brief'] = "$item->sn 收取 $item->amt ";
					$this -> wtx_dao -> insert($tx);
				} else {
					$res['error_msg'] = "驗證碼錯誤";
				}
			} else {
				$res['error_msg'] = "狀態錯誤";
			}
		}
		$this -> to_json($res);
	}

	public function do_transfer() {
		$res = array();
		$res['success'] = TRUE;

		$corp = $this -> corp_dao -> find_by_id(1);
		$ope_pct = $corp -> transfer_gift_pct; // 1%
		// $ope_pct = 0;

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];
		$gift_id = $this -> get_post('gift_id');
		$amt = $this -> get_post('amt');
		$is_save = $this -> get_post('is_save');

		if(empty($user_id) || empty($amt) || empty($gift_id)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$out_user = $this -> users_dao -> find_by_id($user_id);
			$in_user = $this -> users_dao -> find_by("gift_id", $gift_id);

			if(!empty($in_user)) {
				$samt =  $this -> wtx_dao -> get_sum_amt($out_user -> id);
				$ope_amt = floatval($amt) * floatval($ope_pct) / 100.0;
				$ope_amt1 = floatval(floatval($amt) * floatval($ope_pct) / 100.0)/4.0;//0.25%歸屬介紹人向上分配  

				$transfer_amt = $amt + $ope_amt;
				if($transfer_amt > $samt) {
					$res['error_msg'] = "餘額不足";
					$res['samt'] = $samt;
					$res['transfer_amt'] = $transfer_amt;
				} else {
					$i = array();
					$i['corp_id'] = $in_user -> corp_id;
					$i['amt'] = $amt;
					$i['out_user_id'] = $out_user -> id;
					$i['in_user_id'] = $in_user -> id;
					$i['ope_pct'] = $ope_pct;
					$i['ope_amt'] = $ope_amt;
					$last_id = $this -> tsg_dao ->  insert($i);
					$item = $this -> tsg_dao -> find_by_id($last_id);

					$u = array();
					$u['transfer_amt'] = $transfer_amt;
					$u['sn'] = "TG" . date("YmdHi") . $last_id;

					$code = $this -> get_reg_code();
					$u['gift_code'] = $code;
					$this -> tsg_dao -> update($u, $last_id);

					$res['last_id'] = $last_id;
					// $res['code'] = $code;

					// 㽪禮扣點
					$tx = array();
					$tx['tx_type'] = "gift_transfer";
					$tx['tx_id'] = $last_id;
					$tx['corp_id'] = $item -> corp_id; // corp id
					$tx['user_id'] = $item -> out_user_id;
					$tx['amt'] = -($transfer_amt);

					$tx['brief'] = "$out_user->nick_name 贈禮給 $in_user->nick_name - {$item->amt} 扣點 {$transfer_amt} 手續費 {$ope_amt1}";
					$this -> wtx_dao -> insert($tx);

					$tx = array();
					$tx['corp_id'] = $out_user -> corp_id;
					$tx['amt'] = $ope_amt;
					$tx['income_type'] = "transfer_gift_ope_amt";
					$tx['income_id'] = $last_id;
					$tx['note'] = "贈禮手續費 {$ope_amt1}";
					$this -> ctx_dao -> insert($tx);

					// 確認匯款
					// $tx = array();
					// $tx['transfer_gift_id'] = $last_id;
					// $tx['corp_id'] = $item -> corp_id; // corp id
					// $tx['user_id'] = $item -> in_user_id;
					// $tx['amt'] = ($item -> amt);
					//
					// $tx['type_id'] = 92; // 收取贈禮
					// $tx['brief'] = "$item->sn 收取 $item->amt ";
					// $this -> wtx_dao -> insert($tx);
				}
			} else {
				$res['error_msg'] = "查無收禮者";
			}
		}
		$this -> to_json($res);
	}

	public function list_friends() {
		$res = array();
		$res['success'] = TRUE;

		$user_id = $this -> get_post('user_id');

		if(empty($user_id) ) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$list = $this -> tsgf_dao -> list_all($user_id);
			$res['list'] = $list;
		}
		$this -> to_json($res);
	}

	public function remove_friend() {
		$res = array();
		$res['success'] = TRUE;

		$tgf_id = $this -> get_post('tgf_id');

		if(empty($tgf_id) ) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$this -> tsgf_dao -> delete($tgf_id);
		}
		$this -> to_json($res);
	}

	private function get_reg_code() {
		$digits = 4;
		return rand(pow(10, $digits - 1), pow(10, $digits) - 1);
	}
}
?>
