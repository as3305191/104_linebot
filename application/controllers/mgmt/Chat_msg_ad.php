<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat_msg_ad extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Chat_msg_ad_dao', 'dao');
	}

	public function index()
	{
		$data = array();
		$this -> setup_user_data($data);
		$this->load->view('mgmt/chat_msg_ad/list', $data);
	}

	public function get_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'company_id'
		));

		$items = $this -> dao -> query_ajax($data);
		$res['items'] = $items;
		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();
		$data['id'] = $id;
		if(!empty($id)) {
			$item = $this -> dao -> find_me_by_id($id);
			$data['item'] = $item;
		}
		$this->load->view('mgmt/chat_msg_ad/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$gift_id = $this -> get_post('gift_id');
		$data = $this -> get_posts(array(
			'title',
			'minutes',
		));

		$res['gid'] = $gift_id;
		$user = $this -> users_dao -> find_by("gift_id", $gift_id);
		if(!empty($user)) {
			$data['user_id'] = $user -> id;
			$data['gift_id'] = $user -> gift_id;

			if(empty($id)) {
				// insert
				$this -> dao -> insert($data);
			} else {
				// update
				$this -> dao -> update($data, $id);
			}
		} else {
			$res['error_msg'] = "查無使用者";
		}

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete($id);
		$this -> to_json($res);
	}

	function do_push($id) {
		$res = array();
		$item = $this -> dao -> find_by_id($id);
		if(!empty($item)) {
			// push to all
			$list = $this -> users_dao -> find_all_line_user();
			foreach($list as $each) {
				// if($each -> id == 10953) {
					$p = array();
					$p['to'] = $each -> line_sub;
					$p['messages'][] = array(
						"type" => "text",
						"text" => $item -> content
					);
					call_line_api("POST", "https://api.line.me/v2/bot/message/push", json_encode($p), CHANNEL_ACCESS_TOKEN);
				// }
			}

			$res['list'] = $list;
			$this -> dao -> update(array(
				'push_time' => date("Y-m-d H:i:s"),
				'status' => 1,
			), $id);
		}

		$this -> to_json($res);
	}
}
