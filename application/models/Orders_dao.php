<?php
class Orders_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('orders');

		$this -> alias_map = array(
			'member_account' => 'm.account',
			'member_mobile' => 'm.mobile'
		);
	}

	function ym_list() {
		$sql = "select distinct DATE_FORMAT(create_time, '%Y-%m') as ym from orders order by create_time desc";
		$list = $this -> db -> query($sql) -> result();
		return $list;
	}

	function ym_list_by_year($year) {
		$sql = "select distinct DATE_FORMAT(create_time, '%Y-%m') as ym from orders where create_time like '$year%' order by create_time asc";
		$list = $this -> db -> query($sql) -> result();
		return $list;
	}

	function y_list() {
		$sql = "select distinct DATE_FORMAT(create_time, '%Y') as y from orders order by create_time desc";
		$list = $this -> db -> query($sql) -> result();
		return $list;
	}

	function count_by_ym($ym) {
		$this -> db -> select("count(id) as cnt");
		$this -> db -> from('orders');
		$this -> db -> where("create_time like '$ym%'");

		$list = $this -> db -> get() -> result();
		if(count($list > 0)) {
			return (!empty($list[0] -> cnt) ? $list[0] -> cnt : 0);
		}
		return 0;
	}

	function find_all_me($id) {

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('os.order_status_name');
		$this -> db -> select('ps.order_pay_status_name');
		$this -> db -> select('ss.order_shipping_status_name ');
		$this -> db -> select('p.product_name ');
		$this -> db -> select('u.user_name, u.account as user_account ');

		// join
		$this -> ajax_from_join();

		// search always
		$this -> search_always(array());

		// search
		$this -> ajax_column_setup(array(), array(), $this -> alias_map);

		// order
		$this -> ajax_order_setup(array(), array(), $this -> alias_map);
		$this -> db -> where('_m.id', $id);

		// query results
		$query = $this -> db -> get();
		return $query -> result();
	}

	function query_ajax($data) {
		$start = isset($data['start']) ? $data['start'] : 0;
		$limit = isset($data['length']) ? $data['length'] : 1 ;
		$columns = isset($data['columns']) ? $data['columns'] : array();
		$search = isset($data['search']) ? $data['search'] : array();
		$order = isset($data['order']) ? $data['order'] : array();

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('os.order_status_name');
		$this -> db -> select('ps.order_pay_status_name');
		$this -> db -> select('ss.order_shipping_status_name ');
		$this -> db -> select('p.product_name ');

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
		if(empty($data['no_limit'])) {
			$this -> db -> limit($limit, $start);
		}

		// query results
		$query = $this -> db -> get();
		return $query -> result();
	}

	function search_always($data) {
		// $this -> db -> where('_m.status', 0);
		if(isset($data['status_filter'])) {
			$sf = $data['status_filter'];
			if($sf == 'all') {
			} else if($sf == 'cod') {
				$this -> db -> where('(_m.pay_type_id = 3)');
				$this -> db -> where('(_m.status = 0 or _m.status = 1)');
			} else if($sf == 'cod4') {
				$this -> db -> where('(_m.pay_type_id = 3)');
				$this -> db -> where('(_m.status = 4)');
			} else if($sf == '0') {
				$this -> db -> where('(_m.status = 0)');
			} else if($sf == '') {
				// do nothing
			} else {
				$this -> db -> where("_m.status = $sf");
			}
		}

		// pay staus filter
		if(isset($data['pay_status_filter'])) {
			$sf = $data['pay_status_filter'];
			if($sf == 'all') {
			} else if($sf == '0') {
				$this -> db -> where('(_m.pay_status = 0)');
			} else if($sf == '') {
				// do nothing
			} else {
				$this -> db -> where("_m.pay_status = $sf");
			}
		}

		if(!empty($data['store_id'])) {
			$this -> db -> where('_m.store_id', $data['store_id']);
		}

		if(!empty($data['id'])) {
			$this -> db -> where('_m.id', $data['id']);
		}

		if(!empty($data['dt'])) {
			$dt = $data['dt'];
			$this -> db -> where("_m.create_time like '$dt%'");
		}

		if(!empty($data['ym'])) {
			$ym = $data['ym'];
			$this -> db -> where("_m.create_time like '$ym%'");
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("order_status as os", 'os.id = _m.status', 'left');
		$this -> db -> join("order_pay_status as ps", 'ps.id = _m.pay_status', 'left');
		$this -> db -> join("order_shipping_status as ss", 'ss.id = _m.shipping_status', 'left');
		$this -> db -> join("products as p", 'p.id = _m.product_id', 'left');
		$this -> db -> join("users as u", 'u.id = _m.user_id', 'left');
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
		// $this -> db -> select('os.status_name');
		// $this -> db -> select('ps.status_name as pay_status_name');
		// $this -> db -> select('ss.status_name as shipping_status_name');
		// $this -> db -> select('pt.type_name as pay_type_name');
		// $this -> db -> select('po.option_name');

		// join
		$this -> ajax_from_join();

		if(!empty($f['sn'])) {
			$this -> db -> where('_m.sn', $f['sn']);
		}
		if(!empty($f['id'])) {
			$this -> db -> where('_m.id', $f['id']);
		}
		if(!empty($f['member_id'])) {
			$this -> db -> where('_m.member_id', $f['member_id']);
		}
		if(!empty($f['store_id'])) {
			$this -> db -> where('_m.store_id', $f['store_id']);
		}
		if(!empty($f['order_id'])) {
			$this -> db -> where('_m.id', $f['order_id']);
		}

		if(!empty($f['status'])) {
			$this -> db -> where('_m.status', $f['status']);
		}

		if(!empty($f['type'])) {
			$type = $f['type'];
			if($type == 1) { // 待同意
				$this -> db -> where('_m.status', 1);
			}
			if($type == 2) { // 待製作
				$this -> db -> where('_m.status', 2);
				$this -> db -> where('_m.shipping_status', 0);
			}
			if($type == 3) { // 已交貨
				$this -> db -> where('(_m.status > 1)');
				$this -> db -> where('_m.shipping_status', 4);
			}
		}

		if(!empty($f['shipping_0'])) {
			$this -> db -> where('_m.shipping_status', 0);
		}

		if(!empty($f['is_stock'])) {
			$is_stock = $f['is_stock'];
			if($is_stock == 'yes') {
				$this -> db -> where('_m.pay_type_id', 4); //憑證付款
			} else if($is_stock == 'no') {
				$this -> db -> where('(_m.pay_type_id <> 4)');
			}
		}

		if(!empty($f['option_code'])) {
			$oc = $f['option_code'];
			if($oc == 'pickup+delivery') {
				$this -> db -> where("(_m.option_code = 'pickup' or _m.option_code = 'delivery')");
			} else if($oc == 'pickup+delivery+stock') {
				$this -> db -> where("(_m.option_code = 'pickup' or _m.option_code = 'delivery' or _m.option_code = 'stock')");
			} else if($oc == 'pickup+delivery+unchecked') {
				$this -> db -> where("(_m.option_code = 'pickup' or _m.option_code = 'delivery' or _m.option_code = 'stock')");
				$this -> db -> where('_m.store_check', 0);
			} else if($oc == 'pickup+delivery+checked+stock') {
				$this -> db -> where("(_m.option_code = 'pickup' or _m.option_code = 'delivery' or _m.option_code = 'stock')");
				$this -> db -> where('_m.store_check', 1);
			} else {
				$this -> db -> where('_m.option_code', $f['option_code']);
			}

			$this -> db -> order_by('_m.target_time', 'asc');
		} else {
			$this -> db -> order_by('_m.id', 'desc');
		}

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

		$this -> load -> model('Order_setting_dao', 'order_setting_dao');
		$max_days = $this -> order_setting_dao -> get_max_days();

		foreach($list as $each) {
			if(!empty($each -> image_id)) {
				// $each -> image_url = get_img_url($each -> image_id);
				// $each -> thumb_url = get_thumb_url($each -> image_id);
			}

			// due
			$each -> due = date('Y-m-d H:i:s', strtotime($each -> create_time . " + $max_days days"));
		}
		return $list;
	}

	function find_unpaid_ticket_order_by_member_and_store($member_id, $store_id) {
		$this -> db -> where('member_id', $member_id);
		$this -> db -> where('store_id', $store_id);
		$this -> db -> where('pay_type_id', 4); // pay by ticket
		$this -> db -> where("pay_status", 0); // unpaid
		$this -> db -> where("(status = 1 or status = 2)"); // unpaid
		$list = $this -> find_all();
		return $list;
	}

	function find_by_sn($sn) {
		$this -> db -> where('sn', $sn);
		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	/**
	 * Status
	 */
	 function find_all_status() {
	 	$this -> db -> from('order_status');
		return $this -> db -> get() -> result();
	 }

	 function count_all_status($corp_id = 0) {
	 	// number status
	 	$sql = " SELECT count(id) as cnt, status FROM orders ";
		if(!empty($corp_id)) {
			$sql .= " WHERE corp_id = $corp_id ";
		}
		$sql .= " GROUP BY status ";

		$list = $this -> db -> query($sql) -> result();
		$ret_arr = array();
		foreach($list as $each) {
			$ret_arr[$each -> status] = $each -> cnt;
		}

		// cod
		$sql = "SELECT count(id) as cnt, status FROM orders
				WHERE pay_type_id = 3 and (status = 0 or status = 1)
				GROUP BY status";
		$list = $this -> db -> query($sql) -> result();
		foreach($list as $each) {
			$ret_arr['cod'] = $each -> cnt;
		}

		// cod4
		$sql = "SELECT count(id) as cnt, status FROM orders
				WHERE pay_type_id = 3 and status = 4
				GROUP BY status";
		$list = $this -> db -> query($sql) -> result();
		foreach($list as $each) {
			$ret_arr['cod4'] = $each -> cnt;
		}
		return $ret_arr;
	 }

	 function count_all_status_by_data($data) {
	 	// number status
	 	$sql = " SELECT count(id) as cnt, status FROM orders ";
		if(!empty($store_id)) {
			$sql .= " WHERE store_id = $store_id ";
		}

		if(!empty($data['dt'])) {
			$dt = $data['dt'];
			$sql .= " WHERE create_time like '$dt%'";
		}

		if(!empty($data['ym'])) {
			$ym = $data['ym'];
			$sql .= " WHERE create_time like '$ym%'";
		}
		$sql .= " GROUP BY status ";

		$list = $this -> db -> query($sql) -> result();
		$ret_arr = array();
		foreach($list as $each) {
			$ret_arr[$each -> status] = $each -> cnt;
		}

		// cod
		$sql = "SELECT count(id) as cnt, status FROM orders
				WHERE pay_type_id = 3 and (status = 0 or status = 1) ";

		if(!empty($data['dt'])) {
			$dt = $data['dt'];
			$sql .= " and create_time like '$dt%'";
		}

		if(!empty($data['ym'])) {
			$ym = $data['ym'];
			$sql .= " and create_time like '$ym%'";
		}
		$sql .= " GROUP BY status ";

		$list = $this -> db -> query($sql) -> result();
		foreach($list as $each) {
			$ret_arr['cod'] = $each -> cnt;
		}

		// cod4
		$sql = "SELECT count(id) as cnt, status FROM orders
				WHERE pay_type_id = 3 and status = 4 ";

		if(!empty($data['dt'])) {
			$dt = $data['dt'];
			$sql .= " and create_time like '$dt%'";
		}

		if(!empty($data['ym'])) {
			$ym = $data['ym'];
			$sql .= " and create_time like '$ym%'";
		}
		$sql .= " GROUP BY status ";

		$list = $this -> db -> query($sql) -> result();
		foreach($list as $each) {
			$ret_arr['cod4'] = $each -> cnt;
		}
		return $ret_arr;
	 }

	 function count_all_pay_status_by_data($data) {
		 	// number status
		 	$sql = " SELECT count(id) as cnt, pay_status FROM orders ";


			if(!empty($data['dt'])) {
				$dt = $data['dt'];
				$sql .= " WHERE create_time like '$dt%'";
			}

			if(!empty($data['ym'])) {
				$ym = $data['ym'];
				$sql .= " WHERE create_time like '$ym%'";
			}
			$sql .= " GROUP BY pay_status ";
			$list = $this -> db -> query($sql) -> result();
			$ret_arr = array();
			foreach($list as $each) {
				$ret_arr[$each -> pay_status] = $each -> cnt;
			}
			return $ret_arr;
	}

	function sum_all_by_pay_type_id($data) {
		 // number status
		 $sql = " SELECT sum(total) as cnt, pay_type_id FROM orders ";

		 $sql .= " WHERE pay_status > 0 "; // paid
		 if(!empty($data['dt'])) {
			 $dt = $data['dt'];
			 $sql .= " and create_time like '$dt%'";
		 }

		 if(!empty($data['ym'])) {
			 $ym = $data['ym'];
			 $sql .= " and create_time like '$ym%'";
		 }
		 $sql .= " GROUP BY pay_type_id ";
		 $list = $this -> db -> query($sql) -> result();
		 $ret_arr = array();
		 foreach($list as $each) {
			 $ret_arr[$each -> pay_type_id] = $each -> cnt;
		 }
		 return $ret_arr;
 }

 function sum_all($data) {
		// number status
		$sql = " SELECT sum(total) as cnt FROM orders ";

		$sql .= " WHERE pay_status > 0 "; // paid
		if(!empty($data['dt'])) {
			$dt = $data['dt'];
			$sql .= " and create_time like '$dt%'";
		}

		if(!empty($data['ym'])) {
			$ym = $data['ym'];
			$sql .= " and create_time like '$ym%'";
		}
		$list = $this -> db -> query($sql) -> result();
		$ret_arr = array();
		if(count($list) > 0) {
			return (!empty($list[0] -> cnt) ? $list[0] -> cnt : 0);
		}
		return 0;
}
}
?>
