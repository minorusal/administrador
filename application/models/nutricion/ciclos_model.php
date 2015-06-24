<?php
class ciclos_model extends Base_Model{
	public function __construct(){
		parent::__construct();
	}

	//Función que obtiene toda la información de la tabla av_administracion_areas
	/*public function db_get_data($data=array())	{		
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (ar.area like '%$filtro%' OR
									ar.clave_corta like '%$filtro%' OR
									ar.descripcion like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "	SELECT *
					FROM $tbl[nutricion_ciclos] cl
					WHERE ar.activo = 1 $filtro
					ORDER BY ar.id_administracion_areas ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}*/
}
