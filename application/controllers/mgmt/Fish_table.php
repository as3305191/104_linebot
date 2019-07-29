<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fish_table extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Fish_tab_dao', 'dao');
		$this -> load -> model('Fish_tab_lottery_dao', 'fish_tab_lottery_dao');
		$this -> load -> model('Lottery_dao', 'lottery_dao');
		$this -> load -> model('Com_tx_dao', 'ctx_dao');

	}

	public function index()
	{
		$data = array();
		$this -> setup_user_data($data);

		// $this -> to_json($data);

		$this->load->view('mgmt/fish_table/list', $data);
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
		));

		$res['items'] = $this -> fish_tab_lottery_dao -> query_ajax($data);
		$res['recordsFiltered'] = $this -> fish_tab_lottery_dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> fish_tab_lottery_dao -> count_all_ajax($data);

		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();

		if(!empty($id)) {
			$item = $this -> dao -> find_by_id($id);
			$data['item'] = $item;
		}

		$this->load->view('mgmt/fish_table/edit', $data);
	}

	public function insert() {
		$res = array();
		$res['success'] = TRUE;

		$id = $this -> get_post("item_id");

		$data = $this -> get_posts(array(
			'tab_name',
			'pos',
		));

		if(!empty($id)){
			$this -> dao -> update($data, $id);
		} else {
			$data['corp_id']= 1;
			$id = $this -> dao ->insert($data);
		}

		$s_data = $this -> setup_user_data(array());
		$user = $s_data['l_user'];

		// update pool val
		$data = $this -> get_posts(array(
			'pool_100',
			'pool_100_king',
			'pool_2000',
			'pool_2000_king',
			'pool_20000',
			'pool_20000_king',
			'pool_200000',
			'pool_200000_king',
			'pool_1000000',
			'pool_1000000_king',
		));

		foreach($data as $key => $val) {
			$val = intval($val);
			if($val > 0 || $val < 0) {
				$amt = -$val;

				$this -> dao -> db -> trans_begin();

				// com tx
				$tx = array();
				$tx['corp_id'] = $user -> corp_id;
				$tx['amt'] = $amt;
				$tx['income_type'] = "{$key}";
				$tx['income_id'] = 0;
				$tx['note'] = "彩池更動 {$amt}";
				$this -> ctx_dao -> insert($tx);

				$this -> dao -> db -> query("update fish_tab set {$key} = {$key} + ({$val}) where id = {$id}");

				if ($this -> dao -> db-> trans_status() === FALSE) {
				    $this -> dao -> db-> trans_rollback();
				} else {
				    $this -> dao -> db -> trans_commit();
				}
			}
		}

		$this -> to_json($res);
	}

	public function insert_fish_tab() {
		$data = array();
		$i_data['tab_name']= $this -> get_post("table");
		$i_data['corp_id']= 1;
		$big =$this ->dao-> find_bigger();
		$i_data['pos']= intval($big[0]->pos)+1;
		$res['success'] = $i_data;
		$this -> dao ->insert($i_data);

		$this -> to_json($res);
	}

	public function insert_fish_tab_lottery() {
		$res = array();
		$res['success'] = TRUE;

		$lottery_sn = $this -> get_post("lottery_sn");
		$lottery_no = $this -> get_post("lottery_no");
		$tab_id = $this -> get_post("tab_id");

		if(!empty($lottery_sn) && !empty($lottery_no) && !empty($tab_id)) {
			$lottery = $this -> lottery_dao -> find_by('sn', $lottery_sn);
			if(!empty($lottery)) {
				$exists_sn = $this -> fish_tab_lottery_dao -> find_by("lottery_no", $lottery_no);
				if(empty($exists_sn)) {
					$this -> fish_tab_lottery_dao -> insert(array(
						'lottery_no' => $lottery_no,
						'tab_id' => $tab_id,
						'lottery_id' => $lottery -> id,
					));

					$current = $this -> fish_tab_lottery_dao -> find_current($tab_id);
					if(empty($current)) {
						$this -> fish_tab_lottery_dao -> find_next($tab_id); // mark current
					}
 				} else {
					$res['error_msg'] = "序號已存在";
				}
			} else {
				$res['error_msg'] = "查無摸彩活動";
			}
		} else {
			$res['error_msg'] = "缺少必填欄位";
		}

		$this -> to_json($res);

	}

	public function edit_page() {
		$data = array();


		$this->load->view('mgmt/fish_table/edit_page', $data);
	}

	public function insert_edit_page() {
		$data = array();
		$i_data['tab_name']= $this -> get_post("table");
		$i_data['corp_id']= 1;
		$big =$this ->dao-> find_bigger();
		$i_data['pos']= intval($big[0]->pos)+1;
		$res['success'] = $i_data;

		$this->load->view('mgmt/fish_table/edit_page', $data);
	}

	function delete_tab_lottery($id) {
		$res = array();
		$res['success'] = TRUE;
		$this -> fish_tab_lottery_dao -> delete($id);
		$this -> to_json($res);
	}

	function do_open($id) {
		$res = array();
		$res['success'] = TRUE;
		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];
		$lottery_tx_sn = $this -> get_post("val");
		$ftl = $this -> fish_tab_lottery_dao -> find_by_id($id);
		if(!empty($ftl)) {
			if($ftl -> is_open == 0) {
				// 未開獎
				$error_msg = $this -> fish_tab_lottery_dao -> do_open($id, $lottery_tx_sn);
				if(!empty($error_msg)) {
					$res['error_msg'] = $error_msg;
				}
			} else {
				$res['error_msg'] = "已開獎";
			}
		} else {
			$res['error_msg'] = "查無資料";
		}
		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete($id);
		$this -> to_json($res);
	}
}
