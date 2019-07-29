<?php
class Chat_message_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('chat_message');

		$this -> alias_map = array(

 		);
	}

	function add_message($data) {
		$this -> load -> model('Chat_room_dao', 'chat_room_dao');
		$this -> load -> model('Users_dao', 'users_dao');

		$user = $this -> users_dao -> find_by_id($data['user_id']);
		$target_user = $this -> users_dao -> find_by_id($data['target_user_id']);
		$this -> chat_room_dao -> create_room($user, $target_user);

		$now = date("Y-m-d H:i:s");
		// room1
		$room = $this -> chat_room_dao -> find_my_room($user -> id, $target_user -> id);
		$data['room_id'] = $room -> id;
		$data['corp_id'] = $room -> corp_id;

		$last_id = $this -> insert($data);

		$this -> chat_room_dao -> update(array(
			'needs_notify' => 1,
			'last_message_id' => $last_id,
			'last_update_time' => $now,
			'last_message' => $data['message'],
			'last_message_type' => $data['message_type'],
			'last_emoji_name' => $data['emoji_name'],
		), $room -> id);

		// room2
		$room = $this -> chat_room_dao -> find_my_room($target_user -> id, $user -> id);
		$data['room_id'] = $room -> id;
		$last_id = $this -> insert($data);

		$this -> chat_room_dao -> update(array(
			'needs_notify' => 1,
			'last_message_id' => $last_id,
			'last_update_time' => $now,
			'last_message' => $data['message'],
			'last_message_type' => $data['message_type'],
			'last_emoji_name' => $data['emoji_name'],
		), $room -> id);
	}

	function list_down_messages($room_id, $length, $max_msg_id = 0, $is_up = FALSE) {
		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('tu.user_name');
		$this -> db -> select('tu.account as user_account');
		$this -> db -> select('tu.nick_name as nick_name');
		$this -> db -> select('tu.image_id as image_id');

		// from & join
		$this -> db -> from("{$this->table_name} as _m");
		$this -> db -> join("users tu", "tu.id = _m.user_id", "left");

		$this -> db -> limit($length);

		// search always
		$this -> db -> where('room_id', $room_id);

		// order
		if(empty($max_msg_id)) {
			$this -> db -> order_by('_m.id', 'desc');
		} else {
			if($is_up == TRUE) {
				$this -> db -> where("( _m.id < {$max_msg_id} )");
			 	$this -> db -> order_by('_m.id', 'desc');
			} else {
				$this -> db -> where("( _m.id > {$max_msg_id} )");
				$this -> db -> order_by('_m.id', 'asc');
			}
		}

		// query results
		$query = $this -> db -> get();
		$list = $query -> result();

		if(empty($max_msg_id) || $is_up == TRUE) {
			$list = array_reverse($list);
		}
		$list = $this -> format_target_list($list);
		return $list;
	}

	function find_me($id) {
		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('u.user_name');
		// $this -> db -> select('iu.account as in_account');
		// $this -> db -> select('ou.account as out_account');

		// join
		$this -> ajax_from_join();
		$this -> db -> where('_m.id', $id);

		// query results
		$query = $this -> db -> get();
		$list = $query -> result();
		$list = $this -> format_list($list);
		return $list[0];
	}

	function format_target_list($list) {
		foreach($list as $each) {
			$each -> image_url = '';
			$each -> image_url_thumb = '';
			if($each -> image_id > 0) {
				$each -> image_url = base_url("api/images/get/") . $each -> image_id;
				$each -> image_url_thumb = base_url("api/images/get/") . $each -> image_id . "/thumb";
			}
		}
		return $list;
	}

	function format_list($list) {
		foreach($list as $each) {
			$each -> image_url = '';
			if($each -> image_id > 0) {
				$each -> image_url = base_url("api/images/get/") . $each -> image_id;
			}
		}
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
		$this -> db -> select('u.user_name');
		// $this -> db -> select('iu.account as in_account');
		// $this -> db -> select('ou.account as out_account');

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
		$list = $query -> result();
		$list = $this -> format_list($list);
		return $list;
	}

	function list_by_user_and_query($data) {
		$start = $data['start'];
		$limit = $data['length'];
		$columns = $data['columns'];
		$search = $data['search'];
		$order = $data['order'];

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('u.user_name');
		// $this -> db -> select('iu.account as in_account');
		// $this -> db -> select('ou.account as out_account');

		// join

		// search always


		// order
		$this -> db -> order_by('id', 'asc');

		// query results
		$query = $this -> db -> get();
		$list = $query -> result();
		$list = $this -> format_list($list);
		return $list;
	}

	function search_always($data) {
		// if(isset($data['status']) && strlen($data['status']) > 0) {
		// 	$this -> db -> where('_m.status', $data['status']);
		// }
		//
		// if(isset($data['user_id']) && $data['user_id'] > -1) {
		// 	$this -> db -> where('_m.user_id', $data['user_id']);
		// }
		//
		// if(isset($data['out_user_id']) && $data['out_user_id'] > -1) {
		// 	$this -> db -> where('_m.out_user_id', $data['out_user_id']);
		// }
		//
		// if(isset($data['in_user_id']) && $data['in_user_id'] > -1) {
		// 	$this -> db -> where('_m.in_user_id', $data['in_user_id']);
		// }
		//
		// if(isset($data['id']) && $data['id'] > -1) {
		// 	$this -> db -> where('_m.id', $data['id']);
		// }
		//
		// if(isset($data['corp_id']) && $data['corp_id'] > -1) {
		// 	$this -> db -> where('_m.corp_id', $data['corp_id']);
		// }
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("users u", "u.id = _m.user_id", "left");
		// $this -> db -> join("users iu", "iu.id = _m.in_user_id", "left");
		// $this -> db -> join("users ou", "ou.id = _m.out_user_id", "left");
	}

	function find_user_talks($data, $is_count = FALSE) {

		$user_id = $data['user_id'];
		$start = $data['start'];
		$limit = $data['length'];

		// select
		$this -> db -> from("$this->table_name as _m");

		$this -> db -> select('u.nick_name as nick_name');
		$this -> db -> select('us.nick_name as u_nick_name');
		$this -> db -> select('_m.create_time');
		$this -> db -> select('_m.message');

		$this -> db -> join("users u", "u.id = _m.target_user_id", "left");
		$this -> db -> join("users us", "us.id = _m.user_id", "left");

		$this -> db -> order_by('_m.create_time');

		if(!$is_count) {
			$this -> db -> limit($limit, $start);
		}

		$this -> db -> where('user_id',$user_id);
		$this -> db -> or_where('target_user_id',$user_id);


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
