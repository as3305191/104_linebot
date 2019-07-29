<?php
class User_msg extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('User_msg_dao', 'dao');
	}

	public function list_all() {
		$res = array();
		$res['success'] = TRUE;
		$user_id = $this -> get_post("user_id");
		$list = $this -> dao -> find_all_order($user_id);
		$res['list'] = $list;

		$this -> to_json($res);
	}

	public function delete() {
		$res = array();
		$res['success'] = TRUE;
		$msg_id = $this -> get_post("msg_id");
		$this -> dao -> delete($msg_id);
		$this -> to_json($res);
	}
}
?>
