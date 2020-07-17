<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Directory_upload extends MX_Controller {

	public function __construct ()
	{
			parent::__construct();
			$this->load->model('db_functions');
			$this->load->module('templates');
	}

	public function index()
	{
		$data['module']='directory_upload';
		$data['view']='directory_upload';
		$data['view_js']='directory_upload_js';
		$this->templates->manage($data);
	}

	function upload_file(){
		$return_arr=array();
		// image section starts
		$config['upload_path']          = './uploads/';
		$config['allowed_types']        = 'txt|doc|docx|pdf|png|jpeg|jpg|gif';
		$config['max_size']             = 2000;
		$this->load->library('upload', $config);
		if ($this->upload->do_upload('upload_file')){
			// if($this->input->post('fileId') && $this->input->post('fileId')!=''){
			// 	$path=APPPATH.'../uploads/';
			// 	unlink($path);
			// }
			$data['file_name'] = $file_name = $this->upload->data('file_name');
			$result=$this->db_functions->put_contents('files', $data);

			$logData['log_action'] = 'insert';
			$logData['file_name'] = $file_name;
			$this->db_functions->put_contents('file_action_log', $logData);

			$return_arr['status']=200;
			$return_arr['msg']='Successfully added file';
		}else{
			$error = array('error' => $this->upload->display_errors());
			$return_arr['status']=400;
			$return_arr['msg']=$error['error'];
		}
		// image section ends
		echo json_encode($return_arr);
	}

}
