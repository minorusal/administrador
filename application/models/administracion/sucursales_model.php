<?php
class sucursales_model extends Base_Model
{
	private $db1;
	private $tbl1;
	
	public function __construct()
	{
		parent::__construct();
		$this->db1  = $this->dbinfo[0]['db'];
		$this->tbl1 = $this->dbinfo[0]['tbl_sucursales'];
	}
	//Función que obtiene toda la información de la tabla sys_sucursales
	public function db_get_data($data=array())
	{
		$tbl_sucursales = $this->db1.'.'.$this->tbl1;
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (su.clave_corta like '%$filtro%' OR
									su.direccion like '%$filtro%' OR
									su.sucursal like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "	SELECT 
						 su.id_sucursal
						,su.clave_corta
						,su.rfc
						,su.direccion
						,su.sucursal
						,su.razon_social
					FROM $tbl_sucursales su
					WHERE su.activo = 1 $filtro
					GROUP BY su.id_sucursal ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	/*Trae la información para el formulario de edición de sucursales*/
	public function get_orden_unico_sucursal($id_sucursal)
	{
		$tbl_sucursales = $this->db1.'.'.$this->tbl1;
		$query = "SELECT * FROM $tbl_sucursales WHERE id_sucursal = $id_sucursal";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	/*Actualliza la información en el formuladio de edición de sucursales*/
	public function db_update_data($data=array())
	{
		$tbl_sucursales = $this->db1.'.'.$this->tbl1;
		$condicion = array('id_sucursal !=' => $data['id_sucursal'], 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist($tbl_sucursales, $condicion);
		if(!$existe){
			$condicion = "id_sucursal = ".$data['id_sucursal']; 
			$update    = $this->update_item($tbl_sucursales, $data, 'id_sucursal', $condicion);
			return $update;
		}else{
			return false;
		}
	}

	/*Inserta registro de sucursales*/
	public function db_insert_data($data = array())
	{
		$tbl_sucursales = $this->db1.'.'.$this->tbl1;
		$existe = $this->row_exist($tbl_sucursales, array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$insert = $this->insert_item($tbl_sucursales, $data);
			return $insert;
		}else{
			return false;
		}
	}
}