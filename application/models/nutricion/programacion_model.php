<?php
class programacion_model extends Base_Model{
	public function get_params_ciclos($id_sucursal){
		$tbl = $this->tbl;
		$query = "SELECT 
					 np.id_nutricion_programacion
					,date_format(np.fecha_inicio , '%d/%m/%Y') as fecha_inicio
					,date_format(np.fecha_termino , '%d/%m/%Y') as fecha_termino
					,np.id_sucursal
					,np.id_usuario
				  FROM $tbl[nutricion_programacion] np WHERE np.id_sucursal = $id_sucursal";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function get_dias_festivos($id_sucursal){
		$tbl = $this->tbl;
		$query = "SELECT 
					date_format(f.fecha, '%d/%m/%Y') as fecha
				  FROM $tbl[nutricion_programacion_dias_festivos] f
				  WHERE id_sucursal = $id_sucursal
				  ORDER BY fecha";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}else{
			return null;
		}
	}
	public function get_dias_especiales($id_sucursal){
		$tbl = $this->tbl;
		$query = "	SELECT 
						s.id_nutricion_ciclos,
						date_format(s.fecha, '%d/%m/%Y') as fecha, 
						c.clave_corta, 
						c.ciclo 
					FROM $tbl[nutricion_programacion_dias_especiales] s
					LEFT JOIN $tbl[nutricion_ciclos] c ON c.id_nutricion_ciclos = s.id_nutricion_ciclos
					WHERE s.id_sucursal = $id_sucursal
					ORDER BY fecha";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}else{
			return null;
		}
	}
	public function get_dias_especiales_contenido_ciclo($id_sucursal){
		$tbl = $this->tbl;
		$query = "	SELECT 			 
						cl.ciclo
						,ncr.id_nutricion_ciclo_receta
						,ncr.porciones
						,s.servicio
						,tm.tiempo
						,fm.familia
						,nr.receta
						,concat_ws('-', s.inicio, s.final) as horario
						,e.*
					FROM 
						$tbl[nutricion_programacion_dias_especiales] e
					LEFT JOIN $tbl[nutricion_ciclos] cl  on cl.id_nutricion_ciclos = e.id_nutricion_ciclos
					LEFT JOIN $tbl[nutricion_ciclo_receta] ncr on cl.id_nutricion_ciclos = ncr.id_ciclo
					LEFT JOIN $tbl[nutricion_recetas] nr on nr.id_nutricion_receta = ncr.id_receta
					LEFT JOIN $tbl[nutricion_tiempos] tm on tm.id_nutricion_tiempo = ncr.id_tiempo
					LEFT JOIN $tbl[nutricion_familias] fm on fm.id_nutricion_familia = ncr.id_familia
					LEFT JOIN $tbl[administracion_servicios] s on s.id_administracion_servicio = ncr.id_servicio
					WHERE e.id_sucursal= $id_sucursal
					ORDER BY e.fecha";
		//print_debug($query);
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}else{
			return null;
		}
	}
	public function get_dias_descartados($id_sucursal){
		$tbl   = $this->tbl;
		$query = "SELECT * FROM $tbl[nutricion_programacion_dias_descartados] WHERE id_sucursal = $id_sucursal";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_ciclos($id_sucursal){
		$tbl   = $this->tbl;
		$query = "SELECT * FROM $tbl[nutricion_ciclos] where activo = 1 AND id_sucursal = $id_sucursal";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_ciclos_programados($id_sucursal){
		$tbl   = $this->tbl;
		$query = "	SELECT   
						pc.id_nutricion_programacion_ciclo
						,pc.id_nutricion_ciclos
						,pc.orden
						,nc.ciclo
					FROM 
						$tbl[nutricion_programacion_ciclos] pc
					LEFT JOIN $tbl[nutricion_ciclos] nc ON pc.id_nutricion_ciclos = nc.id_nutricion_ciclos
			 		WHERE pc.id_sucursal = $id_sucursal  
					ORDER BY pc.orden;";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_contenido_ciclo($id_ciclo){
		$tbl = $this->tbl;
		$query = "	SELECT 
						 cl.id_nutricion_ciclos
						,cl.ciclo
						,ncr.id_familia
						,ncr.id_nutricion_ciclo_receta
						,ncr.id_receta
						,ncr.id_servicio
						,ncr.id_tiempo
						,ncr.porciones
						,fm.familia
						,nr.id_nutricion_receta
						,nr.receta
						,s.servicio
						,concat_ws('-', s.inicio, s.final) as horario
						,tm.tiempo
					FROM 
						$tbl[nutricion_ciclo_receta] ncr
					LEFT JOIN $tbl[nutricion_ciclos] cl on cl.id_nutricion_ciclos = ncr.id_ciclo
					LEFT JOIN $tbl[nutricion_recetas] nr on nr.id_nutricion_receta = ncr.id_receta
					LEFT JOIN $tbl[nutricion_tiempos] tm on tm.id_nutricion_tiempo = ncr.id_tiempo
					LEFT JOIN $tbl[nutricion_familias] fm on fm.id_nutricion_familia = ncr.id_familia
					LEFT JOIN $tbl[administracion_servicios] s on s.id_administracion_servicio = ncr.id_servicio
					WHERE cl.id_nutricion_ciclos= $id_ciclo AND ncr.activo = 1
					ORDER BY ncr.id_servicio, ncr.id_tiempo ,ncr.id_familia";
		//print_debug($query);	
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function get_programacion_contenido_ciclo($id_sucursal){
		$tbl   = $this->tbl;
		$query = "	SELECT 
						npc.orden,
						c.*
					FROM 
						$tbl[nutricion_programacion_ciclos] npc
					LEFT JOIN (
						SELECT 
							 cl.id_nutricion_ciclos
							,cl.ciclo
							,ncr.id_familia
							,ncr.id_nutricion_ciclo_receta
							,ncr.id_receta
							,ncr.id_servicio
							,ncr.id_tiempo
							,ncr.porciones
							,fm.familia
							,nr.id_nutricion_receta
							,nr.receta
							,s.servicio
							,concat_ws('-', s.inicio, s.final) as horario
							,tm.tiempo
						FROM 
							$tbl[nutricion_ciclos] cl 
						LEFT JOIN $tbl[nutricion_ciclo_receta] ncr on cl.id_nutricion_ciclos = ncr.id_ciclo
						LEFT JOIN $tbl[nutricion_recetas] nr on nr.id_nutricion_receta = ncr.id_receta
						LEFT JOIN $tbl[nutricion_tiempos] tm on tm.id_nutricion_tiempo = ncr.id_tiempo
						LEFT JOIN $tbl[nutricion_familias] fm on fm.id_nutricion_familia = ncr.id_familia
						LEFT JOIN $tbl[administracion_servicios] s on s.id_administracion_servicio = ncr.id_servicio
						WHERE cl.id_sucursal= $id_sucursal 
						ORDER BY s.servicio, tm.tiempo ,fm.familia, nr.receta
					) c on npc.id_nutricion_ciclos = c.id_nutricion_ciclos
					WHERE npc.id_sucursal = $id_sucursal 
					ORDER BY npc.orden,c.servicio, c.tiempo ,c.familia, c.receta";
		//print_debug($query);	
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function get_programacion_contenido_ciclo_insumos($id_sucursal){
		$tbl   = $this->tbl;
		$query = "		SELECT 
							npc.orden,
							c.*
						FROM 
							$tbl[nutricion_programacion_ciclos] npc
						LEFT JOIN (
									SELECT 
										 cl.id_nutricion_ciclos
										,cl.ciclo
										,ncr.id_familia
										,ncr.id_nutricion_ciclo_receta
										,ncr.id_receta
										,ncr.id_servicio
										,ncr.id_tiempo
										,ncr.porciones as porciones_recetas_ciclos
										,fm.familia
										,nr.*
										,s.servicio
										,concat_ws('-', s.inicio, s.final) as horario
										,tm.tiempo
									FROM 
										$tbl[nutricion_ciclo_receta] ncr
									LEFT JOIN $tbl[nutricion_ciclos] cl on cl.id_nutricion_ciclos = ncr.id_ciclo
									LEFT JOIN 
										(
										SELECT 
											r.id_nutricion_receta
											,r.receta
											,r.clave_corta as clave_receta
											,r.porciones as porciones_receta_preparacion
											,ri.id_compras_articulo
											,ca.articulo
											,ri.porciones as porciones_articulo
											,ap.*
											,cu.um
											,li.linea
											,s.sucursal
										FROM $tbl[nutricion_recetas] r
										LEFT JOIN  $tbl[nutricion_familias] f            ON f.id_nutricion_familia  = r.id_nutricion_familia
										LEFT JOIN  $tbl[nutricion_recetas_articulos] ri  ON r.id_nutricion_receta   = ri.id_nutricion_receta
										LEFT JOIN  $tbl[compras_articulos] ca            ON ca.id_compras_articulo  = ri.id_compras_articulo
										LEFT JOIN  $tbl[compras_lineas] li               ON ca.id_compras_linea     = li.id_compras_linea
										LEFT JOIN  $tbl[compras_um] cu                   ON cu.id_compras_um        = ca.id_compras_um
										LEFT JOIN  $tbl[sucursales] s                    ON s.id_sucursal           = r.id_sucursal
										LEFT JOIN  (
													SELECT 
														a.upc
														,a.sku
														,a.id_articulo
														,a.presentacion_x_embalaje
														,a.costo_sin_impuesto
														,a.um_x_embalaje
														,a.um_x_presentacion
														,a.peso_unitario
														,a.costo_unitario
														,a.costo_x_um
														,a.rendimiento
														,a.id_administracion_region
														,c.nombre_comercial as proveedor
														,d.marca
														,e.presentacion
														,e.clave_corta as cl_presentacion
														,f.embalaje
														,f.clave_corta as cl_embalaje
														,g.valor as impuesto
														,h.clave_corta as cl_um
														,i.clave_corta as cl_region
													from $tbl[compras_articulos_precios] a 
													LEFT JOIN $tbl[compras_articulos] b        on a.id_articulo  	           = b.id_compras_articulo
													LEFT JOIN $tbl[compras_proveedores] c      on a.id_proveedor 	           = c.id_compras_proveedor
													LEFT JOIN $tbl[compras_marcas] d           on a.id_marca			       = d.id_compras_marca
													LEFT JOIN $tbl[compras_presentaciones] e   on a.id_presentacion	           = e.id_compras_presentacion
													LEFT JOIN $tbl[compras_embalaje] f         on a.id_embalaje    	           = f.id_compras_embalaje
													LEFT JOIN $tbl[administracion_impuestos] g on a.id_impuesto    	           = g.id_administracion_impuestos
													LEFT JOIN $tbl[compras_um] h               on b.id_compras_um    	       = h.id_compras_um
													LEFT JOIN $tbl[administracion_regiones] i  on a.id_administracion_region   = i.id_administracion_region
													WHERE a.activo = 1 AND a.articulo_default = 1
												) ap ON (ap.id_articulo = ca.id_compras_articulo AND ap.id_administracion_region = s.id_region)
										WHERE r.activo = 1 AND r.id_sucursal = 1
										)nr ON nr.id_nutricion_receta = ncr.id_receta
									LEFT JOIN $tbl[nutricion_tiempos] tm       ON tm.id_nutricion_tiempo       = ncr.id_tiempo
									LEFT JOIN $tbl[nutricion_familias] fm      ON fm.id_nutricion_familia      = ncr.id_familia
									LEFT JOIN $tbl[administracion_servicios] s ON s.id_administracion_servicio = ncr.id_servicio
									WHERE cl.id_sucursal = 1 AND ncr.activo = 1
									ORDER BY cl.ciclo, ncr.id_servicio, ncr.id_tiempo ,ncr.id_familia
						) c on npc.id_nutricion_ciclos = c.id_nutricion_ciclos
						WHERE npc.id_sucursal = 1 
						ORDER BY npc.orden,c.servicio, c.tiempo ,c.familia, c.receta";
		//print_debug($query);	
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function get_dias_especiales_contenido_ciclo_insumos($id_sucursal){
		$tbl = $this->tbl;
		$query = "	SELECT 			 
						cl.ciclo
						,ncr.id_familia
						,ncr.id_nutricion_ciclo_receta
						,ncr.id_receta
						,ncr.id_servicio
						,ncr.id_tiempo
						,ncr.porciones as porciones_recetas_ciclos
						,s.servicio
						,tm.tiempo
						,fm.familia
						,nr.*
						,concat_ws('-', s.inicio, s.final) as horario
						,e.*
					FROM 
						$tbl[nutricion_programacion_dias_especiales] e
					LEFT JOIN $tbl[nutricion_ciclos] cl  on cl.id_nutricion_ciclos = e.id_nutricion_ciclos
					LEFT JOIN $tbl[nutricion_ciclo_receta] ncr on cl.id_nutricion_ciclos = ncr.id_ciclo
					LEFT JOIN (
								SELECT 
									r.id_nutricion_receta
									,r.receta
									,r.clave_corta as clave_receta
									,r.porciones as porciones_receta_preparacion
									,ri.id_compras_articulo
									,ca.articulo
									,ri.porciones as porciones_articulo
									,ap.*
									,cu.um
									,li.linea
									,s.sucursal
								FROM $tbl[nutricion_recetas] r
								LEFT JOIN  $tbl[nutricion_familias] f            ON f.id_nutricion_familia  = r.id_nutricion_familia
								LEFT JOIN  $tbl[nutricion_recetas_articulos] ri  ON r.id_nutricion_receta   = ri.id_nutricion_receta
								LEFT JOIN  $tbl[compras_articulos] ca            ON ca.id_compras_articulo  = ri.id_compras_articulo
								LEFT JOIN  $tbl[compras_lineas] li               ON ca.id_compras_linea     = li.id_compras_linea
								LEFT JOIN  $tbl[compras_um] cu                   ON cu.id_compras_um        = ca.id_compras_um
								LEFT JOIN  $tbl[sucursales] s                    ON s.id_sucursal           = r.id_sucursal
								LEFT JOIN  (
												SELECT 
													a.upc
													,a.sku
													,a.id_articulo
													,a.presentacion_x_embalaje
													,a.costo_sin_impuesto
													,a.um_x_embalaje
													,a.um_x_presentacion
													,a.peso_unitario
													,a.costo_unitario
													,a.costo_x_um
													,a.rendimiento
													,a.id_administracion_region
													,c.nombre_comercial as proveedor
													,d.marca
													,e.presentacion
													,e.clave_corta as cl_presentacion
													,f.embalaje
													,f.clave_corta as cl_embalaje
													,g.valor as impuesto
													,h.clave_corta as cl_um
													,i.clave_corta as cl_region
												from $tbl[compras_articulos_precios] a 
												LEFT JOIN $tbl[compras_articulos] b on a.id_articulo  	= b.id_compras_articulo
												LEFT JOIN $tbl[compras_proveedores] c on a.id_proveedor 	= c.id_compras_proveedor
												LEFT JOIN $tbl[compras_marcas] d on a.id_marca			= d.id_compras_marca
												LEFT JOIN $tbl[compras_presentaciones] e on a.id_presentacion	= e.id_compras_presentacion
												LEFT JOIN $tbl[compras_embalaje] f on a.id_embalaje    	= f.id_compras_embalaje
												LEFT JOIN $tbl[administracion_impuestos] g on a.id_impuesto    	= g.id_administracion_impuestos
												LEFT JOIN $tbl[compras_um] h on b.id_compras_um    	= h.id_compras_um
												LEFT JOIN $tbl[administracion_regiones] i on a.id_administracion_region   = i.id_administracion_region
												WHERE a.activo = 1 AND a.articulo_default = 1
										) ap ON (ap.id_articulo = ca.id_compras_articulo AND ap.id_administracion_region = s.id_region)
								WHERE r.activo = 1 AND r.id_sucursal = 1
								) nr on nr.id_nutricion_receta = ncr.id_receta
					LEFT JOIN $tbl[nutricion_tiempos] tm on tm.id_nutricion_tiempo = ncr.id_tiempo
					LEFT JOIN $tbl[nutricion_familias] fm on fm.id_nutricion_familia = ncr.id_familia
					LEFT JOIN $tbl[administracion_servicios] s on s.id_administracion_servicio = ncr.id_servicio
					WHERE e.id_sucursal= $id_sucursal
					ORDER BY e.fecha";
		//print_debug($query);
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}else{
			return null;
		}
	}
	public function delete_paramas_programacion($id_sucursal){
		$tbl = $this->tbl;
		$this->db->delete($tbl['nutricion_programacion'], array('id_sucursal' => $id_sucursal)); 
		$this->db->delete($tbl['nutricion_programacion_ciclos'], array('id_sucursal' => $id_sucursal)); 
		$this->db->delete($tbl['nutricion_programacion_dias_festivos'], array('id_sucursal' => $id_sucursal));
		$this->db->delete($tbl['nutricion_programacion_dias_especiales'], array('id_sucursal' => $id_sucursal)); 
		$this->db->delete($tbl['nutricion_programacion_dias_descartados'], array('id_sucursal' => $id_sucursal)); 
	}
	public function insert_params_programacion($data){
		$tbl    = $this->tbl;
		$insert = $this->insert_item($tbl['nutricion_programacion'], $data);
		return $insert;
	}
	public function insert_dias_festivos($data){
		$tbl    = $this->tbl;
		$insert = $this->db->insert_batch($tbl['nutricion_programacion_dias_festivos'], $data);
		return $insert;
	}
	public function insert_dias_especiales($data){
		$tbl    = $this->tbl;
		$insert = $this->db->insert_batch($tbl['nutricion_programacion_dias_especiales'], $data);
		return $insert;
	}
	public function insert_dias_descartados($data){
		$tbl    = $this->tbl;
		$insert = $this->db->insert_batch($tbl['nutricion_programacion_dias_descartados'], $data);
		return $insert;
	}
	public function insert_ciclos_menus($data){
		$tbl = $this->tbl;
		$insert = $this->db->insert_batch($tbl['nutricion_programacion_ciclos'], $data);
		return $insert;
	}
	public function update_cantidad_ciclo_receta($data){
		$tbl       = $this->tbl;
		$condicion = "id_nutricion_ciclo_receta = ".$data['id_nutricion_ciclo_receta']; 
		$update    = $this->update_item($tbl['nutricion_ciclo_receta'], $data, 'id_nutricion_ciclo_receta', $condicion);

		return $update;
	}

}
?>