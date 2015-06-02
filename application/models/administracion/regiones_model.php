<?php
class regiones_model extends Base_Model
{
	//Función que obtiene toda la información de la tabla av_administracion_regiones
	public function db_get_data($data=array())
	{
		$tbl            = $this->dbinfo[1]['db'].'.'.$this->dbinfo[1]['tbl_administracion_regiones'];
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
					FROM $tbl re
					WHERE re.activo = 1 $filtro
					GROUP BY re.id_administracion_region ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	/*Trae la información para el formulario de edición de la tabla av_administracion_areas*/
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
}