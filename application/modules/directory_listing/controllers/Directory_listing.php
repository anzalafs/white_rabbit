<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Directory_listing extends MX_Controller {

	public function __construct ()
	{
			parent::__construct();
			$this->load->model('db_functions');
			$this->load->module('templates');
			$this->load->library('pagination');
	}

	public function index()
	{
		$data['module']='directory_listing';
		$data['view']='directory_listing';
		$data['view_js']='directory_listing_js';
		$this->templates->manage($data);
	}


	function list_all_files($page=''){
		if($this->input->get('file_name')){
			$search_cond = $this->input->get('file_name');
		}else{
			$search_cond = '';
		}
		$lists = $this->db_functions->fetch_contents(FALSE, FALSE, $search_cond);

		if($lists){
			$config = array();
	    $config["base_url"] = base_url() . "directory_listing/list_all_files/";
	    $config["total_rows"] = count($lists);
	    $config["per_page"] = 5;
	    $config["uri_segment"] = 3;
			$this->pagination->initialize($config);

			$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
	    $data["results"] = $this->db_functions->fetch_contents($config["per_page"], $page, $search_cond);
	    $data["links"] = $this->pagination->create_links();
		}else{
			$data["results"] = array();
	    $data["links"] = '';
		}

		$data['module']='directory_listing';
		$data['view']='directory_listing';
		$data['view_js']='directory_listing_js';
		$this->templates->manage($data, $data);
	}


	function logs($page=''){
		$lists = $this->db_functions->get_contents('file_action_log');
		if($lists){
			$config = array();
	    $config["base_url"] = base_url() . "directory_listing/logs/";
	    $config["total_rows"] = count($lists);
	    $config["per_page"] = 5;
	    $config["uri_segment"] = 3;
			$this->pagination->initialize($config);

			$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
	    $data["results"] = $this->db_functions->fetch_logs($config["per_page"], $page);
	    $data["links"] = $this->pagination->create_links();
		}else{
	    $data["results"] = array();
	    $data["links"] = '';
		}


		$data['module']='directory_listing';
		$data['view']='directory_logs';
		$this->templates->manage($data, $data);
	}

	function fetchAllFiles(){
		$data['lists']=$this->db_functions->get_contents('files');
		echo json_encode($data);
	}


	function delete_file(){
		$return_arr=array();
		if($this->input->post('fileId') && $this->input->post('fileId')!=''){

			$file_id = $this->input->post('fileId');
			$query = "SELECT `file_id`, `file_name` FROM `files` WHERE `file_id`=$file_id";
			$result = $this->db_functions->makequery($query);

			if(count($result)>0){
				$file_name = $result[0]->file_name;
				if($file_name!=''){
					$path=APPPATH.'../uploads/'.$file_name;
					unlink($path);
				}

				$delete_cond = array('file_id'=>$file_id);
				$this->db_functions->delete_contents('files', $delete_cond);

				$logData['log_action'] = 'delete';
				$logData['file_name'] = $file_name;
				$this->db_functions->put_contents('file_action_log', $logData);

				$return_arr['status']=200;
				$return_arr['msg']='Successfully deleted file';

			}else{

				$return_arr['status']=400;
				$return_arr['msg']='Failed';

			}

			echo json_encode($return_arr);
		}
	}


	function deleteSelectedFile(){
		$return_arr=array();
		if($this->input->post('fileName') && $this->input->post('fileName')!=''){

			$file_name = $this->input->post('fileName');
			if($file_name!=''){
				$path=APPPATH.'../uploads/'.$file_name;
				unlink($path);

				$logData['log_action'] = 'delete';
				$logData['file_name'] = $file_name;
				$this->db_functions->put_contents('file_action_log', $logData);

				$return_arr['status']=200;
				$return_arr['msg']='Successfully deleted file';
			}else{

				$return_arr['status']=400;
				$return_arr['msg']='Failed';

			}

			echo json_encode($return_arr);
		}
	}


}
