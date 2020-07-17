<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MX_Controller {

	public $authStatus = false;

	public function __construct(){
		parent::__construct();
		$this->load->model('auth_model');
		$this->load->module('site_settings');
		$this->authStatus=$this->site_settings->api_auth();
	}

	public function login(){
		$method = $_SERVER['REQUEST_METHOD'];

		if($method != 'POST'){
				json_output(400,array('status' => 400,'message' => 'Bad request.'));
		}else{

				// $check_auth_client = $this->auth_model->check_auth_client();

				if($this->authStatus == true){
						$params = json_decode(file_get_contents('php://input'));

		        $username = $params->username;
						$password = md5($params->password);
		        $channel = $params->channel;

						$cond=array('cust_phone'=>$username, 'cust_password'=>$password, 'cust_status'=>1);
		        $response = $this->auth_model->get_contents('customer_login', $cond);

						if(($response->num_rows())==1){
								$token = random_string('alnum', 20);
								$last_login = date('Y-m-d H:i:s');

								$response=$response->result();

								$loginDetails=array(
											'cust_id'=>(int)$response[0]->cust_id,
											'last_login'=>$last_login,
											'cust_access_token'=>$token,
											'cust_login_channel'=>$channel
								);
								$cond=array('cust_id'=>$response[0]->cust_id);
								$this->auth_model->update_contents('customer_authentication', $loginDetails, $cond);

								$loginData=array(
											'cust_id'=>(int)$response[0]->cust_id,
											'cust_phone'=>$username,
											'cust_access_token'=>$token
								);
								// set user details session here
								// $userData=array('cust_id'=>$response[0]->cust_id);
								// $this->session->set_userdata('logindetails', $userData);

								json_output(200,array('status' => 200,'message' => 'Successfully logined', 'data' => $loginData));

						}else{
								return json_output(400,array('status' => 400, 'mobile_no'=>$username, 'message' => 'Invalid login details'));
						}
					}
				}
	}

	// login using mobile no starts
	public function validateMobileNo(){
		$method = $_SERVER['REQUEST_METHOD'];

		if($method != 'POST'){
				json_output(400,array('status' => 400,'message' => 'Bad request.'));
		}else{

				// $check_auth_client = $this->auth_model->check_auth_client();

				if($this->authStatus == true){
						$params = json_decode(file_get_contents('php://input'));

		        $username = $params->username;
		        $channel = $params->channel;

						if(!empty($username)){

								if(!$this->validate_mobile($username)){
									return json_output(400,array('status' => 400,'message' => 'Invalid mobile number'));
								}
						}

							$cond=array('cust_phone'=>$username);
			        $response = $this->auth_model->get_contents('customer_login', $cond);

							if(($response->num_rows())==1){
									json_output(200,array('status' => 200,'found' => 1));
							}else{
									$otp=1234;
									json_output(200,array('status' => 200,'found' => 0, 'otp'=>$otp));
							}

					}else{
						json_output(400,array('status' => 400,'message' => 'Bad request.'));
					}
				}
	}
	// login using mobile no ends

	// forgot password starts
	public function forgotpassword(){
		$method = $_SERVER['REQUEST_METHOD'];

		if($method != 'POST'){
				json_output(400,array('status' => 400,'message' => 'Bad request.'));
		}else{

				// $check_auth_client = $this->auth_model->check_auth_client();

				if($this->authStatus == true){
						$params = json_decode(file_get_contents('php://input'));

		        $username = $params->username;
		        $channel = $params->channel;

						if(!empty($username)){

								if(!$this->validate_mobile($username)){
									return json_output(400,array('status' => 400,'message' => 'Invalid mobile number'));
								}
						}

							$cond=array('cust_phone'=>$username);
			        $response = $this->auth_model->get_contents('customer_login', $cond);

							if(($response->num_rows())==1){
								$otp=1234;
								json_output(200,array('status' => 200, 'otp'=>$otp, 'message'=>'success'));
							}else{
								json_output(400,array('status' => 400,'message' => 'Invalid mobile number'));
							}

					}else{
						json_output(400,array('status' => 400,'message' => 'Bad request.'));
					}
				}
	}
	// forgot password ends

	// reset password starts
	public function reset_password(){
			$method = $_SERVER['REQUEST_METHOD'];
			if($method != 'POST'){
				json_output(400,array('status' => 400,'message' => 'Bad request.'));
			}else{

					if($this->authStatus == true){
								$params = json_decode(file_get_contents('php://input'));
				        $channel=$params->channel;
								if($channel!=''){
										$phone=strip_tags($params->phone);
										$userCount=$this->check_user_exists($phone);
										if($userCount==1){

												$channel=strip_tags($params->channel);
												$password=strip_tags($params->password);
												$userValue = array(
														'cust_password' => strip_tags(md5($password))
												);
												$cust_id=$this->getCustomerIdByPhone($phone)[0]->cust_id;
												$userCond=array('cust_id'=>$cust_id);
												$insert=$this->auth_model->update_contents('customer_login', $userValue, $userCond);

												if($insert){

														$token = random_string('alnum', 20);
														$last_login = date('Y-m-d H:i:s');

														$loginDetails=array(
															'cust_id'=>$cust_id,
															'cust_access_token'=>$token,
															'cust_login_channel'=>$channel
														);
														$this->auth_model->put_contents('customer_authentication', $loginDetails);

														json_output(200,array('status' => 200,'message' => 'Successfully updated password', 'data' => $loginDetails));

												}
										}else{
												json_output(400,array('status' => 400,'message' => 'Invalid mobile number'));
										}
								}
					}

			}

	}
	// reset password ends

	// customer registration goes here
	public function registration(){
			$method = $_SERVER['REQUEST_METHOD'];
			if($method != 'POST'){
				json_output(400,array('status' => 400,'message' => 'Bad request.'));
			}else{

					if($this->authStatus == true){
								$params = json_decode(file_get_contents('php://input'));

				        $channel=$params->channel;
								if($channel!=''){
										$phone=strip_tags($params->phone);
										// $name=strip_tags($params->name);
										// $email=strip_tags($params->email);
										// $password=strip_tags($params->password);
										if(!empty($phone)){

												if(!$this->validate_mobile($phone)){
													return json_output(400,array('status' => 400,'message' => 'Invalid mobile number'));
												}
												$userCount=$this->check_user_exists($phone);
												if($userCount>0){
													json_output(400,array('status' => 400,'message' => 'The given mobile number already exists.'));
												}else{
														// $otp=rand(1000, 9999);
														$otp=1234;
														// Insert user data
						                /*$userData = array(
						                    'cust_phone' => $phone,
						                    'cust_name' => $name,
						                    'cust_email' => $email,
						                    'cust_password' => md5($password),
																'cust_ip' => $_SERVER['REMOTE_ADDR'],
																'cust_channel' => $channel,
																'cust_created_date' => date('Y-m-d h:i:s')
						                );*/
						                json_output(200,array('status' => 200, 'otp'=>$otp, 'message' => 'Please very the OTP sent to your number'));
												}
										}else{
											json_output(400,array('status' => 400,'message' => 'Please fill all fields and try again'));
										}
								}
					}

			}

	}

	// check user details exists or not starts
	function check_user_exists($phone=FALSE){
			if($phone!=FALSE){
					$cond=array('cust_phone'=>$phone);
					$userCount=$this->auth_model->get_contents('customer_login', $cond)->num_rows();
			}else{
					$userCount=0;
			}
			return $userCount;
	}
	// check user details exists or not ends

	// get custmer id by mobile no starts
	function getCustomerIdByPhone($phone=FALSE){
			if($phone!=FALSE){
					$cond=array('cust_phone'=>$phone);
					$userdata=$this->auth_model->get_contents('customer_login', $cond, false, 'cust_id')->result();
			}else{
					$userdata=array();
			}
			return $userdata;
	}
	// get custmer id by mobile no ends

	// check user id exists or not starts
	function check_user_id_exists($userid=FALSE){
			if($userid!=FALSE){
					$cond=array('cust_id'=>$userid);
					$userCount=$this->auth_model->get_contents('customer_login', $cond)->num_rows();
			}else{
					$userCount=0;
			}
			return $userCount;
	}
	// check user id exists or not ends

	// registration after otp validation starts
	function save_userdetails(){
			$method = $_SERVER['REQUEST_METHOD'];
			if($method != 'POST'){
					json_output(400,array('status' => 400,'message' => 'Bad request.'));
			}else{
					if($this->authStatus == true){
							$params = json_decode(file_get_contents('php://input'));
							$phone=$params->userdata->phone;
							$userCount=$this->check_user_exists(strip_tags($phone));
							// $userCount=0;
							if($userCount==0){

									$channel=strip_tags($params->channel);
									$userData = array(
											'cust_phone' => strip_tags($params->userdata->phone),
											'cust_name' => strip_tags($params->userdata->name),
											'cust_email' => strip_tags($params->userdata->email),
											'cust_password' => strip_tags(md5($params->userdata->password)),
											'cust_ip' => $_SERVER['REMOTE_ADDR'],
											'cust_channel' => $channel,
											'cust_created_date' => date('Y-m-d h:i:s')
									);
									$insert=$this->auth_model->put_contents('customer_login', $userData);
									if($insert){
											$token = random_string('alnum', 20);
											$last_login = date('Y-m-d H:i:s');

											$loginDetails=array(
												'cust_id'=>$insert,
												'cust_access_token'=>$token,
												'cust_login_channel'=>$channel
											);
											$this->auth_model->put_contents('customer_authentication', $loginDetails);
											$loginDetails['cust_phone'] = $phone;
											json_output(200,array('status' => 200,'message' => 'Successfully registered', 'data' => $loginDetails));

									}
							}else{
									json_output(400,array('status' => 400,'message' => 'User exist'));
							}

					}else{
						json_output(400,array('status' => 400,'message' => 'Authentication failed.'));
					}
			}
	}
	// registration after otp validation ends

	// otp verification delete this starts
	// validate OTP
	/*function validate_registration(){
				$method = $_SERVER['REQUEST_METHOD'];

				if($method != 'POST'){
						json_output(400,array('status' => 400,'message' => 'Bad request.'));
				}else{

						// $check_auth_client=$this->site_settings->api_auth();

						if($this->authStatus == true){
								$params = json_decode(file_get_contents('php://input'));

								$otp = $params->otp;
								$channel = $params->channel;

								// $sessionOtp=$this->session->userdata('otp');
								$sessionOtp=$this->session->tempdata('otp');

								if($otp==$sessionOtp){
											$insert=$this->auth_model->put_contents('customer_login', $this->session->userdata('userdata'));
											if($insert){
													$token = random_string('alnum', 20);
													$last_login = date('Y-m-d H:i:s');

													$loginDetails=array(
														'cust_id'=>$insert,
														'cust_access_token'=>$token,
														'cust_login_channel'=>$channel
													);
													$this->auth_model->put_contents('customer_authentication', $loginDetails);

													json_output(200,array('status' => 200,'message' => 'Successfully logined', 'data' => $loginDetails));

											}
								}else{
										return json_output(400,array('status' => 400,'message' => 'Invalid OTP'));
								}

						}
			}
	}*/
	// otp verification delete this ends





	// validate mobile
	function validate_mobile($mobile){
	    return preg_match('/^[0-9]{10}+$/', $mobile);
	}

	public function logout(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			if($this->authStatus == true){
						echo "<pre>";
						print_r($this->session->userdata('logindetails'));
						echo "</pre>";
						/*$this->delete_contents('customer_authentication', array('cust_id'=>))
						$this->db->where('users_id',$users_id)->where('token',$token)->delete('users_authentication');
						return array('status' => 200,'message' => 'Successfully logout.');
		        $response = $this->MyModel->logout();
						json_output($response['status'],$response);*/
			}
		}
	}

}
