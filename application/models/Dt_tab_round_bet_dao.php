<?php
class Dt_tab_round_bet_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('dt_tab_round_bet');

		$this -> alias_map = array(
 		);
	}

	function find_total_rolling_by_user_id($user_id, $after_dt = '') {
		$this -> load -> model("Users_dao", 'u_dao');
		$user = $this -> u_dao -> find_by_id($user_id);

		$this -> db -> select("sum(rolling) as sum");
		$this -> db -> from($this -> table_name);
		$this -> db -> where("user_id", $user_id);
		$this -> db -> where("(id > $user->last_btrb_id)");

		if(!empty($after_dt)) {
			$this -> db -> where("(create_time > '{$after_dt} 00:00:00')");
		}

		$list = $this -> db -> get() -> result();

		if(count($list) > 0) {
			return (!empty($list[0] -> sum) ? $list[0] -> sum : 0);
		}
		return 0;
	}

	// function find_by_user_id_and_search_type($user_id, $search_type = 'today') {
	// 	$this -> db -> select("_m.*");
	// 	$this -> db -> select("btr.sn");
	// 	$this -> db -> select("bt.tab_name");
	// 	$this -> db -> select("btrd.player_c_0, btrd.player_c_1, btrd.player_c_2, btrd.player_val,
	// 												btrd.banker_c_0, btrd.banker_c_1, btrd.banker_c_2, btrd.banker_val,
	// 												btrd.winner, btrd.winner_type, btrd.pos");
	// 	$this -> db -> where('_m.user_id', $user_id);
	// 	$this -> db -> from($this -> table_name . ' as _m');
	// 	$this -> db -> join("baccarat_tab_round_detail btrd ", 'btrd.id = _m.detail_id and btrd.status = 5', 'inner');
	// 	$this -> db -> join("baccarat_tab_round btr ", 'btr.id = btrd.round_id', 'left');
	// 	$this -> db -> join("baccarat_tab bt ", 'bt.id = btr.tab_id', 'left');
	// 	// order
	// 	$this -> db -> order_by('id', 'desc');
	//
	// 	if($search_type == 'today') {
	// 		$today = date('Y-m-d');
	// 		$this -> db -> where("(_m.create_time like '%$today%')");
	// 	}
	// 	if($search_type == '3d') {
	// 		$date = strtotime(date("Y-m-d", strtotime("-3 day")));
	// 		$this -> db -> where("(_m.create_time > '$date')");
	// 	}
	// 	if($search_type == '2w') {
	// 		$date = strtotime(date("Y-m-d", strtotime("-14 day")));
	// 		$this -> db -> where("(_m.create_time > '$date')");
	// 	}
	//
	// 	$list = $this -> db -> get() -> result();
	// 	return $list;
	// }

	function list_by_detail_id($detail_id) {
		$this -> db -> where('detail_id', $detail_id);
		$this -> db -> from($this -> table_name);
		$list = $this -> db -> get() -> result();
		return $list;
	}

	// function sum_un_finished($user_id) {
	// 	$sql = "select btrb.* from baccarat_tab_round_bet btrb
	// 					inner join baccarat_tab_round_detail btrd on btrd.id = btrb.detail_id and btrd.status < 4
	// 					where btrb.user_id = $user_id
	// 					";
	// 	$list = $this -> db -> query($sql) -> result();
	// 	$sum = 0;
	// 	foreach($list as $each) {
	// 		$sum += $each -> bet_0;
	// 		$sum += $each -> bet_1;
	// 		$sum += $each -> bet_2;
	// 		$sum += $each -> bet_3;
	// 		$sum += $each -> bet_4;
	// 	}
	// 	return $sum;
	// }

	// function sum_rolling_by_ym($user_id, $ym) {
	// 	$sql = "select sum(rolling) as samt from baccarat_tab_round_bet
	// 					where user_id = $user_id and create_time like '$ym%'
	// 					";
	// 	$list = $this -> db -> query($sql) -> result();
	// 	if(count($list) > 0) {
	// 		$item = $list[0];
	// 		return (!empty($item -> samt) ? $item -> samt : 0);
	// 	}
	// 	return 0;
	// }

	function check_new($user_id, $detail_id) {
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('detail_id', $detail_id);
		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		} else {
			// create new one
			$i_data['user_id'] = $user_id;
			$i_data['detail_id'] = $detail_id;
			$last_id = $this -> insert($i_data);
			$item = $this -> find_by_id($last_id);
			return $item;
		}
	}

	function sum_un_finished_without_me($user_id, $me_id) {
		$sql = "select btrb.* from dt_tab_round_bet btrb
						inner join dt_tab_round_detail btrd on btrd.id = btrb.detail_id and btrd.status < 4
						where btrb.user_id = $user_id and btrb.id <> $me_id
						";
		$list = $this -> db -> query($sql) -> result();
		$sum = 0;
		foreach($list as $each) {
			$sum += $each -> bet_0;
			$sum += $each -> bet_1;
			$sum += $each -> bet_2;
			$sum += $each -> bet_3;
			$sum += $each -> bet_4;
		}
		return $sum;
	}

	// function list_ranking($corp_id, $win_date, $limit = 100) {
	// 	$this -> db -> select("_m.user_id");
	// 	$this -> db -> select("_m.total_win");
	// 	$this -> db -> select("_m.detail_id");
	// 	$this -> db -> select("u.nick_name");
	// 	$this -> db -> select("u.image_id");
	// 	$this -> db -> select("bt.tab_name");
	//
	// 	$this -> db -> where('_m.corp_id', $corp_id);
	// 	$this -> db -> where("(_m.create_time like '$win_date%')");
	// 	$this -> db -> where("_m.total_win > 0");
	// 	$this -> db -> from($this -> table_name . ' as _m');
	// 	$this -> db -> join('users u', 'u.id = _m.user_id', 'left');
	// 	$this -> db -> join('baccarat_tab_round_detail bd', 'bd.id = _m.detail_id', 'left');
	// 	$this -> db -> join('baccarat_tab_round br', 'br.id = bd.round_id', 'left');
	// 	$this -> db -> join('baccarat_tab bt', 'bt.id = br.tab_id', 'left');
	// 	$this -> db -> order_by('_m.total_win', 'desc');
	// 	$this -> db -> order_by('_m.create_time', 'asc');
	// 	$this -> db -> limit($limit);
	// 	$list = $this -> db -> get() -> result();
	// 	$rank = 1;
	// 	foreach($list as $each) {
	// 		$each -> rank = $rank++;
	// 		$each -> image_url = !empty($each -> image_id) ? IMG_URL . $each -> image_id : '';
	// 		$each -> image_url_thumb = !empty($each -> image_id) ? IMG_URL . $each -> image_id . '/thumb' : '';
	// 	}
	// 	return $list;
	// }
	//
	// function list_ranking_all($win_date, $limit = 100) {
	// 	$this -> db -> select("_m.user_id");
	// 	$this -> db -> select("_m.corp_id");
	// 	$this -> db -> select("_m.total_win");
	// 	$this -> db -> select("_m.detail_id");
	// 	$this -> db -> select("c.sys_name");
	// 	$this -> db -> select("u.nick_name");
	// 	$this -> db -> select("u.image_id");
	// 	$this -> db -> select("bt.tab_name");
	//
	// 	$this -> db -> where("(_m.create_time like '$win_date%')");
	// 	$this -> db -> where("_m.total_win > 0");
	// 	$this -> db -> from($this -> table_name . ' as _m');
	// 	$this -> db -> join('users u', 'u.id = _m.user_id', 'left');
	// 	$this -> db -> join('corp c', 'c.id = _m.corp_id', 'left');
	// 	$this -> db -> join('baccarat_tab_round_detail bd', 'bd.id = _m.detail_id', 'left');
	// 	$this -> db -> join('baccarat_tab_round br', 'br.id = bd.round_id', 'left');
	// 	$this -> db -> join('baccarat_tab bt', 'bt.id = br.tab_id', 'left');
	// 	$this -> db -> order_by('_m.total_win', 'desc');
	// 	$this -> db -> order_by('_m.create_time', 'asc');
	// 	$this -> db -> limit($limit);
	// 	$list = $this -> db -> get() -> result();
	// 	$rank = 1;
	// 	foreach($list as $each) {
	// 		$each -> rank = $rank++;
	// 		$each -> image_url = !empty($each -> image_id) ? IMG_URL . $each -> image_id : '';
	// 		$each -> image_url_thumb = !empty($each -> image_id) ? IMG_URL . $each -> image_id . '/thumb' : '';
	// 	}
	// 	return $list;
	// }
}
?>
