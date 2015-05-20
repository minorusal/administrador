<?php
class impuestos_model extends Base_Model
{
	private $db1;
	private $tbl1;
	
	public function __construct()
	{
		parent::__construct();
		$this->db1 = $this->dbinfo[0]['db'];
		$this->tbl1 = $this->dbinfo[0]['tbl_impuestos'];
	}
	//Función que obtiene toda la información de la tabla sys_impuestos
	public function db_get_data($data=array())
	{
		$tbl            = $this->db1.'.'.$this->tbl1;
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (im.clave_corta like '%$filtro%' OR
									im.descripcion like '%$filtro%' OR
									im.impuesto like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "	SELECT 
						 *
					FROM $tbl im
					WHERE im.activo = 1 $filtro
					GROUP BY im.id_impuesto ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
}