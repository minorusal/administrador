<?php
class sucursales_model extends Base_Model
{
	public function get_sucursales($data=array()){
		// DB Info
		$db1 	= $this->dbinfo[0]['db'];
		$tbl1 	= $this->dbinfo[0]['tbl_sucursales'];
		// Query
		$query = "	SELECT *
					FROM $db1.$tbl1
					WHERE 1 $filtro
					";
      	
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
}