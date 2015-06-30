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
						
					FROM $tbl[nutricion_ciclos] cl
					WHERE cl.activo = 1 AND
						  cl.id_sucursal = $data[buscar]
					ORDER BY cl.id_nutricion_ciclos ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	function get_ciclo_contenido($id_ciclo){
		$tbl = $this->tbl;
		$query = "	SELECT 
						 cl.id_nutricion_ciclos
						,cl.ciclo
						,ncr.id_nutricion_ciclo_receta
						,ncr.id_receta
						,ncr.id_servicio
						,ncr.porciones
						,nr.id_nutricion_receta
						,nr.receta
						,s.servicio
					FROM 
						$tbl[nutricion_ciclo_receta] ncr
					LEFT JOIN $tbl[nutricion_ciclos] cl on cl.id_nutricion_ciclos = ncr.id_ciclo
					LEFT JOIN $tbl[nutricion_recetas] nr on nr.id_nutricion_receta = ncr.id_receta
					LEFT JOIN $tbl[administracion_servicios] s on s.id_administracion_servicio = ncr.id_servicio
					WHERE cl.id_nutricion_ciclos= $id_ciclo AND ncr.activo = 1
					ORDER BY s.servicio";
		//print_debug($query);	
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	
}
