<?php
class regiones_model extends Base_Model{
	//Función que obtiene toda la información de la tabla av_administracion_regiones
	public function db_get_data($data=array()){
		// DB Info		
		$tbl = $this->tbl;
		// Filtro
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (re.region like '%$filtro%' OR
									re.clave_corta like '%$filtro%' OR
									re.descripcion like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "	SELECT *
					FROM   $tbl[administracion_regiones] re					
					WHERE re.activo = 1 $filtro
					GROUP BY re.id_administracion_region ASC
					$limit
					";
					//print_debug($query);
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			//print_debug($query->result_array());
			return $query->result_array();
		}	
	}

	/*Trae las entidades y regiones relacionadas */
	public function get_entidades_regiones($id_administracion_region){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$query = "	SELECT 
						 en.entidad,
						 en.id_administracion_entidad,
						 en.clave_corta
					FROM   $tbl[administracion_entidad_region] er
						 , $tbl[administracion_entidades] en
						 , $tbl[administracion_regiones] re
					WHERE re.activo = 1 
					AND   er.activo = 1
					AND   re.id_administracion_region = er.id_region
					AND   er.id_entidad = en.id_administracion_entidad
					AND   re.id_administracion_region =". $id_administracion_region;
					
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	/*Trae la información para el formulario de edición de la tabla av_administracion_region*/
	public function get_orden_unico_region($id_administracion_region){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[administracion_regiones] WHERE id_administracion_region = $id_administracion_region";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	/*Inserta informacion en la tabla av_administracion_regiones*/
	public function db_insert_data($data = array())	{
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$existe = $this->row_exist($tbl['administracion_regiones'], array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string($tbl['administracion_regiones'], $data);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}

	/*Inserta informacion en la tabla av_administracion_entidad_region*/
	public function db_insert_entidades($data = array()){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$query = $this->db->insert_string($tbl['administracion_regiones'], $data);
		$query = $this->db->query($query);
		if($query)
			return $query;
		else
			return false;
	}

	/*Actualiza la información en el formuladio de edición de la tabla av_administracion_regiones*/
	public function db_update_data($data=array()){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$condicion = array('id_administracion_region !=' => $data['id_administracion_region'], 'clave_corta = '=> $data['clave_corta']); 
		$existe    = $this->row_exist($tbl['administracion_regiones'], $condicion);
		if(!$existe){
			$condicion = "id_administracion_region = ".$data['id_administracion_region'];			
			$update = $this->update_item($tbl['administracion_regiones'], $data, 'id_administracion_region', $condicion);
			return $update;
		}else{
			return false;
		}
	}

	public function db_update_entidades($data=array()){	
		// DB Info		
		$tbl = $this->tbl;
		// Query
		foreach ($data['id_entidad'] as $key => $value) {
			$new_data[] = array(
								'id_entidad' => $value,
							    'id_region'  => $data['id_region']	
								);
		}
		$condicion = array("id_region" => $data['id_region']);
		$this->db->where($condicion);
		$query = $this->db->delete($tbl['administracion_regiones']);
		$query = $this->db->insert_batch($tbl['administracion_regiones'], $new_data);
		return $query;	
	}

}