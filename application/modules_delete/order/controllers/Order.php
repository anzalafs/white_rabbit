<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends MX_Controller {

	public $authStatus = false;

	public function __construct ()
	{
			parent::__construct();
			$this->load->model('db_functions');
			$this->load->module('site_settings');
			$this->load->module('auth');
			$this->authStatus=$this->site_settings->api_auth();
	}

	public function index()
	{
	}

	function processorder(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		}else{

				if($this->authStatus == true){
							$params = json_decode(file_get_contents('php://input'));

							$channel=$params->channel;

							if($params->customer_id=='' || $params->restaurant_id=='' || $params->address_id=='' || $params->payment_method==''){
									json_output(400,array('status' => 400,'message' => 'Missing arguments. Failed to place order.'));
							}else if($channel!=''){

									$order_discount=strip_tags($params->coupon);
									$order_delivery_note=strip_tags($params->delivery_note);
									$delivery_charge=strip_tags($params->delivery_charge);
									$delivery_address_id=strip_tags($params->address_id);
									if(isset($params->items) && count($params->items)>0){
										$subtotal=$tax=$total=0;
										foreach($params->items as $order_detail){
												$subtotal+=($order_detail->item_price)*($order_detail->item_qty);
										}
										$tax=$subtotal*(5/100);
										$total=$subtotal+$tax+$delivery_charge;

										// order basic data array starts
										$ordersData['rest_id']=$rest_id=strip_tags($params->restaurant_id);
										$ordersData['cust_id']=$cust_id=strip_tags($params->customer_id);
										$ordersData['order_total_amount']=$total;
										$ordersData['order_total_items']=count($params->items);
										$ordersData['order_payment_type']=strip_tags($params->payment_method);
										$ordersData['order_gst']=$tax;
										$ordersData['order_package_charge']=strip_tags($params->package_charge);
										$ordersData['order_delivery_charge']=strip_tags($params->delivery_charge);
										$ordersData['order_discount']=strip_tags($params->coupon);
										$ordersData['order_delivery_note']=strip_tags($params->delivery_note);
										$ordersData['order_status']=1;
										$ordersData['order_placed_date']=date('Y-m-d h:i:s');
										// order basic data array ends


										$this->db->trans_start(); # Starting Transaction

										// insert order details
										$order_id=$this->db_functions->put_contents('orders', $ordersData);

										// generate order id
										$date_year=date('y');
										$date_month=date('m');
										$date_day=date('d');
										$date_hour=date('H');
										$generated_order_id='#IFTR'.$date_year.$date_month.$date_day.$date_hour.sprintf("%02d", $order_id);
										$this->db_functions->update_contents('orders', array('order_ref_id'=>$generated_order_id), array('order_id'=>$order_id));

										// order delivery address data array starts
										$addr_cond=array('addr_id'=>$delivery_address_id);
										$addr_result=$this->db_functions->get_contents('delivery_address', $addr_cond);
										$addressData=array();
										if(count($addr_result)==1){
											foreach($addr_result as $addr_res){
													$addressData['order_id']=$order_id;
													$addressData['order_addr_fullname']=$addr_res->addr_fullname;
													$addressData['order_addr_latitude']=$addr_res->addr_latitude;
													$addressData['order_addr_longitude']=$addr_res->addr_longitude;
													$addressData['order_building_no']=$addr_res->addr_building_no;
													$addressData['order_landmark']=$addr_res->addr_landmark;
													$addressData['order_addr_type']=$addr_res->addr_type;
											}
										}
										$this->db_functions->put_contents('order_address', $addressData);
										// order delivery address data array ends

										// order detail insertion starts
										$orderDetailsData=array();
										foreach($params->items as $order_detail){
												$orderDetailsData[]=array(
																									'order_id'=>$order_id,
																									'inventory_id'=>$order_detail->inventory_id,
																									'detail_qty'=>$order_detail->item_qty,
																									'detail_price'=>$order_detail->item_price
																						);
										}
										$this->db_functions->insert_batch('order_details', $orderDetailsData);
										// order detail insertion ends

										// order status section starts
										$orderStatusData['order_id']=$order_id;
										$orderStatusData['od_status']=1;
										$orderStatusData['od_updated_time']=date('Y-m-d h:i:s');




										$this->db_functions->put_contents('order_status', $orderStatusData);
										// order status section ends

										$this->db->trans_complete(); # Completing transaction
										if ($this->db->trans_status() === FALSE) {
										    $this->db->trans_rollback();
												json_output(400,array('status' => 400,'message' => 'Failed to place order'));
										}
										else {
										    $this->db->trans_commit();
												json_output(200,array('status' => 200, 'orderId'=>$order_id, 'orderRefId'=>$generated_order_id, 'message' => 'Successfully placed order'));
										}

									}else{
											json_output(400,array('status' => 400,'message' => 'Failed to place order'));
									}
							}
				}
		}

	}

	function details(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		}else{
				if($this->authStatus == true){
							$params = json_decode(file_get_contents('php://input'));
							$order_id=$params->order_id;
							$user_id=$params->user_id;

							$resultJsonArray=array();
							$order_status_array=array();

							// get order basic details
							$order_basic_detail_query="SELECT t1.`order_id`, t1.`order_ref_id`, t1.`order_placed_date` as order_date, t1.`order_total_amount` as order_total, t1.order_gst as gst, t1.order_package_charge as package_charge, t1.order_delivery_charge as delivery_charge, t1.order_discount as discount, t1.`order_total_items` as total_items, t1.`order_payment_type` as payment_method, t1.`order_status`, t1.`order_status_msg` as status_msg, t2.rest_name, t2.rest_address FROM `orders` as t1
                            JOIN restaurants as t2 ON t2.rest_id=t1.`rest_id`
                            WHERE t1.`cust_id`=$user_id AND t1.order_id=$order_id LIMIT 1";
							$order_details_array=$this->db_functions->makequery($order_basic_detail_query);
							$resultJsonArray['order_details']=$order_details_array;

							// get order status details
							if(count($order_details_array)==1){
								$order_status_query="SELECT `od_status_id` as status_id, `od_status` as status, `od_updated_time` as status_time FROM `order_status` WHERE `order_id`=$order_id ORDER BY od_status_id ASC";
								$order_status_result=$this->db_functions->makequery($order_status_query);
								if(count($order_status_result)>0){
									foreach($order_status_result as $row){
											$order_status_array['status_id']=$row->status_id;
											$order_status=$row->status;
											if($order_status==1){
												$status_text='Placed';
											}elseif($order_status==2){
												$status_text='Confirmed';
											}elseif($order_status==3){
												$status_text='Packed';
											}elseif($order_status==4){
												$status_text='Dispatched';
											}elseif($order_status==5){
												$status_text='Delivered';
											}else{
												$status_text='Cancelled';
											}
											$order_status_array['order_status']=$status_text;
											$order_status_array['status_time']=$row->status_time;
											$order_status_json[]=$order_status_array;
									}
									$resultJsonArray['order_status']=$order_status_json;
								}

								// get order item details
								$item_details_query="SELECT t1.inventory_id, t1.detail_sku, t1.detail_name as item_name, t1.detail_qty as item_qty, t1.detail_price as item_price	FROM `order_details` as t1 WHERE t1.order_id=$order_id";
								$order_items_json=$this->db_functions->makequery($item_details_query);
								$resultJsonArray['order_items']=$order_items_json;

								// get address details
								$address_details_query="SELECT t1.order_addr_fullname as address_full_name, t1.order_building_no as address_building_no, t1.order_landmark as address_landmark,	t1.order_addr_type as address_type FROM `order_address` as t1 WHERE t1.`order_id`=$order_id";
								$delivery_address_result=$this->db_functions->makequery($address_details_query);
								if(count($delivery_address_result)==1){
									foreach($delivery_address_result as $row){
										$delivery_address_array['address_full_name']=$row->address_full_name;
										$delivery_address_array['address_building_no']=$row->address_building_no;
										$delivery_address_array['address_landmark']=$row->address_landmark;
										$delivery_address_array['address_type_id']=$row->address_type;
										if($row->address_type==1){
											$delivery_address_array['address_type']='home';
										}
										$delivery_address_json[]=$delivery_address_array;
									}
									$resultJsonArray['delivery_address']=$delivery_address_json;
								}
							}


							json_output(200,array('status' => 200, 'message'=>'success', 'data'=>$resultJsonArray));
				}
		}
	}

	function past_orders(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		}else{
				if($this->authStatus == true){
							$params = json_decode(file_get_contents('php://input'));
							$user_id=$params->user_id;

							$resultArray=array();
							$resultData=array();

							// get order basic details
							$order_basic_detail_query="SELECT t1.`order_id`, t1.`order_ref_id`, t1.`order_placed_date` as order_date, t1.`order_total_amount` as order_total, t1.`order_total_items` as total_items, t1.`order_payment_type` as payment_method, t1.`order_status`, t1.`order_status_msg` as status_msg, t2.rest_name, t2.rest_address FROM `orders` as t1
                            JOIN restaurants as t2 ON t2.rest_id=t1.`rest_id`
                            WHERE t1.`cust_id`=$user_id ORDER BY t1.order_id DESC";
							$resultArray=$this->db_functions->makequery($order_basic_detail_query);
							$order_count=count($resultArray);
							if($order_count>0){
								foreach($resultArray as $row){
									$resultData['order_id']=$row->order_id;
									$resultData['order_ref_id']=$row->order_ref_id;
									$resultData['order_date']=date('d M, Y H:i', strtotime($row->order_date));
									$resultData['order_total']=$row->order_total;
									$resultData['total_items']=$row->total_items;
									$resultData['payment_method']=$row->payment_method;
									$order_status=$row->order_status;
									if($order_status==1){
										$status_text='Placed';
									}elseif($order_status==2){
										$status_text='Confirmed';
									}elseif($order_status==3){
										$status_text='Packed';
									}elseif($order_status==4){
										$status_text='Dispatched';
									}elseif($order_status==5){
										$status_text='Delivered';
									}else{
										$status_text='Cancelled';
									}
									$resultData['order_status']=$status_text;
									$resultData['order_status_id']=$row->order_status;;
									$resultData['rest_name']=$row->rest_name;
									$resultData['rest_address']=$row->rest_address;
									$resultDataArray[]=$resultData;
								}
							}


							json_output(200,array('status' => 200, 'message'=>'success', 'total_orders'=>$order_count, 'data'=>$resultDataArray));
				}
		}
	}


}
