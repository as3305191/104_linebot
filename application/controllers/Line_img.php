<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Line_img extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Post_log_dao', 'post_log_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');

		$this -> load -> model('Transfer_coin_dao', 'tc_dao');
		$this -> load -> model('Com_tx_dao', 'ctx_dao');

		$this -> load -> model('Baccarat_tab_round_detail_dao', 'btrd_dao');
		$this -> load -> model('Baccarat_tab_round_dao', 'btr_dao');
		$this -> load -> model('Play_game_dao', 'play_game_dao');
		$this -> load -> model('Daily_quotes_dao', 'd_q_dao');


		// line models

	}

	public function index() {
		echo "Line_img";
	}


	public function service_img($v, $size) {
		$download_file_name = HOME_DIR . "img/line688/line/service.jpg";
		header("Content-Disposition: attachment; filename=service.jpg");
		header("Content-type: image/jpeg");
		header("Content-Length: " . filesize($download_file_name));

		ob_clean();
		flush();
		readfile($download_file_name);
		exit ;
		show_404();
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

	public function line_result($id, $v, $size = 0) {
		$bet = $this -> play_game_dao -> find_by_id($id);
		$sum_amt = $this -> wtx_dao -> get_sum_amt($bet ->user_id);
		$result = $bet -> result;
		$value = json_decode($result);
		$win_status = $bet -> win_status;
		$win = json_decode($win_status);

		$im = HOME_DIR . "img/line688/line/0807.jpg";
		header("Content-Disposition: attachment; ");
		header("Content-type: image/jpeg");
		// header("Content-Length: " . filesize($im)); // 不要加這行
		$jpg_image = imagecreatefromjpeg($im);
		$font = HOME_DIR . "img/line688/font/wt006.ttf";

		$black = imagecolorallocate($jpg_image, 0, 0, 0);
		$white = imagecolorallocate($jpg_image, 255, 255, 255);
		$img00 = $value[0][0];
		$img01 = $value[0][1];
		$img02 = $value[0][2];
		$img10 = $value[1][0];
		$img11 = $value[1][1];
		$img12 = $value[1][2];
		$img20 = $value[2][0];
		$img21 = $value[2][1];
		$img22 = $value[2][2];

		$total_win_point=mb_substr($bet->total_win_point,0,-7);
		$bet_b=mb_substr($bet->bet,0,-7);
		$bet_sum_amt=mb_substr($sum_amt,0,-7);
		$bet_bureau_num=$bet->bureau_num;

		imagettftext($jpg_image, 30, 0, 510, 260, $white, $font, $bet_b);
		imagettftext($jpg_image, 30, 0, 220, 260, $white, $font, $bet_bureau_num);
		imagettftext($jpg_image, 30, 0, 275, 1375, $white, $font, $total_win_point);
		imagettftext($jpg_image, 30, 0, 810, 260, $white, $font, $bet_sum_amt);


		$pic_00 = HOME_DIR . "img/line688/line/$img00.png";
		$pic_01 = HOME_DIR . "img/line688/line/$img01.png";
		$pic_02 = HOME_DIR . "img/line688/line/$img02.png";
		$pic_10 = HOME_DIR . "img/line688/line/$img10.png";
		$pic_11 = HOME_DIR . "img/line688/line/$img11.png";
		$pic_12 = HOME_DIR . "img/line688/line/$img12.png";
		$pic_20 = HOME_DIR . "img/line688/line/$img20.png";
		$pic_21 = HOME_DIR . "img/line688/line/$img21.png";
		$pic_22 = HOME_DIR . "img/line688/line/$img22.png";
		$light = HOME_DIR . "img/line688/line/light.png";

		$imageData_00 = imagecreatefromstring(file_get_contents($pic_00));
		$imageData_00 = imagescale($imageData_00, 200,200);
		imagecopy($jpg_image, $imageData_00, 105, 385, 0, 0, 200, 200);

		$imageData_01 = imagecreatefromstring(file_get_contents($pic_01));
		$imageData_01 = imagescale($imageData_01, 200,200);
		imagecopy($jpg_image, $imageData_01, 420, 385, 0, 0, 200, 200);

		$imageData_02 = imagecreatefromstring(file_get_contents($pic_02));
		$imageData_02 = imagescale($imageData_02, 200,200);
		imagecopy($jpg_image, $imageData_02, 740, 385, 0, 0, 200, 200);

		$imageData_10 = imagecreatefromstring(file_get_contents($pic_10));
		$imageData_10 = imagescale($imageData_10, 200,200);
		imagecopy($jpg_image, $imageData_10, 105, 700, 0, 0, 200, 200);

		$imageData_11 = imagecreatefromstring(file_get_contents($pic_11));
		$imageData_11 = imagescale($imageData_11, 200,200);
		imagecopy($jpg_image, $imageData_11, 420, 700, 0, 0, 200, 200);

		$imageData_12 = imagecreatefromstring(file_get_contents($pic_12));
		$imageData_12 = imagescale($imageData_12, 200,200);
		imagecopy($jpg_image, $imageData_12, 740, 700, 0, 0, 200, 200);

		$imageData_20 = imagecreatefromstring(file_get_contents($pic_20));
		$imageData_20 = imagescale($imageData_20, 200,200);
		imagecopy($jpg_image, $imageData_20, 105, 1020, 0, 0, 200, 200);

		$imageData_21 = imagecreatefromstring(file_get_contents($pic_21));
		$imageData_21 = imagescale($imageData_21, 200,200);
		imagecopy($jpg_image, $imageData_21, 420, 1020, 0, 0, 200, 200);

		$imageData_22 = imagecreatefromstring(file_get_contents($pic_22));
		$imageData_22 = imagescale($imageData_22, 200,200);
		imagecopy($jpg_image, $imageData_22, 740, 1020, 0, 0, 200, 200);

		if(!empty($win->line1)){

			$imageData_11 = imagecreatefromstring(file_get_contents($light));
			$imageData_11 = imagescale($imageData_11, 350,390);
			imagecopy($jpg_image, $imageData_11, 30, 605, 0, 0, 350, 390);//10

			$imageData_12 = imagecreatefromstring(file_get_contents($light));
			$imageData_12 = imagescale($imageData_12, 350,390);
			imagecopy($jpg_image, $imageData_12, 350, 605, 0, 0, 350, 390);//11

			$imageData_13 = imagecreatefromstring(file_get_contents($light));
			$imageData_13 = imagescale($imageData_13, 350,390);
			imagecopy($jpg_image, $imageData_13, 670, 605, 0, 0, 350, 390);//12
		}

		if(!empty($win->line2)){
			$imageData_11 = imagecreatefromstring(file_get_contents($light));
			$imageData_11 = imagescale($imageData_11, 350,390);
			imagecopy($jpg_image, $imageData_11, 30, 300, 0, 0, 350, 390);//00

			$imageData_41 = imagecreatefromstring(file_get_contents($light));
			$imageData_41 = imagescale($imageData_41, 350,390);
			imagecopy($jpg_image, $imageData_41, 350, 300, 0, 0, 350, 390);//01

			$imageData_13 = imagecreatefromstring(file_get_contents($light));
			$imageData_13 = imagescale($imageData_13, 350,390);
			imagecopy($jpg_image, $imageData_13, 670, 300, 0, 0, 350, 390);//02
		}

		if(!empty($win->line3)){
			$imageData_31 = imagecreatefromstring(file_get_contents($light));
			$imageData_31 = imagescale($imageData_31, 350,390);
			imagecopy($jpg_image, $imageData_31, 30, 920, 0, 0, 350, 390);//20

			$imageData_32 = imagecreatefromstring(file_get_contents($light));
			$imageData_32 = imagescale($imageData_32, 350,390);
			imagecopy($jpg_image, $imageData_32, 350, 920, 0, 0, 350, 390);//21

			$imageData_33 = imagecreatefromstring(file_get_contents($light));
			$imageData_33 = imagescale($imageData_33, 350,390);
			imagecopy($jpg_image, $imageData_33, 670, 920, 0, 0, 350, 390);//22
		}

		if(!empty($win->line4)){
			$imageData_41 = imagecreatefromstring(file_get_contents($light));
			$imageData_41 = imagescale($imageData_41, 350,390);
			imagecopy($jpg_image, $imageData_41, 30, 300, 0, 0, 350, 390);//00

			$imageData_42 = imagecreatefromstring(file_get_contents($light));
			$imageData_42 = imagescale($imageData_42, 350,390);
			imagecopy($jpg_image, $imageData_42, 350, 605, 0, 0, 350, 390);//11

			$imageData_43 = imagecreatefromstring(file_get_contents($light));
			$imageData_43 = imagescale($imageData_43, 350,390);
			imagecopy($jpg_image, $imageData_43, 670, 920, 0, 0, 350, 390);//22
		}

		if(!empty($win->line5)){
			$imageData_51 = imagecreatefromstring(file_get_contents($light));
			$imageData_51 = imagescale($imageData_51, 350,390);
			imagecopy($jpg_image, $imageData_51, 670, 300, 0, 0, 350, 390);//02

			$imageData_52 = imagecreatefromstring(file_get_contents($light));
			$imageData_52 = imagescale($imageData_52, 350,390);
			imagecopy($jpg_image, $imageData_52, 350, 605, 0, 0, 350, 390);//11

			$imageData_53 = imagecreatefromstring(file_get_contents($light));
			$imageData_53 = imagescale($imageData_53, 350,390);
			imagecopy($jpg_image, $imageData_53, 30, 920, 0, 0, 350, 390);//20
		}

		if(!empty($win->line6)){

			$imageData_61 = imagecreatefromstring(file_get_contents($light));
			$imageData_61 = imagescale($imageData_61, 350,390);
			imagecopy($jpg_image, $imageData_61, 670, 300, 0, 0, 350, 390);//02

			$imageData_62 = imagecreatefromstring(file_get_contents($light));
			$imageData_62 = imagescale($imageData_62, 350,390);
			imagecopy($jpg_image, $imageData_62, 670, 605, 0, 0, 350, 390);//12

			$imageData_63 = imagecreatefromstring(file_get_contents($light));
			$imageData_63 = imagescale($imageData_63, 350,390);
			imagecopy($jpg_image, $imageData_63, 670, 920, 0, 0, 350, 390);//22
		}

		if(!empty($win->line7)){
			$imageData_71 = imagecreatefromstring(file_get_contents($light));
			$imageData_71 = imagescale($imageData_71, 350,390);
			imagecopy($jpg_image, $imageData_71, 350, 300, 0, 0, 350, 390);//01

			$imageData_72 = imagecreatefromstring(file_get_contents($light));
			$imageData_72 = imagescale($imageData_72, 350,390);
			imagecopy($jpg_image, $imageData_72, 350, 605, 0, 0, 350, 390);//11

			$imageData_73 = imagecreatefromstring(file_get_contents($light));
			$imageData_73 = imagescale($imageData_73, 350,390);
			imagecopy($jpg_image, $imageData_73, 350, 920, 0, 0, 350, 390);//21
		}

		if(!empty($win->line8)){
			$imageData_81 = imagecreatefromstring(file_get_contents($light));
			$imageData_81 = imagescale($imageData_81, 350,390);
			imagecopy($jpg_image, $imageData_81, 30, 300, 0, 0, 350, 390);//00

			$imageData_82 = imagecreatefromstring(file_get_contents($light));
			$imageData_82 = imagescale($imageData_82, 350,390);
			imagecopy($jpg_image, $imageData_82, 30, 605, 0, 0, 350, 390);//10

			$imageData_83 = imagecreatefromstring(file_get_contents($light));
			$imageData_83 = imagescale($imageData_83, 350,390);
			imagecopy($jpg_image, $imageData_83, 30, 920, 0, 0, 350, 390);//20
		}

		if(!empty($win->c)){
			$imageData_81 = imagecreatefromstring(file_get_contents($light));
			$imageData_81 = imagescale($imageData_81, 350,390);
			imagecopy($jpg_image, $imageData_81, 30, 300, 0, 0, 350, 390);//00

			$imageData_82 = imagecreatefromstring(file_get_contents($light));
			$imageData_82 = imagescale($imageData_82, 350,390);
			imagecopy($jpg_image, $imageData_82, 30, 605, 0, 0, 350, 390);//10

			$imageData_83 = imagecreatefromstring(file_get_contents($light));
			$imageData_83 = imagescale($imageData_83, 350,390);
			imagecopy($jpg_image, $imageData_83, 30, 920, 0, 0, 350, 390);//20
		}


		if(!empty($win->not_line)){
			$imageData_ = imagecreatefromstring(file_get_contents($light));
			$imageData_ = imagescale($imageData_, 350,390);
			imagecopy($jpg_image, $imageData_, 30, 300, 0, 0, 350, 390);//00

			$imageData_1 = imagecreatefromstring(file_get_contents($light));
			$imageData_1 = imagescale($imageData_1, 350,390);
			imagecopy($jpg_image, $imageData_1, 350, 300, 0, 0, 350, 390);//01

			$imageData_2 = imagecreatefromstring(file_get_contents($light));
			$imageData_2 = imagescale($imageData_2, 350,390);
			imagecopy($jpg_image, $imageData_2, 670, 300, 0, 0, 350, 390);//02

			$imageData_3 = imagecreatefromstring(file_get_contents($light));
			$imageData_3 = imagescale($imageData_3, 350,390);
			imagecopy($jpg_image, $imageData_3, 30, 605, 0, 0, 350, 390);//10

			$imageData_4 = imagecreatefromstring(file_get_contents($light));
			$imageData_4 = imagescale($imageData_4, 350,390);
			imagecopy($jpg_image, $imageData_4, 350, 605, 0, 0, 350, 390);//11

			$imageData_5 = imagecreatefromstring(file_get_contents($light));
			$imageData_5 = imagescale($imageData_5, 350,390);
			imagecopy($jpg_image, $imageData_5, 670, 605, 0, 0, 350, 390);//12

			$imageData_6 = imagecreatefromstring(file_get_contents($light));
			$imageData_6 = imagescale($imageData_6, 350,390);
			imagecopy($jpg_image, $imageData_6, 30, 920, 0, 0, 350, 390);//20

			$imageData_7 = imagecreatefromstring(file_get_contents($light));
			$imageData_7 = imagescale($imageData_7, 350,390);
			imagecopy($jpg_image, $imageData_7, 350, 920, 0, 0, 350, 390);//21

			$imageData_8 = imagecreatefromstring(file_get_contents($light));
			$imageData_8 = imagescale($imageData_8, 350,390);
			imagecopy($jpg_image, $imageData_8, 670, 920, 0, 0, 350, 390);//22
		}

		if(!empty($win->seven)){
			if(mb_substr($img00,0,-2)=="seven"){
				$imageData_seven = imagecreatefromstring(file_get_contents($light));
				$imageData_seven = imagescale($imageData_seven, 350,390);
				imagecopy($jpg_image, $imageData_seven, 30, 300, 0, 0, 350, 390);//00
			}
			if(mb_substr($img01,0,-2)=="seven"){
				$imageData_seven = imagecreatefromstring(file_get_contents($light));
				$imageData_seven = imagescale($imageData_seven, 350,390);
				imagecopy($jpg_image, $imageData_seven, 350, 300, 0, 0, 350, 390);//01

			}
			if(mb_substr($img02,0,-2)=="seven"){
				$imageData_seven = imagecreatefromstring(file_get_contents($light));
				$imageData_seven = imagescale($imageData_seven, 350,390);
				imagecopy($jpg_image, $imageData_seven, 670, 300, 0, 0, 350, 390);//02

			}
			if(mb_substr($img10,0,-2)=="seven"){
				$imageData_seven = imagecreatefromstring(file_get_contents($light));
				$imageData_seven = imagescale($imageData_seven, 350,390);
				imagecopy($jpg_image, $imageData_seven, 30, 605, 0, 0, 350, 390);//10
			}
			if(mb_substr($img11,0,-2)=="seven"){
				$imageData_seven = imagecreatefromstring(file_get_contents($light));
				$imageData_seven = imagescale($imageData_seven, 350,390);
				imagecopy($jpg_image, $imageData_seven, 350, 605, 0, 0, 350, 390);//11

			}
			if(mb_substr($img12,0,-2)=="seven"){
				$imageData_seven = imagecreatefromstring(file_get_contents($light));
				$imageData_seven = imagescale($imageData_seven, 350,390);
				imagecopy($jpg_image, $imageData_seven, 670, 605, 0, 0, 350, 390);//12
			}
			if(mb_substr($img20,0,-2)=="seven"){
				$imageData_seven = imagecreatefromstring(file_get_contents($light));
				$imageData_seven = imagescale($imageData_seven, 350,390);
				imagecopy($jpg_image, $imageData_seven, 30, 920, 0, 0, 350, 390);//20
			}
			if(mb_substr($img21,0,-2)=="seven"){
				$imageData_seven = imagecreatefromstring(file_get_contents($light));
				$imageData_seven = imagescale($imageData_seven, 350,390);
				imagecopy($jpg_image, $imageData_seven, 350, 920, 0, 0, 350, 390);//21

			}
			if(mb_substr($img22,0,-2)=="seven"){
				$imageData_seven = imagecreatefromstring(file_get_contents($light));
				$imageData_seven = imagescale($imageData_seven, 350,390);
				imagecopy($jpg_image, $imageData_seven, 670, 920, 0, 0, 350, 390);//22
			}
		}

		if(!empty($win->bar)){
			if($img00=="bar"){
				$imageData_bar = imagecreatefromstring(file_get_contents($light));
				$imageData_bar = imagescale($imageData_bar, 350,390);
				imagecopy($jpg_image, $imageData_bar, 30, 300, 0, 0, 350, 390);//00
			}
			if($img01=="bar"){
				$imageData_bar = imagecreatefromstring(file_get_contents($light));
				$imageData_bar = imagescale($imageData_bar, 350,390);
				imagecopy($jpg_image, $imageData_bar, 350, 300, 0, 0, 350, 390);//01

			}
			if($img02=="bar"){
				$imageData_bar = imagecreatefromstring(file_get_contents($light));
				$imageData_bar = imagescale($imageData_bar, 350,390);
				imagecopy($jpg_image, $imageData_bar, 670, 300, 0, 0, 350, 390);//02

			}
			if($img10=="bar"){
				$imageData_bar = imagecreatefromstring(file_get_contents($light));
				$imageData_bar = imagescale($imageData_bar, 350,390);
				imagecopy($jpg_image, $imageData_bar, 30, 605, 0, 0, 350, 390);//10
			}
			if($img11=="bar"){
				$imageData_bar = imagecreatefromstring(file_get_contents($light));
				$imageData_bar = imagescale($imageData_bar, 350,390);
				imagecopy($jpg_image, $imageData_bar, 350, 605, 0, 0, 350, 390);//11

			}
			if($img12=="bar"){
				$imageData_bar = imagecreatefromstring(file_get_contents($light));
				$imageData_bar = imagescale($imageData_bar, 350,390);
				imagecopy($jpg_image, $imageData_bar, 670, 605, 0, 0, 350, 390);//12
			}
			if($img20=="bar"){
				$imageData_bar = imagecreatefromstring(file_get_contents($light));
				$imageData_bar = imagescale($imageData_bar, 350,390);
				imagecopy($jpg_image, $imageData_bar, 30, 920, 0, 0, 350, 390);//20
			}
			if($img21=="bar"){
				$imageData_bar = imagecreatefromstring(file_get_contents($light));
				$imageData_bar = imagescale($imageData_bar, 350,390);
				imagecopy($jpg_image, $imageData_bar, 350, 920, 0, 0, 350, 390);//21

			}
			if($img22=="bar"){
				$imageData_bar = imagecreatefromstring(file_get_contents($light));
				$imageData_bar = imagescale($imageData_bar, 350,390);
				imagecopy($jpg_image, $imageData_bar, 670, 920, 0, 0, 350, 390);//22
			}
		}

		if(!empty($win->medal)){
			if($img00=="medal"){
				$imageData_medal = imagecreatefromstring(file_get_contents($light));
				$imageData_medal = imagescale($imageData_medal, 350,390);
				imagecopy($jpg_image, $imageData_medal, 30, 300, 0, 0, 350, 390);//00
			}
			if($img01=="medal"){
				$imageData_medal = imagecreatefromstring(file_get_contents($light));
				$imageData_medal = imagescale($imageData_medal, 350,390);
				imagecopy($jpg_image, $imageData_medal, 350, 300, 0, 0, 350, 390);//01

			}
			if($img02=="medal"){
				$imageData_medal = imagecreatefromstring(file_get_contents($light));
				$imageData_medal = imagescale($imageData_medal, 350,390);
				imagecopy($jpg_image, $imageData_medal, 670, 300, 0, 0, 350, 390);//02

			}
			if($img10=="medal"){
				$imageData_medal = imagecreatefromstring(file_get_contents($light));
				$imageData_medal = imagescale($imageData_medal, 350,390);
				imagecopy($jpg_image, $imageData_medal, 30, 605, 0, 0, 350, 390);//10
			}
			if($img11=="medal"){
				$imageData_medal = imagecreatefromstring(file_get_contents($light));
				$imageData_medal = imagescale($imageData_medal, 350,390);
				imagecopy($jpg_image, $imageData_medal, 350, 605, 0, 0, 350, 390);//11

			}
			if($img12=="medal"){
				$imageData_medal = imagecreatefromstring(file_get_contents($light));
				$imageData_medal = imagescale($imageData_medal, 350,390);
				imagecopy($jpg_image, $imageData_medal, 670, 605, 0, 0, 350, 390);//12
			}
			if($img20=="medal"){
				$imageData_medal = imagecreatefromstring(file_get_contents($light));
				$imageData_medal = imagescale($imageData_medal, 350,390);
				imagecopy($jpg_image, $imageData_medal, 30, 920, 0, 0, 350, 390);//20
			}
			if($img21=="medal"){
				$imageData_medal = imagecreatefromstring(file_get_contents($light));
				$imageData_medal = imagescale($imageData_medal, 350,390);
				imagecopy($jpg_image, $imageData_medal, 350, 920, 0, 0, 350, 390);//21

			}
			if($img22=="medal"){
				$imageData_medal = imagecreatefromstring(file_get_contents($light));
				$imageData_medal = imagescale($imageData_medal, 350,390);
				imagecopy($jpg_image, $imageData_medal, 670, 920, 0, 0, 350, 390);//22
			}
		}

		if(!empty($win->bell)){
			if($img00=="bell"){
				$imageData_bell = imagecreatefromstring(file_get_contents($light));
				$imageData_bell = imagescale($imageData_bell, 350,390);
				imagecopy($jpg_image, $imageData_bell, 30, 300, 0, 0, 350, 390);//00
			}
			if($img01=="bell"){
				$imageData_bell = imagecreatefromstring(file_get_contents($light));
				$imageData_bell = imagescale($imageData_bell, 350,390);
				imagecopy($jpg_image, $imageData_bell, 350, 300, 0, 0, 350, 390);//01

			}
			if($img02=="bell"){
				$imageData_bell = imagecreatefromstring(file_get_contents($light));
				$imageData_bell = imagescale($imageData_bell, 350,390);
				imagecopy($jpg_image, $imageData_bell, 670, 300, 0, 0, 350, 390);//02

			}
			if($img10=="bell"){
				$imageData_bell = imagecreatefromstring(file_get_contents($light));
				$imageData_bell = imagescale($imageData_bell, 350,390);
				imagecopy($jpg_image, $imageData_bell, 30, 605, 0, 0, 350, 390);//10
			}
			if($img11=="bell"){
				$imageData_bell = imagecreatefromstring(file_get_contents($light));
				$imageData_bell = imagescale($imageData_bell, 350,390);
				imagecopy($jpg_image, $imageData_bell, 350, 605, 0, 0, 350, 390);//11

			}
			if($img12=="bell"){
				$imageData_bell = imagecreatefromstring(file_get_contents($light));
				$imageData_bell = imagescale($imageData_bell, 350,390);
				imagecopy($jpg_image, $imageData_bell, 670, 605, 0, 0, 350, 390);//12
			}
			if($img20=="bell"){
				$imageData_bell = imagecreatefromstring(file_get_contents($light));
				$imageData_bell = imagescale($imageData_bell, 350,390);
				imagecopy($jpg_image, $imageData_bell, 30, 920, 0, 0, 350, 390);//20
			}
			if($img21=="bell"){
				$imageData_bell = imagecreatefromstring(file_get_contents($light));
				$imageData_bell = imagescale($imageData_bell, 350,390);
				imagecopy($jpg_image, $imageData_bell, 350, 920, 0, 0, 350, 390);//21

			}
			if($img22=="bell"){
				$imageData_bell = imagecreatefromstring(file_get_contents($light));
				$imageData_bell = imagescale($imageData_bell, 350,390);
				imagecopy($jpg_image, $imageData_bell, 670, 920, 0, 0, 350, 390);//22
			}
		}

		if(!empty($win->watermelon)){
			if($img00=="watermelon"){
				$imageData_watermelon = imagecreatefromstring(file_get_contents($light));
				$imageData_watermelon = imagescale($imageData_watermelon, 350,390);
				imagecopy($jpg_image, $imageData_watermelon, 30, 300, 0, 0, 350, 390);//00
			}
			if($img01=="watermelon"){
				$imageData_watermelon = imagecreatefromstring(file_get_contents($light));
				$imageData_watermelon = imagescale($imageData_watermelon, 350,390);
				imagecopy($jpg_image, $imageData_watermelon, 350, 300, 0, 0, 350, 390);//01

			}
			if($img02=="watermelon"){
				$imageData_watermelon = imagecreatefromstring(file_get_contents($light));
				$imageData_watermelon = imagescale($imageData_watermelon, 350,390);
				imagecopy($jpg_image, $imageData_watermelon, 670, 300, 0, 0, 350, 390);//02

			}
			if($img10=="watermelon"){
				$imageData_watermelon = imagecreatefromstring(file_get_contents($light));
				$imageData_watermelon = imagescale($imageData_watermelon, 350,390);
				imagecopy($jpg_image, $imageData_watermelon, 30, 605, 0, 0, 350, 390);//10
			}
			if($img11=="watermelon"){
				$imageData_watermelon = imagecreatefromstring(file_get_contents($light));
				$imageData_watermelon = imagescale($imageData_watermelon, 350,390);
				imagecopy($jpg_image, $imageData_watermelon, 350, 605, 0, 0, 350, 390);//11

			}
			if($img12=="watermelon"){
				$imageData_watermelon = imagecreatefromstring(file_get_contents($light));
				$imageData_watermelon = imagescale($imageData_watermelon, 350,390);
				imagecopy($jpg_image, $imageData_watermelon, 670, 605, 0, 0, 350, 390);//12
			}
			if($img20=="watermelon"){
				$imageData_watermelon = imagecreatefromstring(file_get_contents($light));
				$imageData_watermelon = imagescale($imageData_watermelon, 350,390);
				imagecopy($jpg_image, $imageData_watermelon, 30, 920, 0, 0, 350, 390);//20
			}
			if($img21=="watermelon"){
				$imageData_watermelon = imagecreatefromstring(file_get_contents($light));
				$imageData_watermelon = imagescale($imageData_watermelon, 350,390);
				imagecopy($jpg_image, $imageData_watermelon, 350, 920, 0, 0, 350, 390);//21

			}
			if($img22=="watermelon"){
				$imageData_watermelon = imagecreatefromstring(file_get_contents($light));
				$imageData_watermelon = imagescale($imageData_watermelon, 350,390);
				imagecopy($jpg_image, $imageData_watermelon, 670, 920, 0, 0, 350, 390);//22
			}
		}

		if(!empty($win->grape)){
			if($img00=="grape"){
				$imageData_grape = imagecreatefromstring(file_get_contents($light));
				$imageData_grape = imagescale($imageData_grape, 350,390);
				imagecopy($jpg_image, $imageData_grape, 30, 300, 0, 0, 350, 390);//00
			}
			if($img01=="grape"){
				$imageData_grape = imagecreatefromstring(file_get_contents($light));
				$imageData_grape = imagescale($imageData_grape, 350,390);
				imagecopy($jpg_image, $imageData_grape, 350, 300, 0, 0, 350, 390);//01

			}
			if($img02=="grape"){
				$imageData_grape = imagecreatefromstring(file_get_contents($light));
				$imageData_grape = imagescale($imageData_grape, 350,390);
				imagecopy($jpg_image, $imageData_grape, 670, 300, 0, 0, 350, 390);//02

			}
			if($img10=="grape"){
				$imageData_grape = imagecreatefromstring(file_get_contents($light));
				$imageData_grape = imagescale($imageData_grape, 350,390);
				imagecopy($jpg_image, $imageData_grape, 30, 605, 0, 0, 350, 390);//10
			}
			if($img11=="grape"){
				$imageData_grape = imagecreatefromstring(file_get_contents($light));
				$imageData_grape = imagescale($imageData_grape, 350,390);
				imagecopy($jpg_image, $imageData_grape, 350, 605, 0, 0, 350, 390);//11

			}
			if($img12=="grape"){
				$imageData_grape = imagecreatefromstring(file_get_contents($light));
				$imageData_grape = imagescale($imageData_grape, 350,390);
				imagecopy($jpg_image, $imageData_grape, 670, 605, 0, 0, 350, 390);//12
			}
			if($img20=="grape"){
				$imageData_grape = imagecreatefromstring(file_get_contents($light));
				$imageData_grape = imagescale($imageData_grape, 350,390);
				imagecopy($jpg_image, $imageData_grape, 30, 920, 0, 0, 350, 390);//20
			}
			if($img21=="grape"){
				$imageData_grape = imagecreatefromstring(file_get_contents($light));
				$imageData_grape = imagescale($imageData_grape, 350,390);
				imagecopy($jpg_image, $imageData_grape, 350, 920, 0, 0, 350, 390);//21

			}
			if($img22=="grape"){
				$imageData_grape = imagecreatefromstring(file_get_contents($light));
				$imageData_grape = imagescale($imageData_grape, 350,390);
				imagecopy($jpg_image, $imageData_grape, 670, 920, 0, 0, 350, 390);//22
			}
		}

		if(!empty($win->cherry)){
			if($img00=="cherry"){
				$imageData_cherry = imagecreatefromstring(file_get_contents($light));
				$imageData_cherry = imagescale($imageData_cherry, 350,390);
				imagecopy($jpg_image, $imageData_cherry, 30, 300, 0, 0, 350, 390);//00
			}
			if($img01=="cherry"){
				$imageData_cherry = imagecreatefromstring(file_get_contents($light));
				$imageData_cherry = imagescale($imageData_cherry, 350,390);
				imagecopy($jpg_image, $imageData_cherry, 350, 300, 0, 0, 350, 390);//01

			}
			if($img02=="cherry"){
				$imageData_cherry = imagecreatefromstring(file_get_contents($light));
				$imageData_cherry = imagescale($imageData_cherry, 350,390);
				imagecopy($jpg_image, $imageData_cherry, 670, 300, 0, 0, 350, 390);//02

			}
			if($img10=="cherry"){
				$imageData_cherry = imagecreatefromstring(file_get_contents($light));
				$imageData_cherry = imagescale($imageData_cherry, 350,390);
				imagecopy($jpg_image, $imageData_cherry, 30, 605, 0, 0, 350, 390);//10
			}
			if($img11=="cherry"){
				$imageData_cherry = imagecreatefromstring(file_get_contents($light));
				$imageData_cherry = imagescale($imageData_cherry, 350,390);
				imagecopy($jpg_image, $imageData_cherry, 350, 605, 0, 0, 350, 390);//11

			}
			if($img12=="cherry"){
				$imageData_cherry = imagecreatefromstring(file_get_contents($light));
				$imageData_cherry = imagescale($imageData_cherry, 350,390);
				imagecopy($jpg_image, $imageData_cherry, 670, 605, 0, 0, 350, 390);//12
			}
			if($img20=="cherry"){
				$imageData_cherry = imagecreatefromstring(file_get_contents($light));
				$imageData_cherry = imagescale($imageData_cherry, 350,390);
				imagecopy($jpg_image, $imageData_cherry, 30, 920, 0, 0, 350, 390);//20
			}
			if($img21=="cherry"){
				$imageData_cherry = imagecreatefromstring(file_get_contents($light));
				$imageData_cherry = imagescale($imageData_cherry, 350,390);
				imagecopy($jpg_image, $imageData_cherry, 350, 920, 0, 0, 350, 390);//21

			}
			if($img22=="cherry"){
				$imageData_cherry = imagecreatefromstring(file_get_contents($light));
				$imageData_cherry = imagescale($imageData_cherry, 350,390);
				imagecopy($jpg_image, $imageData_cherry, 670, 920, 0, 0, 350, 390);//22
			}
		}

		if(!empty($win->orange)){
			if($img00=="orange"){
				$imageData_orange = imagecreatefromstring(file_get_contents($light));
				$imageData_orange = imagescale($imageData_orange, 350,390);
				imagecopy($jpg_image, $imageData_orange, 30, 300, 0, 0, 350, 390);//00
			}
			if($img01=="orange"){
				$imageData_orange = imagecreatefromstring(file_get_contents($light));
				$imageData_orange = imagescale($imageData_orange, 350,390);
				imagecopy($jpg_image, $imageData_orange, 350, 300, 0, 0, 350, 390);//01

			}
			if($img02=="orange"){
				$imageData_orange = imagecreatefromstring(file_get_contents($light));
				$imageData_orange = imagescale($imageData_orange, 350,390);
				imagecopy($jpg_image, $imageData_orange, 670, 300, 0, 0, 350, 390);//02

			}
			if($img10=="orange"){
				$imageData_orange = imagecreatefromstring(file_get_contents($light));
				$imageData_orange = imagescale($imageData_orange, 350,390);
				imagecopy($jpg_image, $imageData_orange, 30, 605, 0, 0, 350, 390);//10
			}
			if($img11=="orange"){
				$imageData_orange = imagecreatefromstring(file_get_contents($light));
				$imageData_orange = imagescale($imageData_orange, 350,390);
				imagecopy($jpg_image, $imageData_orange, 350, 605, 0, 0, 350, 390);//11

			}
			if($img12=="orange"){
				$imageData_orange = imagecreatefromstring(file_get_contents($light));
				$imageData_orange = imagescale($imageData_orange, 350,390);
				imagecopy($jpg_image, $imageData_orange, 670, 605, 0, 0, 350, 390);//12
			}
			if($img20=="orange"){
				$imageData_orange = imagecreatefromstring(file_get_contents($light));
				$imageData_orange = imagescale($imageData_orange, 350,390);
				imagecopy($jpg_image, $imageData_orange, 30, 920, 0, 0, 350, 390);//20
			}
			if($img21=="orange"){
				$imageData_orange = imagecreatefromstring(file_get_contents($light));
				$imageData_orange = imagescale($imageData_orange, 350,390);
				imagecopy($jpg_image, $imageData_orange, 350, 920, 0, 0, 350, 390);//21

			}
			if($img22=="orange"){
				$imageData_orange = imagecreatefromstring(file_get_contents($light));
				$imageData_orange = imagescale($imageData_orange, 350,390);
				imagecopy($jpg_image, $imageData_orange, 670, 920, 0, 0, 350, 390);//22
			}
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

	public function line_gift($id, $v, $size = 0) {
		$sum_amt = $this -> wtx_dao -> get_sum_amt($id);

		$Date = date("Y-m-d");
		$price = $this -> d_q_dao -> find_d_q($Date);
		if(!empty($price)){
			$total=floatval($price->now_price)*floatval($sum_amt);

		} else{
			$p = $this -> d_q_dao -> find_last_d_q($Date);
			$dtx = array();
			$dtx['date'] = $Date;
			$dtx['last_price'] = $p->now_price;
			$dtx['now_price'] = $p->now_price;
			$this -> d_q_dao -> insert($dtx);
			$total=floatval($p->now_price)*floatval($sum_amt);
		}

		$im = HOME_DIR . "img/line688/line/wallet_card.jpg";
		header("Content-Disposition: attachment; ");
		header("Content-type: image/jpeg");
		// header("Content-Length: " . filesize($im)); // 不要加這行
		$jpg_image = imagecreatefromjpeg($im);
		$font = HOME_DIR . "img/line688/font/wt006.ttf";

		$black = imagecolorallocate($jpg_image, 0, 0, 0);
		$white = imagecolorallocate($jpg_image, 255, 255, 255);

		// if($total=0){
		// 	$bet_total=$total;
		// }else{
		// 	$bet_total=mb_substr($total,0,-7);
		// }
		imagettftext($jpg_image, 25, 0, 470, 225, $white, $font, $sum_amt);
		imagettftext($jpg_image, 25, 0, 470, 285, $white, $font, $total);

		ob_clean();
		flush();
		// Send Image to Browser
		imagejpeg($jpg_image);
		// Clear Memory
		imagedestroy($jpg_image);

		exit ;
		show_404();
	}


}
