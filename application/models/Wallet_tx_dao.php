<?php
class Wallet_tx_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('wallet_tx');

		$this -> alias_map = array(

		);
	}

	function sum_all($corp_id) {
		$sql = "select sum(amt) as samt from wallet_tx wtx
						inner join users u on u.id = wtx.user_id and u.is_test = 0 and u.corp_id = $corp_id ";
		$list = $this -> db -> query($sql) -> result();
		if(count($list) > 0) {
			return !empty($list[0] -> samt) ? $list[0] -> samt : 0;
		}
		return 0;
	}
	function get_max_id($user_id) {
		$sql = "select max(id) as m_max from wallet_tx where user_id  = $user_id";
		$list = $this -> db -> query($sql) -> result();
		if(count($list) > 0) {
			return !empty($list[0] -> m_max) ? $list[0] -> m_max : 0;
		}
		return 0;
	}

	function get_sum_amt_wash($user_id) {
		$this -> load -> model("Users_dao", 'u_dao');
		$user = $this -> u_dao -> find_by_id($user_id);

		$sql = "select sum(amt_wash) as samt from $this->table_name where user_id = $user_id and id > $user->last_wallet_tx_id ";
		$list = $this -> db -> query($sql) -> result();
		if(count($list) > 0) {
			return (!empty($list[0] -> samt) ? $list[0] -> samt : 0);
		}
		return 0;
	}

	function check_ym($ym, $user_id) {
		$this -> db -> where('ym', $ym);
		$this -> db -> where('user_id', $user_id);
		$list = $this -> find_all();
		return $list;
	}

	function find_all_corp_tx($data) {
		$s_date = $data['s_date'];
		$e_date = $data['e_date'];
		$sql = "select sum(wtx.amt) as sum_amt, br.corp_id, c.corp_name from wallet_tx wtx
		inner join buy_records br on br.id = wtx.buy_record_id and br.corp_id > 1
		left join corp c on c.id = br.corp_id
		where wtx.corp_id = 1 and (wtx.type_id = 9 or wtx.type_id = 10)
		and wtx.create_time >= '$s_date 00:00:00' and wtx.create_time <= '$e_date 23:59:59'
		group by br.corp_id";

		$list = $this -> db -> query($sql) -> result();

		return $list;
	}

	function find_all_user_tx($data) {
		$s_date = $data['s_date'];
		$e_date = $data['e_date'];
		$user_id = $data['user_id'];
		$sql = "select * from wallet_tx wtx
		where wtx.user_id = $user_id and (wtx.type_id = 6 or wtx.type_id = 7)
		and wtx.create_time >= '$s_date 00:00:00' and wtx.create_time <= '$e_date 23:59:59'
		";

		$list = $this -> db -> query($sql) -> result();

		return $list;
	}

	function find_withdraw_lock($w_id) {
		$this -> db -> where('withdraw_id', $w_id);
		$this -> db -> where('type_id', 18);
		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	function get_sum_amt($user_id) {
		$this -> db -> select("sum(amt) as samt");
		$this -> db -> where('user_id', $user_id);
		$list = $this -> find_all();
		if(count($list) > 0) {
			$itm = $list[0];
			return (!empty($itm -> samt) ? $itm -> samt : 0);
		}
		return 0;
	}

	function get_sum_amt_all($last_id) {
		$this -> db -> select("sum(amt) as samt");
		$this -> db -> where('tx_id<',$last_id+1);
		$list = $this -> find_all();
		if(count($list) > 0) {
			$itm = $list[0];
			return (!empty($itm -> samt) ? $itm -> samt : 0);
		}
		return 0;
	}

	function corp_sum_amt_all($corp_id, $type_id) {
		$this -> db -> select("sum(amt) as samt");
		$this -> db -> where('corp_id', $corp_id);
		$this -> db -> where('type_id', $type_id);
		$list = $this -> find_all();
		if(count($list) > 0) {
			$itm = $list[0];
			return $itm -> samt;
		}
		return 0;
	}

	function corp_sum_amt_time($time, $corp_id, $type_id) {
		$this -> db -> select("sum(amt) as samt");
		$this -> db -> where('corp_id', $corp_id);
		$this -> db -> where('type_id', $type_id);
		$this -> db -> where("create_time like '$time%'");
		$list = $this -> find_all();
		if(count($list) > 0) {
			$itm = $list[0];
			return $itm -> samt;
		}
		return 0;
	}



	function query_ajax($data) {
		$start = $data['start'];
		$limit = $data['length'];
		$columns = $data['columns'];
		$search = $data['search'];
		$order = $data['order'];

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('wt.type_name');
		// $this -> db -> select('c.company_name');

		// join
		$this -> ajax_from_join();

		// search always
		$this -> search_always($data);

		// search
		$this -> ajax_column_setup($columns, $search, $this -> alias_map);

		// order
		$this -> ajax_order_setup($order, $columns, $this -> alias_map);
		$this -> db -> order_by('id', 'desc');

		// limit
		$this -> db -> limit($limit, $start);

		// query results
		$query = $this -> db -> get();
		return $query -> result();
	}

	function search_always($data) {
		// $this -> db -> where('_m.status', 0);
		// if(isset($data['user_role_id']) && $data['user_role_id'] > -1) {
		// 	$this -> db -> where('user_role_id', $data['user_role_id']);
		// }
		if(isset($data['user_id']) && $data['user_id'] > -1) {
			$this -> db -> where('_m.user_id', $data['user_id']);
		}

		if(isset($data['id']) && $data['id'] > -1) {
			$this -> db -> where('_m.id', $data['id']);
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("wallet_types wt", "wt.id = _m.type_id", "left");
		// $this -> db -> join("company c", "c.id = _m.com_id", "left");
		// $this -> db -> join("users iu", "iu.id = _m.intro_id", "left");
		// $this -> db -> join("roles r", "r.id = _m.role_id", "left");
	}

	function delete_by_bj_bet_id_and_type($bj_bet_id, $type_id) {
		$this -> db -> where('bj_bet_id', $bj_bet_id);
		$this -> db -> where('type_id', $type_id);
		$this -> db -> delete("{$this->table_name}");
	}

	function find_check_in($user_id,$getDate) {
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> select('_m.*');

		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('tx_id', $user_id);

		$this -> db -> where('tx_type', 'check_in_reward');
		$this -> db -> where("create_time like '$getDate%'");

		$query = $this -> db -> get();
		$list = $query -> result();
		return $list;
	}


}
?>
