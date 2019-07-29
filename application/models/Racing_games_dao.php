<?php
class Racing_games_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('racing_games');

		$this -> alias_map = array(

		);
	}

	function sum_pool_by_corp($corp_id) {
		$this -> db -> select("sum(pool_amt) as samt");
		$this -> db -> where('corp_id', $corp_id);
		$list = $this -> find_all();
		$samt = 0;
		if(count($list) > 0) {
			$samt = $list[0] -> samt;
		}
		if(empty($samt)) {
			$samt = 0;
		}
		return $samt;
	}

	function find_game_by_corp($corp_id) {
		$this -> db -> where('corp_id', $corp_id);
		$this -> db -> where('(status >= 0 and status < 4)'); // not finish
		$list = $this -> find_all();
		$a_game = NULL;
		if(count($list) > 0) {
			$a_game =  $list[0];
		}
		if(empty($a_game)) {
			/// create one
			$i_data = array();

			$cnt = $this -> count_game_by_corp($corp_id);
			$sn =  date("Ymd"). sprintf('%04d', ++$cnt);
			$cp_id =  sprintf('%03d', $corp_id);
			$i_data['sn'] = "R" . $cp_id . $sn;
			$i_data['corp_id'] = $corp_id;
			$i_data['create_time'] = date("Y-m-d H:i:s");
			$last_id = $this -> insert($i_data);
			$a_game = $this -> find_by_id($last_id);
		}
		return $a_game;
	}

	function count_game_by_corp($corp_id) {
		$this -> db -> where('corp_id', $corp_id);
		$today = date("Y-m-d");
		$this -> db -> where("create_time like '$today%'");
		$cnt = $this -> db -> count_all_results($this -> table_name);
		return $cnt;
	}

	function find_all_valid() {
		$list = $this -> find_all();

		return $list;
	}

	function find_all_valid_by_corp($corp_id, $limit = 0) {
		$this -> db -> select("id, sn, ranking, open_time as create_time");
		$this -> db -> where('corp_id', $corp_id);
		$this -> db -> where('status = 4');

		if($limit > 0) {
			$this -> db -> limit($limit);
		}

		$this -> db -> order_by('id', 'desc');

		$list = $this -> find_all();

		return $list;
	}

	function query_ajax($data) {
		$start = $data['start'];
		$limit = $data['length'];
		$columns = $data['columns'];
		$search = $data['search'];
		$order = $data['order'];

		// select
		$this -> db -> select('_m.*');

		// join
		$this -> ajax_from_join();

		// search always
		$this -> search_always($data);

		// search
		$this -> ajax_column_setup($columns, $search, $this -> alias_map);

		// order
		$this -> ajax_order_setup($order, $columns, $this -> alias_map);
		$this -> db -> order_by('_m.id', 'desc');

		// limit
		$this -> db -> limit($limit, $start);

		// query results
		$query = $this -> db -> get();
		return $query -> result();
	}

	function search_always($data) {
		if(!empty($data['id'])) {
			$this -> db -> where('_m.id', $data['id']);
		}

		if(!empty($data['corp_id'])) {
			$this -> db -> where('_m.corp_id', $data['corp_id']);
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
	}
}
?>
