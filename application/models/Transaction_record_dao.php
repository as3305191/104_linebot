<?php
class Transaction_record_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('transaction_record');

		$this -> alias_map = array(
			// 'store_name' => 'st.store_name'
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
		// $this -> db -> select('st.store_name as store_name');

		// join
		$this -> ajax_from_join();

		// search always
		$this -> search_always($data);

		// search
		$this -> ajax_column_setup($columns, $search, $this -> alias_map);

		// order
		$this -> ajax_order_setup($order, $columns, $this -> alias_map);
		$this -> db -> order_by('_m.id', 'desc');

		// limit
		$this -> db -> limit($limit, $start);

		// query results
		$query = $this -> db -> get();
		return $query -> result();
	}

	function search_always($data) {
		$this -> db -> where('_m.status', 0);
		$this -> db -> where('_m.corp_id',$data['corp_id']);
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		// $this -> db -> join("corp as co", 'st.id = _m.store_id', 'left');
	}

	function query_all($f, &$res = array()) {
		// select
		$this -> db -> select('_m.*');
		// $this -> db -> select('st.store_name as store_name');
		// join
		$this -> ajax_from_join();

		if(!empty($f['id'])) {
			$this -> db -> where('_m.id', $f['id']);
		}

		$this -> db -> order_by('_m.id', 'desc');

		// search always
		$this -> search_always($f);

		// limit
		if(empty($f['page'])) {
			$page = 0;
		} else {
			$page = intval($f['page']);
		}

		if(empty($f['limit'])) {
			// default is 10
			$limit = 10;
		} else {
			$limit = intval($f['limit']);
		}

		$res['page'] = $page;
		$res['limit'] = $limit;

		$start = $page * $limit;
		$this -> db -> limit($limit, $start);

		// query results
		$query = $this -> db -> get();
		$list = $query -> result();

		foreach($list as $each) {
			if(!empty($each -> image_id)) {
				$each -> image_url = get_img_url($each -> image_id);
				$each -> thumb_url = get_thumb_url($each -> image_id);
			}
		}

		return $list;
	}

	//override
	function ajax_column_setup($columns, $search, $alias_map) {
		// search
		foreach($columns as $col) {
			if(!empty($col['search']['value'])) {
				if($col['data'] == 'mul_cate'){
					$mul_cate_val = explode(',',$col['search']['value']);
					foreach($mul_cate_val as $each){
						//** key step
							$this -> db -> where("FIND_IN_SET('$each',_m.main_cate) <>", 0);
					}

				}else{
					$col_name = $col['data'];
					$this -> db -> like($this -> get_alias_val($alias_map, $col_name), $col['search']['value']);
				}

			}
		}
	}

	//
	function find_by_parameter($m){
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("products p", "p.id = _m.product_id ", "left");

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('p.product_name');

		if(!empty($m['product_id'])){
			$this -> db -> where("_m.product_id", $m['product_id'] );
		}

		$this -> db -> where("_m.status", 0);

		// limit
		if(empty($m['page'])) {
			$page = 0;
		} else {
			$page = intval($m['page']);
		}
		if(empty($m['limit'])) {
			// default is 20
			$limit = 20;
		}
		$start = $page * $limit;
		$this -> db -> limit($limit, $start);

		$query = $this -> db -> get();
		$list = $query -> result();

		return $list;
	}

}
?>
