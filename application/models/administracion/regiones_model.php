<?php
class regiones_model extends Base_Model
{
	//Función que obtiene toda la información de la tabla av_administracion_regiones
	public function db_get_data($data=array())
	{
		$tbl            = $this->dbinfo[1]['db'].'.'.$this->dbinfo[1]['tbl_administracion_regiones'];
		$tb2            = $this->dbinfo[1]['db'].'.'.$this->dbinfo[1]['tbl_administracion_entidades'];
		$tb3            = $this->dbinfo[1]['db'].'.'.$this->dbinfo[1]['tbl_administracion_entidad_region'];
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (re.region like '%$filtro%' OR
									re.clave_corta like '%$filtro%' OR
									re.descripcion like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "	SELECT 
						 *
					FROM   $tbl re
					
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
	public function get_entidades_regiones($id_administracion_region)
	{
		$tbl            = $this->dbinfo[1]['db'].'.'.$this->dbinfo[1]['tbl_administracion_regiones'];
		$tb2            = $this->dbinfo[1]['db'].'.'.$this->dbinfo[1]['tbl_administracion_entidades'];
		$tb3            = $this->dbinfo[1]['db'].'.'.$this->dbinfo[1]['tbl_administracion_entidad_region'];
		$query = "	SELECT 
						 en.entidad,
						 en.id_administracion_entidad,
						 en.clave_corta
					FROM   $tb3 er, $tb2 en, $tbl re
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
	public function get_orden_unico_region($id_administracion_region)
	{
		$tbl   = $this->dbinfo[1]['db'].'.'.$this->dbinfo[1]['tbl_administracion_regiones'];
		$query = "SELECT * FROM $tbl WHERE id_administracion_region = $id_administracion_region";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	/*Inserta informacion en la tabla av_administracion_regiones*/
	public function db_insert_data($data = array())
	{
		$tbl = $this->dbinfo[1]['db'].'.'.$this->dbinfo[1]['tbl_administracion_regiones'];
		$existe = $this->row_exist($tbl, array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string($tbl, $data);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}

	/*Inserta informacion en la tabla av_administracion_entidad_region*/
	public function db_insert_entidades($data = array())
	{
		$tbl = $this->dbinfo[1]['db'].'.'.$this->dbinfo[1]['tbl_administracion_entidad_region'];
		$query = $this->db->insert_string($tbl, $data);
		$query = $this->db->query($query);
		if($query)
			return $query;
		else
			return false;
	}

	/*Actualiza la información en el formuladio de edición de la tabla av_administracion_regiones*/
	public function db_update_data($data=array())
	{
		$tbl = $this->dbinfo[1]['db'].'.'.$this->dbinfo[1]['tbl_administracion_regiones'];
		$condicion = array('id_administracion_region !=' => $data['id_administracion_region'], 'clave_corta = '=> $data['clave_corta']); 
		$existe    = $this->row_exist($tbl, $condicion);
		if(!$existe)
		{
			$condicion = "id_administracion_region = ".$data['id_administracion_region'];
			
			$update = $this->update_item($tbl, $data, 'id_administracion_region', $condicion);
			return $update;
		}else{
			return false;
		}
	}

	public function db_update_entidades($data=array())
	{	
		$tbl = $this->dbinfo[1]['db'].'.'.$this->dbinfo[1]['tbl_administracion_entidad_region'];
		$condicion = array("id_entidad =" => $data['id_entidad'], "id_region = " => $data['id_region']);
		$existe = $this->row_exist($tbl,$condicion);
		if(!$existe)
		{
			$query = $this->db->insert_string($tbl, $data);
			$query = $this->db->query($query);
			return $query;	
		}
		else
		{
			$condicion = array("id_region = " => $data['id_region']);
			$this->db->where($condicion);
			$query = $this->db->delete($tbl);
		}
		
		return false;
	}

}
/*
$this->db->where($condicion);
				$query = $this->db->delete($tbl);*/