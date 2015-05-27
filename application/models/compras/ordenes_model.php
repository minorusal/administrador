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
			//$db1 	= $this->dbinfo[1]['db'];
			//$tbl1 	= $this->dbinfo[1]['vw_compras_orden_proveedores'];
		//$db1 	= $this->dbinfo[1]['db'];
		$tbl1 	= $this->dbinfo[1]['tbl_compras_ordenes'];
		$tbl2 	= $this->dbinfo[1]['tbl_compras_proveedores'];
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
		$query="SELECT 
					a.id_compras_orden AS id_compras_orden
					,a.orden_num
					,a.descripcion AS descripcion
					,h.razon_social
				from $tbl1 a 
				LEFT JOIN $tbl2 h on a.id_proveedor=h.id_compras_proveedor
				WHERE 1 $filtro
				GROUP BY orden_num ASC
				$limit";
		 //dump_var($query);
      	// Execute querie
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function get_orden_unico($id_compras_orden){
		// DB Info
		//$tbl1 	= $this->dbinfo[1]['vw_compras_orden_proveedores'];
		$db1 	= $this->dbinfo[0]['db'];
		$tbl1 	= $this->dbinfo[1]['tbl_compras_ordenes'];
		$tbl2 	= $this->dbinfo[1]['tbl_compras_ordenes_articulos'];
		$tbl3 	= $this->dbinfo[1]['tbl_compras_articulos_precios'];
		$tbl4 	= $this->dbinfo[1]['tbl_compras_articulos'];
		$tbl5 	= $this->dbinfo[1]['tbl_compras_lineas'];
		$tbl6 	= $this->dbinfo[1]['tbl_compras_marcas'];
		$tbl7 	= $this->dbinfo[1]['tbl_compras_presentaciones'];
		$tbl8 	= $this->dbinfo[1]['tbl_compras_um'];
		$tbl9 	= $this->dbinfo[1]['tbl_compras_proveedores'];
		$tbl10 	= $this->dbinfo[0]['tbl_usuarios'];
		$tbl11 	= $this->dbinfo[0]['tbl_personales'];
		$tbl12 	= $this->dbinfo[0]['tbl_sucursales'];
		// Query
		//$query = "SELECT * FROM $db1.$tbl1 WHERE id_compras_orden = $id_compras_orden";
		$query="SELECT 
					a.id_compras_orden AS id_compras_orden
					,a.orden_num
					,a.id_orden_tipo AS orden_tipo
					,a.descripcion AS descripcion
					,a.id_sucursal
					,a.orden_fecha
					,a.entrega_direccion
					,a.entrega_fecha
					,a.observaciones
					,a.id_forma_pago
					,a.id_credito
					,a.prefactura_num
					,a.observaciones
					,k.sucursal
					,a.id_proveedor AS id_proveedor
					,h.razon_social
					,b.id_compras_articulo_precios AS id_compras_articulo_precios
					,art.articulo AS articulo
					,art.clave_corta AS clave_corta
					,art.id_compras_linea
					,d.linea
					,art.id_compras_marca
					,e.marca
					,art.id_compras_presentacion
					,f.presentacion
					,art.id_compras_um
					,g.um
					,b.cantidad AS cantidad
					,b.costo_unitario AS costo_unitario
					,(b.costo_unitario * b.cantidad) AS costo_subtotal
					,b.descuento_unitario_porcentaje AS descuento_unitario_porcentaje
					,b.descuento_unitario_monto AS descuento_unitario_monto
					,(ifnull(b.descuento_unitario_monto,0) * b.cantidad) AS descuento_unitario_monto_total
					,((b.costo_unitario * b.cantidad) - (ifnull(b.descuento_unitario_monto,0) * b.cantidad)) AS costo_total 
					,b.impuesto_aplica
					,b.impuesto_porcentaje
					,(((b.costo_unitario * b.cantidad) - (ifnull(b.descuento_unitario_monto,0) * b.cantidad)) * (IFNULL(b.impuesto_porcentaje,0)/100)) as impuesto_por_costo_total
					,(((b.costo_unitario * b.cantidad) - (ifnull(b.descuento_unitario_monto,0) * b.cantidad)) * (1+(IFNULL(b.impuesto_porcentaje,0)/100))) as costo_total_con_impuesto
					,a.id_usuario
					,CONCAT(IFNULL(j.nombre,''),' ',IFNULL(j.paterno,''),' ',IFNULL(j.materno,'')) as usuario_nombre
					,a.timestamp
					from $tbl1 a 
					left join $tbl2 b on a.id_compras_orden = b.id_compras_orden 
					left join $tbl3 c on b.id_compras_articulo_precios = c.id_compras_articulo_precios
					left join $tbl4 art on c.id_articulo=art.id_compras_articulo
					LEFT JOIN $tbl5 d on art.id_compras_linea=d.id_compras_linea
					lEFT JOIN $tbl6 e on art.id_compras_marca=e.id_compras_marca
					LEFT JOIN $tbl7 f on art.id_compras_presentacion=f.id_compras_presentacion
					LEFT JOIN $tbl8 g on art.id_compras_um=g.id_compras_um
					LEFT JOIN $tbl9 h on a.id_proveedor=h.id_compras_proveedor
					LEFT JOIN $db1.$tbl10 i on a.id_usuario=i.id_usuario
					LEFT JOIN $db1.$tbl11 j on i.id_personal=j.id_personal
					LEFT JOIN $db1.$tbl12 k on a.id_sucursal=k.id_sucursal;";

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
	/*public function db_get_total_rows($data=array()){
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
	}*/
}
?>