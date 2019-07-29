<?php
class Upcoming_events extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		// setup models
		$this -> load -> model('Upcoming_events_dao', 'dao');
	}

	public function test() {
		echo "test";
	}

	public function list_all() {
		$res = array();
		$res['success'] = TRUE;

		$list = $this -> dao -> find_all_list();
		foreach($list as $each) {
			$each -> image_url = "";
			if(!empty($each -> image_id)) {
				$each -> image_url = base_url("api/images/get/{$each->image_id}");
			}
		}
		$res['list'] = $list;
		$this -> to_json($res);
	}

	public function show_status() {
		$res = array();
		$res['success'] = TRUE;
		$ope_pct = 1;

		$user_id = $this -> get_post('user_id');

		if(empty($user_id)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$user = $this -> users_dao -> find_by_id($user_id);
			if($user -> lp_always_on == 1) {
				$res['lp_always_on'] = 1;
				$res['lp_on'] = TRUE;
			} else {
				if(!empty($user -> lp_due_date) && (strtotime(date('Y-m-d')) <= strtotime($user -> lp_due_date))) {
					// show due date
					$res['due_date'] = $user -> lp_due_date;
					$res['lp_on'] = TRUE;
				} else {
					$snum = $this -> lp_tx_dao -> sum_num_by_user($user_id);
					if($snum > 0) {
						$res['num'] = $snum;
						$res['lp_on'] = TRUE;
					} else {
						$res['lp_on'] = FALSE;
					}
				}
			}
		}
		$this -> to_json($res);
	}
}
?>
