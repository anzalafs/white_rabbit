<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Restaurants extends MX_Controller {

	public $authStatus = false;
	public $applicationStatus = true;

	public function __construct(){
		parent::__construct();
		$this->load->model('rest_model');
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
						$latitude = $params->latitude;
						$longitude = $params->longitude;
						$channel = $params->channel;
						$distance=15;
						// query to get near by restaurants
						$query="SELECT `rest_id` as restId, `rest_name` as restName, rest_cat_text as rest_cat, `rest_url` as restUrl, `rest_address` as restAddress, `rest_latitude` as restLatitude, `rest_longitude` as restLongitude, `rest_image` as restImage,
       (6371 * acos( cos( radians($latitude) ) * cos( radians(`rest_latitude`) ) *
        cos( radians(`rest_longitude`) - radians($longitude)) + sin(radians($latitude)) *
        sin(radians(`rest_latitude`)) )) as distance FROM restaurants WHERE `rest_status`=1 HAVING distance < $distance ORDER BY distance asc";
						$lists=$this->db_model->makequery($query)->result();
						if(count($lists)<=0){
							json_output(200,array('status' => 200,'message' => 'Sorry no near by restaurants found', 'data'=>array()));
						}else{
							json_output(200,array('status' => 200,'data' => $lists));
						}
				}
		}
	}

}
