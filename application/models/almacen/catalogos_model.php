<?php
class catalogos_model extends Base_Model{

	/*ALMACENES*/

	/*Traer información para el listado de los almacenes*/
	public function db_get_data_almacen($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
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
					FROM $tbl[almacen_almacenes] av					
					LEFT JOIN $tbl[sucursales] su on su.id_sucursal = av.id_sucursal
					LEFT JOIN $tbl[almacen_tipos] ti on ti.id_almacen_tipos = av.id_almacen_tipos
					WHERE av.activo = 1 $filtro
					ORDER BY av.id_almacen_almacenes ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	/*Trae la información para el formulario de edición de almacen*/
	public function get_orden_unico_almacen($id_almacen_almacenes){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[almacen_almacenes] WHERE id_almacen_almacenes = $id_almacen_almacenes";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}


	/*Actualliza la información en el formuladio de edición de almacen*/
	public function db_update_data($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$condicion = array('id_almacen_almacenes !=' => $data['id_almacen_almacenes'], 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist($tbl['almacen_almacenes'], $condicion);
		if(!$existe){
			$condicion = "id_almacen_almacenes = ".$data['id_almacen_almacenes']; 
			$update    = $this->update_item($tbl['almacen_almacenes'], $data, 'id_almacen_almacenes', $condicion);
			return $update;
		}else{
			return false;
		}
	}
	/*Inserta registro de almacenes*/
	public function db_insert_data($data = array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$existe = $this->row_exist($tbl['almacen_almacenes'], array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$insert = $this->insert_item($tbl['almacen_almacenes'], $data);
			return $insert;
		}else{
			return false;
		}
	}

	public function db_get_data_tipos($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$limit 	= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query  = "SELECT * FROM $tbl[almacen_tipos] at";
      	$query  = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	/*PASILLOS*/
	public function db_get_data_pasillo($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
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
					FROM $tbl[almacen_pasillos] av
					LEFT JOIN $tbl[almacen_almacenes] al on al.id_almacen_almacenes = av.id_almacen_almacenes
					WHERE av.activo = 1 $filtro
					ORDER BY av.id_almacen_pasillos ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	/*Trae la información para el formulario de edición de pasillo*/
	public function get_orden_unico_pasillo($id_almacen_pasillos){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[almacen_pasillos] WHERE id_almacen_pasillos = $id_almacen_pasillos";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	/*Actualliza la información en el formuladio de edición de pasillos*/
	public function db_update_data_pasillo($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$condicion = array('id_almacen_pasillos !=' => $data['id_almacen_pasillos'], 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist($tbl['almacen_pasillos'], $condicion);
		if(!$existe){
			$condicion = "id_almacen_pasillos = ".$data['id_almacen_pasillos']; 
			$update    = $this->update_item($tbl['almacen_pasillos'], $data, 'id_almacen_pasillos', $condicion);
			return $update;
		}else{
			return false;
		}
	}

	/*Inserta registro de pasillos*/
	public function db_insert_data_pasillos($data = array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$existe = $this->row_exist($tbl['almacen_pasillos'], array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$insert = $this->insert_item($tbl['almacen_pasillos'], $data);
			return $insert;
		}else{
			return false;
		}
	}

	/*GAVETAS*/

	public function db_get_data_gaveta($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (am.almacenes like '%$filtro%' OR
									al.pasillos like '%$filtro%' OR
									av.gavetas like '%$filtro%' OR 
									av.clave_corta like '%$filtro%' OR
									av.descripcion like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "	SELECT 
						 av.id_almacen_gavetas
						,al.pasillos
						,av.id_almacen_pasillos
						,am.almacenes
						,av.id_almacen_almacenes
						,av.clave_corta
						,av.descripcion
						,av.gavetas
					FROM $tbl[almacen_gavetas] av
					LEFT JOIN $tbl[almacen_pasillos] al on al.id_almacen_pasillos = av.id_almacen_pasillos
					LEFT JOIN $tbl[almacen_almacenes] am on am.id_almacen_almacenes = av.id_almacen_almacenes
					WHERE av.activo = 1 $filtro
					ORDER BY av.id_almacen_gavetas ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	/*Trae la información para el formulario de edición de gaveta*/
	public function get_orden_unico_gaveta($id_almacen_gavetas){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[almacen_gavetas] WHERE id_almacen_gavetas = $id_almacen_gavetas";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	/*Actualliza la información en el formuladio de edición de gavetas*/
	public function db_update_data_gaveta($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$condicion = array('id_almacen_gavetas !=' => $data['id_almacen_gavetas'], 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist($tbl['almacen_gavetas'], $condicion);
		if(!$existe){
			$condicion = "id_almacen_gavetas = ".$data['id_almacen_gavetas']; 
			$update    = $this->update_item($tbl['almacen_gavetas'], $data, 'id_almacen_gavetas', $condicion);
			return $update;
		}else{
			return false;
		}
	}

	/*Inserta registro de gavetas*/
	public function db_insert_data_gavetas($data = array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$existe = $this->row_exist($tbl['almacen_gavetas'], array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$insert = $this->insert_item($tbl['almacen_gavetas'], $data);
			return $insert;
		}else{
			return false;
		}
	}


	/*TRANSPORTES*/

	public function db_get_data_transporte($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (tr.conductor like '%$filtro%' OR
									tr.modelo like '%$filtro%' OR
									tr.num_lic like '%$filtro%' OR 
									tr.marca like '%$filtro%' OR  
									tr.placas like '%$filtro%' OR
									tr.clave_corta like '%$filtro%' OR
									tr.descripcion like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "	SELECT *
					FROM $tbl[almacen_transportes] tr
					WHERE tr.activo = 1 $filtro
					ORDER BY tr.id_almacen_transportes ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	/*Trae la información para el formulario de edición de trensporte*/
	public function get_orden_unico_transporte($id_almacen_transportes){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[almacen_transportes] WHERE id_almacen_transportes = $id_almacen_transportes";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	/*Actualliza la información en el formuladio de edición de transportes*/
	public function db_update_data_transporte($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$condicion = array('id_almacen_transportes !=' => $data['id_almacen_transportes'], 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist($tbl['almacen_transportes'], $condicion);
		if(!$existe){
			$condicion = "id_almacen_transportes = ".$data['id_almacen_transportes']; 
			$update    = $this->update_item($tbl['almacen_transportes'], $data, 'id_almacen_transportes', $condicion);
			return $update;
		}else{
			return false;
		}
	}

	/*Inserta registro de transportes*/
	public function db_insert_data_transportes($data = array())	{
		// DB Info
		$tbl = $this->tbl;
		// Query
		$existe = $this->row_exist($tbl['almacen_transportes'], array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$insert = $this->insert_item($tbl['almacen_transportes'], $data);
			return $insert;
		}else{
			return false;
		}
	}
}