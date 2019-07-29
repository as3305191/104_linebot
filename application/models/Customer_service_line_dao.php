<?php
class Customer_service_line_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('customer_service_line');

		$this -> alias_map = array(

 		);
	}

	function list_msg_by_user($user_id) {
		$this -> db -> select("_m.*");

		$this -> db -> where("user_id", $user_id);
		$this -> db -> from("{$this->table_name} as _m");
		$list = $this -> db -> get() -> result();
		return $list;
	}

	function add_msg($evt) {
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Customer_service_line_room_dao', 'room_dao');

		$message = $evt -> message;
		$source = $evt -> source;


		$user = $this -> users_dao -> find_by("line_sub", $source -> userId);
		$room = $this -> room_dao -> find_by("user_id", $user -> id);

		if(!empty($user)) {
			if($message -> type == "text") {
				$i_data = array();
				$i_data['msg'] = $message -> text;
				$i_data['msg_type'] = "text";
				$i_data['corp_id'] = 1;
				$i_data['user_id'] = $user -> id;
				$this -> insert($i_data);

				// room data
				$u_data = array();
				$u_data['last_msg'] = $message -> text;
				$u_data['last_msg_type'] = "text";
				$u_data['last_time'] = date("Y-m-d H:i:s");
				$this -> room_dao -> update($u_data, $room -> id);
			}
		}


 	}

	function add_cs_msg($user_id, $msg, $reply_user_id) {
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Customer_service_line_room_dao', 'room_dao');

		$user = $this -> users_dao -> find_by_id($user_id);
		$room = $this -> room_dao -> find_by("user_id", $user -> id);

		$i_data = array();
		$i_data['msg'] = $msg;
		$i_data['msg_type'] = "text";
		$i_data['corp_id'] = 1;
		$i_data['is_cs'] = 1;
		$i_data['reply_user_id'] = $reply_user_id;
		$i_data['user_id'] = $user -> id;
		$this -> insert($i_data);

		// room data
		$u_data = array();
		$u_data['last_msg'] = $msg;
		$u_data['last_msg_type'] = "text";
		$u_data['last_time'] = date("Y-m-d H:i:s");
		$this -> room_dao -> update($u_data, $room -> id);
 	}

	function mark_read($user_id) {
		$list = $this -> get_unread($user_id);
		foreach($list as $each) {
			$this -> update(array(
				'is_read' =>  1
			), $each -> id);
		}
	}

	function get_unread($user_id) {
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('status', 1); // 已回覆
		$this -> db -> where('is_read', 0); // 未讀取
		$list = $this -> find_all();
		return $list;
	}

	function list_by_status($user_id, $status) {
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('status', $status);
		$this -> db -> from($this -> table_name . " as _m");
		$list = $this -> db -> get() -> result();
		return $list;
	}

	function list_all($user_id) {
		$this -> db -> where('user_id', $user_id);
		$this -> db -> from($this -> table_name . " as _m");
		$list = $this -> db -> get() -> result();
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
		$this -> db -> select('u.account as user_account');
		$this -> db -> select('u.user_name as user_name');

		// join
		$this -> ajax_from_join();

		// search always
		$this -> search_always($data);

		// search
		$this -> ajax_column_setup($columns, $search, $this -> alias_map);

		// order
		$this -> ajax_order_setup($order, $columns, $this -> alias_map);
		$this -> db -> order_by('id', 'asc');

		// limit
		$this -> db -> limit($limit, $start);

		// query results
		$query = $this -> db -> get();
		return $query -> result();
	}

	function search_always($data) {
		if(!empty($data['id'])) {
			$this -> db -> where("_m.id", $data['id']);
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("users as u", "u.id = _m.user_id", "left");
	}

}
?>
