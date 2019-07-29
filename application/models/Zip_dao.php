<?php
class Zip_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('zip');
	}

	function find_all_by_order_id($order_id) {
		$this -> db -> select('_m.*');
		$this -> db -> select('os.status_name,os.status_key');
		$this -> db -> select('m.member_name,m.account as member_account');
		$this -> db -> select('u.user_name,u.account as user_account');

		$this -> db -> from($this -> table_name . " as _m");

		$this -> db -> join('order_status as os', 'os.id = _m.status', 'left');
		$this -> db -> join('members as m', 'm.id = _m.member_id', 'left');
		$this -> db -> join('users as u', 'u.id = _m.user_id', 'left');

		$this -> db -> where('_m.order_id', $order_id);

		$this -> db -> order_by('id', 'asc');

		$query = $this -> db -> get();
		$list =  $query -> result();
		return $list;
	}

	function find_all_city($only_city = FALSE) {
		$sql = "SELECT distinct city FROM zip order by code asc;";
		$list = $this -> db -> query($sql) -> result();

		if(!$only_city) {
			foreach($list as $each) {
				$each -> district_list = $this -> find_district_by_city($each -> city);
			}
		}

		return $list;
	}

	function find_district_by_city($city) {
			$sql = "SELECT district, code FROM zip where city = '$city' order by code asc;";
			return $this -> db -> query($sql) -> result();
	}

}
?>
