<?php
class App_version extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('App_version_dao', 'dao');
	}

	public function check() {
		$res = array();
		$version = $this -> get_post('version');
		$type = $this -> get_post('type');
		if(empty($version) && empty($type) && $type != 'ios_switch') {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			if(empty($type)) {
				$type = 'android';
			}
			if($type == 'android') {
				$res['result'] = $this -> dao -> check_version('android', $version);
			}
			if($type == 'ios') {
				$res['result'] = $this -> dao -> check_version('ios', $version);
			}
			if($type == 'ios_switch') {
				$item =  $this -> dao -> find_by_id(3);
				$res['result'] = $item -> version == 'true';
			}
			$res['type'] = $type;
		}
		$this -> to_json($res);
	}

	public function check_maintain() {
		$corp = $this -> corp_dao -> find_by_id(1);
		$res = array();

		$res['evn'] = ENVIRONMENT_SETUP;
		$res['is_maintain'] = (ENVIRONMENT_SETUP == 'production' ? $corp -> is_maintain_production: $corp -> is_maintain);
		$res['maintain_start_time'] = (ENVIRONMENT_SETUP == 'production' ? $corp -> maintain_start_time_production : $corp -> maintain_start_time);
		$res['maintain_end_time'] = (ENVIRONMENT_SETUP == 'production' ? $corp -> maintain_end_time_production : $corp -> maintain_end_time);
		$this -> to_json($res);
	}

	public function update_maintain() {
		$corp = $this -> corp_dao -> find_by_id(1);
		$u_data = $this -> get_posts(array(
			'is_maintain',
			'maintain_start_time',
			'maintain_end_time',
		));
		$this -> corp_dao -> update($u_data, $corp -> id);
		$corp = $this -> corp_dao -> find_by_id(1);
		$res = array();
		$res['is_maintain'] = $corp -> is_maintain;
		$res['maintain_start_time'] = $corp -> maintain_start_time;
		$res['maintain_end_time'] = $corp -> maintain_end_time;

		$this -> to_json($res);
	}

	public function test() {

		$res = "hello..";

		echo $res;
	}
}
?>
