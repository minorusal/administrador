<?php
class entradas_almacen_model extends Base_Model{
	public function db_get_data($data=array()){	
		// DB Info
		$tbl = $this->tbl;
		// Filtro
		$filtro = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;

		$filtro = ($filtro!="") ? "and (a.orden_num LIKE '%$filtro%' 
							   or b.razon_social LIKE '%$filtro%'
							   or a.descripcion LIKE '%$filtro%'
							   or c.estatus LIKE '%$filtro%'
							   )" 
							: "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		// Query
		$query="SELECT 
					a.id_stock,
					a.id_almacen,
					a.id_pasillo,
					a.id_gaveta,
					a.id_almacen_entradas_recibir,
					a.id_compras_orden_articulo,
					a.id_articulo_tipo,
					a.stock,
					a.timestamp as fecha_recepcion,
					c.id_articulo,
					d.articulo,
					e.presentacion,
					f.almacenes,
					g.gavetas
				from $tbl[almacen_stock] a 
				LEFT JOIN $tbl[compras_ordenes_articulos] b on a.id_compras_orden_articulo=b.id_compras_orden_articulo
				LEFT JOIN $tbl[compras_articulos_precios] c on b.id_compras_articulo_precios=c.id_compras_articulo_precios
				LEFT JOIN $tbl[compras_articulos] d on c.id_articulo=d.id_compras_articulo
				LEFT JOIN $tbl[compras_presentaciones] e on c.id_presentacion=e.id_compras_presentacion
				LEFT JOIN $tbl[almacen_almacenes] f on a.id_almacen=f.id_almacen_almacenes
				LEFT JOIN $tbl[almacen_gavetas] g on a.id_gaveta=g.id_almacen_gavetas";
		/*
	FALTAN LOS FILTROS Y LA BUSQUEDA

		LEFT JOIN $tbl[compras_articulos] c on b.id_articulo=c.id_compras_articulo
				LEFT JOIN $tbl[compras_ordenes_estatus] c on a.estatus=c.id_estatus
				LEFT JOIN $tbl[compras_ordenes_tipo] d on a.id_orden_tipo=d.id_orden_tipo
				LEFT JOIN $tbl[sucursales] e on a.id_sucursal=e.id_sucursal
				LEFT JOIN $tbl[administracion_forma_pago] f on a.id_forma_pago=f.id_forma_pago
				LEFT JOIN $tbl[administracion_creditos] g on a.id_credito=g.id_administracion_creditos
				WHERE a.activo=1 AND a.estatus = 8 AND 1  $filtro
				ORDER BY orden_num ASC
				$limit*/
				//echo $query;
	  	// Execute querie

	  	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_data_unico($id_compras_articulo_precio){
		$tbl = $this->tbl;
		// Query
		$query = "SELECT 
					a.id_compras_orden_articulo, 
					a.id_compras_orden, 
					a.id_compras_articulo_precios, 
					b.upc, 
					b.sku,
					b.id_articulo,
					b.id_marca
				FROM 
					$tbl[compras_ordenes_articulos] a
				LEFT JOIN $tbl[compras_articulos_precios] b ON a.id_compras_articulo_precios=b.id_compras_articulo_precios
				WHERE id_compras_articulo_precios = $id_compras_articulo_precio";
				echo $query;
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
}
?>