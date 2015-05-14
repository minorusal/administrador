<?php
class sucursales_model extends Base_Model
{
	public function get_sucursales($limit, $offset, $filtro="", $aplicar_limit = true){
		$query = "	SELECT 
						cp.id_sucursal
						,cp.sucursal
					FROM
						00_av_system.sys_sucursales cp
					";
      	
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
}