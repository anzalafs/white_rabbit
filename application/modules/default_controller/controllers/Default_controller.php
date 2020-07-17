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
		$data['module']='directory_listing';
		$data['view']='directory_listing';
		$this->templates->manage($data);
	}
}
