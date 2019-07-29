<?php
class Menu extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Award_center_dao', 'ac_dao');
		$this -> load -> model('Ranking_dao', 'rank_dao');
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Fish_bet_dao', 'fish_bet_dao');
	}

	public function create_award() {
		$res = array();

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$date = $this -> get_post('date');
		$cate = $this -> get_post('cate');
		$detail = $this -> get_post('detail');

		if(!empty($user_id) && !empty($date) && !empty($cate) && !empty($detail)) {
			$user = $this -> users_dao -> find_by_id($user_id);
			if(!empty($user)) {
				$i = array();
				$i['date'] = $date;
				$i['user_id'] = $user -> id;
				$i['cate'] = $cate;
				$i['detail'] = $detail;
				$last_id = $this -> ac_dao -> insert($i);
				$res['success'] = TRUE;
				$res['last_id'] = $last_id;
			} else {
				$res['error_msg'] = "使用者不存在";
			}
		} else {
			$res['error_msg'] = "缺少必要欄位";
		}
		$this -> to_json($res);
	}

	public function list_award() {
		$res = array();

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		if(empty($user_id)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$list = $this -> ac_dao -> find_by_parameter(array('user_id' => $user_id));
			$res['list'] = $list;
		}
		$this -> to_json($res);
	}

	public function get_prize() {
		$res = array();
		$id = $this -> get_post('id');

		if(empty($id)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$m = $this -> ac_dao -> find_by_id($id);
			if(!empty($m)){
				if($m -> status == 1){
					$res['error_msg'] = "已領取過獎勵";
				}else{
					$list = $this -> ac_dao -> update(array('status' => 1),$id);
					$res['success'] = TRUE;
				}
			}else{
				$res['error_msg'] = "此獎勵不存在";
			}
		}
		$this -> to_json($res);
	}

	public function list_ranking() {
		$res = array();

		// $date = date('Y-m-d');
		$m = $this -> get_posts(array(
			'date',
			's_date',
			'e_date',
		));

		$list = $this -> rank_dao -> group_by_parameter($m);
		$res['list'] = $list;

		$this -> to_json($res);
	}

	public function get_ranking() {
		$res = array("success" => TRUE);

		$date = $this -> get_get('date');
		$yesterday = "";
		if(empty($date)) {
			$date = date("Y-m-d");
			$yesterday = date('Y-m-d',strtotime("-1 days"));
		}

		$res['date'] = $date;
		$diff = time() - strtotime($date);
		// date
		$this -> do_get_ranking($date);
		if(!empty($yesterday) && $diff < (60 * 60)) { // before 01:00 of this day
			$this -> do_get_ranking($yesterday);
		}

		$this -> to_json($res);
	}

	private function do_get_ranking($date) {
		$user_list = $this -> users_dao -> find_all();
		foreach($user_list as $user) {
			$this -> get_rank_by_user($user -> id, $date);
		}
	}

	public function get_rank_by_user($user_id, $date) {
		$rank = $this -> rank_dao -> find_by_user_and_date($user_id, $date);
		$sum_win_amt = $this -> fish_bet_dao -> sum_win_amt_by_user_and_date($user_id, $date);
		if(empty($rank)) {
			// insert
			$i = array();
			$i['date'] = $date;
			$i['user_id'] = $user_id;
			$i['score'] = $sum_win_amt;
			$last_id = $this -> rank_dao -> insert($i);
		} else {
			// update
			$u = array();
			$u['score'] = $sum_win_amt;
			$this -> rank_dao -> update($u, $rank -> id);
		}
	}
	

	public function create_ranking() {
		$res = array();

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$date = $this -> get_post('date');
		$score = $this -> get_post('score');

		if(!empty($user_id) && !empty($date) && !empty($score) ) {
			$user = $this -> users_dao -> find_by_id($user_id);
			if(!empty($user)) {
				$i = array();
				$i['date'] = $date;
				$i['user_id'] = $user -> id;
				$i['score'] = $score;
				$last_id = $this -> rank_dao -> insert($i);
				$res['success'] = TRUE;
				$res['last_id'] = $last_id;
			} else {
				$res['error_msg'] = "使用者不存在";
			}
		} else {
			$res['error_msg'] = "缺少必要欄位";
		}
		$this -> to_json($res);
	}


}
?>
