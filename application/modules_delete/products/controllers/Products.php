<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends MX_Controller {

	public $authStatus = false;
	public $applicationStatus = true;

	public function __construct(){
		parent::__construct();
		$this->load->model('product_model');
		$this->load->module('site_settings');
		$this->authStatus=$this->site_settings->api_auth();
		$this->applicationStatus=$this->site_settings->application_status();
		if($this->applicationStatus==false){
			return json_output(401,array('status' => 401,'message' => 'Service unavailable right now.'));
		}
	}

	public function listing(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
				json_output(400,array('status' => 400,'message' => 'Bad request.'));
		}else{

				if($this->authStatus == true){
						$params = json_decode(file_get_contents('php://input'));
						$rest_id = $params->rest_id;
						$channel = $params->channel;
						$isVeg = $params->veg;
						$search=$params->search;


						$categories=array();
						$categories=$this->product_model->get_contents('item_category', array('cat_status'=>1), 'cat_id DESC')->result();

						// initialize empty category array
						$categoryArr = [];
						if(count($categories)>0){

							$where_cond='';
							if($isVeg!=''){
								$where_cond.=' AND t1.item_cat_type = '.$isVeg;
							}
							$current_time=date('H:i:s');
							foreach($categories as $category){

									$query="SELECT t1.item_id,t3.inventory_id,t1.cat_id,t1.item_image,t1.item_short_desc,													t1.item_cat_type,t1.item_type,t1.item_special,t1.item_prep_time,t1.item_status,													t3.item_name,t3.price_cost,t3.price_discount,t1.item_start_time,t1.item_end_time
													FROM `menu_items` t1
													JOIN  restaurant_item_rel t2 ON  t2.item_id=t1.item_id
													JOIN item_price t3 ON t3.item_id=t1.item_id
													WHERE t1.item_status=1 AND t2.rest_id=$rest_id AND  t2.item_rest_status=1 $where_cond AND t1.cat_id=$category->cat_id  AND `item_start_time` < '$current_time' AND `item_end_time` > '$current_time' AND t3.item_name LIKE '%$search%'
													GROUP BY t1.`item_id`
													ORDER BY t1.cat_id DESC";
									$items=$this->product_model->makequery($query)->result();
									if(count($items)>0){
										$categoryArr[$category->cat_id]['category_id'] = $category->cat_id;
										$categoryArr[$category->cat_id]['category_name'] = $category->cat_name;
										$categoryArr[$category->cat_id]['products'] = $items;
									}

							}
						}
						$result = array_values($categoryArr);

						if(count($result)<=0){
							json_output(200,array('status' => 200, 'message' => 'Sorry no data found', 'categories'=>$categories, 'data' => $items));
						}else{
							json_output(200,array('status' => 200, 'message' => 'Success', 'categories'=>$categories, 'data' => $result));
						}
				}else{
					json_output(400,array('status' => 400,'message' => 'Authentication failed'));
				}
		}
	}


	public function variations(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
				json_output(400,array('status' => 400,'message' => 'Bad request.'));
		}else{

				if($this->authStatus == true){
						$params = json_decode(file_get_contents('php://input'));
						$rest_id = $params->rest_id;
						$item_id = $params->item_id;
						$channel = $params->channel;

						$cond=array('item_id'=>$item_id);
						$items=$this->product_model->get_contents('item_price', $cond, FALSE, 'inventory_id as inventoryId, item_name as itemName, price_cost as price, price_discount as discount')->result();

						$menu_cond=array('item_id'=>$item_id);
						$menu_details=$this->product_model->get_contents('menu_items', $menu_cond, FALSE, 'item_name, item_short_desc')->result();
						$item_name=$item_short_desc='';
						if(count($menu_details)==1){
							$item_name=$menu_details[0]->item_name;
							$item_short_desc=$menu_details[0]->item_short_desc;
						}

						if(count($items)<=0){
							json_output(200,array('status' => 200,'message' => 'Sorry no data found', 'data' => $items));
						}else{
							json_output(200,array('status' => 200, 'message' => 'Success', 'itemname'=>$item_name, 'itemshortdesc'=>$item_short_desc, 'data' => $items));
						}
				}
		}
	}


	public function details(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
				json_output(400,array('status' => 400,'message' => 'Bad request.'));
		}else{

				if($this->authStatus == true){
						$params = json_decode(file_get_contents('php://input'));
						$item_id = $params->item_id;
						$channel = $params->channel;

						$menu_details_query="SELECT
menu_items.item_id,
item_price.item_name,
item_image,
item_long_desc
FROM `menu_items`
JOIN item_price ON item_price.item_id=menu_items.item_id
WHERE menu_items.`item_id`=$item_id LIMIT 1";
						$menu_details=$this->product_model->makequery($menu_details_query)->result();
						$item_details=array();
						if(count($menu_details)==1){

							$server_path=$this->config->item('server_path');

							$item_details[]=array(
																		'name'=>$menu_details[0]->item_name,
																		'image'=>$server_path.'uploads/'.$menu_details[0]->item_image,
																		'fulldesc'=>$menu_details[0]->item_long_desc
																	);
						}

						if(count($item_details)<=0){
							json_output(200,array('status' => 200,'message' => 'Sorry no data found', 'data' => $item_details));
						}else{
							json_output(200,array('status' => 200, 'message' => 'Success', 'data' => $item_details));
						}
				}else{
					json_output(400,array('status' => 400,'message' => 'Authentication failed'));
				}
		}
	}


}
