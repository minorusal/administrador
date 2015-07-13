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
						$tbl[nutricion_programacion_ciclo] pc
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

	public function delete_paramas_programacion($id_sucursal){
		$tbl = $this->tbl;
		$this->db->delete($tbl['nutricion_programacion'], array('id_sucursal' => $id_sucursal)); 
		$this->db->delete($tbl['nutricion_programacion_ciclo'], array('id_sucursal' => $id_sucursal)); 
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
		$insert = $this->db->insert_batch($tbl['nutricion_programacion_ciclo'], $data);
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