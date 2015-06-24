<?php
class entidades_model extends Base_Model{
	public function get_entidades_default($data = array()){
		// DB Info		
		$tbl = $this->tbl;
		// Filtro
		$buscar = (array_key_exists('buscar',$data))?$data['buscar']:false;
		$filtro = ($buscar) ? "AND ( 	e.ent_abrev  LIKE '%$buscar%' OR 
										e.id_administracion_entidad  LIKE '%$buscar%' OR
										e.clave_corta  LIKE '%$buscar%')" : "";
		
		$limit 			= (array_key_exists('limit',$data)) ?$data['limit']:0;
		$offset 		= (array_key_exists('offset',$data))?$data['offset']:0;
		$aplicar_limit 	= (array_key_exists('aplicar_limit',$data)) ? $data['aplicar_limit'] : false;
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		
		/*$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		
		$filtro = ($filtro) ? "AND (e.ent_abrev  LIKE '%$filtro%' OR 
										e.id_administracion_entidad  LIKE '%$filtro%' OR
										e.clave_corta  LIKE '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";*/
		// Query
		$query = "	SELECT *
					FROM $tbl[administracion_entidades] e
					WHERE e.activo = 1 $filtro
					ORDER BY e.id_administracion_entidad ASC
					$limit
					";
		//print_debug($query);
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	
	/*Traer informacion para el formulario de edicion de entidades*/
	public function get_orden_unico_entidad($id_entidad){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[administracion_entidades] WHERE id_administracion_entidad = $id_entidad";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	/*Actualiza la información de formulario de edicion de entidades*/
	public function db_update_data($data = array())	{
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$condicion = array('id_administracion_entidad !=' => $data['id_administracion_entidad'], 'clave_corta' => $data['clave_corta']);
		$existe = $this->row_exist($tbl['administracion_entidades'],$condicion);
		if(!$existe){
			$condicion = "id_administracion_entidad = ".$data['id_administracion_entidad']; 
			$update    = $this->update_item($tbl['administracion_entidades'], $data, 'id_administracion_entidad', $condicion);
			return $update;
		}else{
			return false;
		}		
	}

	/*Inserta informacion en la tabla av_administracion_entidades*/
	public function db_insert_data($data = array())	{
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$existe = $this->row_exist($tbl['administracion_entidades'], array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$insert = $this->insert_item($tbl['administracion_entidades'], $data);
			return $insert;
		}else{
			return false;
		}
	}
}