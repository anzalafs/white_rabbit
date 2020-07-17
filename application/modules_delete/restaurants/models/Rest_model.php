<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Rest_model extends CI_Model{

	public function __construct(){
		parent::__construct();
		$this->load->database();
	}

	var $client_service = "frontend-client";
	var $auth_key       = "ifthar";

	public function check_auth_client(){
			$client_service = $this->input->get_request_header('Client-Service', TRUE);
			$auth_key  = $this->input->get_request_header('Auth-Key', TRUE);

			if($client_service == $this->client_service && $auth_key == $this->auth_key){
					return true;
			} else {
					return json_output(401,array('status' => 401,'message' => 'Unauthorized.'));
			}
	}


	//custom query
	public function makequery($query){
		$return=false;
		if($query){
			$return=$this->db->query($query);
		}
		return $return;
	}

	//function to insert elements
	function put_contents ( $table, $value ){
		$this->db->insert($table, $value);
		$insert_id = $this->db->insert_id();
   		return  $insert_id;
	}

	//function to get data
	function get_contents( $table, $value = FALSE , $order=FALSE, $select = '*', $limit=FALSE, $group=FALSE){
		$this->db->select($select);
		if ( $value === FALSE ){
			$query = $this->db->get($table);

			return $query;
		}
		if($order != FALSE){
			$this->db->order_by($order);
		}
		if($limit != FALSE){
			$this->db->limit($limit);
		}
		if($group != FALSE){
			$this->db->group_by($group);
		}

		$query = $this->db->get_where($table, $value);
		return $query;
	}


	//function to update elements
	function update_contents ( $table, $value, $conditions = FALSE ){
		if ( $conditions === FALSE ){
			return $this->db->update($table, $value);
		}
		return $this->db->update($table, $value, $conditions);
	}

	//function to delete elements
	function delete_contents ( $table, $conditions = FALSE ){
		if ( $conditions == FALSE ){
			return $this->db->delete($table);
		}
		return $this->db->delete($table, $conditions);
	}


	//Delete multiple
	function delete_mul_data($table, $table_id){
		$this->db->where_in($table_id, $this->input->post('ids'));
		return $this->db->delete($table);
	}

	//update multiple
	function update_mul_data($table, $table_id,$values){
		$this->db->where_in($table_id, $this->input->post('ids'));
		return $this->db->update($table,$values);
	}



}
?>
