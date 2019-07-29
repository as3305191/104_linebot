<?php
class Products_v3_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('products_v3');

		$this -> alias_map = array(
		);
	}

	function find_all_online() {
		$this -> db -> where('online', 1);
		return $this -> find_all();
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
		// $this -> db -> where('_m.status', 0);
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		// $this -> db -> join("stores as st", 'st.id = _m.store_id', 'left');
	}

	function find_by_account($account) {
		$this -> db -> where('account', $account);
		$query = $this -> db -> get($this -> table_name);
		foreach ($query->result() as $row){
		    return $row;
		}
		return NULL;
	}

	function query_all($f, &$res = array()) {
		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('st.store_name as store_name');
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

	// api query
	function query_all_once($f) {
		$this -> load -> model("Product_pro_dao", "p_pro_dao");
		$this -> load -> model("Product_off_dao", "p_off_dao");
		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('st.store_name as store_name');

		// join
		$this -> ajax_from_join();

		if(!empty($f['id'])) {
			$this -> db -> where('_m.id', $f['id']);
		}

		if(!empty($f['store_id'])) {
			$this -> db -> where('_m.store_id', $f['store_id']);
		}

		$this -> db -> order_by('_m.pos', 'asc');
		$this -> db -> order_by('_m.id', 'desc');

		// search always
		$this -> search_always($f);
		$now_time = date('Y-m-d H:i:s');
		$this -> db -> where("((_m.start_time <= '$now_time' and _m.end_time >= '$now_time') or _m.ever_time = 1 )");
		$this -> db -> where('_m.post_checked', 1);

		// query results
		$query = $this -> db -> get();
		$list = $query -> result();

		foreach($list as $each) {
			if(!empty($each -> image_id)) {
				$each -> image_url = get_img_url($each -> image_id);
				$each -> thumb_url = get_thumb_url($each -> image_id);
			}

			// is property_map() {
			if($each -> is_pro == 1) {
				$f['product_pro_id'] = $each -> id;
				$f['for_api'] = TRUE;
				$each -> pro_list = $this -> p_pro_dao -> query_all($f);
			}

			// check is off
			$off['is_off'] = TRUE;
			$off['for_api'] = TRUE;
			$off['product_id'] = $each -> id;
			$each -> off_list = $this -> p_off_dao -> query_all($off);
			if(!empty($each -> off_list)) {
				$an_off = $each -> off_list[0];
				$each -> off_price = $an_off -> off_price;
				$each -> is_off = 1;
			}
		}

		return $list;
	}
}
?>
