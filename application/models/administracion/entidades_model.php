<?php
class entidades_model extends Base_Model
{
	public function get_entidades_default($data = array()){
		
		$tbl =  $this->dbinfo[1]['db'].'.'. $this->dbinfo[1]['tbl_administracion_entidades'];
		$buscar = (array_key_exists('buscar',$data))?$data['buscar']:false;
		$filtro = ($buscar) ? "AND ( 	e.ent_abrev  LIKE '%$buscar%' OR 
										e.id_administracion_entidad  LIKE '%$buscar%' OR
										e.clave_corta  LIKE '%$buscar%'
									)" : "";
		
		$limit 			= (array_key_exists('limit',$data)) ?$data['limit']:0;
		$offset 		= (array_key_exists('offset',$data))?$data['offset']:0;
		$aplicar_limit 	= (array_key_exists('aplicar_limit',$data)) ? $data['aplicar_limit'] : false;
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";


		$query = "	SELECT 
						*
					FROM
						$tbl e
					WHERE e.activo = 1 $filtro
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	
	/*Traer informacion para el formulario de edicion de entidades*/
	public function get_orden_unico_entidad($id_entidad)
	{
		$tbl = $this->dbinfo[1]['db'].'.'.$this->dbinfo[1]['tbl_administracion_entidades'];
		$query = "SELECT * FROM $tbl WHERE $id_entidad = $id_entidad";
		$query = $this->db->query($query);
		if($query->num_rows >= 1)
		{
			return $query->result_array();
		}
	}

	/*Actualiza la información de formulario de edicion de entidades*/
	public function db_update_data($data = array())
	{
		$tbl = $this->dbinfo[1]['db'].'.'.$this->dbinfo[1]['tbl_administracion_entidades'];
		$condicion = array('id_administracion_entidad !=' => $data['id_administracion_entidad'], 'clave_corta' => $data['clave_corta']);
		$existe = $this->row_exist($tbl,$condicion);
		if(!$existe)
		{
			$query = $this->db->update_string($tbl,$data);
			$query = $this->db->query($query);
			return $query;
		}
		else
		{
			return false;
		}		
	}

	/*Inserta informacion en la tabla av_administracion_entidades*/
	public function db_insert_data($data = array())
	{
		$tbl = $this->dbinfo[1]['db'].'.'.$this->dbinfo[1]['tbl_administracion_entidades'];
		$condicion = array('id_administracion_entidad !=' => $data['id_administracion_entidad'], 'clave_corta' => $data['clave_corta']);
		$existe = $this->row_exist($tbl,$condicion);
		if(!$existe)
		{		
			$query = $this->db->insert_string($tbl,$data);
			$query = $this->db->query($query);
			return $query;
		}
		else
		{
			return false;
		}
	}
}