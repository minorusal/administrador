<?php
class areas_model extends Base_Model
{
	private $db1;
	private $tbl1;
	
	public function __construct()
	{
		parent::__construct();
		$this->db1  = $this->dbinfo[1]['db'];
		$this->tbl1 = $this->dbinfo[1]['tbl_administracion_areas'];
	}

	//Función que obtiene toda la información de la tabla av_administracion_areas
	public function db_get_data($data=array())
	{
		$tbl            = $this->db1.'.'.$this->tbl1;
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (ar.area like '%$filtro%' OR
									ar.clave_corta like '%$filtro%' OR
									ar.descripcion like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "	SELECT 
						 *
					FROM $tbl ar
					WHERE ar.activo = 1 $filtro
					GROUP BY ar.id_administracion_areas ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	/*Trae la información para el formulario de edición de la tabla av_administracion_areas*/
	public function get_orden_unico_puesto($id_administracion_areas)
	{
		$tbl   = $this->db1.'.'.$this->tbl1;
		$query = "SELECT * FROM $tbl WHERE id_administracion_areas = $id_administracion_areas";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	/*Actualiza la información en el formuladio de edición de la tabla av_administracion_areas*/
	public function db_update_data($data=array())
	{
		$tbl       = $this->db1.'.'.$this->tbl1;
		$condicion = array('id_administracion_areas !=' => $data['id_administracion_areas'], 'clave_corta = '=> $data['clave_corta']); 
		$existe    = $this->row_exist($tbl, $condicion);
		if(!$existe)
		{
			$condicion = "id_administracion_areas = ".$data['id_administracion_areas']; 
			$query = $this->db->update_string($tbl, $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}

	/*Inserta registro de la tabla ac_administracion_areas*/
	public function db_insert_data($data = array())
	{
		$tbl    = $this->db1.'.'.$this->tbl1;
		$existe = $this->row_exist($tbl, array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string($tbl, $data);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}
}