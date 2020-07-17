<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Default_controller extends MX_Controller {

	public function __construct ()
	{
			parent::__construct();
			$this->load->model('db_functions');
			$this->load->module('templates');
	}

	public function index()
	{
		$data['active']="dashboard";
		$data['module']="dashboard";
		$data['view']="dashboard";
		$this->templates->manage($data);
	}
}
