<?php
class proveedores_model extends Base_Model{
	
	public function db_get_data($data=array()){
		
		$tbl1 = $this->dbinfo[1]['tbl_compras_proveedores'];
		$tbl2 = $this->dbinfo[1]['tbl_administracion_entidades'];

		$buscar = (array_key_exists('buscar',$data))?$data['buscar']:false;
		$filtro = ($buscar) ? "AND ( 	p.razon_social  LIKE '%$buscar%' OR 
										p.nombre_comercial  LIKE '%$buscar%' OR
										p.clave_corta  LIKE '%$buscar%' OR
										p.rfc  LIKE '%$buscar%' OR
										e.entidad LIKE '%$buscar%'
											)" : "";
		
		$limit 			= (array_key_exists('limit',$data)) ?$data['limit']:0;
		$offset 		= (array_key_exists('offset',$data))?$data['offset']:0;
		$aplicar_limit 	= (array_key_exists('aplicar_limit',$data)) ? $data['aplicar_limit'] : false;
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";

		$query = "	SELECT
						 p.id_compras_proveedor
						,p.razon_social
						,p.nombre_comercial
						,p.clave_corta
						,p.rfc
						,p.calle
						,p.num_int
						,p.num_ext
						,p.colonia
						,p.municipio
						,e.entidad
						,p.cp
						,p.telefonos
						,p.email
						,p.contacto
						,p.comentarios
						,p.edit_timestamp
						,p.edit_id_usuario
						,p.timestamp
						,p.id_usuario
						,p.activo
					FROM $tbl1 p 
					LEFT JOIN $tbl2 e on p.id_administracion_entidad = e.id_administracion_entidad
					WHERE p.activo = 1 $filtro
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function db_get_total_rows($data=array()){
		$tbl1 = $this->dbinfo[1]['tbl_compras_proveedores'];
		$tbl2 = $this->dbinfo[1]['tbl_administracion_entidades'];

		$buscar = (array_key_exists('buscar',$data))?$data['buscar']:false;
		$filtro = ($buscar) ? "AND ( 	p.razon_social  LIKE '%$buscar%' OR 
										p.nombre_comercial  LIKE '%$buscar%' OR
										p.clave_corta  LIKE '%$buscar%' OR
										p.rfc  LIKE '%$buscar%' OR
										e.entidad LIKE '%$buscar%'
											)" : "";
		$query = "SELECT
						p.id_compras_proveedor
						,p.razon_social
						,p.nombre_comercial
						,p.clave_corta
						,p.rfc
						,p.calle
						,p.num_int
						,p.num_ext
						,p.colonia
						,p.municipio
						,e.entidad
						,p.cp
						,p.telefonos
						,p.email
						,p.contacto
						,p.comentarios
						,p.edit_timestamp
						,p.edit_id_usuario
						,p.timestamp
						,p.id_usuario
						,p.activo
					FROM $tbl1 p 
					LEFT JOIN $tbl2 e on p.id_administracion_entidad = e.id_administracion_entidad
					WHERE p.activo = 1	$filtro";

      	$query = $this->db->query($query);
		return $query->num_rows;
	}
	public function insert($data=array()){
		// DB Info

		$tbl1 	= $this->dbinfo[1]['tbl_compras_proveedores'];
		// Query
		$existe = $this->row_exist($tbl1,array('clave_corta ='=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string($tbl1, $data);
			$query = $this->db->query($query);

			return $query;
		}else{
			return false;
		}
	}
	public function db_update_data($data=array()){
		// DB Info
		$tbl1 	   = $this->dbinfo[1]['tbl_compras_proveedores'];
		$condicion = array('id_compras_proveedor !=' => $data['id_compras_proveedor'], 'clave_corta'=> $data['clave_corta']); 
		$existe    = $this->row_exist($tbl1, $condicion);
		
		if(!$existe){
			$condicion = "id_compras_proveedor = ".$data['id_compras_proveedor']; 
			$query = $this->db->update_string($tbl1 , $data, $condicion);
			
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}

	}


	public function get_proveedor_unico($id_compras_proveedor){
		// DB Info
		$tbl1 = $this->dbinfo[1]['tbl_compras_proveedores'];
		$tbl2 = $this->dbinfo[1]['tbl_administracion_entidades'];
		// Query
		$query = "SELECT
						p.id_compras_proveedor
						,p.razon_social
						,p.nombre_comercial
						,p.clave_corta
						,p.rfc
						,p.calle
						,p.num_int
						,p.num_ext
						,p.colonia
						,p.municipio
						,e.id_administracion_entidad
						,p.cp
						,p.telefonos
						,p.email
						,p.contacto
						,p.comentarios
						,p.edit_timestamp
						,p.edit_id_usuario
						,p.timestamp
						,p.id_usuario
						,p.activo
					FROM $tbl1 p 
					LEFT JOIN $tbl2 e on p.id_administracion_entidad = e.id_administracion_entidad
					WHERE id_compras_proveedor = $id_compras_proveedor";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
}