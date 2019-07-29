<?php
class Product_images_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('product_images');

		$this -> alias_map = array(
		);
	}

	function find_image_by_product_id($product_id) {
		$this -> db -> select('pi.product_id');
		$this -> db -> select('i.*');
		$this -> db -> where('product_id', $product_id);
		$this -> db -> from($this -> table_name . " as pi ");
		$this -> db -> join('images i', 'i.id = pi.image_id', 'inner');
		$this -> db -> order_by('pi.id', 'asc');
		$query = $this -> db -> get();
		$list = $query -> result();
		return $list;
	}


	function find_images_by_product_id($product_id) {
		$this -> db -> select('image_id');

		$this -> db -> where('product_id', $product_id);
		$this -> db -> from($this -> table_name);
		$this -> db -> order_by('id', 'asc');

		$query = $this -> db -> get();
		$list = $query -> result();
		// if(count($list)>0){
			foreach($list as $each) {
				$each -> img_url = get_img_url($each -> image_id);
				$each -> thumb_img_url = get_thumb_url($each -> image_id);
			}
		// }
		return $list;
	}


	function add_imgs($product_id, $img_id_list) {
		// remove all first
		$this -> db -> delete($this -> table_name, array('product_id' => $product_id));
		$main_img_id = 0;
		$is_first = true;

		// add all
		foreach($img_id_list as $img_id) {
			$i_data['product_id'] = $product_id;
			$i_data['image_id'] = $img_id;
			$this -> insert($i_data);

			if($is_first) {
				$is_first = false;
				$main_img_id = $img_id;
			}
		}

		// update product image id
		$this -> db -> where('id', $product_id);
		$this -> db -> update('products', array('image_id' => $main_img_id));
	}
}
?>
