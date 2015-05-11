<?php
class catalogos_model extends Base_Model
{
	/*ALMACENES*/
		
	/*Traer información para el listado de los almacenes*/
	public function db_get_data($data=array())
	{
		// Filtro
		$buscar = (isset($data['buscar']))?$data['buscar']:false;
		$filtro = ($buscar) ? "" : "";

		// Limit
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";

		// Query
		$query = "	SELECT 
						 id_almacen_almacenes
						,clave_corta
						,descripcion
						,almacenes
					FROM av_almacen_almacenes
					WHERE 1 $filtro
					GROUP BY id_almacen_almacenes ASC
					$limit
					";
		
      	// Execute querie
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	/*Traer el número total de registros*/
	public function db_get_total_rows($data=array())
	{
		// Filtro
		$buscar = (isset($data['buscar']))?$data['buscar']:false;
		$filtro = ($buscar) ? "" : "";
		// Query
		$query = "	SELECT count(*)
					FROM av_almacen_almacenes
					WHERE 1 $filtro
					GROUP BY id_almacen_almacenes ASC
					";
      	// Execute querie
      	$query = $this->db->query($query);
		if($query->num_rows >= 1)
			return $query->result_array();
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
}

//WHERE cp.activo = 1 $filtro