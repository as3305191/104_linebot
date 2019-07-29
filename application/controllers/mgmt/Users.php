<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Mgmt_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Users_dao', 'dao');
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
		$this -> load -> model('Products_items_dao', 'products_items_dao');
		$this -> load -> model('Lottery_tx_dao', 'lottery_tx_dao');
		$this -> load -> model('Transfer_gift_dao', 'transfer_gift_dao');
		$this -> load -> model('Fish_tab_lottery_dao', 'f_t_l_dao');
		$this -> load -> model('Product_strengthen_dao', 'product_strengthen_dao');
		$this -> load -> model('Products_dao', 'product_dao');
		$this -> load -> model('Transaction_record_coins_dao', 't_r_c_dao');
		$this -> load -> model('Product_items_dao', 'product_items_dao');
		$this -> load -> model('Ranking_dao', 'ranking_dao');
		$this -> load -> model('Transaction_record_props_dao', 'Trp_dao');
		$this -> load -> model('Chat_friends_dao', 'friends_dao');
		$this -> load -> model('Chat_message_dao', 'c_m_dao');
		$this -> load -> model('Fish_bet_dao', 'fish_bet_dao');
		$this -> load -> model('Product_strengthen_record_dao', 'product_s_r_dao');
		$this -> load -> model('User_log_dao', 'user_log_dao');

	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['role_list'] = $this -> dao -> find_all_roles();
		$data['login_user'] = $this -> dao -> find_by_id($data['login_user_id']);
		// $this -> to_json($data);

		$this->load->view('mgmt/users/list', $data);
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
			'role_id',
			'is_valid_mobile'
		));
		$s_data = $this -> setup_user_data(array());
		$login_user = $this -> dao -> find_by_id($s_data['login_user_id']);

		if($login_user -> role_id != 99) {
			$data['corp_id'] = $login_user -> corp_id;
		}

		if($login_user -> role_id == 1 || $login_user -> role_id == 99) {

		} else {
			$data['intro_id'] = $login_user -> id;
		}

		$res['items'] = $this -> dao -> query_ajax($data);
		$res['sql'] = $this -> dao -> db -> last_query();
		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$this -> to_json($res);
	}

	public function get_data_lottery() {
		$res = array();

		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'user_id',
			'tab_id',
			'tab_id',
			'lottery_no'
		));

		$res['items'] = $this -> lottery_tx_dao -> find_user_lottery($data);
		$res['recordsFiltered'] = $this -> lottery_tx_dao -> find_user_lottery($data, TRUE);
		$res['recordsTotal'] = $this -> lottery_tx_dao -> find_user_lottery($data, TRUE);

		$this -> to_json($res);
	}

	public function get_data_store() {
		$res = array();

		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'user_id',
			'tab_id',
			'tab_id',
			'lottery_no'
		));

		$res['items'] = $this -> Trp_dao -> find_user_store($data);
		$res['recordsFiltered'] = $this -> Trp_dao -> find_user_store($data, TRUE);
		$res['recordsTotal'] = $this -> Trp_dao -> find_user_store($data, TRUE);

		$this -> to_json($res);
	}

	public function get_data_buy() {
		$res = array();

		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'user_id',
			'tab_id',
			'tab_id',
			'lottery_no'
		));

		$res['items'] = $this -> Trp_dao -> find_user_buy($data);
		$res['recordsFiltered'] = $this -> Trp_dao -> find_user_buy($data, TRUE);
		$res['recordsTotal'] = $this -> Trp_dao -> find_user_buy($data, TRUE);

		$this -> to_json($res);
	}

	public function get_data_gift() {
		$res = array();

		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'user_id',
			'tab_id',
			'gift_select'
		));


		$items = $this -> transfer_gift_dao -> find_user_gift($data);
		$res['recordsFiltered'] = $this -> transfer_gift_dao -> find_user_gift($data, TRUE);
		$res['recordsTotal'] = $this -> transfer_gift_dao -> find_user_gift($data, TRUE);
		$res['items'] = $items;
		$this -> to_json($res);
	}

	public function get_data_friends() {
		$res = array();

		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'user_id',
			'tab_id'
		));


		$items = $this -> friends_dao -> find_user_friends($data);
		$res['recordsFiltered'] = $this -> friends_dao -> find_user_friends($data, TRUE);
		$res['recordsTotal'] = $this -> friends_dao -> find_user_friends($data, TRUE);
		$res['items'] = $items;
		$this -> to_json($res);
	}

	public function level_record_list() {
		$res = array();

		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'user_id',
			'tab_id',
			// 'gift_select'
		));


		$items = $this -> product_s_r_dao -> find_user_level_r($data);
		$res['recordsFiltered'] = $this -> product_s_r_dao -> find_user_level_r($data, TRUE);
		$res['recordsTotal'] = $this -> product_s_r_dao -> find_user_level_r($data, TRUE);
		$res['items'] = $items;
		$this -> to_json($res);
	}

	public function get_data_user_login() {
		$res = array();

		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'user_id',
			'tab_id',
			// 'gift_select'
		));


		$items = $this -> user_log_dao -> find_user_log($data);
		$res['recordsFiltered'] = $this -> user_log_dao -> find_user_log($data, TRUE);
		$res['recordsTotal'] = $this -> user_log_dao -> find_user_log($data, TRUE);
		$res['items'] = $items;
		$this -> to_json($res);
	}

	public function get_recharge_record() {
		$res = array();

		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'user_id',
			'tab_id',
			// 'gift_select'
		));


		$items = $this -> t_r_c_dao -> find_user_coins($data);
		$res['recordsFiltered'] = $this -> t_r_c_dao -> find_user_coins($data, TRUE);
		$res['recordsTotal'] = $this -> t_r_c_dao -> find_user_coins($data, TRUE);
		$res['items'] = $items;
		$this -> to_json($res);
	}

	public function get_talk_record() {
		$res = array();

		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'user_id',
			'tab_id',
			// 'gift_select'
		));


		$items = $this -> c_m_dao -> find_user_talks($data);
		$res['recordsFiltered'] = $this -> c_m_dao -> find_user_talks($data, TRUE);
		$res['recordsTotal'] = $this -> c_m_dao -> find_user_talks($data, TRUE);
		$res['items'] = $items;
		$this -> to_json($res);
	}

	public function get_catch_fish_record() {
		$res = array();

		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'user_id',
			'tab_id',
			// 'gift_select'
		));


		$items = $this -> fish_bet_dao -> find_user_fish($data);
		$res['recordsFiltered'] = $this -> fish_bet_dao -> find_user_fish($data, TRUE);
		$res['recordsTotal'] = $this -> fish_bet_dao -> find_user_fish($data, TRUE);
		$res['items'] = $items;
		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();
		$data['id'] = $id;
		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];
		$fish_tab_lottery_id = $this -> get_post('fish_tab_lottery_id');

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

		if($login_user -> role_id == 99) {
			// all roles
			$data['role_list'] = $this -> dao -> find_all_roles();
		} else {
			$data['role_list'] = $this -> dao -> find_all_roles();
		}
		$produsts = $this -> product_dao -> find_all_list();
		$data['produsts'] = $produsts;

		$data['user_levels'] = $this -> ul_dao -> find_all();

		$lang = $this -> session -> userdata('lang');
		$data['lang'] = $lang;

		// $article = $this -> products_items_dao -> find_all_article($id,1);


		// $mList[]= $article;
		$mList = array();
		for($i=1;$i<16;$i++){
			$article = $this -> products_items_dao -> find_all_article($id,$i);
			if(empty($article)){
				$article =array('total'=>0,'product_id'=>$i);
				$mList[]= $article;
			}else{
				$mList[]= $article[0];
			}
		}
		$data['article'] = $mList;
		$material = $this -> products_items_dao -> find_all_article($id,23);
		$data['material'] = $material ;


		$data['bank_list'] = $this -> banks_dao -> find_all_by_country(0);

		$list_tab = $this -> f_t_l_dao -> find_by_user($id);
		$data['list_tab'] = $list_tab;

		$mList1 = array();
		for($j=1;$j<13;$j++){
			$p_level = $this -> product_strengthen_dao -> find_level($id,$j);
			if(empty($p_level)){
				$p_level =array('level'=>"0",'product_id'=>$j);
				$mList1[]= $p_level;
			}else{
				$mList1[]= $p_level[0];
			}
		}
		$data['p_level'] = $mList1 ;


		$items = $this -> ranking_dao -> group_by_parameter(array(
			'date' => date("Y-m-d"),
			'user_id' => $id,
		));
		$this -> fb -> log($this -> ranking_dao -> db -> last_query());
		$score = 0;
		if(!empty($items) && count($items) > 0) {
			$score = $items[0] -> score;
		}
		$data['score'] = $score;
		// $this -> to_json($data);

		$this->load->view('mgmt/users/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'address',
			'birthday',
			'user_name',
			'email',
			'mobile',
			'is_valid_mobile',
			'is_bypass_sum_amt_rank',
			'lang',
			'zip'
		));

		if(empty($id)) {
			// insert

			// get code
			$find_code = FALSE;
			while(!$find_code) {
				$code = generate_random_string();
				$c_list = $this -> dao -> find_all_by('code', $code);
				$find_code = (count($c_list) == 0);
				$data['code'] = $code;
			}

			$data['wallet_code_dbc'] = coin_token(34);
			$data['wallet_code_ntd'] = coin_token(35);
			$data['wallet_code_btc'] = '3AkzfW99twBhjZtb8sSXFWNEDTpoEYLmuQ';
			$data['wallet_code_eth'] = '0x08f42bf6f720f0de21df0af68d488cda14c72564';
			$this -> dao -> insert($data);
		} else {
			// update
			$this -> dao -> update($data, $id);
		}

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
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

	public function insert_items() {
		$user_id = $this -> get_post('user_id');
		$product_id = $this -> get_post('product_id');
		$a= $this -> get_post('for_i');
		$i=intval($a);
		$idata['user_id'] = $user_id;
		$idata['product_id'] = $product_id;
		$res = array();

		if(!empty($user_id) && !empty($product_id) && !empty($i)){
			if($i>0){
				for ($j=1;$j<$i+1;$j++) {
					$this -> product_items_dao ->insert($idata);
				}
				$res['success'] = "123";
			}
			else{
					if($i<0){
						$ii=abs($i);
						$count_num = $this -> product_items_dao ->fint_all($user_id,$product_id);
						for ($k=1;$k<$ii+1;$k++) {
							$find_de = $this -> product_items_dao ->fint_all_id_de($user_id,$product_id);
							$this -> product_items_dao ->delete($find_de->id);
						}
						$res['success1'] = "刪除成功";

					} else{
							$res['err_message'] = "您的道具會刪到基本道具,無法刪除";
						}
					}
					$this -> to_json($res);
			}
		}


}
