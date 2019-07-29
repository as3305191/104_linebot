<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Composite extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Post_log_dao', 'post_log_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');

		$this -> load -> model('Transfer_coin_dao', 'tc_dao');
		$this -> load -> model('Com_tx_dao', 'ctx_dao');

		$this -> load -> model('Baccarat_tab_round_detail_dao', 'btrd_dao');
		$this -> load -> model('Baccarat_tab_round_dao', 'btr_dao');
    $this -> load -> model('Products_items_dao', 'products_items_dao');
		$this -> load -> model('Product_strengthen_dao', 'product_strengthen_dao');
		$this -> load -> model('Product_strengthen_record_dao', 'product_s_r_dao');
		$this -> load -> model('Fish_daily_task_dao', 'fish_d_t_dao');

	}

	public function index() {
		echo "Line_img";
	}

	public function do_composite() {
		// $this -> do_log('do_composite');

    $weapon_array = $this -> get_post("weapon_array");//武器
    $components_array = $this -> get_post("components_array");//零件

		$weapon_list=json_decode($weapon_array,TRUE);
		$components_list=json_decode($components_array,TRUE);

    $idata['is_delete']=1;
		$num=count($weapon_list);
		$num1=count($components_list);
		$idata1['user_id']=$weapon_list[0]['user_id'];
		$idata1['product_id']=intval($weapon_list[0]['product_id'])+1;

		$counter_a = 0;//a零件數量計算
		$counter_b = 0;//b零件數量計算
		$counter_c = 0;//c零件數量計算

		$res['weapon_list']=$weapon_list;
		$res['components_array']= $components_list;
		$res['n'] = $num;
		$res['n1'] = $num1;

		$contain_base = FALSE;
		foreach($weapon_array as $each) {
			if($each -> is_base == 1) {
				$contain_base = TRUE;
			}
		}

		if(!$contain_base) {
			if($num >= 2 && $num1 >= 3){
				foreach($components_list as $components) {
					if($components['product_id']==13){
						$counter_a++;
					}
					if($components['product_id']==14){
						$counter_b++;
					}
					if($components['product_id']==15){
						$counter_c++;
					}
				}
				$res['success1']=$counter_a;
				$res['success2']=$counter_b;
				$res['success3']=$counter_c;

				if($weapon_list[0]['product_id'] == $weapon_list[1]['product_id']){
					if($counter_a==$weapon_list[0]['need_material'] && $counter_b==$weapon_list[0]['need_material'] && $counter_c==$weapon_list[0]['need_material']){

					//武器是否同一等級
					$p=mt_rand(1,100);
					if($num==2){
						if($p<=$weapon_list[0]['pct']){
							//抓取此武器要合成的機率
							foreach($weapon_list as $each) {
								$weapon_id = $each['id'];
						    $this -> products_items_dao->delete($weapon_id);
							}
							foreach($components_list as $components) {
					 			$components_id = $components['id'];
					 			$this -> products_items_dao->delete($components_id);
					 		}
							$this -> products_items_dao-> insert($idata1);
							$res['success']="合成成功";
						} else{
							$this -> products_items_dao->delete($weapon_list[0]['id']);
							foreach($components_list as $components) {
								$components_id = $components['id'];
								$this -> products_items_dao->delete($components_id);
							}
							$res['message']="合成失敗";
						}
					 } else {
							 if($num>2){
								 $res['error']="給太多武器";
							 }
							 if($num<2){
								 $res['error']="武器數量不足";
							 }
					 }
					} else{
						$res['error']="零件不足";
					}
				} else{
					$res['error']="武器等級不同，不能合成";
				}
	    } else {
				$res['error']="零件不足";
			}
		} else {
			$res['error']="含有基礎零件無法合成";
		}

		$this -> to_json($res);
	}

	public function show_strengthen() {
		$res = array();
		$payload = $this -> get_payload();
		$user_id = $payload["user_id"];
		$product_id = $this -> get_post("product_id");
		$strengthen_list = $this -> product_strengthen_dao-> find_id($user_id,$product_id);
		$level = 1;
		if(count($strengthen_list) > 0) {
 			$level = $strengthen_list[0] -> level;
		}
		$res['level'] = $level;
		$this -> to_json($res);
	}

	public function do_strengthen() {
		$payload = $this -> get_payload();
		$user_id = $payload["user_id"];
		$getDate = date("Y-m-d");
		$product_id = $this -> get_post("product_id");
		$material_id = $this -> get_post("material_id");

		$idata['is_delete']=1;
		$strengthen = $this -> product_strengthen_dao-> find_id($user_id,$product_id);//刪除強化零件
		$find_p = $this -> products_items_dao -> find_owner($user_id,$product_id);
		$find_m = $this -> products_items_dao -> find_owners($user_id,$material_id);

		$find_day_mission = $this-> fish_d_t_dao -> find_day_mission($user_id,$getDate);
		$idata_11['user_id']=$user_id;
		$idata_11['online_data']=$getDate;
		if(!empty($find_p)) {
			if(!empty($find_m)) {
				if(!empty($strengthen)) {
					$idata1['level']=intval($strengthen[0]->level)+1;
					$idata2['level']=intval($strengthen[0]->level)-1;
					$idata_1['level']=intval($strengthen[0]->level)+1;
					if(!empty($user_id) && !empty($product_id) && !empty($material_id)){
						$p=mt_rand(1,100);
						if($p<=$strengthen[0]->pct){//機率40%
							$idata_1['user_id']=$user_id;
							$idata_1['product_id']=$product_id;
							$idata_1['status']=1;
							if($strengthen[0]->level<20){
								$this -> products_items_dao->delete($material_id);//刪除強化零件
								$this -> product_strengthen_dao-> update_by($idata1,'id',$strengthen[0]->id);
								$this -> product_s_r_dao-> insert($idata_1);

								$res['message']="強化成功";
								if(empty($find_day_mission)){
									$this-> fish_d_t_dao->insert($idata_11);
								} else{
									if($find_day_mission->is_strengthen==0){
										$this-> fish_d_t_dao -> update_by(array("is_lottery"=>1),id,$find_day_mission->id);//每日任務達成
									}
								}
							}
						} else{
							$idata_2['user_id']=$user_id;
							$idata_2['product_id']=$product_id;
							$idata_2['status']=0;
							if($strengthen[0]->level==1){
								$idata_2['level']=1;
								$this -> products_items_dao-> delete($material_id);//刪除強化零件
								$this -> product_s_r_dao-> insert($idata_2);
								$res['message']="強化失敗";
						} else {
								$idata_2['level']=intval($strengthen[0]->level)-1;
								$this -> products_items_dao-> delete($material_id);//刪除強化零件
								$this -> product_strengthen_dao-> update_by($idata2,'id',$strengthen[0]->id);
								$this -> product_s_r_dao-> insert($idata_2);
								$res['message']="強化失敗";
							}
						}
					}
				}  else {
					$idata1['user_id']=$user_id;
					$idata1['product_id']=$product_id;
					$idata1['level']=2;
					$idata1['pct']=40;

					$idata2['user_id']=$user_id;
					$idata2['product_id']=$product_id;
					$idata2['level']=1;
					$idata2['pct']=40;

					$idata_1['user_id']=$user_id;
					$idata_1['product_id']=$product_id;
					$idata_1['level']=2;
					$idata_1['status']=1;

					$idata_2['user_id']=$user_id;
					$idata_2['product_id']=$product_id;
					$idata_2['status']=0;

					if(!empty($user_id) && !empty($product_id) && !empty($material_id)){
						$p=mt_rand(1,100);
						if($p<=$strengthen[0]->pct){//機率40%
							if($strengthen[0]->level<20){
								$this -> products_items_dao->delete($material_id);//刪除強化零件
								$this -> product_strengthen_dao-> insert($idata1);
								$this -> product_s_r_dao-> insert($idata_1);
								$res['message']="強化成功";
								if(empty($find_day_mission)){
									$this->fish_d_t_dao->insert($idata_11);
								} else{
									if($find_day_mission->is_strengthen==0){
										$this-> fish_d_t_dao -> update_by(array("is_lottery"=>1),id,$find_day_mission->id);//每日任務達成
									}
								}
							}
						} else{
							if($strengthen[0]->level==1){
								$this -> products_items_dao-> delete($material_id);//刪除強化零件
								$this -> product_strengthen_dao-> insert($idata2);
								$this -> product_s_r_dao-> insert($idata_2);

								$res['message']="強化失敗";
						} else {
								$this -> products_items_dao-> delete($material_id);//刪除強化零件
								$this -> product_strengthen_dao-> insert($idata2);
								$this -> product_s_r_dao-> insert($idata_2);
								$res['message']="強化失敗";
							}
						}
					}
				}

			}else {
					$res['err_message']="不屬於這個人的強化材料";
				}
		}else {
				$res['err_message']="這個人沒有這個product_id";
			}
		$this -> to_json($res);
	}

	public function line_jpg($img_name, $v, $size) {
		$download_file_name = HOME_DIR . "img/line688/line/$img_name.jpg";
		// header("Content-Disposition: attachment; filename=$img_name.jpg");
		header("Content-type: image/jpeg");
		header("Content-Length: " . filesize($download_file_name));

		ob_clean();
		flush();
		readfile($download_file_name);
		exit ;
		show_404();
	}

	public function line_png($img_name, $v, $size) {
		$download_file_name = HOME_DIR . "img/line688/line/$img_name.png";
		// header("Content-Disposition: attachment; filename=$img_name.jpg");
		header("Content-type: image/png");
		header("Content-Length: " . filesize($download_file_name));

		ob_clean();
		flush();
		readfile($download_file_name);
		exit ;
		show_404();
	}

	public function path_png($path, $img_name, $v, $size) {

		$download_file_name = HOME_DIR . "img/line688/{$path}/{$img_name}.png";
		// echo $download_file_name;
		// header("Content-Disposition: attachment; filename=$img_name.jpg");
		header("Content-type: image/png");
		header("Content-Length: " . filesize($download_file_name));

		ob_clean();
		flush();
		readfile($download_file_name);
		exit ;
		show_404();
	}

	public function payment_jpg($img_name, $v, $size) {
		$download_file_name = HOME_DIR . "img/line688/payment/$img_name.jpg";
		//header("Content-Disposition: attachment; filename=$img_name.jpg");
		header("Content-type: image/jpeg");
		//header("Content-Length: " . filesize($download_file_name));

		ob_clean();
		flush();
		readfile($download_file_name);
		exit ;
		show_404();
	}

	public function constellation($user_id, $v, $size = 0) {

		// phpinfo();
		$user = $this -> users_dao -> find_by_id($user_id);
		$cs = mb_substr($user -> constellation, 0, 2);
		$download_file_name = HOME_DIR . "img/line688/constellation/{$cs}.jpg";
		// echo $download_file_name;
		// exit;
		$font = HOME_DIR . "img/line688/font/wt006.ttf";
		header("Content-type: image/jpeg");

		// Create Image From Existing File
		$jpg_image = imagecreatefromjpeg($download_file_name);

		$black = imagecolorallocate($jpg_image, 0, 0, 0);

		$cs = $this -> c_dao -> find_by("name", $user -> constellation);
		$v_arr = split(",", $cs -> val);
		$v1 = "1";
		$v2 = "2";
		$v3 = "3";
		$v4 = "4";
		$v5 = "5";
		// // Print Text On Image
		imagettftext($jpg_image, 30, 0, 190, 600, $black, $font, $v_arr[0]);
		imagettftext($jpg_image, 30, 0, 500, 600, $black, $font, $v_arr[1]);
		imagettftext($jpg_image, 30, 0, 830, 600, $black, $font, $v_arr[2]);
		imagettftext($jpg_image, 18, 0, 180, 800, $black, $font, $v_arr[3]);
		imagettftext($jpg_image, 30, 0, 500, 800, $black, $font, $v_arr[4]);

		$white = imagecolorallocate($jpg_image, 255, 255, 255);
		imagettftext($jpg_image, 64, 0, 60, 190, $white, $font, date("m,d"));

		$weekday = date('w');
    $weekday = '星期' . ['日', '一', '二', '三', '四', '五', '六'][$weekday];
		imagettftext($jpg_image, 28, 0, 292, 185, $white, $font, $weekday);

		$image = $cs -> img_url;
		$imageData = imagecreatefromstring(file_get_contents($image));
		$imageData = imagescale($imageData, 78);
		imagecopy($jpg_image, $imageData, 420, 130, 0, 0, 78, 62);

		ob_clean();
		flush();

		// Send Image to Browser
		imagejpeg($jpg_image);

		// Clear Memory
		imagedestroy($jpg_image);

		// readfile($download_file_name);
		exit ;
		show_404();
	}

	public function baccarat_road_img($tab_id, $is_thumb = 0, $detail_id = 0, $size = 0) {
		$list = $this -> btrd_dao -> list_road_by_tab_and_detail($tab_id, $detail_id);
		$list = array_reverse($list);

		$download_file_name = HOME_DIR . "img/line688/line_joy/road.jpg";
		// echo $download_file_name;
		// exit;
		$font = HOME_DIR . "img/line688/font/wt006.ttf";


		header("Content-type: image/jpeg");

		// Create Image From Existing File
		$jpg_image = imagecreatefromjpeg($download_file_name);

		$white = imagecolorallocate($jpg_image, 255, 255, 255);
		$red   = imagecolorallocate($jpg_image, 255,   0,   0);
		$green = imagecolorallocate($jpg_image,   0, 255,   0);
		$blue  = imagecolorallocate($jpg_image,   0,   0, 255);
		$yellow  = imagecolorallocate($jpg_image,   255,   255, 0);

		$x = 0;
		$y = 0;
		$bx = 42;
		$by = 36;

		$x_diff = 64;
		$y_diff = 56;

		$tie_count = 0;
		$last_winner = -1;
		$tmp_x = 0;
		$tie_txt = "";

		$last_is_tie = FALSE;
		foreach($list as $each) {
			$color = $red;

			if($each -> winner == 0 && FALSE) {
				$tie_count++;
			} else {
				// if($tie_count > 0) {
				// 	// draw tie count
				// 	$tie_txt = "{$tie_count}";
				// 	$tie_count = 0;
				// }

				if($each -> winner == 1) {
					$last_is_tie = FALSE;
					if($last_winner != 1 && !$last_is_tie) {
						if($last_winner != -1) {
							$x++;
						}
						$y = 0;
						$tmp_x = 0;
					} else {
						$y++;
						if($y > 5) {
							$y = 5;
							$tmp_x++;
						} else {
							$tmp_x = 0;
						}
					}
					$color = $red;
				}

				if($each -> winner == 2) {
					$last_is_tie = FALSE;
					if($last_winner != 2 && !$last_is_tie) {
						if($last_winner != -1) {
							$x++;
						}
						$y = 0;
						$tmp_x = 0;
					} else {
						$y++;
						if($y > 5) {
							$y = 5;
							$tmp_x++;
						} else {
							$tmp_x = 0;
						}
					}
					$color = $blue;
				}

				if($each -> winner == 0) {
					$last_is_tie = TRUE;
					if($last_winner != 0 ) {
						$y++;
					} else {
						$y++;
						if($y > 5) {
							$y = 5;
							$tmp_x++;
						} else {
							$tmp_x = 0;
						}
					}
					$color = $yellow;
				}

				$dx = $bx + ($x + $tmp_x) * $x_diff;
				$dy = $by + $y * $y_diff;
			}

			if($each -> winner > 0) {
				$last_winner = $each -> winner;
			}
		}

		// $x = 0;
		if($x > 15) {
			$x = 15 - $x;
		} else {
			$x = 0;
		}
		$y = 0;
		$bx = 42;
		$by = 36;

		$x_diff = 64;
		$y_diff = 56;

		$tie_count = 0;
		$last_winner = -1;
		$tmp_x = 0;
		$tie_txt = "";

		$last_is_tie = FALSE;
		foreach($list as $each) {
			$color = $red;
			if($each -> winner == 0 && FALSE) {
				// $tie_count++;
			} else {
				// if($tie_count > 0 ) {
				// 	// draw tie count
				// 	$tie_txt = "{$tie_count}";
				// 	$tie_count = 0;
				// }

				if($each -> winner == 1) {
					$last_is_tie = FALSE;
					if($last_winner != 1 && !$last_is_tie) {
						if($last_winner != -1) {
							$x++;
						}
						$y = 0;
						$tmp_x = 0;
					} else {
						$y++;
						if($y > 5) {
							$y = 5;
							$tmp_x++;
						} else {
							$tmp_x = 0;
						}
					}
					$color = $red;
				}
				if($each -> winner == 2) {
					$last_is_tie = FALSE;
					if($last_winner != 2 && !$last_is_tie) {
						if($last_winner != -1 ) {
							$x++;
						}
						// $y++;
						$y = 0;
						$tmp_x = 0;
					} else {
						$y++;
						if($y > 5) {
							$y = 5;
							$tmp_x++;
						} else {
							$tmp_x = 0;
						}
					}
					$color = $blue;
				}

				if($each -> winner == 0) {
					$last_is_tie = TRUE;
					if($last_winner != 0 ) {
						$y++;
						$tmp_x = 0;
					} else {
						$y++;
						if($y > 5) {
							$y = 5;
							$tmp_x++;
						} else {
							$tmp_x = 0;
						}
					}
					$color = $yellow;
				}

				$dx = $bx + ($x + $tmp_x) * $x_diff;
				$dy = $by + $y * $y_diff;

				if($x > -1) {
					imagearc($jpg_image, $dx, $dy, 50, 50,  0, 360, $color);
					imagearc($jpg_image, $dx, $dy, 48, 48,  0, 360, $color);
					imagearc($jpg_image, $dx, $dy, 46, 46,  0, 360, $color);

					if($each -> winner_type == 3) {
						imagefilledellipse($jpg_image, $dx - 17, $dy - 17, 10, 10, $red);
					} elseif($each -> winner_type == 4) {
						imagefilledellipse($jpg_image, $dx + 17, $dy + 17, 10, 10, $blue);
					} elseif($each -> winner_type == 6) {
						imagefilledellipse($jpg_image, $dx - 17, $dy - 17, 10, 10, $red);
						imagefilledellipse($jpg_image, $dx + 17, $dy + 17, 10, 10, $blue);
					}

					// if(!empty($tie_txt)) {
					// 	imagettftext($jpg_image, 30, 0, $dx - 14, $dy + 14, $green, $font, $tie_txt);
					// 	$tie_txt = "";
					// }
				}
			}
			if($each -> winner > 0) {
				$last_winner = $each -> winner;
			}

		}

		if($is_thumb == 1) {
			list($width, $height) = getimagesize($download_file_name);
			$thumb = imagecreatetruecolor($width / 4, $height / 4);
			imagecopyresized($thumb, $jpg_image, 0, 0, 0, 0, $width / 4, $height / 4, $width, $height);
			$jpg_image = $thumb;
		}

		ob_clean();
		flush();

		// Send Image to Browser
		imagejpeg($jpg_image);

		// Clear Memory
		imagedestroy($jpg_image);

		exit ;
		show_404();
	}

	public function two_poker($poker_1, $poker_2, $is_thumb = 0, $v, $size = 0) {
		header("Content-type: image/jpeg");
		$poker_1_img = "";
		$poker_2_img = "";
		if(is_numeric($poker_1)) {
			$poker_1_img = HOME_DIR . "img/line688/poker/{$poker_1}.png";
		}
		if(is_numeric($poker_2)) {
			$poker_2_img = HOME_DIR . "img/line688/poker/{$poker_2}.png";
		}

		list($width, $height) = getimagesize($poker_1_img);
		$poker_1_png = imagecreatefrompng($poker_1_img);
		$out = imagecreatetruecolor($width / 1, $height / 2);

		imagesavealpha($out, true);
    $color = imagecolorallocatealpha($out, 200, 200, 200, 0);
    imagefill($out, 0, 0, $color);

		imagecopyresized($out, $poker_1_png, 0, 0, 0, 0, $width / 2, $height / 2, $width, $height);

		if(!empty($poker_2_img)) {
			$poker_2_png = imagecreatefrompng($poker_2_img);
			imagecopyresized($out, $poker_2_png, $width / 2, 0 , 0, 0, $width / 2, $height / 2, $width, $height);
		}

		ob_clean();
		flush();

		// Send Image to Browser
		imagejpeg($out);

		// Clear Memory
		imagedestroy($out);

		exit ;
		show_404();
	}

	public function one_poker($poker_1, $is_thumb = 0, $v, $size = 0) {
		header("Content-type: image/jpeg");
		$poker_1_img = "";
		$poker_2_img = "";
		if(is_numeric($poker_1)) {
			$poker_1_img = HOME_DIR . "img/line688/poker/{$poker_1}.png";
		}
		if(is_numeric($poker_2)) {
			$poker_2_img = HOME_DIR . "img/line688/poker/{$poker_2}.png";
		}

		list($width, $height) = getimagesize($poker_1_img);
		$poker_1_png = imagecreatefrompng($poker_1_img);
		$out = imagecreatetruecolor($width / 2, $height / 2);

		imagesavealpha($out, true);
    $color = imagecolorallocatealpha($out, 200, 200, 200, 0);
    imagefill($out, 0, 0, $color);

		imagecopyresized($out, $poker_1_png, 0, 0, 0, 0, $width / 2, $height / 2, $width, $height);



		ob_clean();
		flush();

		// Send Image to Browser
		imagejpeg($out);

		// Clear Memory
		imagedestroy($out);

		exit ;
		show_404();
	}

	public function detail_img($detail_id, $is_thumb = 0, $v, $size = 0) {
		$detail = $this -> btrd_dao -> find_by_id($detail_id);
		$a_round = $this -> btr_dao -> find_by_id($detail -> round_id);

		$download_file_name = HOME_DIR . "img/line688/line_joy/detail.jpg";
		$card = HOME_DIR . "img/line688/poker/0.png";
		// echo $download_file_name;
		// exit;
		$font = HOME_DIR . "img/line688/font/Carre.ttf";
		$c_font = HOME_DIR . "img/line688/font/wt005.ttf";
		header("Content-type: image/jpeg");

		// Create Image From Existing File
		// $jpg_image = imagecreatefromjpeg($download_file_name);

		// $black = imagecolorallocate($jpg_image, 0, 0, 0);
		//
		// $cs = $this -> c_dao -> find_by("name", $user -> constellation);
		// $v_arr = split(",", $cs -> val);
		// $v1 = "1";
		// $v2 = "2";
		// $v3 = "3";
		// $v4 = "4";
		// $v5 = "5";
		// // // Print Text On Image
		// imagettftext($jpg_image, 30, 0, 190, 600, $black, $font, $v_arr[0]);
		// imagettftext($jpg_image, 30, 0, 500, 600, $black, $font, $v_arr[1]);
		// imagettftext($jpg_image, 30, 0, 830, 600, $black, $font, $v_arr[2]);
		// imagettftext($jpg_image, 18, 0, 180, 800, $black, $font, $v_arr[3]);
		// imagettftext($jpg_image, 30, 0, 500, 800, $black, $font, $v_arr[4]);
		//
		// $white = imagecolorallocate($jpg_image, 255, 255, 255);
		// imagettftext($jpg_image, 64, 0, 60, 190, $white, $font, date("m,d"));
		//
		// $weekday = date('w');
    // $weekday = '星期' . ['日', '一', '二', '三', '四', '五', '六'][$weekday];
		// imagettftext($jpg_image, 28, 0, 292, 185, $white, $font, $weekday);
		//
		// $image = $cs -> img_url;
		// $imageData = imagecreatefromstring(file_get_contents($image));
		// $imageData = imagescale($imageData, 78);
		// $imageData = imagecreatefrompng($card);
		// imagecopy($jpg_image, $imageData, 420, 130, 0, 0, 78, 62);

		$png = imagecreatefrompng($card);
		$jpeg = imagecreatefromjpeg($download_file_name);

		list($width, $height) = getimagesize($download_file_name);
		list($newwidth, $newheight) = getimagesize($card);

		$out = imagecreatetruecolor($width, $height);

		// left
		imagecopyresampled($out, $jpeg, 0, 0, 0, 0, $width, $height, $width, $height);

		$val = $detail -> player_c_0;
		if(is_numeric($val)) {
			imagecopyresampled($out, imagecreatefrompng(HOME_DIR . "img/line688/poker/{$val}.png"), 250, 80, 0, 0, $newwidth/2.5 , $newheight/2.5, $newwidth, $newheight);
		}
		$val = $detail -> player_c_1;
		if(is_numeric($val)) {
			imagecopyresampled($out, imagecreatefrompng(HOME_DIR . "img/line688/poker/{$val}.png"), 350, 80, 0, 0, $newwidth/2.5, $newheight/2.5, $newwidth, $newheight);
		}

		$val = $detail -> player_c_2;
		if(is_numeric($val)) {
			$r_png = imagerotate(imagecreatefrompng(HOME_DIR . "img/line688/poker/{$val}.png"),90,0);
			imagecopyresampled($out, $r_png, 275, 220, 0, 0, $newheight/2.5, $newwidth/2.5, $newheight, $newwidth);
		}

		$white = imagecolorallocate($out, 255, 255, 255);
		imagettftext($out, 88, 0, 100, 300, $white, $font, $detail -> player_val);

		// right
		$val = $detail -> banker_c_0;
		if(is_numeric($val)) {
			imagecopyresampled($out, imagecreatefrompng(HOME_DIR . "img/line688/poker/{$val}.png"), 600, 80, 0, 0, $newwidth/2.5 , $newheight/2.5, $newwidth, $newheight);
		}

		$val = $detail -> banker_c_1;
		if(is_numeric($val)) {
			imagecopyresampled($out, imagecreatefrompng(HOME_DIR . "img/line688/poker/{$val}.png"), 700, 80, 0, 0, $newwidth/2.5, $newheight/2.5, $newwidth, $newheight);
		}

		$val = $detail -> banker_c_2;
		if(is_numeric($val)) {
			$r_png = imagerotate(imagecreatefrompng(HOME_DIR . "img/line688/poker/{$val}.png"),-90,0);
			imagecopyresampled($out, $r_png, 625, 220, 0, 0, $newheight/2.5, $newwidth/2.5, $newheight, $newwidth);
		}

		$white = imagecolorallocate($out, 255, 255, 255);
		imagettftext($out, 88, 0, 870, 300, $white, $font, $detail -> banker_val);

		// round
		imagettftext($out, 22, 0, 350, 62, $white, $c_font, "局號:{$a_round->sn}-{$detail->id}");

		if($is_thumb == 1) {
			$thumb = imagecreatetruecolor($width / 4, $height / 4);
			imagecopyresized($thumb, $out, 0, 0, 0, 0, $width / 4, $height / 4, $width, $height);
			$out = $thumb;
		}

		ob_clean();
		flush();

		// Send Image to Browser
		imagejpeg($out);

		// Clear Memory
		imagedestroy($out);

		exit ;
		show_404();
	}


	private function do_log($tag = '', $note = '') {
		$i_data['post'] =json_encode($_POST, JSON_UNESCAPED_UNICODE);
		$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$i_data['tag'] = $tag;
		$i_data['full_path'] = $actual_link;
		$i_data['note'] = $note;
		$i_data['q'] = file_get_contents('php://input');
		$i_data['h'] = json_encode(getallheaders());
		$this -> post_log_dao -> insert($i_data);
	}

	private function get_reg_code() {
		$digits = 6;
		return rand(pow(10, $digits - 1), pow(10, $digits) - 1);
	}
}
