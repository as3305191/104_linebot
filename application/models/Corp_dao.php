<?php
class Corp_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('corp');

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

	function find_default_corp() {
		$this -> db -> where('is_default', 1);
		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	function query_ajax($data) {
		$start = $data['start'];
		$limit = $data['length'];
		$columns = $data['columns'];
		$search = $data['search'];
		$order = $data['order'];

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('ct.type_name');
		$this -> db -> select('cl.lang_name');

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
		if(!empty($data['show_closed'])) {
			$this -> db -> where('(_m.status = 0 or _m.status = 2)');
		} else {
			$this -> db -> where('_m.status', 0);
		}

		if(!empty($data['id'])) {
			$this -> db -> where('_m.id', $data['id']);
		}

		if(!empty($data['lang'])) {
			$this -> db -> where('_m.lang', $data['lang']);
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("corp_type ct", 'ct.id = _m.corp_type', 'left');
		$this -> db -> join("corp_lang cl", 'cl.lang = _m.lang', 'left');
	}

	function find_all_company() {
		$this -> db -> where('status', 0);
		$list=  $this -> find_all();
		return $list;
	}

	function find_active_all() {
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

	function update_amt_jp($jp_amt,$payload) {
		$this -> load -> model('Com_tx_dao', 'ctx_dao');

		$sql = "UPDATE corp
						SET fish_jp_amt = fish_jp_amt+{$jp_amt}
						WHERE id = '1'
						";
		$this -> db -> query($sql);
		$amt=-$jp_amt;
		$tx1 = array();
		$tx1['corp_id'] = 1;
		$tx1['income_type'] = "jp增減";
		$tx1['income_id'] = 0;
		$tx1['amt'] =$amt;
		$tx1['note'] = "jp增減{$amt}";
		$this -> ctx_dao -> insert($tx1);
	}
}
?>
