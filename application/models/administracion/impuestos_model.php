<?php
class impuestos_model extends Base_Model
{
	private $db1;
	private $tbl1;
	
	public function __construct()
	{
		parent::__construct();
		$this->db1  = $this->dbinfo[1]['db'];
		$this->tbl1 = $this->dbinfo[1]['tbl_administracion_impuestos'];
	}
	//Función que obtiene toda la información de la tabla sys_impuestos
	public function db_get_data($data=array())
	{
		$tbl            = $this->db1.'.'.$this->tbl1;
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (im.valor like '%$filtro%' OR
									im.clave_corta like '%$filtro%' OR
									im.descripcion like '%$filtro%' OR
									im.impuesto like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "	SELECT 
						 *
					FROM $tbl im
					WHERE im.activo = 1 $filtro
					GROUP BY im.id_administracion_impuestos ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	/*Trae la información para el formulario de edición de la tabla sys_impuestos*/
	public function get_orden_unico_sucursal($id_administracion_impuestos)
	{
		$tbl   = $this->db1.'.'.$this->tbl1;
		$query = "SELECT * FROM $tbl WHERE id_administracion_impuestos = $id_administracion_impuestos";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	/*Actualiza la información en el formuladio de edición de la tabla sys_impuestos*/
	public function db_update_data($data=array())
	{
		$tbl       = $this->db1.'.'.$this->tbl1;
		$condicion = array('id_administracion_impuestos !=' => $data['id_administracion_impuestos'], 'clave_corta = '=> $data['clave_corta']); 
		$existe    = $this->row_exist($tbl, $condicion);
		if(!$existe)
		{
			$condicion = "id_administracion_impuestos = ".$data['id_administracion_impuestos']; 
			$update    = $this->update_item($tbl, $data, 'id_administracion_impuestos', $condicion);
			return $update;
		}else{
			return false;
		}
	}

	/*Inserta registro de la tabla sys_impuestos*/
	public function db_insert_data($data = array())
	{
		$tbl    = $this->db1.'.'.$this->tbl1;
		$existe = $this->row_exist($tbl, array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$insert = $this->insert_item($tbl, $data);
			return $insert;
		}else{
			return false;
		}
	}
}