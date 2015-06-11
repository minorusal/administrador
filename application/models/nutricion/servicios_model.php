<?php
class servicios_model extends Base_Model{

	//Función que obtiene toda la información de la tabla av_nutricion_servicios
	public function db_get_data($data=array())	{		
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (se.servicio like '%$filtro%' OR
									se.clave_corta like '%$filtro%' OR
									se.descripcion like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "	SELECT *
					FROM $tbl[nutricion_servicios] se
					WHERE se.activo = 1 $filtro
					GROUP BY se.id_nutricion_servicio ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	/*Trae la información para el formulario de edición de la tabla av_nutricion_servicios*/
	public function get_orden_unico_servicio($id_nutricion_servicio){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[nutricion_servicios] WHERE id_nutricion_servicio = $id_nutricion_servicio";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	/*Actualiza la información en el formuladio de edición de la tabla av_nutricion_servicios*/
	public function db_update_data($data=array()){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$condicion = array('id_nutricion_servicio !=' => $data['id_nutricion_servicio'], 'clave_corta = '=> $data['clave_corta']); 
		$existe    = $this->row_exist($tbl['nutricion_servicios'], $condicion);
		if(!$existe){
			$condicion = "id_nutricion_servicio = ".$data['id_nutricion_servicio']; 
			$update    = $this->update_item($tbl['nutricion_servicios'], $data, 'id_nutricion_servicio', $condicion);
			return $update;
		}else{
			return false;
		}
	}

	/*Inserta registro de la tabla av_nutricion_servicios*/
	public function db_insert_data($data = array())	{
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$existe = $this->row_exist($tbl['nutricion_servicios'], array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$insert = $this->insert_item($tbl['nutricion_servicios'], $data);
			return $insert;
		}else{
			return false;
		}
	}
}