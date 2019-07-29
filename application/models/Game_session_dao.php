<?php
class Game_session_dao extends MY_Model {

	function __construct() {
		parent::__construct();
		// initialize table name
		$this -> set_table_name("game_session");

		$this -> alias_map = array(
		);
	}

	function create_session() {

	}

}
?>
