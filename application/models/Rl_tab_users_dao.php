<?php
class Rl_tab_users_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('rl_tab_users');

		$this -> alias_map = array(

		);
	}

	function delete_expired($minutes = 5) {
		$sql = "delete from {$this->table_name} where TIMESTAMPDIFF(MINUTE,update_time,NOW()) > {$minutes}";
		$this -> db -> query($sql);
	}

	function find_all_by_tab_id($tab_id, $hall_id = 0) {
		$this -> db -> where('tab_id', $tab_id);
		$this -> db -> where('hall_id', $hall_id);
		$list = $this -> find_all();
		return $list;
	}

	function find_all_user_by_tab_id($tab_id, $hall_id = 0) {
		$this -> db -> select("_m.*");
		$this -> db -> select("u.user_name");
		$this -> db -> select("u.account as user_account");
		$this -> db -> where('_m.tab_id', $tab_id);
		$this -> db -> where('_m.hall_id', $hall_id);

		$this -> db -> from($this -> table_name . " as _m");
		$this -> db -> join("users u", "u.id = _m.user_id", "left");
		$list = $this -> db -> get() -> result();
		return $list;
	}

	function find_all_user_info_by_tab_id($tab_id, $hall_id = 0) {
		$this -> db -> select("_m.user_id");

		$this -> db -> where('_m.tab_id', $tab_id);
		$this -> db -> where('_m.hall_id', $hall_id);

		$this -> db -> from($this -> table_name . " as _m");
		// $this -> db -> join("users u", "u.id = _m.user_id", "left");
		$list = $this -> db -> get() -> result();
		return $list;
	}

	function find_all_user_info_with_samt_by_tab_id($tab_id, $hall_id = 0) {
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');

		$this -> db -> select("_m.user_id");
		$this -> db -> select("u.user_name");
		$this -> db -> select("u.account");
		$this -> db -> select("u.nick_name");

		$this -> db -> where('_m.tab_id', $tab_id);
		$this -> db -> where('_m.hall_id', $hall_id);

		$this -> db -> from($this -> table_name . " as _m");
		$this -> db -> join("users u", "u.id = _m.user_id", "left");
		$list = $this -> db -> get() -> result();

		foreach($list as $user) {
			$samt = $this -> wtx_dao -> get_sum_amt($user -> user_id);
			$user -> samt = $samt;
		}

		return $list;
	}

	function find_all_by_tab_and_user($tab_id, $user_id, $hall_id = 0) {
		$this -> db -> select("_m.*");
		$this -> db -> select("u.user_name");
		$this -> db -> select("u.account as user_account");
		$this -> db -> where('_m.tab_id', $tab_id);
		$this -> db -> where('_m.hall_id', $hall_id);
		$this -> db -> where('_m.user_id', $user_id);

		$this -> db -> from($this -> table_name . " as _m");
		$this -> db -> join("users u", "u.id = _m.user_id", "left");
		$list = $this -> db -> get() -> result();
		return $list;
	}

	function find_all_by_user_id($user_id, $hall_id = 0) {
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('hall_id', $hall_id);
		$list = $this -> find_all();
		return $list;
	}

	function find_by_user_id($user_id, $hall_id = 0) {
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('hall_id', $hall_id);
		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	function delete_by_tab_id($tab_id) {
		$this -> db -> query("delete from {$this->tab_name} where tab_id = $tab_id ");
	}

	function enter_tab($tab_id, $user_id, $hall_id) {
		$this -> load -> model('Rl_tab_status_dao', 'nn_tab_status_dao');

		$last_id = $this -> insert(array(
			'corp_id' => 1,
			'tab_id' => $tab_id,
			'user_id' => $user_id,
			'hall_id' => $hall_id,
			'last_update_time' => date("Y-m-d H:i:s")
		));

		// update users
		$this -> update_user_count($tab_id, $hall_id);
	}

	function update_time($tab_id, $user_id, $hall_id) {
		$list = $this -> find_all_by_tab_and_user($tab_id, $user_id, $hall_id);
		foreach($list as $each) {
			$this -> update(array(
				'last_update_time' => date("Y-m-d H:i:s")
			), $each -> id);
		}
	}

	function leave_tab($tab_id, $user_id, $hall_id) {
		$this -> load -> model('Rl_tab_status_dao', 'nn_tab_status_dao');

		$this->db->where('tab_id', $tab_id);
		$this->db->where('user_id', $user_id);
		$this->db->where('hall_id', $hall_id);
		$this->db->delete($this -> table_name);

		// update users
		$this -> update_user_count($tab_id, $hall_id);
	}

	function update_user_count($tab_id, $hall_id) {
		$this -> load -> model('Rl_tab_users_dao', 'nn_tab_users_dao');

		// update users
		$tb_users = $this -> nn_tab_users_dao -> find_all_by_tab_id($tab_id, $hall_id);
		$this -> nn_tab_status_dao -> set_user_count($tab_id, $hall_id, count($tb_users));
	}

	function random_one() {
		$list = $this -> db -> query("select * from {$this->table_name} order by rand() limit 1 ") -> result();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}
}
?>
