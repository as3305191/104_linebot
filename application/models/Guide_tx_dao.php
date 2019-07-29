<?php
class Guide_tx_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('guide_tx');

		$this -> alias_map = array(

		);
	}

	function find_all_by_guide_id($guide_id) {
		$this -> db -> where('_m.guide_id', $guide_id);
		$this -> db -> where('_m.bet_type', 1);

		$this -> db -> from($this -> table_name . ' as _m');

		$this -> db -> order_by('_m.id', 'asc');

		$list = $this -> db -> get() -> result();
		return $list;
	}

	function find_last_tx($guide_id) {
		$this -> db -> where('guide_id', $guide_id);
		$this -> db -> order_by('id', 'desc');
		$list = $this -> db -> get($this -> table_name) -> result();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	function find_last_tx_count($guide_id, $limit = 1) {
		$this -> db -> where('guide_id', $guide_id);
		$this -> db -> order_by('id', 'desc');
		$this -> db -> limit($limit);
		$list = $this -> db -> get($this -> table_name) -> result();
		return $list;
	}

	function cont_win_count($guide_id) {
		$this -> db -> where('guide_id', $guide_id);
		$this -> db -> order_by('id', 'desc');
		$list = $this -> db -> get($this -> table_name) -> result();
		$cont_win_count = 0;
		$has_fault = FALSE;
		if(count($list) > 0) {
			// at lesat one history
			// echo json_encode($list);
			$last_tx = $list[0];
			if($last_tx -> is_win == 1) {
				// only last tx is 1
				foreach($list as $each) {
					if($each -> is_win == 1 && !$has_fault) {
						// only last tx is win
						$cont_win_count++;
					} else {
						$has_fault = TRUE;
					}
				}
			}
		}
		return $cont_win_count;
	}

	function cont_loose_count($guide_id) {
		$this -> db -> where('guide_id', $guide_id);
		$this -> db -> order_by('id', 'desc');
		$list = $this -> db -> get($this -> table_name) -> result();
		$cont_loose_count = 0;
		$has_fault = FALSE;
		if(count($list) > 0) {
			// at lesat one history
			// echo json_encode($list);
			$last_tx = $list[0];
			if($last_tx -> is_win == -1) {
				// only last tx is 1
				foreach($list as $each) {
					if($each -> is_win == -1 && !$has_fault) {
						// only last tx is win
						$cont_loose_count++;
					} else {
						$has_fault = TRUE;
					}
				}
			}
		}
		return $cont_loose_count;
	}

	function check3times_win_loose($guide_id) {
		$this -> db -> where('guide_id', $guide_id);
		$this -> db -> order_by('id', 'desc');
		$list = $this -> db -> get($this -> table_name) -> result();
		$cont_loose_count = 0;
		$first_win = FALSE;
		$next_tollge = 'loose';

		$check_count = 0;

		if(count($list) < 6) {
			return FALSE;
		}

		$is_fault = FALSE;

		if(count($list) > 0) {
			$last_tx = $list[0];
			if($last_tx -> is_win == -1) {

				foreach($list as $each) {
					if($is_fault) { // stop and return
						return $check_count;
					}

					if(!$first_win) {
						if($each -> is_win == 1) {
							// first win
							$first_win = TRUE;
							$next_tollge == 'loose';
							$check_count = 1;
						}
					} else if($first_win) {
						// has first win
						if($next_tollge == 'loose') { // loose toggle
							if($each -> is_win == -1) {
								$next_tollge = 'win';
							} else {
								$is_fault = TRUE;
							}
						} else if($next_tollge == 'win') { // win toggle
							if($each -> is_win == 1) {
								$check_count++;
								$next_tollge = 'loose';
							} else {
								$is_fault = TRUE;
							}
						}
						// end has first win
					}
				}
			}
		}
		return $check_count;
	}

	function query_ajax($data) {
		$start = $data['start'];
		$limit = $data['length'];
		$columns = $data['columns'];
		$search = $data['search'];
		$order = $data['order'];

		// select
		$this -> db -> select('_m.*');
		// $this -> db -> select('gs.status_name');
		// $this -> db -> select('c.company_name');

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
		// $this -> db -> where('_m.status', 0);
		// if(isset($data['user_role_id']) && $data['user_role_id'] > -1) {
		// 	$this -> db -> where('user_role_id', $data['user_role_id']);
		// }
		// if(isset($data['role_id']) && $data['role_id'] > -1) {
		// 	$this -> db -> where('_m.role_id', $data['role_id']);
		// }

		if(isset($data['id']) && $data['id'] > -1) {
			$this -> db -> where('_m.id', $data['id']);
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		// $this -> db -> join("guide_status gs", "gs.id = _m.status", "left");
		// $this -> db -> join("company c", "c.id = _m.com_id", "left");
		// $this -> db -> join("users iu", "iu.id = _m.intro_id", "left");
		// $this -> db -> join("roles r", "r.id = _m.role_id", "left");
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
