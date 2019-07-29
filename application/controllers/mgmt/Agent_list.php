<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agent_list extends MY_Mgmt_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Users_agent_dao', 'dao');
		$this -> load -> model('Images_dao', 'img_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');
		$this -> load -> model('Agent_tx_dao', 'agent_tx_dao');
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
		$l_user = $this -> dao -> find_by_id($data['login_user_id']);
		$data['login_user'] = $l_user;
		if($l_user -> agent_lv != 1) {
			$this->load->view('mgmt/agent_list/list_error', $data);
		} else {
			$this->load->view('mgmt/agent_list/list', $data);
		}
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
			's_account'
		));
		$s_data = $this -> setup_user_data(array());
		$login_user = $this -> dao -> find_by_id($s_data['login_user_id']);

		// if($login_user -> role_id != 99) {
		// 	$data['corp_id'] = $login_user -> corp_id;
		// }

		if($login_user -> role_id == 1 || $login_user -> role_id == 99) {

		} else {
			// $data['intro_id'] = $login_user -> id;
		}
		$data['parent_user_id'] = $login_user -> id;
		$data['lv2_query'] = "Y";
		$items = $this -> dao -> query_ajax($data);
		foreach($items as $each) {
			$samt = $this -> agent_tx_dao -> get_sum_amt_time_by_user(date("Y-m"), $each -> id);
			$each -> monthly_amt = $samt;
		}
		$res['items'] = $items;
		$res['sql'] = $this -> dao -> db -> last_query();
		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);
		$res['s_account'] = $data['s_account'];

		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();
		$data['id'] = $id;


		if(!empty($id)) {
			$item = $this -> dao -> find_by_id($id);
			$data['corp'] = $this -> corp_dao -> find_by_id($item -> corp_id);
			$data['item'] = $item;
			$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($item -> id);
		}

		$s_data = $this -> setup_user_data(array());
		$login_user = $this -> dao -> find_by_id($s_data['login_user_id']);
		$data['login_user'] = $login_user;


		$this->load->view('mgmt/agent_list/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'agent_lv',
			'agent_pct',
		));
		$agetn_pct = floatval($data['agent_pct']);

		if($agetn_pct < 0 || $agetn_pct > 15) {
			$res['error_msg'] = "需設定在1~15%之間";
		} else {
			$s_data = $this -> setup_user_data(array());
			if($data['agent_lv'] == 0) {
				// clear
				$this -> users_dao -> update(array(
					'agent_lv' => 0,
					'agent_pct' => $data['agent_pct'],
					'agent_parent_user_id' => 0,
				), $id);
			} else {
				$this -> users_dao -> update(array(
					'agent_lv' => 2,
					'agent_pct' => $data['agent_pct'],
					'agent_parent_user_id' => $s_data['login_user_id'],
				), $id);
			}

		}

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete($id);
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
