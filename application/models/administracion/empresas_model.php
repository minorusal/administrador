<?php
class empresas_model extends Base_Model
{
	private $db1;
	private $tbl1;
	
	public function __construct()
	{
		parent::__construct();
		$this->db1 = $this->dbinfo[0]['db'];
		$this->tbl1 = $this->dbinfo[0]['tbl_empresas'];
	}
	//Función que obtiene toda la información de la tabla sys_sucursales
	public function db_get_data($data=array())
	{
		$tbl_empresas   = $this->db1.'.'.$this->tbl1;
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (su.empresa like '%$filtro%' OR
									su.sarzon_social like '%$filtro%' OR
									su.rfc like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "	SELECT 
						*
					FROM $tbl_empresas em
					WHERE em.activo = 1 $filtro
					GROUP BY em.id_empresa ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	/*Actualiza la información en el formuladio de edición de la tabla sys_empresas*/
	public function db_update_data($data=array()){
		$tbl       = $this->db1.'.'.$this->tbl1;
		$condicion = array('id_empresa' => $data['id_empresa']); 
		$existe    = $this->row_exist($tbl, $condicion);
		if(!$existe){
			$insert = $this->insert_item($tbl, $data);
			return $insert;
		}else if($existe){
			$condicion = "id_empresa".$data['id_empresa'];
			$update = $this->update_item($tbl, $data, 'id_empresa', $condicion);
			return $update;
		}
		else
		{
			return false;
		}
	}
}
// 2015-02-03 17:15:57