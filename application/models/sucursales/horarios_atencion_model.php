<?php
class horarios_atencion_model extends Base_Model{
	public function db_get_data($data=array()){
		// DB Info		
		$tbl = $this->tbl;
		// Filtro
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (h.clave_corta like '%$filtro%' OR
									h.horario like '%$filtro%' OR
									h.inicio like '%$filtro%' OR
									h.final like '%$filtro%' OR
									h.descripcion like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "SELECT 
				  		*
				  FROM $tbl[administracion_horario_atencion] h
				  WHERE h.activo = 1 $filtro
				  ORDER BY h.id_administracion_horario_atencion ASC
				  $limit";
		
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	public function get_orden_unico_horario($id_administracion_horario)	{
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[administracion_horario_atencion] WHERE id_administracion_horario_atencion = $id_administracion_horario";
		//print_debug($query);
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	public function db_update_data($data=array()){
		//print_debug($data);
		// DB Info
		$tbl = $this->tbl;
		// Query
		$condicion = array('id_administracion_horario_atencion !=' => $data['id_administracion_horario_atencion'], 'clave_corta = '=> $data['clave_corta']); 
		$existe    = $this->row_exist($tbl['administracion_horario_atencion'], $condicion);
		if(!$existe){
			$condicion = "id_administracion_horario_atencion = ".$data['id_administracion_horario_atencion'];			
			$update = $this->update_item($tbl['administracion_horario_atencion'], $data, 'id_administracion_horario_atencion', $condicion);
			return $update;
		}else{
			return false;
		}
	}
}