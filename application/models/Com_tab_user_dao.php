<?php
class Com_tab_user_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('com_tab_user');

		$this -> alias_map = array(

		);
	}

	function find_all_by_com_id($user_id, $com_id) {
		$this -> db -> where('com_id', $com_id);
		$this -> db -> where('user_id', $user_id);
		$this -> db -> order_by('tab_id', 'asc');
		$list=  $this -> find_all();
		return $list;
	}

	function find_all_com_tab_by_com_id($user_id, $com_id) {
		$this -> db -> select('_m.*');
		$this -> db -> select('ctu.current_percent as cp_val');
		$this -> db -> where('_m.com_id', $com_id);
		$this -> db -> order_by('tab_id', 'asc');
		$this -> db -> from('com_tab _m');
		$this -> db -> join('com_tab_user ctu', "ctu.tab_id = _m.tab_id and ctu.com_id = _m.com_id and ctu.user_id = $user_id", 'left');
		$list=  $this -> db -> get() -> result();
		return $list;
	}

	function create_by_com_and_tab($user_id, $com_id, $tab_id, $current_percent) {
		$i_data['com_id'] = $com_id;
		$i_data['user_id'] = $user_id;
		$i_data['tab_id'] = $tab_id;
		$i_data['current_percent'] = $current_percent;
		$this -> insert($i_data);
	}

	function find_by_com_and_tab($user_id, $com_id, $tab_id) {
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('com_id', $com_id);
		$this -> db -> where('tab_id', $tab_id);
		$list=  $this -> find_all();
		// echo $this -> db -> 	last_query() . '<br/>';
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}
}
?>
