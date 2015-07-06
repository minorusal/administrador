<?php
class programacion_model extends Base_Model{
	public function get_params_ciclos($id_sucursal){
		$query = "SELECT 
					 np.id_nutricion_programacion
					,date_format(np.fecha_inicio , '%d/%m/%Y') as fecha_inicio
					,date_format(np.fecha_termino , '%d/%m/%Y') as fecha_termino
					,np.id_sucursal
					,np.id_usuario
				  FROM av_nutricion_programacion np WHERE np.id_sucursal = $id_sucursal";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function get_dias_descartados($id_sucursal){
		$query = "SELECT * FROM av_nutricion_programacion_dias_descartados WHERE id_sucursal = $id_sucursal";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_ciclos($id_sucursal){
		$query = "SELECT * FROM av_nutricion_ciclos where activo = 1 AND id_sucursal = $id_sucursal";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_ciclos_programados($id_sucursal){
		$query = "	SELECT   
						pc.id_nutricion_programacion_ciclo
						,pc.id_nutricion_ciclos
						,pc.orden
						,nc.ciclo
					FROM 
						av_nutricion_programacion_ciclos pc
					LEFT JOIN av_nutricion_ciclos nc ON pc.id_nutricion_ciclos = nc.id_nutricion_ciclos
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
		$query = "	SELECT 
						npc.orden,
						c.*
					FROM 
						av_nutricion_programacion_ciclos npc
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
							av_nutricion_ciclo_receta ncr
						LEFT JOIN av_nutricion_ciclos cl on cl.id_nutricion_ciclos = ncr.id_ciclo
						LEFT JOIN av_nutricion_recetas nr on nr.id_nutricion_receta = ncr.id_receta
						LEFT JOIN av_nutricion_tiempos tm on tm.id_nutricion_tiempo = ncr.id_tiempo
						LEFT JOIN av_nutricion_familias fm on fm.id_nutricion_familia = ncr.id_familia
						LEFT JOIN av_administracion_servicios s on s.id_administracion_servicio = ncr.id_servicio
						WHERE cl.id_sucursal= $id_sucursal AND ncr.activo = 1
						ORDER BY ncr.id_servicio, ncr.id_tiempo ,ncr.id_familia
					) c on npc.id_nutricion_ciclos = c.id_nutricion_ciclos
					WHERE npc.id_sucursal = $id_sucursal 
					ORDER BY npc.orden ";
		//print_debug($query);	
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	/*Funciones para guardar paremtros de programacion*/
	public function delete_paramas_programacion($id_sucursal){
		$this->db->delete('av_nutricion_programacion', array('id_sucursal' => $id_sucursal)); 
		$this->db->delete('av_nutricion_programacion_ciclos', array('id_sucursal' => $id_sucursal)); 
		$this->db->delete('av_nutricion_programacion_dias_festivos', array('id_sucursal' => $id_sucursal)); 
		$this->db->delete('av_nutricion_programacion_dias_descartados', array('id_sucursal' => $id_sucursal)); 
	}
	public function insert_params_programacion($data){
		$insert = $this->insert_item('av_nutricion_programacion', $data);
		return $insert;
	}
	public function insert_dias_descartados($data){
		$insert = $this->db->insert_batch('av_nutricion_programacion_dias_descartados', $data);
		return $insert;
	}
	public function insert_ciclos_menus($data){
		$insert = $this->db->insert_batch('av_nutricion_programacion_ciclos', $data);
		return $insert;
		return $query;
	}

	public function update_cantidad_ciclo_receta($data){
		$condicion = "id_nutricion_ciclo_receta = ".$data['id_nutricion_ciclo_receta']; 
		$update    = $this->update_item('av_nutricion_ciclo_receta', $data, 'id_nutricion_ciclo_receta', $condicion);

		return $update;
	}

}
?>