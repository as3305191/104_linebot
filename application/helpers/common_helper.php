<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

if (!function_exists('nn_min_bet')) {
	function nn_min_bet($hall_id) {
		switch($hall_id) {
			case 0:
				return 100;
			case 1:
				return 1000;
			case 2:
				return 10000;
			default: // -1
				return 10;
		}
	}
}

if (!function_exists('call_line_api')) {
		function call_line_api($method, $url, $data, $auth){
		   $curl = curl_init();

		   switch ($method){
		      case "POST":
		         curl_setopt($curl, CURLOPT_POST, 1);
		         if ($data)
		            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		         break;
		      case "PUT":
		         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
		         if ($data)
		            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		         break;
		      default:
		         if ($data)
		            $url = sprintf("%s?%s", $url, http_build_query($data));
		   }

			 // 這裡略過檢查 SSL 憑證有效性
			 curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		   // OPTIONS:
		   curl_setopt($curl, CURLOPT_URL, $url);
		   curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		      "Authorization: Bearer $auth",
		      'Content-Type: application/json',
		   ));
		   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

		   // EXECUTE:
		   $result = curl_exec($curl);
		   if(!$result){die("Connection Failure");}
		   curl_close($curl);
		   return $result;
		}
	}

	if (!function_exists('jwt_encode')) {
		function jwt_encode($payload, $key, $alg = 'SHA256')
		{
			$key = md5($key);
			$jwt = base64_encode(json_encode(['typ' => 'JWT', 'alg' => $alg])) . '.' . base64_encode(json_encode($payload));
			return $jwt . '.' . signature($jwt, $key, $alg);
		}
	}

	if (!function_exists('jwt_decode')) {
		function jwt_decode($jwt, $key)
		{
			$tokens = explode('.', $jwt);
			$key    = md5($key);

			if (count($tokens) != 3)
					return false;

			list($header64, $payload64, $sign) = $tokens;

			$header = json_decode(safe_base64_decode($header64), JSON_OBJECT_AS_ARRAY);
			if (empty($header['alg']))
					return false;

			// if (signature($header64 . '.' . $payload64, $key, "SHA256") !== $sign)
			// 		return false;

			$payload = json_decode(safe_base64_decode($payload64), JSON_OBJECT_AS_ARRAY);

			// $time = $_SERVER['REQUEST_TIME'];
			// if (isset($payload['iat']) && $payload['iat'] > $time)
			// 		return false;
			//
			// if (isset($payload['exp']) && $payload['exp'] < $time)
			// 		return false;

			return $payload;
		}
	}

	if (!function_exists('safe_base64_encode')) {
		function safe_base64_encode($input)
		{
			return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
		}
	}

	if (!function_exists('safe_base64_decode')) {
		function safe_base64_decode($input)
		{
			$remainder = strlen($input) % 4;

			if ($remainder)
			{
					$padlen = 4 - $remainder;
					$input .= str_repeat('=', $padlen);
			}

			return base64_decode(strtr($input, '-_', '+/'));
		}
	}

	if (!function_exists('signature')) {
		function signature($input, $key, $alg)
		{
		    return hash_hmac($alg, $input, $key);
		}
	}

	if (!function_exists('py_des3_encode')) {
		function py_des3_encode($params)
		{
			ob_start();
			$params = urlencode($params);
			passthru("python ./des_enc.py {$params}");
			$output = ob_get_clean();
			return $output;
		}
	}

	if (!function_exists('py_des3_decode')) {
		function py_des3_decode($params)
		{
			ob_start();
			passthru("python ./des_dec.py {$params}");
			$output = ob_get_clean();
			return $output;
		}
	}

	if (!function_exists('coin_token')) {
		function coin_token($length)
		{
		    $token = "";
		    // $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		    // $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
		    // $codeAlphabet.= "0123456789";
		    // $max = strlen($codeAlphabet); // edited
				//
		    // for ($i=0; $i < $length; $i++) {
		    //     $token .= $codeAlphabet[crypto_rand_secure(0, $max-1)];
		    // }
				$token = generate_random_string($length);

		    return $token;
		}
	}

	if (!function_exists('generate_random_string')) {
		function generate_random_string($length = 8) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
		}
	}

	if (!function_exists('generate_random_digit')) {
		function generate_random_digit($length = 8) {
	    $characters = '0123456789';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
		}
	}

	if (!function_exists('is_disabled_4role')) {
		function is_disabled_4role($user_role_id, $each_role_id) {
	    if($user_role_id != 99) {
				if($user_role_id == 1) { // admin
					if($each_role_id == 2 || $each_role_id == 99) {
						return "disabled";
					}
				}
				if($user_role_id == 2 || $user_role_id == 3 || $user_role_id == 11) {
					return "disabled";
				}
			}

			return "";
		}
	}

	if (!function_exists('sp_color')) {
		function sp_color($str) {
	    $arr = explode('.',$str);
			$out = '';
			if(isset($arr[0])) {
				$out .= '<span style="color: black;">' . (empty($arr[0]) ? '0' : $arr[0]) . '</span>';
			}
			if(count($arr) > 1)  {
				$out .= '.';
				$out .= '<span style="color: blue;">' . $arr[1] . '</span>';
			}

			return $out;
		}
	}

	if (!function_exists('get_bet_multiply')) {
		function get_bet_multiply($winner) {
			$mul = 0.0;
			switch ($winner) {
				case 0: // tie
					$mul = 8.0;
					break;
				case 1:
					$mul = 0.95;
					break;
				case 2:
					$mul = 1.0;
					break;
				case 3:
					$mul = 11.0;
					break;
				case 4:
					$mul = 11.0;
					break;
				default:
					break;
			}

			return $mul;
		}
	}

if (!function_exists('is_win_str')) {
	function is_win_str($is_win) {
		$str = '';
		switch ($is_win) {
			case -1:
				$str = '輸';
				break;
			case 0:
				$str = '和';
				break;
			case 1:
				$str = '贏';
				break;

			default:
				break;
		}

		return $str;
	}
}

if (!function_exists('send_gcm')) {
	function send_gcm($title, $message, $id_arr, $badge = 1) {
	    $url = 'https://fcm.googleapis.com/fcm/send';

	    $fields = array (
			    'registration_ids' => $id_arr,
					"priority" => "high",

					'notification' => array(
							"body" => $message,
							"title" => $title,
							'badge' => $badge,
							"sound" => "default",
							"content_available" => true
					)
	    );
	    $fields = json_encode($fields);
			//echo $fields;
	    $headers = array (
        'Authorization: key=AIzaSyCW69EciWfJ3cn_UEZex0sEXYiQzxdNo38',
        'Content-Type: application/json'
	    );

	    $ch = curl_init ();
	    curl_setopt ( $ch, CURLOPT_URL, $url );
	    curl_setopt ( $ch, CURLOPT_POST, true );
	    curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
	    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

	    $result = curl_exec ( $ch );
	   	//echo $result;
	    curl_close ( $ch );
	}
}

if (!function_exists('send_gcm_topic')) {
	function send_gcm_topic($title, $message, $condition, $badge = 1) {
	    $url = 'https://fcm.googleapis.com/fcm/send';

	    $fields = array (
					"priority" => "high",
					"condition" => $condition,
					'notification' => array(
							"body" => $message,
							"title" => $title,
							'badge' => $badge,
							"sound" => "default",
							"content_available" => true
					)
	    );
	    $fields = json_encode($fields);
			//echo $fields;
	    $headers = array (
        'Authorization: key=AIzaSyCW69EciWfJ3cn_UEZex0sEXYiQzxdNo38',
        'Content-Type: application/json'
	    );

	    $ch = curl_init ();
	    curl_setopt ( $ch, CURLOPT_URL, $url );
	    curl_setopt ( $ch, CURLOPT_POST, true );
	    curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
	    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

	    $result = curl_exec ( $ch );
	   	//echo $result;
	    curl_close ( $ch );
	}
}

if (!function_exists('send_sms')) {
	function send_sms($scope, $mobile, $msg) {
		if(empty($msg)){
			return;
		}

		$n_res = $scope -> curl -> simple_get("http://smexpress.mitake.com.tw:9600/SmSendGet.asp"
		. "?username=" . SMS_ACCOUNT . "&password=" . SMS_PASSWORD . "&dstaddr=$mobile&DestName=SteveYeh&dlvtime=&vldtime=&smbody=$msg");
	}
}

if (!function_exists('mobile_html')) {
	function mobile_html($source) {
		if($source == ""){
			return;
		}

		$html = "
		<!DOCTYPE html>
			<head>
			<meta charset=\"utf-8\">
			<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
				<style>
				img {
					max-width: 100%;
					height: auto;
				}
				</style>
			</head>
			<body>
				$source
			</body>
		</html>";
		return $html;
	}
}

if (!function_exists('shipping_comp')) {
	function shipping_comp($compare, $a, $b, $c) {
		$result = FALSE;

		switch ($compare) {
			case '=':
				$result = ($a == $b);
				break;

			case '>=':
				$result = ($a >= $b);
				break;

			case '<=':
				$result = ($a <= $b);
				break;

			case '>':
				$result = ($a > $b);
				break;

			case '<':
				$result = ($a < $b);
				break;

			case 'between':
				$result = ($a >= $b && $a <= $c);
				break;
			default:

				break;
		}
		return $result;
	}
}

if (!function_exists('num_to_weekday')) {
	function num_to_weekday($j) {
		$weekday = array(
			'日','一','二','三','四','五','六'
		);
		return $weekday[$j];
	}
}

if (!function_exists('get_img_url')) {
	function get_img_url($img_id) {
		return base_url("mgmt/images/get/$img_id");
	}
}

if (!function_exists('get_thumb_url')) {
	function get_thumb_url($img_id) {
		return get_img_url($img_id) . "/thumb";
	}
}

if (!function_exists('yes_or_no')) {
	function yes_or_no($int_value) {
		return ($int_value == 0 ? '否' : '是');
	}
}

if (!function_exists('item_val')) {
	function item_val($item, $key) {
		if(isset($item) && !empty($item -> $key)) {
			return $item -> $key;
		}
		return "";
 	}
}

if (!function_exists('get_random_digits')) {
	function get_random_digits($digits) {
		$temp = "";

		for ($i = 0; $i < $digits; $i++) {
			if ($i == 0) {
				$temp .= rand(1, 9);
			} else {
				$temp .= rand(0, 9);
			}
		}

		return (int)$temp;
	}

}

if (!function_exists('get_ip')) {
	function get_ip() {
		$ip = '';
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

}

if (!function_exists('ellipsis')) {
	function ellipsis($text, $max = 30, $append = '&hellip;') {
		if (mb_strlen($text) <= $max)
			return $text;
		$out = mb_substr($text, 0, $max);
		if (strpos($text, ' ') === FALSE)
			return $out . $append;
		return preg_replace('/\w+$/', '', $out) . $append;
	}

}

if (!function_exists('str_contains')) {
	function str_contains($str, $delimiter, $return_str = 'selected') {
		if (mb_strpos($str, $delimiter) === FALSE) {
			return '';
		}
		return $return_str;
	}

}

if (!function_exists('show_selected')) {
	function show_selected($show) {
		return ($show ? 'selected="selected"' : '');
	}

}

if (!function_exists('get_day_of_week')) {
	function get_day_of_week($day) {
		$data = array("日", "一", "二", "三", "四", "五", "六");
		return $data[$day];
	}

}

if (!function_exists('is_login')) {
	function is_login($scope) {
		$s_account = $scope -> session -> userdata('account');
		return (isset($s_account) && strlen($s_account) > 0);
	}

}

if (!function_exists('get_login_type')) {
	function get_login_type($scope) {
		$login_type = $scope -> session -> userdata('login_type');
		return (isset($login_type) && strlen($login_type) > 0) ? $login_type : '';
	}

}

if (!function_exists('br2nl')) {
	function br2nl($text) {
		return preg_replace('!<br.*>!iU', "\n", $text);
	}

}

if (!function_exists('br2delimiter')) {
	function br2delimiter($text, $delimiter = '$$!!$$!!') {
		return preg_replace('!<br.*>!iU', $delimiter, $text);
	}

}

if (!function_exists('delimiter2br')) {
	function delimiter2br($text, $delimiter = '$$!!$$!!') {
		return str_replace($delimiter, '<br />', $text);
	}

}

// gmail
if (!function_exists('mail_config')) {
	function mail_config() {
		$config = Array(
		    'protocol' => 'smtp',
		    'smtp_host' => 'localhost',
		    'smtp_port' => 25,
		    'smtp_user' => 'root',
		    'smtp_pass' => 'qweq9999',
		    'mailtype'  => 'html',
		    'charset'   => 'UTF-8'
		);
		return $config;
	}

}

// trim tel
if (!function_exists('trim_tel')) {
	function trim_tel($tel) {
		$search = array('-', ' ', '(', ')', '#', '*');
		$tel = str_replace($search, '', $tel);
		return $tel;
	}

}

if (!function_exists('geocoding')) {
	function geocoding($address, $curl) {
		if (!empty($address)) {
			$res = $curl -> simple_get('http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&sensor=true');
			$obj = json_decode($res);
			if (isset($obj -> results) && count($obj -> results) > 0) {
				$results = $obj -> results;
				$loc = $results[0] -> geometry -> location;
				return $loc;
			}
		}
		return NULL;
	}

}

if (!function_exists('number_pad')) {
	function number_pad($number, $n) {
		return str_pad((int)$number, $n, "0", STR_PAD_LEFT);
	}
}

if (!function_exists('check_is_up')) {
	function check_is_up($item) {
		if(empty($item)){
			return;
		}
		$p_id = $item->id;
		$s_time = $item->start_time;
		$e_time = $item->end_time;
		$ever = $item->ever_time;
		$today = date('Y-m-d');
		//如果是 就先檢查是否永久上架 然後再檢查是否在上架期間
		if($item -> post_checked == 1){
			//是否永久上架
			if($ever == 1){//永久
				$item -> is_up = TRUE;
				$item -> up_msg = '永久上架';
			}else{//根據上下架時間
				if($today < $s_time){//還沒到上架時間
					$item -> is_up = FALSE;
					$item -> up_msg = '上架時間：'.$s_time;
				}elseif($today > $e_time){//過了下架時間
					$item -> is_up = FALSE;
					$item -> up_msg = '下架時間：'.$e_time;
				}else{//OK
					$item -> is_up = TRUE;
					$item -> up_msg = "於上架期間內";
				}
			}
		}else{//如果不是就顯示下架
			$item -> is_up = FALSE;
			$item -> up_msg = '強制下架中';
		}
		return $item;
	}
}
?>
