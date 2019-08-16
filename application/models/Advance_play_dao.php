<?php
class Advance_play_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('advance_play');

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

	function find_rand($get_all,$type,$total_magnification) {
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> select('_m.*');
		$this -> db -> order_by('id', 'RANDOM');
		if($type==1){
			$this -> db -> where('_m.type',$type);
			$this -> db -> where('_m.total_multiple<',floatval($get_all));
		}
		if($type==0){
			$this -> db -> where('_m.type',$type);
			$this -> db -> where('_m.total_multiple<',floatval($get_all));
		}
		if($type==3){
			$this -> db -> where('_m.type',0);
			$this -> db -> where('_m.total_multiple',0.00000000);
		}

		if($total_magnification<=5 && $total_magnification>0){
			if($total_magnification==1){
				//10-50
				$this -> db -> where('_m.total_multiple>=10');
				$this -> db -> where('_m.total_multiple<=50');
			}
			if($total_magnification==2){
				//51-99
				$this -> db -> where('_m.total_multiple>=51');
				$this -> db -> where('_m.total_multiple<=99');
			}
			if($total_magnification==3){
				//100-199
				$this -> db -> where('_m.total_multiple>=100');
				$this -> db -> where('_m.total_multiple<=199');
			}
			if($total_magnification==4){
				//200-299
				$this -> db -> where('_m.total_multiple>=200');
				$this -> db -> where('_m.total_multiple<=299');
			}
			if($total_magnification==5){
				//300-399
				$this -> db -> where('_m.total_multiple>300');
				$this -> db -> where('_m.total_multiple<399');
			}
		} elseif($total_magnification>5 && $total_magnification<=13){//全盤7
				if($total_magnification==6){
					$this -> db -> where('_m.counter_num',7);
					$this -> db -> where('_m.counter_system','seven');

				}
				if($total_magnification==7){
					$this -> db -> where('_m.counter_num',7);
					$this -> db -> where('_m.counter_system','bar');

				}
				if($total_magnification==8){
					$this -> db -> where('_m.counter_num',7);
					$this -> db -> where('_m.counter_system','medal');

				}
				if($total_magnification==9){
					$this -> db -> where('_m.counter_num',7);
					$this -> db -> where('_m.counter_system','bell');

				}
				if($total_magnification==10){
					$this -> db -> where('_m.counter_num',7);
					$this -> db -> where('_m.counter_system','watermelon');

				}
				if($total_magnification==11){
					$this -> db -> where('_m.counter_num',7);
					$this -> db -> where('_m.counter_system','grape');

				}
				if($total_magnification==12){
					$this -> db -> where('_m.counter_num',7);
					$this -> db -> where('_m.counter_system','orange');

				}
				if($total_magnification==13){
					$this -> db -> where('_m.counter_num',7);
					$this -> db -> where('_m.counter_system','cherry');

				}
			}elseif($total_magnification>13 && $total_magnification<=21){//全盤8

				if($total_magnification==14){
					$this -> db -> where('_m.counter_num',8);
					$this -> db -> where('_m.counter_system','seven');

				}
				if($total_magnification==15){
					$this -> db -> where('_m.counter_num',8);
					$this -> db -> where('_m.counter_system','bar');

				}
				if($total_magnification==16){
					$this -> db -> where('_m.counter_num',8);
					$this -> db -> where('_m.counter_system','medal');

				}
				if($total_magnification==17){
					$this -> db -> where('_m.counter_num',8);
					$this -> db -> where('_m.counter_system','bell');

				}
				if($total_magnification==18){
					$this -> db -> where('_m.counter_num',8);
					$this -> db -> where('_m.counter_system','watermelon');

				}
				if($total_magnification==19){
					$this -> db -> where('_m.counter_num',8);
					$this -> db -> where('_m.counter_system','grape');

				}
				if($total_magnification==20){
					$this -> db -> where('_m.counter_num',8);
					$this -> db -> where('_m.counter_system','orange');

				}
				if($total_magnification==21){
					$this -> db -> where('_m.counter_num',8);
					$this -> db -> where('_m.counter_system','cherry');

				}
			}elseif($total_magnification>21 && $total_magnification<=29){//全盤9
				if($total_magnification==22){
					$this -> db -> where('_m.counter_num',9);
					$this -> db -> where('_m.counter_system','seven');

				}
				if($total_magnification==23){
					$this -> db -> where('_m.counter_num',9);
					$this -> db -> where('_m.counter_system','bar');

				}
				if($total_magnification==24){
					$this -> db -> where('_m.counter_num',9);
					$this -> db -> where('_m.counter_system','medal');

				}
				if($total_magnification==25){
					$this -> db -> where('_m.counter_num',9);
					$this -> db -> where('_m.counter_system','bell');

				}
				if($total_magnification==26){
					$this -> db -> where('_m.counter_num',9);
					$this -> db -> where('_m.counter_system','watermelon');

				}
				if($total_magnification==27){
					$this -> db -> where('_m.counter_num',9);
					$this -> db -> where('_m.counter_system','grape');

				}
				if($total_magnification==28){
					$this -> db -> where('_m.counter_num',9);
					$this -> db -> where('_m.counter_system','orange');

				}
				if($total_magnification==29){
					$this -> db -> where('_m.counter_num',9);
					$this -> db -> where('_m.counter_system','cherry');

				}
			}



    $this -> db -> limit(1);
		$query = $this -> db -> get();
		$list = $query -> result();
		if(!empty($list) ) {
			return $list;
		} else{
			return NULL;
		}
	}
}
?>
