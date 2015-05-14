<?php
class catalogos_model extends Base_Model
{
	/*ALMACENES*/
		
	/*Traer información para el listado de los almacenes*/
	public function db_get_data_almacen($data=array())
	{
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (av.almacenes like '%$filtro%' OR 
												  av.clave_corta like '%$filtro%' OR
												  av.descripcion like '%$filtro%' OR
												  su.sucursal like '%$filtro%' OR
												  ti.tipos like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "	SELECT 
						 av.id_almacen_almacenes
						,av.clave_corta
						,av.descripcion
						,av.almacenes
						,av.id_sucursal
						,su.sucursal
						,ti.tipos
					FROM 00_av_mx.av_almacen_almacenes av
					LEFT JOIN 00_av_system.sys_sucursales su on su.id_sucursal = av.id_sucursal
					LEFT JOIN 00_av_mx.av_almacen_tipos ti on ti.id_almacen_tipos = av.id_almacen_tipos
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
	public function get_orden_unico_almacen($id_almacen_almacenes){
		$query = "SELECT * FROM 00_av_mx.av_almacen_almacenes WHERE id_almacen_almacenes = $id_almacen_almacenes";
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

	/*Obtiene las sucursales relacionadas con almacenes*/
	public function get_sucursales($limit, $offset, $filtro="", $aplicar_limit = true){
		$limit = ($aplicar_limit) ?  "LIMIT $offset ,$limit " : "";
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


	/*Obtiene los tipos de almacenes*/
	public function db_get_data_tipos($limit, $offset, $filtro="", $aplicar_limit = true){
		$limit = ($aplicar_limit) ?  "LIMIT $offset ,$limit " : "";
		$query = "	SELECT 
						at.id_almacen_tipos
						,at.tipos
					FROM
						av_almacen_tipos at
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}


	/*PASILLOS*/
	public function db_get_data_pasillo($data=array())
	{
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (av.pasillos like '%$filtro%' OR
									av.descripcion like '%$filtro%' OR  
									av.clave_corta like '%$filtro%' OR
									al.almacenes like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "	SELECT 
						 av.id_almacen_pasillos
						,av.clave_corta
						,av.descripcion
						,av.pasillos
						,al.id_almacen_almacenes
						,al.almacenes
					FROM av_almacen_pasillos av
					LEFT JOIN av_almacen_almacenes al on al.id_almacen_almacenes = av.id_almacen_almacenes
					WHERE av.activo = 1 $filtro
					GROUP BY av.id_almacen_pasillos ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	/*Trae la información para el formulario de edición de pasillo*/
	public function get_orden_unico_pasillo($id_almacen_pasillos){
		$query = "SELECT * FROM av_almacen_pasillos WHERE id_almacen_pasillos = $id_almacen_pasillos";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	/*Actualliza la información en el formuladio de edición de pasillos*/
	public function db_update_data_pasillo($data=array())
	{
		$condicion = array('id_almacen_pasillos !=' => $data['id_almacen_pasillos'], 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_almacen_pasillos', $condicion);
		if(!$existe){
			$condicion = "id_almacen_pasillos = ".$data['id_almacen_pasillos']; 
			$query = $this->db->update_string('av_almacen_pasillos', $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}



	/*GAVETAS*/

	/*public function db_get_data_gaveta($data=array())
	{
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (av.gavetas like '%$filtro%' OR 
									av.clave_corta like '%$filtro%' OR
									av.descripcion like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "	SELECT 
						 av.id_almacen_gavetas
						,av.clave_corta
						,av.descripcion
						,av.gavetas
					FROM av_almacen_gavetas av
					WHERE av.activo = 1 $filtro
					GROUP BY av.id_almacen_gavetas ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}*/
}

//WHERE cp.activo = 1 $filtro