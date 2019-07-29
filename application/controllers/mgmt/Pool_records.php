<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Pool_records extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Pool_records_dao', 'dao');
		$this -> load -> model('Pool_record_details_dao', 'prd_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Products_v3_dao', 'p_dao');
		$this -> load -> model('Pay_types_dao', 'pt_dao');

		$this -> load -> model('Buy_records_v3_dao', 'br_v3_dao');

		$this -> load -> model('Params_dao', 'params_dao');
		$this -> load -> model('Users_dao', 'u_dao');
		$this -> load -> model('Role_share_dao', 'rs_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);

		$this->load->view('mgmt/pool_records/list', $data);
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
			$data['prd_list'] = $this -> prd_dao -> find_all_by_pool_record($item->id);
		}

		$data['product_list'] = $this -> p_dao -> find_all_online();



		$s_data = $this -> setup_user_data(array());

		$this->load->view('mgmt/pool_records/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$s_data = $this -> setup_user_data(array());
		$data = $this -> get_posts(array(
			'product_id',
			'amt'
		));

		if(empty($id)) {
			// insert
			$id = $this -> dao -> insert($data);


			$b_list = $this -> br_v3_dao -> find_valid_records($data['product_id']);
			$average_amt = intval($data['amt'] / count($b_list));
			foreach($b_list as $each) {
				$i_data = array();
				$i_data['pool_record_id'] = $id;
				$i_data['user_id'] = $each -> user_id;
				$i_data['amt'] = $average_amt;
				$this -> prd_dao -> insert($i_data);

				// // buy tx
				$tx = array();
				$tx['pool_record_id'] = $id;
				$tx['corp_id'] = $s_data['corp'] -> id; // corp id
				$tx['user_id'] = $each -> user_id;
				$tx['amt'] = ($average_amt);
				$tx['type_id'] = 15; // 彩池發放

				$product = $this -> p_dao -> find_by_id($data['product_id']);
				$tx['brief'] = "彩池獎金 $product->product_name $average_amt 點";
				$this -> wtx_dao -> insert($tx);
			}
		}

		$res['success'] = TRUE;
		$res['last_id'] = $id;
 		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
		$this -> to_json($res);
	}
}
