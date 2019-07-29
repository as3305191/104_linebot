<?php
class App_version_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('app_version');
	}

	function check_version($app_name, $version) {
		$item = $this -> find_by('key', $app_name);
		if(!empty($item)) {
			if($item -> version == $version) {
				return TRUE;
			}
		}
		return FALSE;

	}

}
?>
