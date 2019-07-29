<?php
class Cs_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('cs_talk');

		$this -> alias_map = array(
			// 'account' => '_m.account',
			// 'user_name' => '_m.user_name'
		);
	}

	function last_id($user_id) {
		$this -> db -> order_by('id', 'desc');
		$this -> db -> limit(1);
		$list = $this -> find_all();
		if(count($list) > 0) {
			$item = $list[0];
			return $item -> id;
		}
		return 0;
	}

	function list_all($user_id) {
		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('u.account as user_account');
		$this -> db -> select('su.account as send_user_account, su.image_id as send_user_image_id');

		$this -> db -> where('user_id', $user_id);
		$this -> db -> order_by('_m.id', 'asc');

		// join
		$this -> ajax_from_join();

		// search always
		$this -> search_always(array());

		$list = $query = $this -> db -> get() -> result();
		foreach($list as $each) {
			if(!empty($each -> send_user_image_id)) {
				$each -> user_image_url = get_thumb_url($each -> send_user_image_id);
			} else {
				$each -> user_image_url = base_url('img/demo/login/logo.png');
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
		$this -> db -> select('u.account as user_account');
		$this -> db -> select('su.account as send_user_account');


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
		if(isset($data['user_role_id']) && $data['user_role_id'] > -1) {
			$this -> db -> where('user_role_id', $data['user_role_id']);
		}
		if(isset($data['role_id']) && $data['role_id'] > -1) {
			$this -> db -> where('_m.role_id', $data['role_id']);
		}

		if(!empty($data['intro_id'])) {
			$this -> db -> where('_m.intro_id', $data['intro_id']);
		}

		if(isset($data['id']) && $data['id'] > -1) {
			$this -> db -> where('_m.id', $data['id']);
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("users u", "u.id = _m.user_id", "left");
		$this -> db -> join("users su", "su.id = _m.send_user_id", "left");
	}


	function find_by_account($account) {
		$this -> db -> where('account', $account);
		$query = $this -> db -> get($this -> table_name);
		foreach ($query->result() as $row){
		    return $row;
		}
		return NULL;
	}

	function nav_list() {
		$this -> load -> model('Nav_dao', 'nav_dao');
		$lv1_list = $this -> nav_dao -> find_all_by_parent_id(0);
		$sub_list = $this -> nav_dao -> find_all_not_lv1();

		$map = array();
		foreach($lv1_list as $each) {
			$map[$each->id] = $each;
			$each -> sub_list = array();
		}

		// add sublist
		foreach($sub_list as $each) {
			if(isset($map[$each -> parent_id])) {
				$lv1 = $map[$each -> parent_id];
				array_push($lv1 -> sub_list, $each);
			}
		}

		foreach($map as $key => $each) {
			if(count($each -> sub_list) == 0 && empty($each -> base_path)) {
				unset($map[$key]);
			}
		}

		return $map;
	}

	function nav_list_with_role_id($role_id) {
		$this -> load -> model('Nav_dao', 'nav_dao');
		$lv1_list = $this -> nav_dao -> find_all_by_parent_id(0);
		$sub_list = $this -> nav_dao -> find_all_not_lv1();

		$sql = "select * from role_power where role_id = $role_id";
		$rp_list = $this -> db -> query($sql) -> result();

		$map = array();
		foreach($lv1_list as $each) {
			$map[$each->id] = $each;
			foreach($rp_list as $rp) {
				if($rp -> nav_id == $each -> id) {
					$each -> rp = $rp;
				}
			}
			$each -> sub_list = array();
		}

		// add sublist
		foreach($sub_list as $each) {
			if(isset($map[$each -> parent_id])) {
				$lv1 = $map[$each -> parent_id];
				foreach($rp_list as $rp) {
					if($rp -> nav_id == $each -> id) {
						$each -> rp = $rp;
					}
				}
				array_push($lv1 -> sub_list, $each);
			}
		}

		foreach($map as $key => $each) {
			if(count($each -> sub_list) == 0 && empty($each -> base_path)) {
				unset($map[$key]);
			}
		}

		return $map;
	}

	function nav_list_by_role_id($role_id) {
		$this -> load -> model('Nav_dao', 'nav_dao');
		$lv1_list = $this -> nav_dao -> find_all_by_parent_id(0);
		$sub_list = $this -> nav_dao -> find_all_not_lv1();

		$sql = "select * from role_power where role_id = $role_id";
		$rp_list = $this -> db -> query($sql) -> result();

		$map = array();
		foreach($lv1_list as $each) {
			foreach($rp_list as $rp) {
				if($rp -> nav_id == $each -> id) {
					$each -> rp = $rp;
					$map[$each->id] = $each;
				}
			}
			$each -> sub_list = array();
		}

		// add sublist
		foreach($sub_list as $each) {
			if(isset($map[$each -> parent_id])) {
				$lv1 = $map[$each -> parent_id];
				foreach($rp_list as $rp) {
					if($rp -> nav_id == $each -> id) {
						$each -> rp = $rp;
						array_push($lv1 -> sub_list, $each);
					}
				}
			}
		}

		foreach($map as $key => $each) {
			if(count($each -> sub_list) == 0 && empty($each -> base_path)) {
				unset($map[$key]);
			}
		}

		return $map;
	}

	function find_menu_list() {
		$sql = " SELECT m.id as main_id, m.nav_name as main_name, m.icon as main_icon, m.key as main_key, s.* "
				. " FROM nav_main as m  "
				. " left join nav_sub as s on m.id = s.nav_main_id "
				. " order by m.pos, s.pos ";
		$res = $this -> query_for_list($sql);

		$data = array();
		foreach ($res as $row) {
			$main_id = $row -> main_id;
			$nav_sub_id = $row -> id;
			if (!empty($nav_sub_id)) {
				if (empty($data[$main_id])) {
					$main_obj['nav_name'] = $row -> main_name;
					$main_obj['icon'] = $row -> main_icon;
					$main_obj['key'] = $row -> main_key;
					$m['main'] = $main_obj;
					$m['sub_list'] = array();
					$data[$main_id] = $m;
				}
				unset($row -> main_id);
				unset($row -> main_name);
				unset($row -> main_icon);
				array_push($data[$main_id]['sub_list'], $row);
			}
		}
		return $data;
	}

	function session_user() {
		$user_id = $this -> session -> userdata('user_id');
		return $this -> find_by_id($user_id);
	}

	function find_all_user_roles() {
		return $this -> db -> get('user_role') -> result();
	}

	function find_all_roles() {
		return $this -> db -> get('roles') -> result();
	}

	function find_group_users($id) {
		$list = array();
		$u = $this -> find_by_id($id);
		if(!empty($u) && !empty($u -> group_id)) {
			$this -> db -> where('status', 0);
			$this -> db -> where('group_id', $u -> group_id);
			$list = $this -> db -> get($this -> table_name) -> result();

		}
		return $list;
	}

}
?>
