<?php
class catalogos_model extends Base_Model
{
	/*ALMACENES*/
	public function get_almacenes($limit, $offset, $filtro="", $aplicar_limit = true){
		$filtro = ($filtro=="") ? "" : "AND (
												cp.clave_corta like '%$filtro%'
											OR
												cp.descripcion like '%$filtro%'
										) ";
		$limit = ($aplicar_limit) ?  "LIMIT $offset ,$limit " : "";
		$query = "	SELECT 
						cp.id_almacen_almacenes
						,cp.clave_corta
						,cp.descripcion
					FROM
						av_almacen_almacenes cp
					ORDER BY cp.id_almacen_almacenes
					$limit";
      	
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
}

//WHERE cp.activo = 1 $filtro