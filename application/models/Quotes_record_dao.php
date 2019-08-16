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
		$this -> db -> where('id<',$last_id+1);
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

		$this -> db -> where('current_point<>',0.00000000);
		$this -> db -> order_by('id','desc');

		$query = $this -> db -> get();
		$list = $query -> result();
		return $list[0];
	}

	function get_current_point1($last_id) {
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> select('_m.current_point');

		$this -> db -> where('current_point<>',0.00000000);
		$this -> db -> where('id',$last_id);

		$query = $this -> db -> get();
		$list = $query -> result();
		return $list[0];
	}

	function get_current_ntd() {
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> select('_m.current_ntd');

		$this -> db -> where('current_ntd<>',0.00000000);
		$this -> db -> order_by('id','desc');

		$query = $this -> db -> get();
		$list = $query -> result();
		return $list[0];
	}

	function insert_all_total($bet_o,$total,$for_q_amt,$user_id,$advance_id,$type) {
		$this -> load -> model('Com_tx_dao', 'ctx_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Daily_quotes_dao', 'd_q_dao');
		$this -> load -> model('Quotes_record_dao', 'q_r_dao');
		$this -> load -> model('Play_game_dao', 'play_game_dao');
		$this -> load -> model('Config_dao', 'config_dao');
		$this -> load -> model('Game_pool_dao', 'game_pool_dao');

		$config = $this -> config_dao -> find_by_id(1);//設定%的地方

		$get_current_point=$this -> q_r_dao -> get_current_point();
		$get_current_ntd=$this -> q_r_dao -> get_current_ntd();
		$bureau_num = generate_random_string($length = 4);
		$get_all_pool=$this -> game_pool_dao -> get_all_pool_amt();


		if($type==1){
			$idata2['bet_type']=$bet_o;
			$idata2['pool_amt']=-$total;
			$idata2['type']=1;
			$this -> game_pool_dao -> insert($idata2);

		}elseif ($type==0) {
			$idata22['bet_type']=$bet_o;
			$idata22['pool_amt']=-$total;
			$idata22['type']=0;
			$this -> game_pool_dao -> insert($idata22);

		}
		$tx_11 = array();
		$tx_11['user_id'] = $user_id;
		$tx_11['bet'] = $bet_o;
		$tx_11['total_win_point'] = $total;
		$tx_11['bureau_num'] = $bureau_num;
		$tx_11['advance_id'] = $advance_id;

		$last_id=	$this -> play_game_dao -> insert($tx_11);


		$tx_1 = array();
		$tx_1['tx_type'] = "play_game";
		$tx_1['tx_id'] = $last_id;
		$tx_1['point_change'] = $for_q_amt;
		$current_point= intval($get_current_point->current_point)+intval($for_q_amt);
		$tx_1['current_point'] =$current_point;
		$tx_1['current_ntd'] =$get_current_ntd->current_ntd; // 需要紀錄ntd
		$last_id_insert_q=$this -> q_r_dao -> insert($tx_1);

		$tx1 = array();
		$tx1['corp_id'] = 1;
		$tx1['user_id'] = $user_id;
		// $tx1['user_id'] = "xxx";

		$tx1['amt'] = -$bet_o;
		$tx1['tx_type'] = "play_game";
		$tx1['tx_id'] = $last_id;
		$tx1['brief'] = "會員 {$user_id}下注遊戲扣點 {$bet_o} ";

		// $tx1['brief'] = "會員 xxx下注遊戲扣點 {$bet_o} ";
		$this -> wtx_dao -> insert($tx1);

		$tx = array();
		$tx['corp_id'] = 1;
		$tx['user_id'] = $user_id;
		// $tx['user_id'] = "xxx";

		$tx['amt'] = $total;
		$tx['tx_type'] = "play_game";
		$tx['tx_id'] = $last_id;
		$tx['brief'] = "會員 {$user_id} 遊戲贏得 {$total} ";
		// $tx['brief'] = "會員 xxx 遊戲贏得 {$total} ";

		$this -> wtx_dao -> insert($tx);
		$add_coin_daily=$this -> q_r_dao -> find_by_id($last_id_insert_q);
		$Date = date("Y-m-d");
		$p1 = $this -> d_q_dao -> find_last_d_q($Date);
		$dq =  $this -> d_q_dao -> find_d_q($Date);
		$dtx = array();
		$cp = floatval(intval($get_current_point->current_point)); // 避免除0問題
		$p = 0;
		if($cp != 0) {
			$p=floatval($get_current_ntd->current_ntd)/floatval(intval($get_current_point->current_point));
		}
		$price=round($p,8);
		$dtx['date'] = $Date;
		$dtx['average_price'] = $p1->last_price;
		$dtx['last_price'] = $price;
		$dtx['now_price'] = $price;
		if(!empty($dq)){
			$u_data['last_price'] = $price;
			$u_data['now_price'] = $price;
			$this -> d_q_dao -> update_by($u_data,'id',$dq->id);
		} else{
			$this -> d_q_dao -> insert($dtx);
		}

		return $last_id;
	}


	function find_last() {
		$this -> db -> order_by("id", 'desc');
		$this -> db -> limit(1);

		$list = $this -> db -> get($this -> table_name) -> result();
		if(count($list) == 0) {
			return NULL;
		}
		return $list[0];
	}

}
?>
