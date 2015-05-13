<?php
class catalogos_model extends Base_Model
{
	/*private $db_b;
	public function __construct()
	{
		$this->db_b = $this->load->database('mx',TRUE);
	}*/
	/*ALMACENES*/
		
	/*Traer información para el listado de los almacenes*/
	public function db_get_data($data=array())
	{
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (av.almacenes like '%$filtro%' OR 
												  av.clave_corta like '%$filtro%' OR
												  av.descripcion like '%$filtro%' OR
												  su.sucursal like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "	SELECT 
						 av.id_almacen_almacenes
						,av.clave_corta
						,av.descripcion
						,av.almacenes
						,av.id_sucursal
						,su.sucursal
					FROM 00_av_mx.av_almacen_almacenes av
					LEFT JOIN 00_av_system.sys_sucursales su on su.id_sucursal = av.id_sucursal
					WHERE av.activo = 1 $filtro
					GROUP BY av.id_almacen_almacenes ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	/*Trae la información para el formulario de edición de almacen*/
	public function get_orden_unico($id_almacen_almacenes){
		$query = "SELECT * FROM av_almacen_almacenes WHERE id_almacen_almacenes = $id_almacen_almacenes";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}


	/*Actualliza la información en el formuladio de edición de almacen*/
	public function db_update_data($data=array())
	{
		//print_debug($data);
		$condicion = array('id_almacen_almacenes !=' => $data['id_almacen_almacenes'], 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_almacen_almacenes', $condicion);
		if(!$existe){
			$condicion = "id_almacen_almacenes = ".$data['id_almacen_almacenes']; 
			$query = $this->db->update_string('av_almacen_almacenes', $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}
	/*Inserta registro de almacenes*/
	public function db_insert_data($data = array())
	{
		$existe = $this->row_exist('av_almacen_almacenes', array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string('av_almacen_almacenes', $data);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}

	public function get_sucursales($limit, $offset, $filtro="", $aplicar_limit = true){
		$filtro = ($filtro=="") ? "" : "(
												cp.presentacion like '%$filtro%'
											OR 
												cp.clave_corta like '%$filtro%'
											OR
												cp.descripcion like '%$filtro%'
										) ";
		$limit = ($aplicar_limit) ?  "LIMIT $offset ,$limit " : "";
		$query = "	SELECT 
						cp.id_sucursal
						,cp.sucursal
					FROM
						sys_sucursales cp
					";
      	
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	/*PASILLOS*/

	/*public function db_get_data_pasillos($data=array());
	{

	}*/
}

//WHERE cp.activo = 1 $filtro