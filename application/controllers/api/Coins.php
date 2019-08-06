<?php
class Coins extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Coins_dao', 'dao');
		$this -> load -> model('Coin_daily_dao', 'c_daily_dao');
		$this -> load -> model('Coin_pick_dao', 'c_pick_dao');

		$this -> load -> model('Corp_dao', 'corp_dao');
		$this -> load -> model('Transfer_coin_dao', 'tc_dao');
		$this -> load -> model('Transfer_gift_allocation_dao', 'tsga_dao');

	}

	public function do_update() {
		$res = array();
		$res['success'] = true;

		// updat btc
		$n_res = $this -> curl -> simple_get('http://www.whateverorigin.org/get?url=' . urlencode("https://www.maicoin.com/api/prices/btc-twd"));
		$cts = json_decode($n_res);
		$obj = json_decode($cts -> contents);
		$item = $this -> dao -> find_by_currency('btc');
		if($obj -> raw_price > 0) {
			$this -> dao -> update(array(
				'price_twd' => $obj -> raw_price / 100000,
				'buy_price_twd' => $obj -> raw_buy_price / 100000,
				'sell_price_twd' => $obj -> raw_sell_price / 100000
			), $item -> id);
		}

		// updat eth
		$n_res = $this -> curl -> simple_get('http://www.whateverorigin.org/get?url=' . urlencode("https://www.maicoin.com/api/prices/eth-twd"));
		$cts = json_decode($n_res);
		$obj = json_decode($cts -> contents);
		$item = $this -> dao -> find_by_currency('eth');
		if($obj -> raw_price > 0) {
			$this -> dao -> update(array(
				'price_twd' => $obj -> raw_price / 100000,
				'buy_price_twd' => $obj -> raw_buy_price / 100000,
				'sell_price_twd' => $obj -> raw_sell_price / 100000
			), $item -> id);
		}

		$corps = $this -> corp_dao -> find_all();
		foreach($corps as $corp) {
			// dbc
			// $corp = $this -> corp_dao -> find_by_id(1);

			// update today
			$today = date('Y-m-d');
			$today_dbc = $this -> c_daily_dao -> find_by_currency_and_date($corp -> corp_code, $today);
			if(empty($today_dbc)) {
				// create
				$this -> c_daily_dao -> insert(array(
					'currency' => $corp -> corp_code,
					'date' => $today,
					'price_twd' => $corp -> price_avg,
					'sell_price_twd' => $corp -> price_sell,
					'buy_price_twd' => $corp -> price_buy
				));
			} else {
				// update
				$this -> c_daily_dao -> update(array(
					'price_twd' => $corp -> price_avg,
					'sell_price_twd' => $corp -> price_sell,
					'buy_price_twd' => $corp -> price_buy
				), $today_dbc -> id);
			}

			// current dbc
			$current_dbc_base = $corp -> price_avg;
			$max_price = $corp -> price_avg * 1.05;
			$min_price = $corp -> price_avg * 0.95;

			$dbc = $this -> dao -> find_by_currency($corp -> corp_code);
			if(empty($dbc)) {
				$last_id = $this -> dao -> insert(array(
					'currency' => $corp -> corp_code,
					'currency_name' => $corp -> sys_name_cht,
					'price_twd' => $corp -> price_avg,
					'sell_price_twd' => $corp -> price_sell,
					'buy_price_twd' => $corp -> price_buy
				));
				$dbc = $this -> dao -> find_by_id($last_id);
			}

			$after = $dbc -> price_twd;

			$exceed = FALSE;
			$target_after = $current_dbc_base;
			if($after > 0) {
				if((floatval($current_dbc_base) / floatval($after)) > 1.1 || (floatval($current_dbc_base) / floatval($after)) < 0.9) {
					if((floatval($current_dbc_base) / floatval($after)) > 1.1) {
						$target_after = $after * 1.1;
						$exceed = TRUE;
					}

					if((floatval($current_dbc_base) / floatval($after)) < 0.9) {
						$target_after = $after * 0.9;
						$exceed = TRUE;
					}
				}
			}

			$cnt = 0;
			$max_loop = 10;

			if(!$exceed) {
				do {
					$rand = floatval(rand(0, 300) - 150) / 1000.0;
					$after = $dbc -> price_twd + $rand;
					echo '(' . $max_price . '- ' . $after . ' - ' . $min_price . ' )';
				} while ((($after > $max_price) || ($after < $min_price)) && $cnt++ < $max_loop);

				if($cnt >= $max_loop ) {
					// reset
					$after = $corp -> price_avg;
				}
			} else {
					// custom upgrade or downgrad
					$after = $target_after;
			}

			// insert tick
			$this -> c_pick_dao -> insert(array(
				'currency' => $corp -> corp_code,
				'dt' => date('Y-m-d H:i:s'),
				'price_twd' => $after,
				'sell_price_twd' => $after * 0.9,
				'buy_price_twd' => $after * 1.1,
			));

			$this -> dao -> update(array(
				'price_twd' => $after,
				'sell_price_twd' => $after * 0.9,
				'buy_price_twd' => $after * 1.1,
			), $dbc -> id);
		}

		$this -> to_json($res);
	}

	public function check_expired_transfer_in() {
		$res = array();
		$res['success'] = true;


		// check_expired_transfer_in
		$this -> tc_dao -> check_expired_transfer_in();

		$this -> to_json($res);
	}

	public function doTest() {
		$list = $this -> tsga_dao -> find_list_limit(array('user_id'=> 525 ,'start' => 0,'length'=> 10));
		$res['list'] = $list;
		$this -> to_json($res);
	}

	public function test() {

		$res = "hello..";

		echo $res;
	}
}
?>
