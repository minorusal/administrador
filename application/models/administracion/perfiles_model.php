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
									pr.descripcion like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "	SELECT 
						 *
					FROM $tbl pr
					WHERE pr.activo = 1 $filtro
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
		$tbl       = $this->db1.'.'.$this->tbl1;
		$condicion = array('id_perfil !=' => $data['id_perfil'], 'perfil = '=> $data['perfil']); 
		$existe    = $this->row_exist($tbl, $condicion);
		if(!$existe)
		{
			$condicion = "id_perfil = ".$data['id_perfil']; 
			$query = $this->db->update_string($tbl, $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}

	/*Inserta informacion en la tabla sys_perfiles*/
	public function db_insert_data($data = array())
	{
		$tbl = $this->dbinfo[0]['db'].'.'.$this->dbinfo[0]['tbl_perfiles'];
		$existe = $this->row_exist($tbl, array('perfil'=> $data['perfil']));
		if(!$existe){
			$query = $this->db->insert_string($tbl, $data);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}
}
