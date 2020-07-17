<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site_settings extends MX_Controller {

	public $authStatus = false;

	public function __construct(){
		parent::__construct();
		$this->load->model('db_model');
	}

	public function application_status(){
			$result=$this->db_model->get_contents('settings', array('settings_name'=>'SHUTDOWN'))->result();
			$status=$result[0]->settings_value;
			if($status=='on'){
				return true;
			}else{
				return false;
			}
	}

	function api_auth(){
		$check_auth_client = $this->db_model->check_auth_client();
		return $check_auth_client;
	}

}
