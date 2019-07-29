<?php
class Customer_service extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Customer_service_dao', 'cs_dao');
		$this -> load -> model('Users_dao', 'users_dao');
	}

	public function create() {
		$res = array();
		$res['success'] = TRUE;
		$user_id = $this -> get_post("user_id");
		$question = $this -> get_post("question");
		$user = $this -> users_dao -> find_by_id($user_id);
		if(!empty($user)) {
			$i = array();
			$i['corp_id'] = $user -> corp_id;
			$i['user_id'] = $user -> id;
			$i['question'] = $question;
			$last_id = $this -> cs_dao -> insert($i);
			$res['last_id'] = $last_id;
		} else {
			$res['error_msg'] = "使用者不存在";
		}

		$this -> to_json($res);
	}

	public function check_unread() {
		$res = array();
		$res['success'] = TRUE;
		$user_id = $this -> get_post("user_id");
		$list = $this -> cs_dao -> get_unread($user_id);
		$res['unread'] = count($list);
		foreach($list as $each) {
			$each -> image_url = !empty($each -> image_id) ? IMG_URL . $each -> image_id : '';
			$each -> image_url_thumb = !empty($each -> image_id) ? IMG_URL . $each -> image_id . '/thumb' : '';
		}
		$res['list'] = $list;
		$this -> to_json($res);
	}

	public function create_questoion() {
		$res = array();

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];
		$question = $this -> get_post("question");
		$image_id = $this -> get_post("image_id");

		$user = $this -> users_dao -> find_by_id($user_id);
		if(!empty($user)) {
			$i = array();
			$i['corp_id'] = $user -> corp_id;
			$i['user_id'] = $user -> id;
			$i['question'] = $question;
			if(!empty($image_id)){
				$i['image_id'] = $image_id;
			}

			$last_id = $this -> cs_dao -> insert($i);

			$res['success'] = TRUE;
			$res['last_id'] = $last_id;
		} else {
			$res['error_msg'] = "使用者不存在";
		}

		$this -> to_json($res);
	}
}
?>
