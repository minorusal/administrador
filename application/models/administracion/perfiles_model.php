<?php
class perfiles_model extends Base_Model
{	
	//Función que obtiene toda la información de la tabla sys_perfiles
	public function db_get_data($data=array())
	{
		$tbl = $this->dbinfo[0]['db'].'.'.$this->dbinfo[0]['tbl_perfiles'];
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (pr.perfil like '%$filtro%' OR
									pr.clave_corta like '%$filtro%' OR
									pr.descripcion like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "	SELECT 
						 *
					FROM $tbl pr
					WHERE pr.activo = 1 $filtro
					AND pr.perfil NOT LIKE 'ROOT'
					GROUP BY pr.id_perfil ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	/*Trae la información para el formulario de edición de la tabla sys_perfiles*/
	public function get_orden_unico_perfil($id_perfil)
	{
		$tbl = $this->dbinfo[0]['db'].'.'.$this->dbinfo[0]['tbl_perfiles'];
		$query = "SELECT * FROM $tbl WHERE id_perfil = $id_perfil";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	/*Actualiza la información en el formuladio de edición de la tabla sys_perfiles*/
	public function db_update_data($data=array())
	{
		$tbl = $this->dbinfo[0]['db'].'.'.$this->dbinfo[0]['tbl_perfiles'];
		$condicion = array('id_perfil !=' => $data['id_perfil'], 'clave_corta = '=> $data['clave_corta']); 
		$existe    = $this->row_exist($tbl, $condicion);
		if(!$existe)
		{
			$condicion = "id_perfil = ".$data['id_perfil']; 
			$update    = $this->update_item($tbl, $data, 'id_perfil', $condicion);
			return $update;
		}else{
			return false;
		}
	}

	/*Inserta informacion en la tabla sys_perfiles*/
	public function db_insert_data($data = array())
	{
		$tbl = $this->dbinfo[0]['db'].'.'.$this->dbinfo[0]['tbl_perfiles'];
		$existe = $this->row_exist($tbl, array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$insert = $this->insert_item($tbl, $data);
			return $insert;
		}else{
			return false;
		}
	}
}
