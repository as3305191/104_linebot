<?php
class Baccarat_tab_round_bet_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('baccarat_tab_round_bet');

		$this -> alias_map = array(
 		);
	}

	function find_total_rolling_by_user_id($user_id) {
		$this -> load -> model("Users_dao", 'u_dao');
		$user = $this -> u_dao -> find_by_id($user_id);

		$this -> db -> select("sum(rolling) as sum");
		$this -> db -> from($this -> table_name);
		$this -> db -> where("user_id", $user_id);
		$this -> db -> where("(id > $user->last_btrb_id)");

		$list = $this -> db -> get() -> result();

		if(count($list) > 0) {
			return (!empty($list[0] -> sum) ? $list[0] -> sum : 0);
		}
		return 0;
	}

	function find_by_user_id_and_search_type($user_id, $search_type = 'today') {
		$this -> db -> select("_m.*");
		$this -> db -> select("btr.sn");
		$this -> db -> select("bt.tab_name");
		$this -> db -> select("btrd.player_c_0, btrd.player_c_1, btrd.player_c_2, btrd.player_val,
													btrd.banker_c_0, btrd.banker_c_1, btrd.banker_c_2, btrd.banker_val,
													btrd.winner, btrd.winner_type, btrd.pos");
		$this -> db -> where('_m.user_id', $user_id);
		$this -> db -> from($this -> table_name . ' as _m');
		$this -> db -> join("baccarat_tab_round_detail btrd ", 'btrd.id = _m.detail_id and btrd.status = 5', 'inner');
		$this -> db -> join("baccarat_tab_round btr ", 'btr.id = btrd.round_id', 'left');
		$this -> db -> join("baccarat_tab bt ", 'bt.id = btr.tab_id', 'left');
		// order
		$this -> db -> order_by('id', 'desc');

		if($search_type == 'today') {
			$today = date('Y-m-d');
			$this -> db -> where("(_m.create_time like '%$today%')");
		}
		if($search_type == '3d') {
			$date = strtotime(date("Y-m-d", strtotime("-3 day")));
			$this -> db -> where("(_m.create_time > '$date')");
		}
		if($search_type == '2w') {
			$date = strtotime(date("Y-m-d", strtotime("-14 day")));
			$this -> db -> where("(_m.create_time > '$date')");
		}

		$list = $this -> db -> get() -> result();
		return $list;
	}

	function list_by_detail_id($detail_id) {
		$this -> db -> where('detail_id', $detail_id);
		$this -> db -> from($this -> table_name);
		$list = $this -> db -> get() -> result();
		return $list;
	}

	function sum_un_finished($user_id) {
		$sql = "select btrb.* from baccarat_tab_round_bet btrb
						inner join baccarat_tab_round_detail btrd on btrd.id = btrb.detail_id and btrd.status < 4
						where btrb.user_id = $user_id
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

	function sum_rolling_by_ym($user_id, $ym) {
		$sql = "select sum(rolling) as samt from baccarat_tab_round_bet
						where user_id = $user_id and create_time like '$ym%'
						";
		$list = $this -> db -> query($sql) -> result();
		if(count($list) > 0) {
			$item = $list[0];
			return (!empty($item -> samt) ? $item -> samt : 0);
		}
		return 0;
	}

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
		$sql = "select btrb.* from baccarat_tab_round_bet btrb
						inner join baccarat_tab_round_detail btrd on btrd.id = btrb.detail_id and btrd.status < 4
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

	function sum_ajax($data) {
		$start = $data['start'];
		$limit = $data['length'];
		$columns = $data['columns'];
		$search = $data['search'];
		$order = $data['order'];

		// select
		$this -> db -> select('sum(_m.total_bet) as s_total_bet');

		$this -> db -> select('u.user_name');
		$this -> db -> select('u.account as user_account');

		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("corp c", "c.id = _m.corp_id", "left");
		$this -> db -> join("users u", "u.id = _m.user_id", "left");

		// group
		$this -> db -> group_by('_m.user_id');

		// search always
		if(!empty($data['corp_id'])) {
			$this -> db -> where('_m.corp_id', $data['corp_id']);
		}
		if(!empty($data['create_date'])) {
			$v = $data['create_date'];
			$this -> db -> where("_m.create_time like '{$v}%' ");
		}
		if(!empty($data['s_user_name'])) {
			$v = $data['s_user_name'];
			$this -> db -> where("(u.account like '{$v}%' or u.user_name like '{$v}%' )");
		}


		$this -> db -> order_by('s_total_bet', 'desc');

		// limit

		// query results
		$query = $this -> db -> get();
		return $query -> result();
	}

	function query_ajax($data) {
		$start = $data['start'];
		$limit = $data['length'];
		$columns = $data['columns'];
		$search = $data['search'];
		$order = $data['order'];

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('c.corp_name');
		$this -> db -> select('bt.tab_name');
		$this -> db -> select('bt.tab_type');

		$this -> db -> select('u.user_name');
		$this -> db -> select('u.account as user_account');

		$this -> db -> select('btr.sn as round_sn');

		$this -> db -> select('btrd.pos as pos');
		$this -> db -> select('btrd.winner_type');
		$this -> db -> select('btrd.banker_c_0');
		$this -> db -> select('btrd.banker_c_1');
		$this -> db -> select('btrd.banker_c_2');
		$this -> db -> select('btrd.banker_val');

		$this -> db -> select('btrd.player_c_0');
		$this -> db -> select('btrd.player_c_1');
		$this -> db -> select('btrd.player_c_2');
		$this -> db -> select('btrd.player_val');
		$this -> db -> select('btrd.player_val');


		// $this -> db -> select('u.account as user_account, u.bank_id, u.bank_account');
		// $this -> db -> select('bk.bank_name');
		// $this -> db -> select('c.corp_name');

		// join
		$this -> ajax_from_join();

		// search always
		$this -> search_always($data);

		// search
		$this -> ajax_column_setup($columns, $search, $this -> alias_map);

		// order
		$this -> ajax_order_setup($order, $columns, $this -> alias_map);
		$this -> db -> order_by('pos', 'asc');

		// limit
		$this -> db -> limit($limit, $start);

		// query results
		$query = $this -> db -> get();
		return $query -> result();
	}

	function search_always($data) {

		if(isset($data['id']) && $data['id'] > -1) {
			$this -> db -> where('_m.id', $data['id']);
		}

		if(!empty($data['tab_id'])) {
			$this -> db -> where('_m.tab_id', $data['tab_id']);
		}

		if(!empty($data['tab_type'])) {
			$this -> db -> where('bt.tab_type', $data['tab_type']);
		}

		if(!empty($data['corp_id'])) {
			$this -> db -> where('_m.corp_id', $data['corp_id']);
		}
		if(!empty($data['create_date'])) {
			$v = $data['create_date'];
			$this -> db -> where("_m.create_time like '{$v}%' ");
		}
		if(!empty($data['s_user_name'])) {
			$v = $data['s_user_name'];
			$this -> db -> where("(u.account like '{$v}%' or u.user_name like '{$v}%' )");
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("baccarat_tab_round_detail btrd", "btrd.id = _m.detail_id", "left");
		$this -> db -> join("baccarat_tab_round btr", "btr.id = btrd.round_id", "left");
		$this -> db -> join("baccarat_tab bt", "bt.id = btr.tab_id", "left");
		$this -> db -> join("corp c", "c.id = _m.corp_id", "left");
		$this -> db -> join("users u", "u.id = _m.user_id", "left");
		// $this -> db -> join("banks bk", "bk.bank_id = u.bank_id", "left");
		// $this -> db -> join("corp c", "c.id = _m.corp_id", "left");
	}

	function sync() {
		$this -> load -> model('Total_bet_dao', 'tb_dao');

		$this -> db -> where("is_sync", 0);
		$list = $this  -> find_all();

		foreach($list as $each) {
			$this -> db -> trans_begin();

			$this -> tb_dao -> insert(array(
				'corp_id' => $each -> corp_id,
				'user_id' => $each -> user_id,
				'game_id' => 1,
				'bet_record_id' => $each -> id,
				'amt' => $each -> total_bet,
				'create_time' => $each -> create_time,
			));

			$this -> update(array('is_sync' => 1), $each -> id);

			if ($this->db->trans_status() === FALSE)
			{
			    $this->db->trans_rollback();
			}
			else
			{
			    $this->db->trans_commit();
			}
		}
	}

	function find_by_user($user_id) {
		$this -> db -> where("user_id", $user_id);
		$this -> db -> order_by("id", "desc");
		$list = $this -> find_all();
		return $list;
	}
}
?>
