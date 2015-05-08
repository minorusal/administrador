<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('Base_DBconfig.php');
class Base_Model extends CI_Model {

	public $dbdata, $dbinfo;
	function __construct(){
		$this->dbdata = new Base_DBconfig();
		$this->dbinfo = $this->dbdata->db_config();
	}

	public function row_exist($table, $row ){
    	$this->db->select();
		$this->db->from($table);
		$this->db->where($row);
		$query = $this->db->get();
		if($query->num_rows >= 1){
			return true;
		}else{
			return false;
		}
    }
}

?>