<?php
class Chat_friends_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('chat_friends');

		$this -> alias_map = array(

 		);
	}

	function list_by_user_and_query($user_id, $query) {

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('fu.user_name as friend_user_name');
		$this -> db -> select('fu.account as friend_user_account');
		$this -> db -> select('fu.nick_name as friend_nick_name');
		$this -> db -> select('fu.image_id as friend_image_id');
		// $this -> db -> select('iu.account as in_account');
		// $this -> db -> select('ou.account as out_account');

		// from & join
		$this -> db -> from("{$this->table_name} as _m");
		$this -> db -> join("users fu", "fu.id = _m.friend_user_id", "left");

		if(!empty($query)) {
			$this -> db -> where("(fu.nick_name like '%{$query}%')");
		}

		// search always
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('is_remove', 0);

		// order
		$this -> db -> order_by('id', 'desc');

		// query results
		$query = $this -> db -> get();
		$list = $query -> result();
		return $list;
	}

	function add_friend($user_1, $user_2) {
		// 1 -> 2
		$item = $this -> find_my_friend($user_1 -> id, $user_2 -> id);
		if(empty($item)) {
			// insert
			$i = array();
			$i['corp_id'] = $user_1 -> corp_id;
			$i['user_id'] = $user_1 -> id;
			$i['friend_user_id'] = $user_2 -> id;
			$this -> insert($i);
 		} else {
			if(!empty($item)) {
				$this -> update(array(
					'is_block' => '0',
					'is_remove' => '0'
				), $item -> id);
			}
		}
		// 2 -> 1
		$item = $this -> find_my_friend($user_2 -> id, $user_1 -> id);
		if(empty($item)) {
			// insert
			$i = array();
			$i['corp_id'] = $user_2 -> corp_id;
			$i['user_id'] = $user_2 -> id;
			$i['friend_user_id'] = $user_1 -> id;
			$this -> insert($i);
 		} else {
			if(!empty($item)) {
				$this -> update(array(
					'is_block' => '0',
					'is_remove' => '0'
				), $item -> id);
			}
		}

		// return room
	}

	function find_my_friend($user_id, $friend_user_id) {
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('friend_user_id', $friend_user_id);
		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	function block_friend($user_id, $friend_user_id) {
		$this -> load -> model('Chat_room_dao', 'chat_room_dao');

		$item = $this -> find_my_friend($user_id, $friend_user_id);
		if(!empty($item)) {
			$this -> update(array(
				'is_block' => '1'
			), $item -> id);

			$room = $this -> chat_room_dao -> find_my_room($user_id, $friend_user_id);
			if(!empty($room)) {
				$this -> chat_room_dao -> update(array(
					'is_block' => '1'
				), $room -> id);
			}
		}
	}

	function remove_friend($user_id, $friend_user_id) {
		$this -> load -> model('Chat_room_dao', 'chat_room_dao');

		$item = $this -> find_my_friend($user_id, $friend_user_id);
		if(!empty($item)) {
			$this -> update(array(
				'is_remove' => '1'
			), $item -> id);
		}
	}

	function find_user_friends($data, $is_count = FALSE) {

		$user_id = $data['user_id'];
		$start = $data['start'];
		$limit = $data['length'];

		// select
		$this -> db -> from("$this->table_name as _m");

		$this -> db -> select('u.nick_name as nick_name');
		$this -> db -> select('_m.create_time');
		$this -> db -> select('_m.is_block');

		$this -> db -> join("users u", "u.id = _m.friend_user_id", "left");
		$this -> db -> order_by('_m.create_time');

		if(!$is_count) {
			$this -> db -> limit($limit, $start);
		}

		$this -> db -> where('user_id',$user_id);


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
