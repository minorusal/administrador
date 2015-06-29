<?php
class ciclos_model extends Base_Model{
	public function __construct(){
		parent::__construct();
	}

	public function db_get_data($data = array()){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro 		= ($filtro) ? "AND (cl.ciclo like '%$filtro%' OR')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "	SELECT
						cl.id_nutricion_ciclos
						,cl.ciclo
						,cl.id_sucursal
						,su.sucursal
						,su.clave_corta
						,su.id_sucursal
					FROM $tbl[nutricion_ciclos] cl
					LEFT JOIN $tbl[sucursales] su on su.id_sucursal = cl.id_sucursal
					WHERE cl.activo = 1 AND
						  su.id_sucursal = $data[buscar]
					ORDER BY cl.id_nutricion_ciclos ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function db_get_all_data($data = array()){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro 		= ($filtro) ? "AND (cl.ciclo like '%$filtro%' OR')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "	SELECT
						cl.id_nutricion_ciclos
						,cl.ciclo
						,cl.id_sucursal
						,su.sucursal
						,su.clave_corta
						,su.id_sucursal
					FROM $tbl[nutricion_ciclos] cl
					LEFT JOIN $tbl[sucursales] su on su.id_sucursal = cl.id_sucursal
					WHERE cl.activo = 1 
					ORDER BY cl.id_nutricion_ciclos ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
}
