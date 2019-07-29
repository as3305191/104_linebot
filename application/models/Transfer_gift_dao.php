<?php
class Transfer_gift_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('transfer_gift');

		$this -> alias_map = array(

 		);
	}

	function find_me($id) {
		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('ts.status_name');
		$this -> db -> select('iu.account as in_account');
		$this -> db -> select('ou.account as out_account');

		// join
		$this -> ajax_from_join();
		$this -> db -> where('_m.id', $id);

		// query results
		$query = $this -> db -> get();
		$list = $query -> result();
		return $list[0];
	}

	function list_all($user_id, $page, $page_size) {
		$page = ($page < 1) ? 1 : $page;
		$start = ($page - 1) * $page_size;
		$limit = $page_size;

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('ts.status_name');
		$this -> db -> select('iu.nick_name as in_nick_name');
		$this -> db -> select('iu.line_picture as in_line_picture');
		$this -> db -> select('ou.nick_name as out_nick_name');
		$this -> db -> select('ou.line_picture as out_line_picture');

		// join
		$this -> ajax_from_join();

		// search always
		$this -> db -> where("(in_user_id = $user_id or out_user_id = $user_id)");

		// search

		// order
		$this -> db -> order_by('id', 'desc');

		// limit
		$this -> db -> limit($limit, $start);

		// query results
		$query = $this -> db -> get();
		return $query -> result();
	}

	function query_ajax($data) {
		$start = $data['start'];
		$limit = $data['length'];
		$columns = $data['columns'];
		$search = $data['search'];
		$order = $data['order'];

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('ts.status_name');
		$this -> db -> select('iu.account as in_account');
		$this -> db -> select('ou.account as out_account');

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
		if(isset($data['status']) && strlen($data['status']) > 0) {
			$this -> db -> where('_m.status', $data['status']);
		}

		if(isset($data['user_id']) && $data['user_id'] > -1) {
			$this -> db -> where('_m.user_id', $data['user_id']);
		}

		if(isset($data['out_user_id']) && $data['out_user_id'] > -1) {
			$this -> db -> where('_m.out_user_id', $data['out_user_id']);
		}

		if(isset($data['in_user_id']) && $data['in_user_id'] > -1) {
			$this -> db -> where('_m.in_user_id', $data['in_user_id']);
		}

		if(isset($data['id']) && $data['id'] > -1) {
			$this -> db -> where('_m.id', $data['id']);
		}

		if(isset($data['corp_id']) && $data['corp_id'] > -1) {
			$this -> db -> where('_m.corp_id', $data['corp_id']);
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("transfer_gift_status ts", "ts.id = _m.status", "left");
		$this -> db -> join("users iu", "iu.id = _m.in_user_id", "left");
		$this -> db -> join("users ou", "ou.id = _m.out_user_id", "left");
	}

	function find_all_status() {
		$sql = "select * from transfer_gift_status ";
		$list = $this -> db -> query($sql) -> result();
		return $list;
	}

	function sum_by_date($date) {
		$this -> db -> select("sum(amt) as samt");
		$this -> db -> from("$this->table_name");
		$this -> db -> where("create_time like '{$date}%'");
		$list = $this -> db -> get() -> result();
		$item = $list[0];
		if(!empty($item -> samt)) {
			return $item -> samt;
		}
		return 0;
	}

	function sum_ope_by_date($date) {
		$this -> db -> select("sum(ope_amt) as samt");
		$this -> db -> from("$this->table_name");
		$this -> db -> where("create_time like '{$date}%'");
		$list = $this -> db -> get() -> result();
		$item = $list[0];
		if(!empty($item -> samt)) {
			return $item -> samt;
		}
		return 0;
	}

	function find_user_gift($data, $is_count = FALSE) {

		$user_id = $data['user_id'];
		$start = $data['start'];
		$limit = $data['length'];
		$gift_select= $data['gift_select'];

		// select
		$this -> db -> from("$this->table_name as _m");

		$this -> db -> select('_m.amt');
		$this -> db -> select('_m.create_time');
		$this -> db -> select('tgs.status_name');
		$this -> db -> select('_m.in_user_id');
		$this -> db -> select('_m.out_user_id');
		$this -> db -> select('u.nick_name as out_user_nick_name');
		$this -> db -> select('us.nick_name as in_user_nick_name');

		$this -> db -> join("transfer_gift_status tgs", "tgs.id = _m.status", "left");
		$this -> db -> join("users u", "u.id = _m.out_user_id", "left");
		$this -> db -> join("users us", "us.id = _m.in_user_id", "left");


		$this -> db -> order_by('_m.in_user_id');

		if(!$is_count) {
			$this -> db -> limit($limit, $start);
		}

		if($gift_select<0) {
			$this -> db -> where('out_user_id',$user_id);
			$this -> db -> or_where('in_user_id',$user_id);
		}
		if($gift_select==0) {
			$this -> db -> where('out_user_id',$user_id);
		}
		if($gift_select>0) {
			$this -> db -> where('in_user_id',$user_id);
		}

		// query results
		if(!$is_count) {
			$query = $this -> db -> get();
			return $query -> result();
		} else {
			return $this -> db -> count_all_results();
		}

	}

}
?>
