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

}
?>