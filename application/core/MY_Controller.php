<?php
class MY_Base_Controller extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> helper('common');
		$this -> load -> library('session');


		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');

		// disable cache for back button
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, application/json");
		header('Access-Control-Max-Age: 86400');

		date_default_timezone_set("Asia/Taipei");

		$lang = $this -> session -> userdata('lang');
		if(empty($lang)) {
			$lang = 'cht';
			$this -> session -> set_userdata('lang', $lang);
		}
	}

	function get_header($key) {
		return $this -> input -> get_request_header($key);
	}

	function get_post($key) {
		return $this -> input -> post($key);
	}

	function get_get($key) {
		return $this -> input -> get($key);
	}

	function get_get_post($key) {
		$val = $this -> get_get($key);
		if ($val === FALSE) {
			$val = $this -> get_post($key);
		}
		return $val;
	}

	function to_json($json_data) {
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($json_data);
	}

	public function get_posts($post_array, $bypass_empty = FALSE) {
		$i_data = array();
		foreach ($post_array as $each) {
			if($bypass_empty) {
				if(!empty($this -> get_post($each))) {
					$i_data[$each] = $this -> get_post($each);
				}
			} else {
				$i_data[$each] = $this -> get_post($each);
			}
		}
		return $i_data;
	}

	public function get_gets($post_array) {
		$i_data = array();
		foreach ($post_array as $each) {
			$i_data[$each] = $this -> get_get($each);
		}
		return $i_data;
	}

	public function get_get_posts($post_array) {
		$i_data = array();
		foreach ($post_array as $each) {
			$val = $this -> get_get_post($each);
			if (!($val === FALSE)) {
				$i_data[$each] = $val;
			}
		}
		return $i_data;
	}

	// resize
	public function resize($img_path, $width = 500, $height = 500) {
		$config['image_library'] = 'gd2';
		$config['source_image'] = $img_path;
		$config['create_thumb'] = FALSE;
		$config['maintain_ratio'] = TRUE;
		$config['width'] = $width;
		$config['height'] = $height;

		$this -> load -> library('image_lib', $config);

		$this -> image_lib -> resize();
	}

	public function check_dir($dir) {
		if (!file_exists($dir)) {
			mkdir($dir);
		}
	}

	public function setup_user_data($data) {
		$user_id = $this -> session -> userdata('user_id');
		$s_uid = $this -> session -> userdata('s_uid');
		$user = $this -> users_dao -> find_by_id($user_id);
		// echo $user -> token . '-';
		// echo $s_uid;
		// if(empty($user_id)|| $user -> token != $s_uid) {
		if(empty($user_id)) {

			if ($this -> input ->is_ajax_request()) {
				echo "<script>window.location.reload();</script>";
			} else {
				redirect("app/login/logout");
			}
		} else {
			$data['login_user_id'] = $user_id;
			$data['l_user'] = $user;
		}



		$corp = $this -> corp_dao -> find_by_id($user -> corp_id);
		// update session
		$this -> session -> set_userdata('corp', $corp);
		$data['corp'] = $corp;

		return $data;
	}

	public function get_payload() {
		$auth = $this -> get_header('Authorization');
		$payload = jwt_decode($auth, "jihad");
		return $payload;
	}


}

class MY_Base_NoSessionController extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> helper('common');


		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');

		// disable cache for back button
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Access-Control-Allow-Origin: *");

		date_default_timezone_set("Asia/Taipei");

	}

	function get_header($key) {
		return $this -> input -> get_request_header($key);
	}

	function get_post($key) {
		return $this -> input -> post($key);
	}

	function get_get($key) {
		return $this -> input -> get($key);
	}

	function get_get_post($key) {
		$val = $this -> get_get($key);
		if ($val === FALSE) {
			$val = $this -> get_post($key);
		}
		return $val;
	}

	function to_json($json_data) {
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($json_data);
	}

	public function get_posts($post_array, $bypass_empty = FALSE) {
		$i_data = array();
		foreach ($post_array as $each) {
			if($bypass_empty) {
				if(!empty($this -> get_post($each))) {
					$i_data[$each] = $this -> get_post($each);
				}
			} else {
				$i_data[$each] = $this -> get_post($each);
			}
		}
		return $i_data;
	}

	public function get_gets($post_array) {
		$i_data = array();
		foreach ($post_array as $each) {
			$i_data[$each] = $this -> get_get($each);
		}
		return $i_data;
	}

	public function get_get_posts($post_array) {
		$i_data = array();
		foreach ($post_array as $each) {
			$val = $this -> get_get_post($each);
			if (!($val === FALSE)) {
				$i_data[$each] = $val;
			}
		}
		return $i_data;
	}

	// resize
	public function resize($img_path, $width = 500, $height = 500) {
		$config['image_library'] = 'gd2';
		$config['source_image'] = $img_path;
		$config['create_thumb'] = FALSE;
		$config['maintain_ratio'] = TRUE;
		$config['width'] = $width;
		$config['height'] = $height;

		$this -> load -> library('image_lib', $config);

		$this -> image_lib -> resize();
	}

	public function check_dir($dir) {
		if (!file_exists($dir)) {
			mkdir($dir);
		}
	}

	public function setup_user_data($data) {
		$user_id = $this -> session -> userdata('user_id');
		$s_uid = $this -> session -> userdata('s_uid');
		$user = $this -> users_dao -> find_by_id($user_id);
		// echo $user -> token . '-';
		// echo $s_uid;
		// if(empty($user_id)|| $user -> token != $s_uid) {
		if(empty($user_id)) {

			if ($this -> input ->is_ajax_request()) {
				echo "<script>window.location.reload();</script>";
			} else {
				redirect("app/login/logout");
			}
		} else {
			$data['login_user_id'] = $user_id;
			$data['l_user'] = $user;
		}



		$corp = $this -> corp_dao -> find_by_id($user -> corp_id);
		// update session
		$this -> session -> set_userdata('corp', $corp);
		$data['corp'] = $corp;
		return $data;
	}

	public function get_payload() {
		$auth = $this -> get_header('Authorization');
		$payload = jwt_decode($auth, "jihad");
		return $payload;
	}
}

/**
 * nne to check session
 */
class MY_Mgmt_Controller extends MY_Base_Controller {
	function __construct() {
		parent::__construct();

		$user_id = $this -> session -> userdata('user_id');
		if(strpos($_SERVER['PATH_INFO'], '/app/mgmt') == 0 && empty($user_id)) {
			echo "<script>window.location.reload();</script>";
			exit;
		}
	}
}
?>
