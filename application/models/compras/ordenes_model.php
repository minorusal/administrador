<?php
class ordenes_model extends Base_Model{

	public function insert($data=array()){
		// DB Info
		$db1 	= $this->dbinfo[1]['db'];
		$tbl1 	= $this->dbinfo[1]['tbl_compras_ordenes'];
		// Query
		$existe = $this->row_exist($db1.'.'.$tbl1,array('orden_num ='=> $data['orden_num']));
		if(!$existe){
			$query = $this->db->insert_string($db1.'.'.$tbl1, $data);
			$query = $this->db->query($query);

			return $query;
		}else{
			return false;
		}
	}
	public function db_update_data($data=array()){
		// DB Info
		$db1 	= $this->dbinfo[1]['db'];
		$tbl1 	= $this->dbinfo[1]['tbl_compras_ordenes'];
		// Filtro
		$resultado = false;
		$id_compras_orden   = (isset($data['id_compras_orden']))?$data['id_compras_orden']:false;
		$filtro 			= ($id_compras_orden)?"id_compras_orden='$id_compras_orden'":'';
		if($id_compras_orden){
			$query 		= $this->db->update_string($db1.'.'.$tbl1, $data, $filtro);
			$resultado 	= $this->db->query($query);
		}
		return $resultado;
	}
	public function db_get_data($data=array()){	
		// DB Info
		$db1 	= $this->dbinfo[1]['db'];
		$tbl1 	= $this->dbinfo[1]['vw_compras_orden_proveedores'];
		// Filtro
		$buscar = (isset($data['buscar']))?$data['buscar']:false;
		$filtro = ($buscar) ? "and (orden_num LIKE '$buscar%' 
							   or razon_social LIKE '$buscar%'
							   or descripcion LIKE '$buscar%'
							   )" 
							: "";
		// Limit
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		// Query
		$query = "SELECT 
						 id_compras_orden
						,orden_num
						,razon_social
						,descripcion
					FROM $db1.$tbl1
					WHERE 1 $filtro
					GROUP BY orden_num ASC
					$limit
					";
		// dump_var($query);
      	// Execute querie
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function db_get_total_rows($data=array()){
		// DB Info
		$db1 	= $this->dbinfo[1]['db'];
		$tbl1 	= $this->dbinfo[1]['vw_compras_orden_proveedores'];
		// Filtro
		$buscar = (isset($data['buscar']))?$data['buscar']:false;
		$filtro = ($buscar) ? "and (orden_num LIKE '$buscar%' 
							   or razon_social LIKE '$buscar%'
							   or descripcion LIKE '$buscar%'
							   )" 
							: "";
		// Query
		$query = "	SELECT count(*)
					FROM $db1.$tbl1
					WHERE 1 $filtro
					GROUP BY orden_num ASC
					";
		// dump_var($query);
      	// Execute querie
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function get_orden_unico($id_compras_orden){
		// DB Info
		$db1 	= $this->dbinfo[1]['db'];
		$tbl1 	= $this->dbinfo[1]['vw_compras_orden_proveedores'];
		// Query
		$query = "SELECT * FROM $db1.$tbl1 WHERE id_compras_orden = $id_compras_orden";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	
	public function db_get_proveedores($data=array()){
		// DB Info
		$db1 	= $this->dbinfo[1]['db'];
		$tbl1 	= $this->dbinfo[1]['tbl_compras_proveedores'];
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
						 id_compras_proveedor
						,razon_social
						,nombre_comercial
						,clave_corta
					FROM $db1.$tbl1
					WHERE 1 $filtro
					GROUP BY clave_corta ASC
					$limit
					";
		// dump_var($query);
      	// Execute querie
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
}
?>