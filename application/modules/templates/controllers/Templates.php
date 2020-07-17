<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Templates extends MX_Controller {

	public function __construct ()
	{
			parent::__construct();
			$this->load->model('db_functions');
			$this->load->module('templates');
	}

	public function manage($data)
	{
		$this->load->view('main', $data);
	}
}
