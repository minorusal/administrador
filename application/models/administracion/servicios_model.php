<?php
class servicios_model extends Base_Model{
	//Función que obtiene toda la información de la tabla av_administracion_servicios
	public function db_get_data($data=array())	{		
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (sr.servicio like '%$filtro%' OR
									sr.clave_corta like '%$filtro%' OR
									sr.descripcion like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "	SELECT *
					FROM $tbl[administracion_servicios] sr
					WHERE sr.activo = 1 $filtro
					GROUP BY sr.id_administracion_servicio ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	/*Trae la información para el formulario de edición de la tabla av_administracion_servicios*/
	public function get_orden_unico_servicio($id_administracion_servicios)	{
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[administracion_servicios] WHERE id_administracion_servicio = $id_administracion_servicios";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
}