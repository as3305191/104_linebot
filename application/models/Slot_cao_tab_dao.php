<?php
class Slot_cao_tab_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('slot_cao_tab');

		$this -> alias_map = array(

		);
	}

	function find_by_corp_code($code) {
		$this -> db -> where('corp_code', $code);
		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	function find_all_active_by_corp_id($corp_id, $hall_id) {
		$sql = "select st.*, IFNULL(stu.user_id, 0) as occupied_user_id from {$this->table_name} st
						left join {$this->table_name}_user stu on stu.tab_id = st.id and stu.hall_id = $hall_id
						where st.corp_id = $corp_id
						order by st.pos ";
		$list = $this -> db -> query($sql) -> result();

		return $list;
	}
	function find_all_public_by_corp($corp_id) {
		$this -> db -> where('corp_id', $corp_id);
		$this -> db -> where('status', 0);
		$this -> db -> where('tab_type', 1);
		$list = $this -> find_all();
		return $list;
	}

	function find_all_public() {
		$this -> db -> where('status', 0);
		$this -> db -> where('tab_type', 1);
		$list = $this -> find_all();
		return $list;
	}

	function find_all_private_by_corp($corp_id) {
		$this -> db -> where('corp_id', $corp_id);
		$this -> db -> where('status', 0);
		$this -> db -> where('tab_type', 2);
		$list = $this -> find_all();
		return $list;
	}

	function find_all_private() {
		$this -> db -> where('status', 0);
		$this -> db -> where('tab_type', 2);
		$list = $this -> find_all();
		return $list;
	}

	function find_default_corp() {
		$this -> db -> where('is_default', 1);
		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	function find_all_valid_tabs() {
		$this -> db -> where('status', 0);
		$list = $this -> find_all();
		return $list;
	}

	function find_all_valid_tabs_by_corp($corp_id) {
		$this -> db -> where('status', 0);
		$this -> db -> where('corp_id', $corp_id);
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
		$this -> db -> select('c.corp_name');
		$this -> db -> select('btt.type_name');
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
		$this -> db -> join("corp c", 'c.id = _m.corp_id', 'left');
		$this -> db -> join("baccarat_tab_type btt", 'btt.id = _m.tab_type', 'left');
		// $this -> db -> join("corp_lang cl", 'cl.lang = _m.lang', 'left');
	}

	function find_all_company() {
		$this -> db -> where('status', 0);
		$list=  $this -> find_all();
		return $list;
	}

	function find_my_company($id) {
		$this -> db -> where('status', 0);
		$this -> db -> where('id', $id);
		$list=  $this -> find_all();
		return $list;
	}
}
?>
