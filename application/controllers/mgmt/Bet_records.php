<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Bet_records extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Bet_records_dao', 'dao');
		$this -> load -> model('Bet_record_details_dao', 'brd_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Products_v3_dao', 'p_dao');
		$this -> load -> model('Pay_types_dao', 'pt_dao');

		$this -> load -> model('Buy_records_v4_dao', 'br_v4_dao');

		$this -> load -> model('Params_dao', 'params_dao');
		$this -> load -> model('Users_dao', 'u_dao');
		$this -> load -> model('Role_share_dao', 'rs_dao');

		$this -> load -> model('Config_dao', 'config_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);

		$this->load->view('mgmt/bet_records/list', $data);
	}

	public function get_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'user_id'
		));

		$res['items'] = $this -> dao -> query_ajax($data);
		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();
		$data['id'] = $id;
		if(!empty($id)) {
			$q_data = $this -> get_posts(array(
				'length',
				'start',
				'columns',
				'search',
				'order'
			));
			$q_data['id'] = $id;
			$list = $this -> dao -> query_ajax($q_data);
			$item = $list[0];

			$data['item'] = $item;
			$data['brd_list'] = $this -> br_v4_dao -> find_all_by_bet_record($id);
		}

		$data['product_list'] = $this -> p_dao -> find_all_online();

		$s_data = $this -> setup_user_data(array());

		$data['current_sum'] = $this -> br_v4_dao -> sum_actual_amt($id);

		$this->load->view('mgmt/bet_records/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$s_data = $this -> setup_user_data(array());
		// $data = $this -> get_posts(array(
		// 	'amt'
		// ));

		if(empty($id)) {
			// insert
			$data['sn'] = 'BET' . date('YmdHis');
			$data['price'] = $this -> config_dao -> get_val_by_corp('bet_record_price', $s_data['corp'] -> id);
			$id = $this -> dao -> insert($data);


			// $b_list = $this -> br_v3_dao -> find_valid_records($data['product_id']);
			// $average_amt = intval($data['amt'] / count($b_list));
			// foreach($b_list as $each) {
			// 	$i_data = array();
			// 	$i_data['pool_record_id'] = $id;
			// 	$i_data['user_id'] = $each -> user_id;
			// 	$i_data['amt'] = $average_amt;
			// 	$this -> prd_dao -> insert($i_data);
			//
			// 	// // buy tx
			// 	$tx = array();
			// 	$tx['pool_record_id'] = $id;
			// 	$tx['corp_id'] = $s_data['corp'] -> id; // corp id
			// 	$tx['user_id'] = $each -> user_id;
			// 	$tx['amt'] = ($average_amt);
			// 	$tx['type_id'] = 15; // 彩池發放
			//
			// 	$product = $this -> p_dao -> find_by_id($data['product_id']);
			// 	$tx['brief'] = "彩池獎金 $product->product_name $average_amt 點";
			// 	$this -> wtx_dao -> insert($tx);
			// }
		}

		$res['success'] = TRUE;
		$res['last_id'] = $id;
 		$this -> to_json($res);
	}

	public function do_open() {
		$res['success'] = TRUE;
		$id = $this -> get_post('id');
		$s_data = $this -> setup_user_data(array());

		$current_sum = $this -> br_v4_dao -> sum_actual_amt($id);
		$buy_list = $this -> br_v4_dao -> find_all_by_bet_record($id);

		$item = $this -> dao -> find_by_id($id);
		$min = intval($item -> price * 0.18);
		$max = intval($item -> price * 4.15);
		$a_arr = array();
		$cnt = 0;
		// reset
		foreach($buy_list as $each) {
			$this -> br_v4_dao -> update(array(
				'reward_amt' => 0
			), $each -> id);
		}

		// dispatch
		while($current_sum > 0 && $cnt < 100) {
				foreach($buy_list as $each) {
					$each = $this -> br_v4_dao -> find_by_id($each -> id);
					$plus = rand(1, $current_sum);

					$ra = $each -> reward_amt + $plus;
					if(($ra < $min)) {
						$ra = $min;
						$plus = $ra - $each -> reward_amt;
					}
					if(($ra > $max)) {
						$ra = $max;
						$plus = $ra - $each -> reward_amt;
					}
					if(($current_sum - $plus) < 0) {
						$plus = $current_sum;
					}
					$current_sum -= $plus;
					$this -> br_v4_dao -> update(array(
						'reward_amt' => $ra
					), $each -> id);
				}
				$cnt++;
		}

		// tx
		// reset
		foreach($buy_list as $each) {
			$each = $this -> br_v4_dao -> find_by_id($each -> id);

			$tx = array();
			$tx['bet_record_id'] = $id;
			$tx['corp_id'] = $s_data['corp'] -> id; // corp id
			$tx['user_id'] = $each -> user_id;
			$tx['amt'] = ($each -> reward_amt);
			$tx['type_id'] = 16; // 每期獎金

			$tx['brief'] = "第 $item->sn 期獎金 $each->reward_amt 點";
			$this -> wtx_dao -> insert($tx);
		}
		$this -> dao -> update(array('is_open' =>  1), $id);
		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
		$this -> to_json($res);
	}
}
