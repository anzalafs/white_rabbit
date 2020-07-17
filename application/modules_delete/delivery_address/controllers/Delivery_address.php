<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery_address extends MX_Controller {

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

	// save delivery address starts
	public function saveaddress(){
			$method = $_SERVER['REQUEST_METHOD'];
			if($method != 'POST'){
				json_output(400,array('status' => 400,'message' => 'Bad request.'));
			}else{

					if($this->authStatus == true){
								$params = json_decode(file_get_contents('php://input'));

				        $channel=$params->channel;
								if($channel!=''){
										$addressData['addr_fullname']=strip_tags($params->address);
										$addressData['addr_latitude']=strip_tags($params->latitude);
										$addressData['addr_longitude']=strip_tags($params->longitude);
										$addressData['addr_type']=strip_tags($params->type);
										$addressData['addr_status']=strip_tags($params->status);
										$addressData['addr_building_no']=strip_tags($params->doorno);
										$addressData['addr_landmark']=strip_tags($params->landmark);
										$addressData['cust_id']=$userid=strip_tags($params->uderid);
										if(!empty($userid)){

												// check user id exists or not
												$usercount=$this->auth->check_user_id_exists($userid);
												if($usercount==1){
														$result=$this->db_functions->put_contents('delivery_address', $addressData);
														if($result){
																json_output(200,array('status' => 200, 'delAddressId'=>$result, 'message' => 'Successfully created delivery address'));
														}
												}else{
														json_output(400,array('status' => 400,'message' => 'Invalid user id'));
												}


										}else{
											json_output(400,array('status' => 400,'message' => 'Please fill all fields and try again'));
										}
								}
					}

			}

	}
	// save delivery address ends

	// get all user saved address starts
	public function getAllUserAddress(){
			$method = $_SERVER['REQUEST_METHOD'];
			if($method != 'POST'){
				json_output(400,array('status' => 400,'message' => 'Bad request.'));
			}else{

					if($this->authStatus == true){
								$params = json_decode(file_get_contents('php://input'));

								$userid=(int)$params->userid;
								if($userid!=''){

										// check user id exists or not
										$usercount=$this->auth->check_user_id_exists($userid);
										if($usercount==1){
											$cond=array('cust_id'=>$userid);

											$result=$this->db_functions->get_contents('delivery_address', $cond);
											if($result){
													json_output(200,array('status' => 200, 'message' => 'Success', 'data'=>$result));
											}else{
												json_output(200,array('status' => 200, 'message' => 'Success', 'data'=>array()));
											}
										}else{
											json_output(400,array('status' => 400,'message' => 'Invalid user id'));
										}

								}
					}

			}

	}
	// get all user saved address ends


	// get selected user saved address starts
	public function getDeliveryAddressById(){
			$method = $_SERVER['REQUEST_METHOD'];
			if($method != 'POST'){
				json_output(400,array('status' => 400,'message' => 'Bad request.'));
			}else{

					if($this->authStatus == true){
								$params = json_decode(file_get_contents('php://input'));

					       $addrid=(int)$params->addrid;
								if($addrid!=''){

										$cond=array('addr_id'=>$addrid);

										$result=$this->db_functions->get_contents('delivery_address', $cond);
										if($result){
												json_output(200,array('status' => 200, 'message' => 'Success', 'data'=>$result));
										}else{
											json_output(200,array('status' => 200, 'message' => 'No data found'));
										}

								}else{
									json_output(400,array('status' => 400,'message' => 'No data found'));
								}
					}else{
						json_output(400,array('status' => 400,'message' => 'Authentication falied'));
					}

			}

	}
	// get all user saved address ends

}
