<?php
class Transaction extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		// setup models
		$this -> load -> model('Products_dao', 'p_dao');
		$this -> load -> model('Product_items_dao', 'p_i_dao');
		$this -> load -> model('Transaction_record_dao', 't_r_dao');
		$this -> load -> model('Transaction_record_props_dao', 't_r_p_dao');
		$this -> load -> model('Transaction_record_coins_dao', 't_r_c_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Users_dao', 'users_dao');

		$this -> load -> model('Config_dao', 'config_dao');
	}

	public function test() {
		echo "test";
	}

	// 金幣商品列表
	public function list_products_gold() {
		$res = array();
		$res['success'] = TRUE;

		$list = $this -> p_dao -> find_by_parameter(array('style' => 2));

		$res['list'] = $list;
		$this -> to_json($res);
	}

	// 道具商品列表
	public function list_products_props() {
		$res = array();
		$res['success'] = TRUE;

		$list = $this -> p_dao -> find_by_parameter(array('style' => 3));

		$res['list'] = $list;
		$this -> to_json($res);
	}


	public function list_drop_products() {
		$res = array();
		$res['success'] = TRUE;

		$list = $this -> p_dao -> find_all_droped();

		$res['list'] = $list;
		$this -> to_json($res);
	}

	public function list_all_products() {
		$res = array();
		$res['success'] = TRUE;

		$list = $this -> p_dao -> find_all();

		$res['list'] = $list;
		$this -> to_json($res);
	}

	public function create_product_item() {
		$res = array();
		$res['success'] = TRUE;

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];
		$product_id = $this -> get_post('product_id');
		if(!empty($user_id) && !empty($product_id)) {
			$last_id = $this -> p_i_dao -> insert(array(
				'user_id' => $user_id,
				'product_id' => $product_id,
				'is_test' => 1,
			));
			$res['item'] = $this -> p_i_dao -> find_by_id($last_id);
		} else {
			$res['error_msg'] = "缺少必要欄位";
		}


		$this -> to_json($res);
	}

	// 拍賣類別列表
	public function list_products_cate() {
		$res = array();
		$res['success'] = TRUE;

		$list = $this -> p_dao -> find_by_parameter(array('style' => 1));

		$res['list'] = $list;
		$this -> to_json($res);
	}

	// 拍賣類別商品列表
	public function list_products() {
		$res = array();
		$res['success'] = TRUE;

		$page = $this -> get_post('page');
		$product_id = $this -> get_post('product_id');

		if(!empty($product_id)){
			$list = $this -> t_r_dao -> find_by_parameter(array('product_id'=> $product_id,'page' => $page));
			$res['list'] = $list;
		}else{
			$res['error_msg'] = "缺少必要欄位";
		}

		$this -> to_json($res);
	}

	// 上架
	public function do_shelf() {
		$res = array();
		$res['success'] = TRUE;

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$product_item_id = $this -> get_post('product_item_id');
		$product_id = $this -> get_post('product_id');
		$price = $this -> get_post('price');

		if(!empty($user_id) && !empty($product_item_id) && !empty($product_id) && $price!=''){
			$itemList = $this -> p_i_dao -> find_by_parameter(array('id'=> $product_item_id));
			if(!empty($itemList)){
				$item = $itemList[0];
				if($item -> is_base == 0) {
					$pct = $item -> price;
					if($price >= $pct){

						$this -> p_i_dao -> update(array('user_id'=> 0), $product_item_id);
						$insert_data = array('user_id'=> $user_id, 'product_item_id' => $product_item_id, 'product_id' => $product_id,'price' => $price);
						$record_id = $this -> t_r_dao -> insert($insert_data);

						$res['record_id'] = $record_id;
					}else{
						$res['error_msg'] = "定價不可低於最低價";
					}
				} else {
					$res['error_msg'] = "無法販賣個人基本物品";
				}
			}else{
				$res['error_msg'] = "此商品不存在";
			}
		}else{
			$res['error_msg'] = "缺少必要欄位";
		}

		$this -> to_json($res);
	}

	// 購買砲塔室交易
	public function do_purchase() {
		$res = array();

		// $user_id = $this -> get_post('user_id');
		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$id = $this -> get_post('id');

		if(!empty($user_id) && !empty($id)){
			$m = $this-> t_r_dao -> find_by_id($id);
			if(!empty($m)){
				if($m->status > 0){
					$res['error_msg'] = "此商品已售出";
				}else{
					// User錢包
					$user_data = $this -> users_dao -> find_by_id($user_id);

					$sum_amt = $this -> wtx_dao -> get_sum_amt($user_id);
					$sum_amt = intval($sum_amt);

					$price = $m -> price;
					$shop_user_id = $m -> user_id;
					$product_id = $m -> product_id;
					$shop_user_data = $this -> users_dao -> find_by_id($shop_user_id);

					$p_data = $this -> p_dao -> find_by_id($product_id);

					if($sum_amt > $price){
						$this-> p_i_dao -> update(array('user_id'=> $user_id), $m-> product_item_id);
						$this-> t_r_dao -> update(array('status'=> 1), $id);
						$data = $this-> p_i_dao -> find_by_id($m -> product_item_id);

						// 新增購買道具交易紀錄
						$i_data = array('user_id'=> $user_id,'shop_user_id'=> $shop_user_id, 'product_id'=> $product_id, 'amt'=> $price, 'number'=> 1, 'total'=> $price);
						$props_id = $this -> t_r_p_dao -> insert($i_data);

						// 減少買家錢包金幣
						$tx = array();
						$tx['tx_type'] = "purchase turret props";
						$tx['tx_id'] = $props_id;
						$tx['corp_id'] = 1; // corp id
						$tx['user_id'] = $user_id;
						$tx['amt'] = -($price);
						$tx['brief'] = "$user_data->nick_name 購買 $p_data->product_name -{$price}";
						$this -> wtx_dao -> insert($tx);

						// 增加賣家錢包金幣
						$tx = array();
						$tx['tx_type'] = "sold turret props";
						$tx['tx_id'] = $props_id;
						$tx['corp_id'] = 1; // corp id
						$tx['user_id'] = $shop_user_id;
						$tx['amt'] = $price;
						$tx['brief'] = "$shop_user_data->nick_name 售出 $p_data->product_name +{$price}";
						$this -> wtx_dao -> insert($tx);

						$res['success'] = TRUE;
					}else{
						$res['error_msg'] = "金幣不足，無法購買";
						$res['amt'] = $sum_amt;
					}

				}
			}else{
				$res['error_msg'] = "此商品不存在";
			}
		}else{
			$res['error_msg'] = "缺少必要欄位";
		}
		$this -> to_json($res);
	}

	// 購買道具
	public function do_purchase_props() {
		$res = array();

		// $user_id = $this -> get_post('user_id');
		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$product_id = $this -> get_post('product_id');
		$number = $this -> get_post('number');

		if(!empty($user_id) && !empty($product_id)){
			$num = 1;
			if(!empty($number)){
				$num = intval($number);
			}

			$user_data = $this -> users_dao -> find_by_id($user_id);

			$p_data = $this -> p_dao -> find_by_id($product_id);
			$p_amt = intval($p_data -> amt);
			$total = $p_amt * $num;

			$sum_amt = $this -> wtx_dao -> get_sum_amt($user_id);
			$sum_amt = intval($sum_amt);

			if($sum_amt > $total){
				// 新增購買道具交易紀錄
				$i_data = array('user_id'=> $user_id, 'product_id'=> $product_id, 'amt'=> $p_amt, 'number'=> $num, 'total'=> $total);
				$props_id = $this -> t_r_p_dao -> insert($i_data);

				// 減少買家錢包金幣
				$tx = array();
				$tx['tx_type'] = "purchase mall props";
				$tx['tx_id'] = $props_id;
				$tx['corp_id'] = 1; // corp id
				$tx['user_id'] = $user_id;
				$tx['amt'] = -($total);
				$tx['brief'] = "$user_data->nick_name 購買 $p_data->product_name -{$total}";
				$this -> wtx_dao -> insert($tx);

				// 新增道具至買家庫存
				$item_id_arr = array();
				for($x = 0; $x < $num; $x++){
					$ii_data = array('user_id'=> $user_id, 'product_id'=> $product_id);
					$item_id = $this -> p_i_dao -> insert($ii_data);
					$item_id_arr[] = $item_id;
				}

				$res['tx_id'] = $props_id;
				$res['item_id_arr'] = $item_id_arr;
			}else{
				$res['error_msg'] = "金幣不足，無法購買";
			}
		}else{
			$res['error_msg'] = "缺少必要欄位";
		}

		$this -> to_json($res);
	}

	// 使用者庫存商品列表
	public function list_stock_all() {
		$res = array();
		$res['success'] = TRUE;

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		if(empty($user_id) ) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$list = $this -> p_dao -> find_by_parameter(array('style' => 1));
			foreach ($list as $each) {
				$item_list = $this -> p_i_dao -> find_by_parameter(array('user_id'=> $user_id,'product_id'=> $each->id));
				$each -> count = count($item_list);
				$each -> detail = $item_list;
			}

			$res['list'] = $list;
		}
		$this -> to_json($res);
	}

	// 購買金幣-取得序號
	public function get_ordersn() {
		$res = array();
		$res['success'] = TRUE;

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$product_id = $this -> get_post('product_id');
		$number = $this -> get_post('number');

		if(!empty($user_id) && !empty($product_id)){
			$num = 1;
			if(!empty($number)){
				$num = intval($number);
			}

			$p_data = $this -> p_dao -> find_by_id($product_id);
			$p_amt = intval($p_data -> amt);
			$total = $p_amt * $num;

			$date = date('YmdHis');
			$i_data = array('shop_user_id'=> $user_id, 'product_id'=> $product_id, 'amt'=> $p_amt, 'number'=> $num, 'total'=> $total);
			$props_id = $this -> t_r_c_dao -> insert($i_data);

			$sn = $date.'C'.$props_id;
			$this -> t_r_c_dao -> update(array('sn'=> $sn),$props_id);
			$res['sn'] = $sn;
		}else{
			$res['error_msg'] = "缺少必要欄位";
		}

		$this -> to_json($res);
	}

	// 購買金幣-付款驗證
	public function do_pay() {
		$res = array();
		$res['success'] = TRUE;

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$sn = $this -> get_post('sn');
		$amt = $this -> get_post('amt');
		$check = $this -> get_post('check');

		$hash_key = 'bdhashkey';

		if(!empty($user_id) && !empty($sn) && !empty($amt) && !empty($check)){
			$list = $this -> t_r_c_dao -> find_by_parameter(array('sn' => $sn, 'shop_user_id'=> $user_id));
			$m = $list[0];
			$m_check = md5("{$hash_key}{$m->sn}");
			if($check == $m_check ){
				$status = $m -> status;
				$total = $m -> total;
				if($status == 0){
					if($total == $amt){
						$number = $m -> number;
						$p_data = $this -> p_dao -> find_by_id($m-> product_id);
						$coins = $p_data -> coins;
						$total  = $number * $coins;

						$user_data = $this -> users_dao -> find_by_id($user_id);

						// 增加買家錢包金幣
						$tx = array();
						$tx['tx_type'] = "purchase gold coins";
						$tx['tx_id'] = $m -> id;
						$tx['corp_id'] = 1; // corp id
						$tx['user_id'] = $user_id;
						$tx['amt'] = $total;
						$tx['brief'] = "$user_data->nick_name 購買 $p_data->product_name X $number  +{$total}";
						$this -> wtx_dao -> insert($tx);

						$this -> t_r_c_dao -> update(array('status' => 1),$m -> id);

						$res['msg'] = "付款成功";
					}else{
						$res['error_msg'] = "訂單金額不符";
					}
				}else{
					$res['error_msg'] = "訂單已付款";
				}
			}else{
				$res['data'] = $m_check;
				$res['error_msg'] = "驗證碼錯誤";
			}
		}else{
			$res['error_msg'] = "缺少必要欄位";
		}

		$this -> to_json($res);
	}




}
?>
