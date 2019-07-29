<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Buy_products_v3 extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Buy_records_v3_dao', 'dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Products_v3_dao', 'p_dao');
		$this -> load -> model('Pay_types_dao', 'pt_dao');

		$this -> load -> model('Params_dao', 'params_dao');
		$this -> load -> model('Users_dao', 'u_dao');
		$this -> load -> model('Role_share_dao', 'rs_dao');

		//載入SDK(路徑可依系統規劃自行調整)
		include APPPATH . 'third_party/ECPay.Payment.Integration.php';
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);

		$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($data['login_user_id']);
		$this->load->view('mgmt/buy_products_v3/list', $data);
	}

	public function shop()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$this->load->view('mgmt/buy_products_v3/shop', $data);
	}


	public function finish() {
		$this -> load -> view('mgmt/buy_products_v3/finish');
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
		}

		$data['product_list'] = $this -> p_dao -> find_all_online();

		$s_data = $this -> setup_user_data(array());
		$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($s_data['login_user_id']);

		$this->load->view('mgmt/buy_products_v3/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$s_data = $this -> setup_user_data(array());
		$data = $this -> get_posts(array(
			'product_id'
		));
		$data['user_id'] = $s_data['login_user_id'];

		if(empty($id)) {
			// insert
			$product = $this -> p_dao -> find_by_id($data['product_id']);
			$data['total_price'] =  $product -> price;
			$data['sn'] = 'B' . date('YmdHis');
			$data['status'] = 1;
			$data['corp_id'] = $s_data['corp'] -> id;
			$id = $this -> dao -> insert($data);

			// update user date
			// find user
			$l_user = $this -> u_dao -> find_by_id($s_data['login_user_id']);

			// buy tx
			$tx = array();
			$tx['buy_record_id'] = $id;
			$tx['corp_id'] = $s_data['corp'] -> id; // corp id
			$tx['user_id'] = $s_data['login_user_id'];
			$tx['amt'] = -($product -> price);
			$tx['type_id'] = 2; // 購買
			$tx['brief'] = "購買 $product->product_name, 花費 $product->price 點";
			$this -> wtx_dao -> insert($tx);

			// intro
			$tx = array();
			$tx['buy_record_id'] = $id;
			$tx['corp_id'] = $s_data['corp'] -> id; // corp id
			$tx['user_id'] = $l_user -> intro_id;

			$pc = $this -> rs_dao -> get_val(3); // 會員
			$amt = ($product -> price * floatval($pc) / 100.0);
			$tx['amt'] = $amt;
			$tx['type_id'] = 5; // 會員收入
			$tx['brief'] = "會員 $l_user->account 購買 $product->product_name, 利潤 $product->price x $pc% = $amt 點";
			$this -> wtx_dao -> insert($tx);

			// manager
			$tx = array();
			$tx['buy_record_id'] = $id;
			$tx['corp_id'] = $s_data['corp'] -> id; // corp id
			$tx['user_id'] = $l_user -> manager_id;

			$pc = $this -> rs_dao -> get_val(2); // 經理人
			$amt = ($product -> price * floatval($pc) / 100.0);
			$tx['amt'] = $amt;
			$tx['type_id'] = 6; // 經理人收入
			$tx['brief'] = "會員 $l_user->account 購買 $product->product_name, 利潤 $product->price x $pc% = $amt 點";
			$this -> wtx_dao -> insert($tx);

			// share holder
			$tx = array();
			$tx['buy_record_id'] = $id;
			$tx['corp_id'] = $s_data['corp'] -> id; // corp id
			$tx['user_id'] = $l_user -> shareholder_id;;

			$pc = $this -> rs_dao -> get_val(11); // 股東
			$amt = ($product -> price * floatval($pc) / 100.0);
			$tx['amt'] = $amt;
			$tx['type_id'] = 7; // 股東收入
			$tx['brief'] = "會員 $l_user->account 購買 $product->product_name, 利潤 $product->price x $pc% = $amt 點";
			$this -> wtx_dao -> insert($tx);

			// corp
			$tx = array();
			$tx['buy_record_id'] = $id;
			$tx['corp_id'] = $s_data['corp'] -> id; // corp id

			// corp admin
			$ca = $this -> u_dao -> find_corp_admin($l_user -> corp_id);
			$tx['user_id'] = $ca -> id;;

			$pc = $this -> rs_dao -> get_val(1); // 公司
			$amt = ($product -> price * floatval($pc) / 100.0);
			$tx['amt'] = $amt;
			$tx['type_id'] = 8; // 公司收入
			$tx['brief'] = "會員 $l_user->account 購買 $product->product_name, 利潤 $product->price x $pc% = $amt 點";
			$this -> wtx_dao -> insert($tx);

			// sys mgmt
			$tx = array();
			$tx['buy_record_id'] = $id;
			$tx['corp_id'] = 1; // sys corp id
			$sa = $this -> u_dao -> find_sys_admin();
			$tx['user_id'] = $sa -> id;

			$pc = $this -> rs_dao -> get_val(999); // 系統管銷
			$amt = ($product -> price * floatval($pc) / 100.0);
			$tx['amt'] = $amt;
			$tx['type_id'] = 9; // 系統管銷
			$tx['brief'] = "會員 $l_user->account 購買 $product->product_name, 利潤 $product->price x $pc% = $amt 點";
			$this -> wtx_dao -> insert($tx);

			// sys margin
			$tx = array();
			$tx['buy_record_id'] = $id;
			$tx['corp_id'] = 1; // sys corp id
			$sa = $this -> u_dao -> find_sys_admin();
			$tx['user_id'] = $sa -> id;

			$pc = $this -> rs_dao -> get_val(99); // 系統獲利
			$amt = ($product -> price * floatval($pc) / 100.0);
			$tx['amt'] = $amt;
			$tx['type_id'] = 10; // 系統獲利
			$tx['brief'] = "會員 $l_user->account 購買 $product->product_name, 利潤 $product->price x $pc% = $amt 點";
			$this -> wtx_dao -> insert($tx);
		}

		$res['success'] = TRUE;
		$res['last_id'] = $id;
 		$this -> to_json($res);
	}

	public function insert_and_pay() {
		$res = array();
		$id = $this -> get_post('id');
		$s_data = $this -> setup_user_data(array());
		$data = $this -> get_posts(array(
			'product_id',
			'pay_type_id'
		));
		$data['user_id'] = $s_data['login_user_id'];

		// insert
		$product = $this -> p_dao -> find_by_id($data['product_id']);
		$data['total_price'] =  $product -> price;
		$data['sn'] = 'B' . date('YmdHis');
		$id = $this -> dao -> insert($data);

		redirect(base_url("mgmt/buy_products_v3/pay/$id"));
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
		$this -> to_json($res);
	}

	public function check_account($id) {
		$account = $this -> get_post('account');
		$list = $this -> dao -> find_all_by('account', $account);
		$res = array();
		if(!empty($id)) {
			if (count($list) > 0) {
				$item = $list[0];
				if($item -> id == $id) {
					$res['valid'] = TRUE;
				} else {
					$res['valid'] = FALSE;
				}

				$res['item'] = $item;
			} else {
				$res['valid'] = TRUE;
			}
		} else {
			if (count($list) > 0) {
				$res['valid'] = FALSE;
			} else {
				$res['valid'] = TRUE;
			}
		}

		$this -> to_json($res);
	}

	public function chg_user() {
		$user_id = $this -> get_post('user_id');
		$this -> session -> set_userdata('user_id', $user_id);
		$res = array();

		$this -> to_json($res);
	}
}
