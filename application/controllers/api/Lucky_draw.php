<?php
class Lucky_draw extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		// setup models
		$this -> load -> model('Lucky_draw_record_dao', 'ld_record_dao');
		$this -> load -> model('Lucky_draw_tx_dao', 'ld_tx_dao');

		$this -> load -> model('Wallet_tx_bdc_dao', 'wtx_bdc_dao');
		$this -> load -> model('Wallet_tx_lucky_draw_dao', 'wtx_ld_dao');
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');

	}

	public function get_win_arr() {
		$win_arr = array(
			10,
			10,
			10,
			10,
			10,
			10, // 6
			20,
			20,
			20, // 3
			50, // 1
			100, // 1
			500, // 1
			1000, // 1
			10000 // 1
		);
		return $win_arr;
	}

	public function test() {
		echo "test";
	}

	public function sum_num() {
		$res = array();
		$res['success'] = TRUE;

		$user_id = $this -> get_post('user_id');

		if(empty($user_id)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$snum =  $this -> ld_tx_dao -> sum_num_by_user($user_id);
			$res['sum_num'] = $snum;
		}
		$this -> to_json($res);
	}

	public function do_use() {
		$res = array();
		$res['success'] = TRUE;

		$user_id = $this -> get_post('user_id');

		if(empty($user_id)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$snum =  $this -> ld_tx_dao -> sum_num_by_user($user_id);
			if($snum > 0) {
				$user = $this -> users_dao -> find_by_id($user_id);
				if(!empty($user)) {
					$pool_amt = $this -> wtx_ld_dao -> sum_all($user -> corp_id);
					$win_arr = $this -> get_win_arr();

					$p = rand(0, 99);
					$counter = 0;
					$win_amt = 0;
					do {
						$seed = rand(0, count($win_arr) - 1);
						// $res['seed'] = $seed;
						$win_amt = $win_arr[$seed];
					} while ($win_amt > $pool_amt && $counter++ < 5); // at most 5 times
					if($win_amt > $pool_amt) {
						// $res['o_win_amt'] = $win_amt;
						$win_amt = $win_arr[0];; // 彩池錢不夠
					}

					$i = array();
					$i['corp_id'] = $user -> corp_id;
					$i['user_id'] = $user -> id;

					$is_win = ($win_amt > 0 ? 1 : 0);
					$i['is_win'] = $is_win;
					$i['amt'] = $win_amt;
					$lucky_draw_record_id = $this -> ld_record_dao -> insert($i);

					// is win
					$res['is_win'] = $is_win;
					$res['win_amt'] = $win_amt;

					$tx = array();
					$tx['corp_id'] = $user -> corp_id;
					$tx['user_id'] = $user -> id;
					$tx['lucky_draw_record_id'] = $lucky_draw_record_id;
					$tx['num'] = -1;
					$tx['brief'] = "{$user->account} 使用一張摸彩卷";
					$lucky_draw_tx_id = $this -> ld_tx_dao -> insert($tx);

					if($win_amt > 0 ) {
						// 藍鑽派彩
						$tx = array();
						$tx['corp_id'] = $user -> corp_id;
						$tx['user_id'] = $user -> id;
						$tx['lucky_draw_tx_id'] = $lucky_draw_tx_id;
						$tx['type_id'] = 110; // 摸彩派彩
						$tx['amt'] = $win_amt;
						$tx['brief'] = "{$user->account} 使用摸彩卷派彩 {$win_amt}";
						$this -> wtx_bdc_dao -> insert($tx);

						// 彩池扣點
						$tx = array();
						$tx['corp_id'] = $user -> corp_id;
						$tx['lucky_draw_tx_id'] = $lucky_draw_tx_id;
						$tx['type_id'] = 100; // 摸彩派彩扣藍鑽
						$tx['amt'] = -$win_amt;
						$tx['brief'] = "{$user->account} 使用摸彩卷派彩，彩池扣 {$win_amt} 藍鑽";
						$this -> wtx_ld_dao -> insert($tx);
					}

					// $pool_amt = $this -> wtx_ld_dao -> sum_all($user -> corp_id);
					// $res['pool_amt'] = $pool_amt;
				} else {
					$res['error_msg'] = "查無使用者";
				}
			} else {
				$res['error_msg'] = "目前無摸彩卷";
			}
		}
		$this -> to_json($res);
	}

	public function list_winner() {
		$res = array();
		$res['success'] = TRUE;

		$corp_id = $this -> get_post('corp_id');
		if(empty($corp_id)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$list = $this -> ld_record_dao -> list_winner($corp_id);
			foreach($list as $each) {
				$each -> image_url = '';
				$each -> image_url_thumb = '';
				if(!empty($each -> image_id)) {
					$each -> image_url = IMG_URL . $each -> image_id;
					$each -> image_url_thumb = IMG_URL . $each -> image_id . '/thumb';
				}
			}
			$res['list'] = $list;
		}

		$this -> to_json($res);
	}
}
?>
