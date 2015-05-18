<?php
class sucursales_model extends Base_Model
{
	public function get_sucursales($limit, $offset, $filtro="", $aplicar_limit = true){
		$query = "	SELECT 
						cp.id_sucursal
						,cp.sucursal
					FROM
						00_av_system.sys_sucursales cp
					";
      	
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	

	/*Traer información para el listado de los almacenes*/
	public function db_get_data($data=array())
	{
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (av.almacenes like '%$filtro%' OR 
												  su.clave_corta like '%$filtro%' OR
												  su.direccion like '%$filtro%' OR
												  su.sucursal like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "	SELECT 
						 su.id_sucursal
						,su.clave_corta
						,su.direccion
						,su.sucursal
						,su.razon_social
					FROM 00_av_system.sys_sucursales su
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
	public function get_orden_unico_sucursal($id_sucursal){
		$query = "SELECT * FROM 00_av_system.sys_sucursales WHERE id_sucursal = $id_sucursal";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
}