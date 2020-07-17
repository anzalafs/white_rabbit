<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Db_functions extends CI_Model{

	public function __construct(){
		parent::__construct();
		$this->load->database();
	}


	//custom query
	public function makequery($query){
		$return=false;
		if($query){
			$return=$this->db->query($query);
		}
		return $return->result();
	}

	//function to insert elements
	function put_contents ( $table, $value ){
		$this->db->insert($table, $value);
		$insert_id = $this->db->insert_id();
   		return  $insert_id;
	}

	//function to get data
	function get_contents( $table){
		$query = $this->db->get($table);
		return $query->result();
	}

	public function fetch_contents($limit='', $start='', $search='') {
    if($limit!='' && $start!=''){
			$this->db->limit($limit, $start);
		}
		if($search!=''){
			$this->db->like('file_name', $search);
			$query = $this->db->get_where('files');
		}else{
			$query = $this->db->get("files");
		}


    if ($query->num_rows() > 0) {
        foreach ($query->result() as $row) {
            $data[] = $row;
        }
        return $data;
    }
    return false;
   }

	 function fetch_logs($limit, $start){
		 $this->db->limit($limit, $start);
		 $query = $this->db->get("file_action_log");

		 if ($query->num_rows() > 0) {
				 foreach ($query->result() as $row) {
						 $data[] = $row;
				 }
				 return $data;
		 }
		 return false;
	 }

	//function to delete elements
	function delete_contents ( $table, $conditions = FALSE ){
		if ( $conditions == FALSE ){
			return $this->db->delete($table);
		}
		return $this->db->delete($table, $conditions);
	}

}
?>
