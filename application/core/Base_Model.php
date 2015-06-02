<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('Base_DBconfig.php');
class Base_Model extends CI_Model {

	public $dbdata, $dbinfo;
	function __construct(){
		$this->dbdata = new Base_DBconfig();
		$this->dbinfo = $this->dbdata->db_config();
	}

	public function row_exist($table, $row, $debug=false){
    	$this->db->select();
		$this->db->from($table);
		$this->db->where($row);
		$query = $this->db->get();
		if($debug){
			print_debug($query->result_array());
		}
		if($query->num_rows >= 1){
			return true;
		}else{
			return false;
		}
    }

    public function enabled_item($table, $clauses){ 	
    	$item  = array('activo' => 0);
		$query = $this->db->update_string($table, $item, $clauses);
		$query = $this->db->query($query);
		return $query;
    }
    public function update_item($tbl, $data, $id_row, $condicion = '') {

    	$route = $this->uri->uri_string();
    	$log   = array(	 'route'      => $route,
	    				 'type'       => 'UPDATE',
	    				 'tabla'      => $tbl,
	    				 'id_row'     => $data[$id_row],
	    				 'id_usuario' => $data['edit_id_usuario'],
	    				 'timestamp'  => $data['edit_timestamp']
	    			);
    	$log    = $this->db->insert_string('av_administracion_movimientos', $log);
    	$log    = $this->db->query($log);
    	if($log){
    		$update = $this->db->update_string($tbl, $data, $condicion);
    		$update = $this->db->query($update);
    	}else{
    		$update = false;
    	}
    	return $update;
    }
    public function insert_item($tbl, $data = array()){
    	$insert  = $this->db->insert_string($tbl, $data);
    	$insert  = $this->db->query($insert);
    	if($insert){
    		$id_row  = $this->db->insert_id();
	    	$route   = $this->uri->uri_string();
	    	$log     = array(	 'route'      => $route,
			    				 'type'       => 'INSERT',
			    				 'tabla'      => $tbl,
			    				 'id_row'     => $id_row,
			    				 'id_usuario' => $data['id_usuario'],
			    				 'timestamp'  => $data['timestamp']
			    			);

	    	$log   = $this->db->insert_string('av_administracion_movimientos', $log);
	    	$log   = $this->db->query($log);
    	}else{
    		$insert = false;
    	}
    	return $insert;
    }
    public function logs(){

    }
}

?>