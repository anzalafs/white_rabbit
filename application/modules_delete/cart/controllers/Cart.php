<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends MX_Controller {

	public $authStatus = false;
	public $applicationStatus = true;

	public function __construct(){
		parent::__construct();
		$this->load->model('cart_model');
		$this->load->module('site_settings');
		$this->authStatus=$this->site_settings->api_auth();
		$this->applicationStatus=$this->site_settings->application_status();
		if($this->applicationStatus==false){
			return json_output(401,array('status' => 401,'message' => 'Service unavailable right now.'));
		}
	}

	public function add_to_cart(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
				json_output(400,array('status' => 400,'message' => 'Bad request.'));
		}else{

				if($this->authStatus == true){
						$params = json_decode(file_get_contents('php://input'));
						$item_id = $params->item_id;
						$qty = $params->qty;
						$query="SELECT item_price.`inventory_id`, item_price.`item_name`, item_price.`price_cost`, item_price.`price_discount` FROM `menu_items`
JOIN item_price ON item_price.item_id=menu_items.item_id
WHERE menu_items.item_id=$item_id";
						$item_result=$this->cart_model->makequery($query)->result();
						$item_details=array();
						if(count($item_result)>0){

							$item_details['item_name']=	$item_result[0]->item_name;
							$item_details['qty']=$qty;

							$item_cost=$item_details['item_cost']=$item_result[0]->price_cost;
							$item_discount=$item_details['item_discount']=$item_result[0]->price_discount;

							if($item_discount>0){
								$sub_total=($item_discount/100)*$item_cost;
							}else{
								$sub_total=$item_cost;
							}
							$item_details['sub_total']=$sub_total;
						}
						json_output(200,array('status' => 200, 'message' => 'Success', 'data' => $item_details));
						// echo "<pre>";print_r($item_details);echo "</pre>";
				}
		}
	}

	/*public function show_cart(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
				json_output(400,array('status' => 400,'message' => 'Bad request.'));
		}else{

				if($this->authStatus == true){
						$params = json_decode(file_get_contents('php://input'));
						$channel = $params->channel;
						print_r($this->show_cart());exit;
						json_output(200,array('status' => 200,'data' => 'ss'));
				}
		}
	}*/

}
