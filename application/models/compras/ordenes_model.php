<?php
class ordenes_model extends Base_Model{

	public function insert($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$existe = $this->row_exist($tbl['compras_ordenes'],array('orden_num ='=> $data['orden_num']));
		if(!$existe){
			$insert = $this->insert_item($tbl['compras_ordenes'], $data);
			return $insert;
		}else{
			return false;
		}
	}
	public function db_update_data($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$id_compras_orden   = (isset($data['id_compras_orden']))?$data['id_compras_orden']:false;
		$filtro 			= ($id_compras_orden)?"id_compras_orden='$id_compras_orden'":'';
		$update    			= $this->update_item($tbl['compras_ordenes'], $data, 'id_compras_orden', $filtro);
		return $update;
	}
	public function db_get_data($data=array()){	
		// DB Info
		$tbl = $this->tbl;
		// Filtro
		$filtro = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;

		$filtro = ($filtro!="") ? "and (a.orden_num LIKE '$filtro%' 
							   or b.razon_social LIKE '$filtro%'
							   or a.descripcion LIKE '$filtro%'
							   )" 
							: "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		// Query
		$query="SELECT 
					a.id_compras_orden 
					,a.orden_num
					,a.orden_fecha
					,a.descripcion
					,a.entrega_direccion
					,a.entrega_fecha
					,a.observaciones
					,a.prefactura_num
					,a.timestamp
					,b.razon_social
					,c.estatus
					,d.orden_tipo
					,e.sucursal
					,f.forma_pago
					,g.credito
				from $tbl[compras_ordenes] a 
				LEFT JOIN $tbl[compras_proveedores] b on a.id_proveedor=b.id_compras_proveedor
				LEFT JOIN $tbl[compras_ordenes_estatus] c on a.estatus=c.id_estatus
				LEFT JOIN $tbl[compras_ordenes_tipo] d on a.id_orden_tipo=d.id_orden_tipo
				LEFT JOIN $tbl[sucursales] e on a.id_sucursal=e.id_sucursal
				LEFT JOIN $tbl[administracion_forma_pago] f on a.id_forma_pago=f.id_forma_pago
				LEFT JOIN $tbl[administracion_creditos] g on a.id_credito=g.id_administracion_creditos
				WHERE a.activo=1 AND 1  $filtro
				GROUP BY orden_num ASC
				$limit";
      	// Execute querie

      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function get_orden_unico($id_compras_orden){
		// DB Info
		$tbl = $this->tbl;
		// Query
		//$query = "SELECT * FROM $tbl[compras_ordenes] WHERE id_compras_orden = $id_compras_orden";
		$query="SELECT *
				from $tbl[compras_ordenes] a 
				WHERE a.activo=1 AND id_compras_orden = $id_compras_orden;";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function db_get_proveedores($data=array()){
		// DB Info
		$tbl = $this->tbl;
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
					FROM $tbl[compras_proveedores]
					WHERE 1 AND activo=1 $filtro
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
	public function db_get_total_rows($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Filtro
		$buscar = (isset($data['buscar']))?$data['buscar']:false;
		$filtro = ($buscar) ? "and (orden_num LIKE '$buscar%' 
							   or razon_social LIKE '$buscar%'
							   or descripcion LIKE '$buscar%'
							   )" 
							: "";
		// Query
		$query = "	SELECT count(*)
					FROM $tbl[vw_compras_orden_proveedores]
					WHERE 1 AND activo=1 $filtro
					GROUP BY orden_num ASC
					";
		 dump_var($query);
      	// Execute querie
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function db_get_tipo_orden(){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[compras_ordenes_tipo] WHERE activo= 1";
      	// Execute querie
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
}
?>