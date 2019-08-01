<?php
class Quotes_record_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('quotes_record');

		$this -> alias_map = array(

		);
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

	function get_sum_ntd($last_id) {
		$this -> db -> select("sum(ntd_change) as sntd");
		$this -> db -> where('tx_id<=',$last_id);
		$list = $this -> find_all();
		if(count($list) > 0) {
			$itm = $list[0];
			return (!empty($itm -> sntd) ? $itm -> sntd : 0);
		}
		return 0;
	}
	function get_sum_ntd1($last_id) {
		$this -> db -> select("sum(ntd_change) as sntd");
		$this -> db -> where('id<=',$last_id);
		$list = $this -> find_all();
		if(count($list) > 0) {
			$itm = $list[0];
			return (!empty($itm -> sntd) ? $itm -> sntd : 0);
		}
		return 0;
	}

	function get_current_point() {
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> select('_m.current_point');

		$this -> db -> where('current_point<>',0);
		$this -> db -> order_by('id','desc');

		$query = $this -> db -> get();
		$list = $query -> result();
		return $list[0];
	}

	function get_current_ntd() {
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> select('_m.current_ntd');

		$this -> db -> where('current_ntd<>',0);
		$this -> db -> order_by('id','desc');

		$query = $this -> db -> get();
		$list = $query -> result();
		return $list[0];
	}

	function insert_all_total($bet_o,$total,$for_q_amt) {
		$this -> load -> model('Com_tx_dao', 'ctx_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Daily_quotes_dao', 'd_q_dao');
		$this -> load -> model('Quotes_record_dao', 'q_r_dao');

		$get_current_point=$this -> q_r_dao -> get_current_point();
		$get_current_ntd=$this -> q_r_dao -> get_current_ntd();

		$tx_1 = array();
		$tx_1['tx_type'] = "play_game";
		$tx_1['tx_id'] = 0;
		$tx_1['point_change'] = $for_q_amt;
		$tx_1['current_point'] = floatval($get_current_point->current_point)+floatval($for_q_amt);
		$last_id=	$this -> q_r_dao -> insert($tx_1);

		$tx1 = array();
		$tx1['corp_id'] = 1;
		// $tx1['user_id'] = $user_id;
		$tx1['user_id'] = "xxx";

		$tx1['amt'] = -$bet_o;
		$tx1['tx_type'] = "quotes_record";
		$tx1['tx_id'] = $last_id;
		// $tx1['brief'] = "會員 {$user_id}下注遊戲扣點 {$bet_o} ";

		$tx1['brief'] = "會員 xxx下注遊戲扣點 {$bet_o} ";
		$this -> wtx_dao -> insert($tx1);

		$tx = array();
		$tx['corp_id'] = 1;
		// $tx['user_id'] = $user_id;
		$tx['user_id'] = "xxx";

		$tx['amt'] = $total;
		$tx['tx_type'] = "quotes_record";
		$tx['tx_id'] = $last_id;
		// $tx['brief'] = "會員 {$user_id} 遊戲贏得 {$total} ";
		$tx['brief'] = "會員 xxx 遊戲贏得 {$total} ";

		$this -> wtx_dao -> insert($tx);

		$Date = date("Y-m-d");
		$dq =  $this -> d_q_dao -> find_d_q($Date);
		$samt1 =  $this -> wtx_dao -> get_sum_amt_all($last_id);
		$sntd =  $this -> q_r_dao -> get_sum_ntd1($last_id);
		$dtx = array();
		$dtx['date'] = $Date;
		$dtx['average_price'] = floatval($sntd)/floatval($samt1);
		$dtx['last_price'] = floatval($sntd)/floatval($samt1);
		$dtx['now_price'] = floatval($sntd)/floatval($samt1);
		if(!empty($dq)){
			$u_data['last_price'] = floatval($sntd)/floatval($samt1);
			$u_data['now_price'] = floatval($sntd)/floatval($samt1);
			$this -> d_q_dao -> update_by($u_data,'id',$dq->id);

		} else{
			$this -> d_q_dao -> insert($dtx);
		}

		return TRUE;
	}
}
?>