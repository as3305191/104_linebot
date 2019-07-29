<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Guide_test extends MY_Mgmt_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Guide_test_dao', 'dao');
		$this -> load -> model('Users_dao', 'u_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['login_user'] = $this -> dao -> find_by_id($data['login_user_id']);
		$this->load->view('mgmt/guide_test/list', $data);
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
			'role_id'
		));
		$s_data = $this -> setup_user_data(array());
		$login_user = $this -> u_dao -> find_by_id($s_data['login_user_id']);

		if($login_user -> role_id != 99) {
			$data['corp_id'] = $login_user -> corp_id;
		}

		if($login_user -> role_id == 1 || $login_user -> role_id == 99) {

		} else {
			$data['intro_id'] = $login_user -> id;
		}

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
			$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($id);
		}

		$s_data = $this -> setup_user_data(array());
		$login_user = $this -> u_dao -> find_by_id($s_data['login_user_id']);
		$data['login_user'] = $login_user;

		$lang = $this -> session -> userdata('lang');
		$data['lang'] = $lang;

		$this->load->view('mgmt/guide_test/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'round_count',
			'round_content',
			'loop_count',
			'loop_content',
			'init_bet'
		));

		if(empty($id)) {
			// insert

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

	public function run() {
		$init_bet = $this -> get_post('init_bet');
		$init_bet = intval($init_bet);

		$loop = $this -> get_post('loop');
		$loop = json_decode($loop);
		$round = $this -> get_post('round');
		$round = json_decode($round);

		$loop_name[1] = "莊";
		$loop_name[0] = "和";
		$loop_name[-1] = "閒";

		$base = intval($init_bet / 50);


		$loop_cnt = count($loop);
		for($i = 0 ; $i < count($round) ; $i++) {
			$n = $i+1;
			echo "第 $n 局開始 ===============<br/>";
			$c_bet = $base;
			$c_rem = $init_bet;
			$l_idx = 0;
			for($r_idx = $i ; $r_idx < count($round) ; $r_idx++) {
				if($c_rem == 0) {
					break;
				}
				$l_idx = $l_idx%$loop_cnt;
				$is_win = 0;
				if($round[$r_idx] != 0) {
					$is_win = ($loop[$l_idx]==$round[$r_idx]) ? 1 : -1;
				}
				$l_idx++;
				$res = $is_win == 1 ? '<font color="red">贏</font>' : ($is_win == -1 ? '<font color="green">輸</font>' : '<font color="blue">和</font>');
				$this_bet = $c_bet;
				if($is_win == -1) {
					$c_rem -= $c_bet;
					$c_bet = ($c_bet * 2);
					if($c_bet > $c_rem) {
						$c_bet = $c_rem;
					}
				} else if($is_win == 1) {
					$c_rem += $c_bet;
					$c_bet = $base;
				}
				if($c_rem == 0) {
					$c_rem = "<font color='red'> $c_rem <=================輸光了 </font>";
				}
				echo  "$res : 下注 $this_bet : 目前輸贏 $c_rem<br/>";
			}
		}
	}
}
