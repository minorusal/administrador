<?php
class regiones_model extends Base_Model
{
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