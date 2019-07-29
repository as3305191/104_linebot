<?php
class Lottery_tx_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('lottery_tx');

		$this -> alias_map = array(

		);
	}

	function create_tx($user_id, $tab_id) {
		$this -> load -> model('Fish_tab_lottery_dao', 'ftl_dao');
		$this -> load -> model('Lottery_dao', 'l_dao');
		$a_ft_lottery = $this -> ftl_dao -> find_current($tab_id);

		if(empty($a_ft_lottery)) {
			$a_ft_lottery = $this -> ftl_dao -> find_next($tab_id);
		}

		$last_id = 0;
		if(!empty($a_ft_lottery)) {
			$lottery_id = $a_ft_lottery -> lottery_id;
			$fish_tab_lottery_id = $a_ft_lottery -> id;
			$lottery = $this -> l_dao -> find_by_id($lottery_id);
			if(!empty($lottery)) {
				$digits = strlen((string)$lottery -> total_num);
				$current_num = $this -> count_by_fish_tab_lottery_id($fish_tab_lottery_id);
				$current_num++;
				$sn = $s = sprintf("%0{$digits}d", $current_num);
				$last_id = $this -> insert(array(
					'fish_tab_lottery_id' => $fish_tab_lottery_id,
					'lottery_id' => $lottery_id,
					'tab_id' => $tab_id,
					'user_id' => $user_id,
					'sn' => $sn,
				));

				// save current num
				$this -> ftl_dao -> update(array(
					'current_num' => $current_num
				), $fish_tab_lottery_id);

				// get next
				if($current_num >= $lottery -> total_num) {
					// get next
					$next_ftl = $this -> ftl_dao -> find_next($tab_id);
				}
			}
		}

		$item = $this -> find_by_id($last_id);
		return $item;
	}

	function count_by_fish_tab_lottery_id($fish_tab_lottery_id) {
		$this -> db -> where("fish_tab_lottery_id", $fish_tab_lottery_id);
		$this -> db -> from($this -> table_name);
		$cnt = $this -> db -> count_all_results();
		return $cnt;
	}

	function count_by_lottery_id($lottery_id) {
		$this -> db -> where("lottery_id", $lottery_id);
		$this -> db -> from($this -> table_name);
		$cnt = $this -> db -> count_all_results();
		return $cnt;
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

	function random_one_by_fish_tab_lottery_id($fish_tab_lottery_id) {
		$list = $this -> db -> query("select * from {$this->table_name} where fish_tab_lottery_id = {$fish_tab_lottery_id} order by rand() limit 1 ") -> result();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	function find_by_fish_tab_lottery_id_and_tx_sn($fish_tab_lottery_id, $lottery_tx_sn) {
		$list = $this -> db -> query(
			"select * from {$this->table_name}
				where fish_tab_lottery_id = {$fish_tab_lottery_id} and sn = '{$lottery_tx_sn}'
			"
		) -> result();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	function count_all_by_fish_tab_lottery_and_user($fish_tab_lottery_id, $user_id) {
		$this -> db -> from("$this->table_name");
		$this -> db -> where("user_id", $user_id);
		$this -> db -> where("fish_tab_lottery_id", $fish_tab_lottery_id);
		$cnt = $this -> db -> count_all_results();
		return $cnt;
	}

	//
	function find_by_parameter($m){
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("fish_tab_lottery ftl", "ftl.id = _m.fish_tab_lottery_id", "left");
		$this -> db -> join("lottery l", "l.id = _m.lottery_id", "left");


		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('l.lottery_name');
		$this -> db -> select('ftl.lottery_no, ftl.is_received, ftl.open_lottery_tx_id');

		if(!empty($m['user_id'])){
			$this -> db -> where("_m.user_id", $m['user_id'] );
		}

		if(!empty($m['fish_tab_lottery_id'])){
			$this -> db -> where("_m.fish_tab_lottery_id", $m['fish_tab_lottery_id'] );
		}

		// // limit
		// if(empty($m['page'])) {
		// 	$page = 0;
		// } else {
		// 	$page = intval($m['page']);
		// }
		// if(empty($m['limit'])) {
		// 	// default is 20
		// 	$limit = 20;
		// }
		// $start = $page * $limit;
		// $this -> db -> limit($limit, $start);

		$query = $this -> db -> get();
		$list = $query -> result();

		return $list;
	}

	function find_user_lottery($data,$is_count = FALSE) {
		$lottery_no = $data['lottery_no'];
		$user_id = $data['user_id'];
		$start = $data['start'];
		$limit = $data['length'];

		// select
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> select('f_t.lottery_no ');
		$this -> db -> select('l.lottery_name ');

		$this -> db -> select('_m.sn');
		$this -> db -> join("fish_tab_lottery f_t", "f_t.id = _m.fish_tab_lottery_id", "left");
		$this -> db -> join("lottery l", "l.id = _m.lottery_id", "left");

		$this -> db -> where('user_id',$user_id);

		if($lottery_no>0) {
			$this -> db -> where('f_t.id',$lottery_no);
		}

		if(!$is_count) {
			$this -> db -> limit($limit, $start);
		}
		// query results
		if(!$is_count) {
			$query = $this -> db -> get();
			return $query -> result();
		} else {
			return $this -> db -> count_all_results();
		}
	}


}
?>
