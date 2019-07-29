<?php
class Transfer_gift_friends_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('transfer_gift_friends');

		$this -> alias_map = array(

 		);
	}

	function add_friend($user_id, $f_user_id) {
		$item = $this -> find_by_me_and_friend($user_id, $f_user_id);
		if(empty($item )) {
			$this -> insert(array(
				'user_id' => $user_id,
				'friend_user_id' => $f_user_id
			));
		}
	}

	function find_by_me_and_friend($user_id, $f_user_id) {
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('friend_user_id', $f_user_id);
		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	function list_all($user_id) {

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('u.account as friend_account');
		$this -> db -> select('u.user_name as friend_user_name');
		$this -> db -> select('u.nick_name as friend_nick_name');

		// join
		$this -> ajax_from_join();

		// search always
		$this -> db -> where("(user_id = $user_id)");

		// order
		$this -> db -> order_by('id', 'desc');


		// query results
		$query = $this -> db -> get();
		return $query -> result();
	}



	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("users u", "u.id = _m.friend_user_id", "left");
	}
}
?>
