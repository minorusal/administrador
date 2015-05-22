<?php
class creditos_model extends Base_Model
{
	private $db1;
	private $tbl1;
	
	public function __construct()
	{
		parent::__construct();
		$this->db1  = $this->dbinfo[1]['db'];
		$this->tbl1 = $this->dbinfo[1]['tbl_administracion_creditos'];
	}

	//Función que obtiene toda la información de la tabla av_administracion_creditos
	public function db_get_data($data=array())
	{
		$tbl            = $this->db1.'.'.$this->tbl1;
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (cr.valor_credito like '%$filtro%' OR
									cr.clave_corta like '%$filtro%' OR
									cr.descripcion like '%$filtro%' OR
									cr.credito like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "	SELECT 
						 *
					FROM $tbl cr
					WHERE cr.activo = 1 $filtro
					GROUP BY cr.id_administracion_creditos ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	/*Trae la información para el formulario de edición de la tabla av_administracion_creditos*/
	public function get_orden_unico_credito($id_administracion_creditos)
	{
		$tbl   = $this->db1.'.'.$this->tbl1;
		$query = "SELECT * FROM $tbl WHERE id_administracion_creditos = $id_administracion_creditos";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	/*Actualiza la información en el formuladio de edición de la tabla av_administracion_creditos*/
	public function db_update_data($data=array())
	{
		$tbl       = $this->db1.'.'.$this->tbl1;
		$condicion = array('id_administracion_creditos !=' => $data['id_administracion_creditos'], 'clave_corta = '=> $data['clave_corta']); 
		$existe    = $this->row_exist($tbl, $condicion);
		if(!$existe)
		{
			$condicion = "id_administracion_creditos = ".$data['id_administracion_creditos']; 
			$query = $this->db->update_string($tbl, $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}

	/*Inserta registro de la tabla ac_administracion_creditos*/
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