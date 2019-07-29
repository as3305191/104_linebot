<?php
class Baccarat_tab_tx_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('baccarat_tab_tx');

		$this -> alias_map = array(

		);
	}

	function sum_by_tab_id($tab_id) {
		$list = $this -> db -> query("select sum(tx_amt) as amt from baccarat_tab_tx where tab_id = $tab_id") -> result();
		if(count($list) > 0 ) {
			return (!empty($list[0] -> amt) ? $list[0] -> amt : 0);
		}
		// echo $this -> db -> last_query();
		return 0;
	}

	function query_ajax($data) {
		$start = $data['start'];
		$limit = $data['length'];
		$columns = $data['columns'];
		$search = $data['search'];
		$order = $data['order'];

		// select
		$this -> db -> select('_m.*');
		// $this -> db -> select('c.corp_name');
		// $this -> db -> select('btt.type_name');
		// $this -> db -> select('cl.lang_name');

		// join
		$this -> ajax_from_join();

		// search always
		$this -> search_always($data);

		// search
		$this -> ajax_column_setup($columns, $search, $this -> alias_map);

		// order
		$this -> ajax_order_setup($order, $columns, $this -> alias_map);
		$this -> db -> order_by('pos', 'asc');

		// limit
		$this -> db -> limit($limit, $start);

		// query results
		$query = $this -> db -> get();
		return $query -> result();
	}

	function search_always($data) {
		if(!empty($data['show_closed'])) {
			$this -> db -> where('(_m.status = 0 or _m.status = 2)');
		} else {
			$this -> db -> where('_m.status', 0);
		}

		if(!empty($data['id'])) {
			$this -> db -> where('_m.id', $data['id']);
		}

		if(!empty($data['corp_id'])) {
			$this -> db -> where('_m.corp_id', $data['corp_id']);
		}

		if(!empty($data['tab_type'])) {
			$this -> db -> where('_m.tab_type', $data['tab_type']);
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		// $this -> db -> join("corp c", 'c.id = _m.corp_id', 'left');
		// $this -> db -> join("baccarat_tab_type btt", 'btt.id = _m.tab_type', 'left');
		// $this -> db -> join("corp_lang cl", 'cl.lang = _m.lang', 'left');
	}
}
?>
