<?php
class ajustes_model extends Base_Model{
	//AJUsTES AGREGAR
	public function db_get_data($data=array()){	
		// DB Info
		$tbl = $this->tbl;
		// Filtro
		$filtro = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;

		$filtro = ($filtro!="") ? "and (b.articulo LIKE '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		// Query
		$query="SELECT 
				a.id_almacen_ajuste,
				a.stock_mov,
				a.stock_um_mov,
				a.id_articulo,
				a.id_almacen,
				a.id_pasillo,
				a.id_gaveta,
				a.timestamp,
				b.articulo,
				b.id_articulo_tipo,
				c.clave_corta as cl_almacen,
				d.clave_corta as cl_gaveta,
				e.clave_corta as cl_pasillo,
				f.clave_corta as cl_um
				from $tbl[almacen_ajustes] a 
				LEFT JOIN $tbl[compras_articulos] b on a.id_articulo=b.id_compras_articulo
				LEFT JOIN $tbl[almacen_almacenes] c on a.id_almacen=c.id_almacen_almacenes
				LEFT JOIN $tbl[almacen_gavetas] d on a.id_gaveta=d.id_almacen_gavetas
				LEFT JOIN $tbl[almacen_pasillos] e on a.id_pasillo=e.id_almacen_pasillos
				LEFT JOIN $tbl[compras_um] f on b.id_compras_um = f.id_compras_um
				WHERE a.estatus = 1 AND 1  $filtro
				ORDER BY a.id_almacen_ajuste ASC
				$limit";
		
			//echo $query;
	  	// Execute querie

	  	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_data_unico($id_almacen_ajuste){	
		// DB Info
		$tbl = $this->tbl;
		// Filtro
		
		// Query
		$query="SELECT 
				a.id_almacen_ajuste,
				a.stock_mov,
				a.stock_um_mov,
				a.id_articulo,
				a.id_almacen,
				a.id_pasillo,
				a.id_gaveta,
				a.timestamp,
				b.articulo,
				c.clave_corta as cl_almacen,
				d.clave_corta as cl_gaveta,
				e.clave_corta as cl_pasillo,
				f.clave_corta as cl_um
				from $tbl[almacen_ajustes] a 
				LEFT JOIN $tbl[compras_articulos] b on a.id_articulo=b.id_compras_articulo
				LEFT JOIN $tbl[almacen_almacenes] c on a.id_almacen=c.id_almacen_almacenes
				LEFT JOIN $tbl[almacen_gavetas] d on a.id_gaveta=d.id_almacen_gavetas
				LEFT JOIN $tbl[almacen_pasillos] e on a.id_pasillo=e.id_almacen_pasillos
				LEFT JOIN $tbl[compras_um] f on b.id_compras_um = f.id_compras_um
				WHERE a.estatus =1 AND id_almacen_ajuste=$id_almacen_ajuste";
		
			//echo $query;
	  	// Execute querie

	  	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function db_get_data_articulos($data=array()){	
		$id_almacen = ($data['id_almacen']!='')?"AND a.id_almacen=$data[id_almacen]":'';
		$id_pasillo = ($data['id_pasillo']!='')?" AND a.id_pasillo=$data[id_pasillo]":'';
		$id_gaveta  = ($data['id_gaveta']!='')?"AND a.id_gaveta=$data[id_gaveta]":'';
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query="SELECT 
					a.id_stock,
					a.id_almacen,
					a.id_pasillo,
					a.id_gaveta,
					a.id_almacen_entradas_recepcion,
					a.id_compras_orden_articulo,
					a.id_articulo_tipo,
					a.stock,
					a.stock_um,
					a.timestamp as fecha_recepcion,
					c.id_articulo,
					d.articulo,
					d.clave_corta as cl_articulo,
					e.presentacion,
					f.almacenes,
					g.gavetas,
					h.articulo_tipo
					,i.embalaje
					,j.clave_corta as cl_um
					,c.peso_unitario
					,e.presentacion
					,c.presentacion_x_embalaje
					,j.unidad_minima_cve
					
				from $tbl[almacen_stock] a 
				LEFT JOIN $tbl[compras_ordenes_articulos] b on a.id_compras_orden_articulo=b.id_compras_orden_articulo
				LEFT JOIN $tbl[compras_articulos_precios] c on b.id_compras_articulo_precios=c.id_compras_articulo_precios
				LEFT JOIN $tbl[compras_articulos] d on c.id_articulo=d.id_compras_articulo
				LEFT JOIN $tbl[compras_presentaciones] e on c.id_presentacion=e.id_compras_presentacion
				LEFT JOIN $tbl[almacen_almacenes] f on a.id_almacen=f.id_almacen_almacenes
				LEFT JOIN $tbl[almacen_gavetas] g on a.id_gaveta=g.id_almacen_gavetas
				LEFT JOIN $tbl[compras_articulos_tipo] h on a.id_articulo_tipo=h.id_articulo_tipo
				LEFT JOIN $tbl[compras_embalaje] i on c.id_embalaje = i.id_compras_embalaje
				LEFT JOIN $tbl[compras_um] j on d.id_compras_um = j.id_compras_um
			WHERE a.activo=1 $id_almacen $id_pasillo $id_gaveta
			GROUP BY c.id_articulo;";
			//echo $query;
	  	// Execute querie

	  	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function db_get_data_x_articulo($data=array()){	
		// DB Info
		$tbl = $this->tbl;

		$id_almacen = ($data['id_almacen']!='')?"AND a.id_almacen=$data[id_almacen]":'';
		$id_pasillo = ($data['id_pasillo']!='')?" AND a.id_pasillo=$data[id_pasillo]":'';
		$id_gaveta  = ($data['id_gaveta']!='')?"AND a.id_gaveta=$data[id_gaveta]":'';
		// Query
		$query="SELECT 
					a.id_stock,
					a.id_almacen,
					a.id_pasillo,
					a.id_gaveta,
					a.id_almacen_entradas_recepcion,
					a.id_compras_orden_articulo,
					a.id_articulo_tipo,
					a.stock,
					a.stock_um,
					a.timestamp as fecha_recepcion,
					c.id_articulo,
					d.articulo,
					d.clave_corta as cl_articulo,
					e.presentacion,
					f.almacenes,
					g.gavetas,
					h.articulo_tipo
					,i.embalaje
					,j.clave_corta as cl_um
					,c.peso_unitario
					,e.presentacion
					,c.presentacion_x_embalaje
					,j.unidad_minima_cve
					
				from $tbl[almacen_stock] a 
				LEFT JOIN $tbl[compras_ordenes_articulos] b on a.id_compras_orden_articulo=b.id_compras_orden_articulo
				LEFT JOIN $tbl[compras_articulos_precios] c on b.id_compras_articulo_precios=c.id_compras_articulo_precios
				LEFT JOIN $tbl[compras_articulos] d on c.id_articulo=d.id_compras_articulo
				LEFT JOIN $tbl[compras_presentaciones] e on c.id_presentacion=e.id_compras_presentacion
				LEFT JOIN $tbl[almacen_almacenes] f on a.id_almacen=f.id_almacen_almacenes
				LEFT JOIN $tbl[almacen_gavetas] g on a.id_gaveta=g.id_almacen_gavetas
				LEFT JOIN $tbl[compras_articulos_tipo] h on a.id_articulo_tipo=h.id_articulo_tipo
				LEFT JOIN $tbl[compras_embalaje] i on c.id_embalaje = i.id_compras_embalaje
				LEFT JOIN $tbl[compras_um] j on d.id_compras_um = j.id_compras_um
			WHERE  a.activo=1 AND c.id_articulo = $data[id_articulo] $id_almacen $id_pasillo $id_gaveta";
			//echo $query;
	  	// Execute querie

	  	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_data_stock($data=array()){	
		$id_almacen = ($data['id_almacen']!=0)?"AND a.id_almacen=$data[id_almacen]":'';
		$id_pasillo = ($data['id_pasillo']!=0)?" AND a.id_pasillo=$data[id_pasillo]":'';
		$id_gaveta  = ($data['id_gaveta']!=0)?"AND a.id_gaveta=$data[id_gaveta]":'';
		$id_articulo  = ($data['id_articulo']!=0)?"AND c.id_articulo=$data[id_articulo]":'';
		
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query="SELECT 
					a.id_stock,
					a.id_almacen,
					a.id_pasillo,
					a.id_gaveta,
					a.id_almacen_entradas_recepcion,
					a.id_compras_orden_articulo,
					a.id_articulo_tipo,
					a.stock,
					a.stock_um,
					a.lote,
					a.caducidad,
					a.id_estatus,
					a.timestamp as fecha_recepcion,
					c.id_articulo,
					d.articulo,
					e.clave_corta,
					e.unidad_minima,
					e.unidad_minima_cve,
					f.almacenes,
					g.gavetas,
					h.pasillos
				from $tbl[almacen_stock] a 
				LEFT JOIN $tbl[compras_ordenes_articulos] b on a.id_compras_orden_articulo=b.id_compras_orden_articulo
				LEFT JOIN $tbl[compras_articulos_precios] c on b.id_compras_articulo_precios=c.id_compras_articulo_precios
				LEFT JOIN $tbl[compras_articulos] d on c.id_articulo=d.id_compras_articulo
				LEFT JOIN $tbl[compras_um] e on d.id_compras_um = e.id_compras_um
				LEFT JOIN $tbl[almacen_almacenes] f on a.id_almacen=f.id_almacen_almacenes
				LEFT JOIN $tbl[almacen_gavetas] g on a.id_gaveta=g.id_almacen_gavetas
				LEFT JOIN $tbl[almacen_pasillos] h on a.id_pasillo=h.id_almacen_pasillos
				WHERE a.activo=1 $id_articulo $id_almacen $id_pasillo $id_gaveta
				ORDER BY a.caducidad, a.timestamp ASC;";
			//echo $query;
	  	// Execute querie

	  	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function insert($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$insert = $this->insert_item($tbl['almacen_ajustes'], $data);
		return $insert;
	}
	public function update_data($data=array()){
		// DB Info
		$tbl = $this->tbl;
		$condicion = "id_almacen_ajuste = ".$data['id_almacen_ajuste'];
		// Query
		$update = $this->update_item($tbl['almacen_ajustes'], $data, 'id_almacen_ajuste', $condicion);
		return $update;
	}
	//Historial Ajuste
	public function get_data_historial($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Filtro
		$filtro = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;

		$filtro = ($filtro!="") ? "and (b.articulo LIKE '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		// Query
		$query="SELECT 
				a.id_almacen_ajuste,
				a.stock_mov,
				a.stock_um_mov,
				a.id_articulo,
				a.id_almacen,
				a.id_pasillo,
				a.id_gaveta,
				a.timestamp,
				a.estatus as id_estatus,
				b.articulo,
				b.id_articulo_tipo,
				c.clave_corta as cl_almacen,
				d.clave_corta as cl_gaveta,
				e.clave_corta as cl_pasillo,
				f.clave_corta as cl_um,
				g.estatus
				from $tbl[almacen_ajustes] a 
				LEFT JOIN $tbl[compras_articulos] b on a.id_articulo=b.id_compras_articulo
				LEFT JOIN $tbl[almacen_almacenes] c on a.id_almacen=c.id_almacen_almacenes
				LEFT JOIN $tbl[almacen_gavetas] d on a.id_gaveta=d.id_almacen_gavetas
				LEFT JOIN $tbl[almacen_pasillos] e on a.id_pasillo=e.id_almacen_pasillos
				LEFT JOIN $tbl[compras_um] f on b.id_compras_um = f.id_compras_um
				LEFT JOIN $tbl[almacen_ajustes_estatus] g on a.estatus = g.id_almacen_ajuste_estatus
				
				WHERE 1  $filtro
				ORDER BY a.id_almacen_ajuste ASC
				$limit";
		
			//echo $query;
	  	// Execute querie

	  	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_data_unico_x_historial($id_almacen_ajuste){	
		// DB Info
		$tbl = $this->tbl;
		// Filtro
		
		// Query
		$query="SELECT 
				a.id_almacen_ajuste,
				a.stock_mov,
				a.stock_um_mov,
				a.id_articulo,
				a.id_almacen,
				a.id_pasillo,
				a.id_gaveta,
				a.timestamp,
				b.articulo,
				b.id_articulo_tipo,
				c.clave_corta as cl_almacen,
				d.clave_corta as cl_gaveta,
				e.clave_corta as cl_pasillo,
				f.clave_corta as cl_um
				from $tbl[almacen_ajustes] a 
				LEFT JOIN $tbl[compras_articulos] b on a.id_articulo=b.id_compras_articulo
				LEFT JOIN $tbl[almacen_almacenes] c on a.id_almacen=c.id_almacen_almacenes
				LEFT JOIN $tbl[almacen_gavetas] d on a.id_gaveta=d.id_almacen_gavetas
				LEFT JOIN $tbl[almacen_pasillos] e on a.id_pasillo=e.id_almacen_pasillos
				LEFT JOIN $tbl[compras_um] f on b.id_compras_um = f.id_compras_um
				WHERE a.estatus =2 AND id_almacen_ajuste=$id_almacen_ajuste";
		
			//echo $query;
	  	// Execute querie

	  	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_data_stock_logs($data=array()){	
		//dump_var($data);
		$id_almacen = ($data['id_almacen']!=0)?"AND a.log_id_almacen_destino=$data[id_almacen]":'';
		$id_pasillo = ($data['id_pasillo']!=0)?" AND a.log_id_pasillo_destino=$data[id_pasillo]":'';
		$id_gaveta  = ($data['id_gaveta']!=0)?"AND a.log_id_gaveta_destino=$data[id_gaveta]":'';
		$id_articulo  = ($data['id_articulo']!=0)?"AND c.id_articulo=$data[id_articulo]":'';
		
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query="SELECT 
					a.id_stock_log,
					a.id_almacen_entradas_recepcion,
					a.id_compras_orden_articulo,
					a.log_id_almacen_destino,
					a.log_id_pasillo_destino,
					a.log_id_gaveta_destino,
					a.log_stock_origen,
					a.log_stock_um_origen,
					a.log_stock_destino,
					a.log_stock_um_destino,
					a.log_lote,
					a.log_caducidad,
					a.timestamp as fecha_recepcion,
					c.id_articulo,
					d.articulo,
					d.id_articulo_tipo,
					e.clave_corta,
					e.unidad_minima,
					e.unidad_minima_cve,
					f.almacenes,
					g.gavetas,
					h.pasillos
				from $tbl[almacen_stock_logs] a 
				LEFT JOIN $tbl[compras_ordenes_articulos] b on a.id_compras_orden_articulo=b.id_compras_orden_articulo
				LEFT JOIN $tbl[compras_articulos_precios] c on b.id_compras_articulo_precios=c.id_compras_articulo_precios
				LEFT JOIN $tbl[compras_articulos] d on c.id_articulo=d.id_compras_articulo
				LEFT JOIN $tbl[compras_um] e on d.id_compras_um = e.id_compras_um
				LEFT JOIN $tbl[almacen_almacenes] f on a.log_id_almacen_origen=f.id_almacen_almacenes
				LEFT JOIN $tbl[almacen_gavetas] g on a.log_id_gaveta_origen=g.id_almacen_gavetas
				LEFT JOIN $tbl[almacen_pasillos] h on a.log_id_pasillo_origen=h.id_almacen_pasillos
				WHERE a.activo=1 AND a.id_accion=4 $id_articulo $id_almacen $id_pasillo $id_gaveta
				ORDER BY a.id_stock_log, a.timestamp ASC;";
			//echo $query;
	  	// Execute querie

	  	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
}
?>