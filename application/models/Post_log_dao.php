<?php
class Post_log_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('post_log');
	}
	
	function logs() {
		$this -> db -> order_by('id', 'desc');
		$this -> db -> limit(20);
		return $this -> find_all();
	}
}
?>