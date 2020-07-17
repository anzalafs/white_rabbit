<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Db_functions extends CI_Model{

	public function __construct(){
		parent::__construct();
		$this->load->database();
	}

	//function to insert elements
	function put_contents ( $table, $value ){
		$this->db->insert($table, $value);
		$insert_id = $this->db->insert_id();
   		return  $insert_id;
	}

}
?>
