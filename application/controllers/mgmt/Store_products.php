<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Store_products extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Products_dao', 'dao');
		$this -> load -> model('Product_cate_dao', 'pc_dao');
		$this -> load -> model('Product_mulcate_dao', 'pm_dao');
		$this -> load -> model('Product_images_dao', 'pimg_dao');
		$this -> load -> model('Images_dao', 'img_dao');

	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);

		//setup product_cate
		$f = array();
		$f['parent_id'] = 0;
		$f['corp_id'] = $data['corp'] -> id;
		$main_cates = $this -> pc_dao -> query_all($f);
		$data['main_cates'] = $main_cates;

		$this->load->view('mgmt/products/list', $data);
	}

	public function get_data() {
		$store = array();
		$s_data = $this -> setup_user_data($store);

		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order'
		));
		$data['corp_id'] = $s_data['corp'] -> id;

		$items = $this -> dao -> query_ajax($data);
		foreach($items as $item) {
			if(!empty($item -> image_id)) {
				$item -> img_url = get_img_url($item -> image_id);
			}
			$p_id = $item -> id;
			$item->mul_cate = $this -> pm_dao -> find_mulcate($p_id);
		}

		$res['items'] = $items;

		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$this -> to_json($res);
	}

	public function get_data_post(){
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order'
		));
		$data['store_id'] = $this -> get_post('store_id');

		$items = $this -> dao -> query_ajax($data);
		foreach($items as $item) {
			if(!empty($item -> image_id)) {
				$item -> img_url = get_img_url($item -> image_id);
			}
			$p_id = $item -> id;
			$item->mul_cate = $this -> pm_dao -> find_mulcate($p_id);
		}

		$res['items'] = $items;

		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);
		$this -> to_json($res);
	}


	public function cate_sub($parent_id) {
		if(empty($parent_id)) {
			$parent_id = 0;
		}

		$f = array();
		$f['parent_id'] = $parent_id;
		$list = $this -> pc_dao -> query_all($f);

		$res['list'] = $list;
		$res['result'] = TRUE;
		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();
		$data['id'] = $id;

		$item = NULL;
		if(!empty($id)) {
			$item = $this -> dao -> find_by_id($id);

			// find images
			$item -> images = $this -> pimg_dao -> find_image_by_product_id($id);
		}



		// product cate
		$f = array();
		$f['parent_id'] = 0;

		$o = array();
		$data = $this -> setup_user_data($o);

		$f['corp_id'] = $data['corp'] -> id;
		$main_cates = $this -> pc_dao -> query_all($f);
		$data['main_cates'] = $main_cates;

		if(!empty($item) && !empty($item -> cate_1)) {
			$f = array();
			$f['parent_id'] = $item -> cate_1;
			$sub_cates = $this -> pc_dao -> query_all($f);
			$data['sub_cates'] = $sub_cates;
		}

		$data['mul_cates'] = array();
		$mul = $this -> pm_dao -> find_all_by('product_id',$id);
		if(!empty($mul)){
			foreach($mul as $each){
				array_push($data['mul_cates'],$each->product_cateid);
			}
		}

		// var_dump($item);
		$data['item'] = $item;
		$this->load->view('mgmt/products/edit', $data);
	}

	public function insert() {
		$res = array();

		$store = array();
		$s_data = $this -> setup_user_data($store);


		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'product_name',
			'start_time',
			'price_origin',
			'price',
			'cost',
			'desc',
			'serial',
			'ever_time',
			'pos'
		));

		//always insert
		$data['corp_id'] = $s_data['corp'] -> id;

		$end_time = $this -> get_post('end_time');
		if(empty($end_time)) {
			$data['end_time'] = NULL;
		} else {
			$data['end_time'] = $end_time;
		}

		if(empty($id)) {
			// insert
			$id = $this -> dao -> insert($data);
		} else {
			// update
			$this -> dao -> update($data, $id);
		}

		// add images
		$img_id_str = $this -> get_post('img_id_list');
		$img_id_list = explode(',', $img_id_str);
		$this -> pimg_dao -> add_imgs($id, $img_id_list);

		//update cate
		$cate_list = $this -> get_post('product_cate');
		$this -> pm_dao -> delete_all_by('product_id',$id);
		if(!empty($cate_list)) {
			$mulcate_id = implode(",",$cate_list);
			$this -> dao -> update(array('main_cate'=>$mulcate_id),$id);
			foreach($cate_list as $each){
				$insert_data = array(
					'product_id'=>$id,
					'product_cateid'=>$each
				);
				$this -> pm_dao -> insert($insert_data);
			}
		}

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
		$this -> to_json($res);
	}

	public function product_post(){
		$product_id = $this -> get_post('product_id');
		$store_id = $this -> session -> userdata('store_id');
		$u_data = array();

		$p = $this -> dao -> find_by_id($product_id);
		if($p -> post_checked == 0) {
			$u_data['post_checked'] = 1;
			$res['success_msg'] = '上架成功';
		}  else {
			$u_data['post_checked'] = 0;
			$res['success_msg'] = '下架成功';
		}
		$this -> dao -> update($u_data, $product_id);
		$res['success'] = TRUE;
		$this -> to_json($res);
	}

	public function copy($id){
		$data = $this -> dao -> find_by_id($id);
		$data = (array)$data;

		//delete no_need data
		$unset_data = array('id','serial','create_time');
		foreach($unset_data as $each){
			unset($data[$each]);
		}

		//change name of product
		$product_name = explode("##",$data['product_name'])[0];

		$s = $this -> dao -> find_copy($product_name);
		$max_product_name = $s[0]->product_name;
		$max_product_name = explode("#@@",$max_product_name);
		if(count($max_product_name) > 1){
			$copy_num = $max_product_name[1]+1;
		}else{
			$copy_num = 0;
		}
		$data['product_name'] = $product_name.'##複製#@@'.$copy_num;

		//insert to products
		$p_id = $this -> dao -> insert($data);

		// add Product_cate
		$f = $this -> pm_dao -> find_all_by('product_id',$id);
		$mulcate_id_arr = array();
		foreach($f as $each){
			$each->product_id = $p_id;
			unset($each->id);
			$i_data = (array)$each;
			$this -> pm_dao -> insert($i_data);

			array_push($mulcate_id_arr,$each->product_cateid);
		}
		//update to table:products main_cate
		$mulcate_id = implode(",",$mulcate_id_arr);
		$this -> dao -> update(array('main_cate'=>$mulcate_id),$p_id);

		//copy images
		$res['success'] = TRUE;
		$this -> to_json($res);
	}


	public function add_spec() {
		$res = array();
		$i_data = array();
		$i_data['spec_name'] = $this -> get_post('spec_name');
		$i_data['product_id'] = $this -> get_post('product_id');
		$id = $this -> ps_dao -> insert($i_data);
		$res['spec'] = $this -> ps_dao -> find_by_id($id);

		$this -> to_json($res);
	}

	public function list_spec() {
		$res = array();
		$product_id = $this -> get_post('product_id');
		$spec_list = $this -> ps_dao -> find_all_by_product_id($product_id);

		foreach($spec_list as $each) {
			$each -> details = $this -> psd_dao -> find_all_by_spec_id($each -> id);
		}
		$res['list'] = $spec_list;

		$this -> to_json($res);
	}

	public function update_spec() {
		$res['success'] = TRUE;
		$id = $this -> get_post('pk');
		$spec_name = $this -> get_post('value');
		$id = $this -> ps_dao -> update(array('spec_name' => $spec_name), $id);
		$this -> to_json($res);
	}
}
