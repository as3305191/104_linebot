<?php
class Chat_room_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('chat_room');

		$this -> alias_map = array(

 		);
	}

	function list_by_user_and_query($user_id, $query) {

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('fu.user_name as friend_user_name');
		$this -> db -> select('fu.nick_name as friend_nick_name');
		$this -> db -> select('fu.image_id as friend_image_id');
		// $this -> db -> select('iu.account as in_account');
		// $this -> db -> select('ou.account as out_account');

		// from & join
		$this -> db -> from("{$this->table_name} as _m");
		$this -> db -> join("users fu", "fu.id = _m.friend_user_id", "left");
		if(!empty($query)) {
			// $this -> db -> join("users u", "u.id = _m.user_id", "left");
			$this -> db -> where("(fu.nick_name like '%{$query}%')");
		}

		// search always
		$this -> db -> where('user_id', $user_id);

		// order
		$this -> db -> order_by('last_update_time', 'desc');

		// query results
		$query = $this -> db -> get();
		$list = $query -> result();
		return $list;
	}

	function create_room($user_1, $user_2) {
		$now = date("Y-m-d H:i:s");

		// 1 -> 2
		$item = $this -> find_my_room($user_1 -> id, $user_2 -> id);
		if(empty($item)) {
			// insert
			$i = array();
			$i['corp_id'] = $user_1 -> corp_id;
			$i['user_id'] = $user_1 -> id;
			$i['friend_user_id'] = $user_2 -> id;
			$i['last_update_time'] = $now;
			$this -> insert($i);
 		}
		// 2 -> 1
		$item = $this -> find_my_room($user_2 -> id, $user_1 -> id);
		if(empty($item)) {
			// insert
			$i = array();
			$i['corp_id'] = $user_2 -> corp_id;
			$i['user_id'] = $user_2 -> id;
			$i['friend_user_id'] = $user_1 -> id;
			$i['last_update_time'] = $now;
			$this -> insert($i);
 		}
	}

	function find_my_room($user_id, $friend_user_id) {
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('friend_user_id', $friend_user_id);
		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}
}
?>
