<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agent extends MY_Mgmt_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Users_dao', 'dao');
		$this -> load -> model('Agent_dao', 'agent_dao');
		$this -> load -> model('Agent_tx_dao', 'agent_tx_dao');
		$this -> load -> model('Images_dao', 'img_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Wallet_tx_btc_dao', 'wtx_btc_dao');
		$this -> load -> model('Wallet_tx_eth_dao', 'wtx_eth_dao');
		$this -> load -> model('Wallet_tx_ntd_dao', 'wtx_ntd_dao');
		$this -> load -> model('Wallet_tx_bdc_dao', 'wtx_bdc_dao');
		$this -> load -> model('Baccarat_tab_round_bet_dao', 'btrb_dao');
		$this -> load -> model('User_level_dao', 'ul_dao');
		$this -> load -> model('Banks_dao', 'banks_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['role_list'] = $this -> dao -> find_all_roles();
		$corp_list = $this -> corp_dao -> find_all();

		$l_user = $this -> users_dao -> find_by_id($data['login_user_id']);
		if($l_user -> role_id != 99 && $l_user -> role_id != 1) {
			$corp_list = array();
		  foreach($corp_list as $each) {
				if($each -> id == $l_user -> corp_id) {
					$corp_list[] = $each;
				}
 			}
		}
		$data['corp_list'] = $corp_list;
		$data['login_user'] = $this -> dao -> find_by_id($data['login_user_id']);
		$this->load->view('mgmt/agent/list', $data);
	}

	public function clear_wash() {
		$res = array();
		$res['success'] = TRUE;

		$user_id = $this -> get_post('user_id');
		$max_id = $this -> wtx_dao -> get_max_id($user_id);

		$this -> dao -> update(array('last_wallet_tx_id' => $max_id, 'withdraw_status' => 1), $user_id);



		$this -> to_json($res);
	}

	public function get_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'company_id',
			'corp_id',
			'agent_lv',
			'agent_type_id',
		));
		$s_data = $this -> setup_user_data(array());
		$login_user = $this -> dao -> find_by_id($s_data['login_user_id']);

		// if($login_user -> role_id != 99) {
		// 	$data['corp_id'] = $login_user -> corp_id;
		// }

		if($login_user -> role_id == 1 || $login_user -> role_id == 99) {

		} else {
			$data['intro_id'] = $login_user -> id;
		}

		$items = $this -> dao -> query_ajax($data);
		foreach($items as $each) {
			$samt = $this -> agent_tx_dao -> get_sum_amt_time_by_user(date("Y-m"), $each -> id);
			$each -> monthly_amt = $samt;
		}
		$res['items'] = $items;
		$res['sql'] = $this -> dao -> db -> last_query();
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
			if(!empty($item -> image_id)) {
				$item -> img = $this -> img_dao -> find_by_id($item -> image_id);
			}
			$data['corp'] = $this -> corp_dao -> find_by_id($item -> corp_id);
			$data['item'] = $item;
			$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($id);

			$data['agnet_child_list'] = $this -> users_dao -> find_all_user_by_agent_parent_user($item -> id);

			$samt = $this -> agent_tx_dao -> get_sum_amt_time_by_user(date("Y-m"), $item -> id);

			$from_year = 2019;
			$y_list = array();
			for($i = $from_year; $i <= intval(date("Y")); $i++) {
				$y_list[] = $i;
			}
			$data['y_list'] = $y_list;
			$data['monthly_amt'] = $samt;
		}

		$s_data = $this -> setup_user_data(array());
		$login_user = $this -> dao -> find_by_id($s_data['login_user_id']);
		$data['login_user'] = $login_user;

		if($login_user -> role_id == 99) {
			// all roles
			$data['role_list'] = $this -> dao -> find_all_roles();
		} else {
			$data['role_list'] = $this -> dao -> find_all_roles();
		}

		$data['user_levels'] = $this -> ul_dao -> find_all();

		$lang = $this -> session -> userdata('lang');
		$data['lang'] = $lang;



		$data['withdraw_thresh'] = $this -> wtx_dao -> get_sum_amt_wash($id);
		$data['rolling_thresh'] = $this -> btrb_dao -> find_total_rolling_by_user_id($id);

		$data['bank_list'] = $this -> banks_dao -> find_all_by_country(0);

		$this->load->view('mgmt/agent/edit', $data);
	}

	public function agent_tx($id) {
		$data = array();
		$data['id'] = $id;
		$data['detail_user_id'] = $id;


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
			if(!empty($item -> image_id)) {
				$item -> img = $this -> img_dao -> find_by_id($item -> image_id);
			}
			$data['corp'] = $this -> corp_dao -> find_by_id($item -> corp_id);
			$data['item'] = $item;
			$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($id);
		}

		$s_data = $this -> setup_user_data(array());
		$login_user = $this -> dao -> find_by_id($s_data['login_user_id']);
		$data['login_user'] = $login_user;
		$data['login_user_id'] = $login_user -> id;

		if($login_user -> role_id == 99) {
			// all roles
			$data['role_list'] = $this -> dao -> find_all_roles();
		} else {
			$data['role_list'] = $this -> dao -> find_all_roles();
		}

		$data['user_levels'] = $this -> ul_dao -> find_all();

		$lang = $this -> session -> userdata('lang');
		$data['lang'] = $lang;

		$data['withdraw_thresh'] = $this -> wtx_dao -> get_sum_amt_wash($id);
		$data['rolling_thresh'] = $this -> btrb_dao -> find_total_rolling_by_user_id($id);

		$data['bank_list'] = $this -> banks_dao -> find_all_by_country(0);

		$this->load->view('mgmt/agent/tx_list', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');

		$item = $this -> users_dao -> find_by_id($id);

		$data = array();
		if($item -> agent_type_id > 0) {
			$data = $this -> get_posts(array(
				'agent_bonus',
				'agent_win_loose_bonus',
			));
		} else {
			$data = $this -> get_posts(array(
				'agent_lv',
				'agent_type_id',
			));
		}

		$this -> users_dao -> update($data, $id);

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		//$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
		$this -> to_json($res);
	}

	public function y_list() {
		$res['success'] = TRUE;
		$year = $this -> get_post("year");
		$user_id = $this -> get_post("user_id");

		$list = $this -> agent_tx_dao -> year_list_by_user($year, $user_id);
		$res['list'] = $list;
		//$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
		$this -> to_json($res);
	}

	public function check_account($id, $corp_id) {
		$account = $this -> get_post('account');
		$item = $this -> dao -> find_by_corp_and_account($corp_id, $account);
		$res = array();
		if(!empty($id)) {
			if (!empty($item)) {
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
			if (!empty($item)) {
				$res['valid'] = FALSE;
			} else {
				$res['valid'] = TRUE;
			}
		}

		$this -> to_json($res);
	}

	public function check_code() {
		$code = $this -> get_post('intro_code');
		$list = $this -> dao -> find_all_by('code', $code);
		$res = array();
		$res['valid'] = (count($list) > 0);
		$this -> to_json($res);
	}

	public function chg_user() {
		$user_id = $this -> get_post('user_id');
		$this -> session -> set_userdata('user_id', $user_id);
		$res = array();

		$this -> to_json($res);
	}

	function export_all() {
			$this->load->dbutil();
      $this->load->helper('file');
      $this->load->helper('download');
      $delimiter = ",";
      $newline = "\r\n";
			$date = date('YmdHis');
      $filename = $date."-user.csv";

			//create a file pointer
    	$f = fopen('php://memory', 'w');
			$fields = array('帳號', '會員姓名', 'Email', 'LINE ID', 'DBC', 'BTC', 'ETH');
			fputcsv($f, $fields, $delimiter);

      $query = "SELECT id, account,
				user_name,
				email, line_id
      	FROM `users`
				WHERE status = 0 ";
      $result = $this->db->query($query) -> result();
			foreach($result as $each) {
				$lineData = array($each -> account, iconv("UTF-8","Big5//IGNORE",$each -> user_name), $each -> email, $each -> line_id);

				$lineData[] = $this -> wtx_dao -> get_sum_amt($each -> id);
				$lineData[] = $this -> wtx_btc_dao -> get_sum_amt($each -> id);
				$lineData[] = $this -> wtx_eth_dao -> get_sum_amt($each -> id);
				// $lineData[]= 0;
				// $lineData[]= 0;
				// $lineData[]= 0;
				// foreach($lineData as $aCol) {
				// 	$aCol = iconv("UTF-8","Big5//IGNORE",$aCol);
				// }

				fputcsv($f, $lineData, $delimiter);
			}
			//move back to beginning of file

    	fseek($f, 0);

			//set headers to download file rather than displayed
			 header('Content-Type: text/csv');
			 header('Content-Disposition: attachment; filename="' . $filename . '";');

			 //output all remaining data on a file pointer
			 fpassthru($f);
      // $data = $this->dbutil->csv_from_result($result, $delimiter, $newline);
      // force_download($filename,@iconv("UTF-8","Big5//IGNORE",$data));
	}
}
