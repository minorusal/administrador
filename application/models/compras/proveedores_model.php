<?php
class proveedores_model extends Base_Model{

	public function db_get_data($data=array()){
		
		$buscar = (isset($data['buscar']))?$data['buscar']:false;
		$filtro = ($buscar) ? "AND ( 	p.razon_social  LIKE '%$filtro%' OR 
										p.nombre_comercial  LIKE '%$filtro%' OR
										p.clave_corta LIKE '%$filtro%'
											)" : "";
		
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";

		$query = "	SELECT
						*
					FROM av_compras_proveedores p
					WHERE p.activo = 1 $filtro
					$limit
					";

      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	public function db_get_total_rows(){

		$query = "SELECT * FROM av_compras_proveedores	";
      	$query = $this->db->query($query);
		return $query->num_rows;
	
	}
}