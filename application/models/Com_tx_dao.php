<?php
class Com_tx_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('com_tx');

		$this -> alias_map = array(
			'create_time' => '_m.create_time'
		);
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
		// $this -> db -> order_by('pos', 'asc');

		// limit
		$this -> db -> limit($limit, $start);

		// query results
		$query = $this -> db -> get();
		return $query -> result();
	}

	function sum_ajax($data) {
		$start = $data['start'];
		$limit = $data['length'];
		$columns = $data['columns'];
		$search = $data['search'];
		$order = $data['order'];

		// select
		$this -> db -> select('sum(_m.amt) as samt');
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
		// $this -> db -> order_by('pos', 'asc');

		// limit
		// $this -> db -> limit($limit, $start);

		// query results
		$query = $this -> db -> get();
		$list = $query -> result();
		if(count($list) > 0) {
			if(!empty($list[0] -> samt)) {
				return $list[0] -> samt;
			}
		}
		return 0;
	}

	function search_always($data) {
		// if(!empty($data['show_closed'])) {
		// 	$this -> db -> where('(_m.status = 0 or _m.status = 2)');
		// } else {
		// 	$this -> db -> where('_m.status', 0);
		// }

		if(!empty($data['id'])) {
			$this -> db -> where('_m.id', $data['id']);
		}
		if(isset($data['start_date'])) {
			$val = $data['start_date'];
			$this -> db -> where("_m.create_time >= '$val 00:00:00'");
		}
		if(isset($data['end_date'])) {
			$val = $data['end_date'];
			$this -> db -> where("_m.create_time <= '$val 23:59:59'");
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		// $this -> db -> join("corp c", 'c.id = _m.corp_id', 'left');
		// $this -> db -> join("baccarat_tab_type btt", 'btt.id = _m.tab_type', 'left');
		// $this -> db -> join("corp_lang cl", 'cl.lang = _m.lang', 'left');
	}

	function find_all_corp_tx($data) {
		$s_date = $data['s_date'];
		$e_date = $data['e_date'];
		$this -> db -> select("sum(ctx.amt) as sum_amt");
		$this -> db -> select("c.corp_name");

		$this -> db -> from("com_tx as ctx");
		$this -> db -> join("corp c", "c.id = ctx.corp_id", "left");
		$this -> db -> where("ctx.create_time >= '$s_date 00:00:00'");
		$this -> db -> where("ctx.create_time <= '$e_date 23:59:59'");

		if(!empty($data['corp_id'])) {
			$this -> db -> where("ctx.corp_id", $data['corp_id']);
		}

		$this -> db -> group_by("ctx.corp_id");

		$list = $this -> db -> get() -> result();

		return $list;
	}

	function get_sum_amt_all() {
		$this -> db -> select("sum(amt) as samt");
		$list = $this -> find_all();
		if(count($list) > 0) {
			$itm = $list[0];
			return $itm -> samt;
		}
		return 0;
	}

	function get_sum_amt_time($time) {
		$this -> db -> select("sum(amt) as samt");
		$this -> db -> where("create_time like '$time%'");
		$list = $this -> find_all();
		if(count($list) > 0) {
			$itm = $list[0];
			return $itm -> samt;
		}
		return 0;
	}

	function sum_win_loose_amt_by_date($date) {
		$this -> db -> select("sum(win_loose_amt) as samt");
		$this -> db -> from("$this->table_name");
		$this -> db -> where("create_time like '{$date}%'");
		$list = $this -> db -> get() -> result();
		$item = $list[0];
		if(!empty($item -> samt)) {
			return $item -> samt;
		}
		return 0;
	}

	function find_all_amt($data) {
		$this -> db -> where('com_id', $com_id);
		$this -> db -> order_by('tab_id', 'asc');
		$list=  $this -> find_all();
		return $list;
	}

	function get_sum_amt_day($date) {
	$this -> db -> from("$this->table_name as _m");

	$this -> db -> select("if(SUM(_m.amt) is null, 0, SUM(_m.amt)) as amt");
	$this -> db -> where("create_time like '$date%'");
	$this -> db -> where("income_type","fish_bet");

	$query = $this -> db -> get();
	$list = $query -> result();

	return $list[0];
}
}
?>
