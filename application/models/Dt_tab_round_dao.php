<?php
class Dt_tab_round_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('dt_tab_round');

		$this -> alias_map = array(
			// 'corp_id' => '_m.corp_id'
			// 'pay_type_name' => 'pt.type_name'
 		);
	}

	function new_round($tab_id) {
		// clear other round first
		$this -> db -> query("update {$this->table_name} set status = 2 where tab_id = $tab_id ");

		$i_data = array();
		$i_data['tab_id'] = $tab_id;
		$last_id = $this -> insert($i_data);
		$this -> update(array('sn' => date('YmdHis') . $last_id),  $last_id);
		$item = $this -> find_by_id($last_id);
		return $item;
	}

	function need_gen_round_list($tab_id) {
		$ng_list = $this -> find_all_need_gen_list($tab_id);
		if(count($ng_list) > 0) {
			return $ng_list;
		} else {
			$no_list = $this -> find_all_non_opening_list($tab_id);
			if(count($no_list) == 0) { // no opening then create new one
				// create new one
				$i_data = array();
				$i_data['tab_id'] = $tab_id;
				$last_id = $this -> insert($i_data);
				$this -> update(array('sn' => date('YmdHis') . $last_id),  $last_id);
				$list = $this -> find_all_need_gen_list($tab_id);
				return $list;
			}
		}
		return array();
	}

	function find_current_round($tab_id) {
		$this -> db -> select('_m.*');
		$this -> db -> select('btrs.status_name');

		$this -> db -> where('_m.tab_id', $tab_id);
		$this -> db -> where('_m.status < 2'); // not finished
		$this -> db -> order_by('_m.id', 'asc');
		$this -> db -> limit(1);
		$this -> db -> from($this -> table_name . ' as _m');
		$this -> db -> join('baccarat_tab_round_status btrs', 'btrs.id = _m.status', 'left');
		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	function find_all_need_gen_list($tab_id) {
		$this -> db -> where('round_details', '0'); // no round
		$this -> db -> where('status', '0'); // non opening
		$this -> db -> where('tab_id', $tab_id);
		$this -> db -> order_by('id', 'asc');
		$this -> db -> limit(1);
		$list = $this -> find_all();
		return $list;
	}

	function find_all_non_opening_list($tab_id) {
		$this -> db -> where('status', '0');
		$this -> db -> where('tab_id', $tab_id);
		$this -> db -> order_by('id', 'asc');
		$list = $this -> find_all();
		return $list;
	}

	function query_ajax($data) {
		$start = $data['start'];
		$limit = $data['length'];
		$columns = $data['columns'];
		$search = $data['search'];
		$order = $data['order'];

		// select
		$this -> db -> select('_m.*');
		// $this -> db -> select('ws.status_name');
		// $this -> db -> select('u.account as user_account, u.bank_id, u.bank_account');
		// $this -> db -> select('bk.bank_name');
		// $this -> db -> select('c.corp_name');

		// join
		$this -> ajax_from_join();

		// search always
		$this -> search_always($data);

		// search
		$this -> ajax_column_setup($columns, $search, $this -> alias_map);

		// order
		$this -> ajax_order_setup($order, $columns, $this -> alias_map);
		$this -> db -> order_by('id', 'desc');

		// limit
		$this -> db -> limit($limit, $start);

		// query results
		$query = $this -> db -> get();
		return $query -> result();
	}

	function search_always($data) {

		if(isset($data['id']) && $data['id'] > -1) {
			$this -> db -> where('_m.id', $data['id']);
		}

		if(!empty($data['tab_id'])) {
			$this -> db -> where('_m.tab_id', $data['tab_id']);
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		// $this -> db -> join("withdraw_status ws", "ws.id = _m.status", "left");
		// $this -> db -> join("users u", "u.id = _m.user_id", "left");
		// $this -> db -> join("banks bk", "bk.bank_id = u.bank_id", "left");
		// $this -> db -> join("corp c", "c.id = _m.corp_id", "left");
	}
}
?>
