<?php
class listado_sucursales_model extends Base_Model{

	//Función que obtiene toda la información de la tabla sys_sucursales
	public function db_get_data($data=array()){
		// DB Info		
		$tbl = $this->tbl;
		// Filtro
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (su.clave_corta like '%$filtro%' OR
									su.direccion like '%$filtro%' OR
									su.sucursal like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "	SELECT 
						 su.id_sucursal
						,su.clave_corta
						,su.inicio
						,su.final
						,su.direccion
						,su.sucursal
						,e.entidad
						,r.clave_corta as region
					FROM $tbl[sucursales] su
					LEFT JOIN $tbl[administracion_regiones] r on su.id_region = r.id_administracion_region
					LEFT JOIN $tbl[administracion_entidades] e on e.id_administracion_entidad = su.id_entidad
					WHERE su.activo = 1 $filtro
					ORDER BY su.id_sucursal ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	public function get_esquema_pago(){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[sucursales_esquema_pago] ";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_esquema_venta(){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[sucursales_esquema_venta] ";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	public function delete_pago($id_sucursal){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$query = "DELETE 
				  FROM $tbl[sucursales_pago]
				  WHERE id_sucursal =".$id_sucursal;
		$query = $this->db->query($query);
		if($query){
			return $query;
		}
	}
	public function db_update_data_pago($data = array()){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$insert = $this->insert_item($tbl['sucursales_pago'], $data);
		if($insert){
			return $insert;
		}
	}

	public function delete_venta($id_sucursal){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$query = "DELETE 
				  FROM $tbl[sucursales_venta]
				  WHERE id_sucursal =".$id_sucursal;
		$query = $this->db->query($query);
		if($query){
			return $query;
		}
	}
	public function db_update_data_venta($data = array()){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$insert = $this->insert_item($tbl['sucursales_venta'], $data);
		if($insert){
			return $insert;
		}
	}
	/*Trae la información para el formulario de edición de sucursales*/
	public function get_orden_unico_sucursal($id_sucursal){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		
		$query = "SELECT 
					 s.*
					 ,ep.esquema_pago
					 ,ep.id_sucursales_esquema_pago
					 ,ev.id_sucursales_esquema_venta

				  FROM $tbl[sucursales] s
				 
				  LEFT JOIN $tbl[sucursales_pago] p on p.id_sucursal = s.id_sucursal
				  LEFT JOIN $tbl[sucursales_venta] v on v.id_sucursal = s.id_sucursal
				  LEFT JOIN $tbl[sucursales_esquema_pago] ep on ep.id_sucursales_esquema_pago = p.id_esquema_pago
				  LEFT JOIN $tbl[sucursales_esquema_venta] ev on ev.id_sucursales_esquema_venta = v.id_esquema_venta				  
				  WHERE s.id_sucursal = $id_sucursal ";
				 //print_debug($query);
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}


	public function get_id_sucursal(){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$query = "SELECT COUNT(*) AS cuantos FROM $tbl[sucursales] WHERE activo = 1";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	/*Actualliza la información en el formuladio de edición de sucursales*/
	public function db_update_data($data=array()){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$condicion = array('id_sucursal !=' => $data['id_sucursal'], 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist($tbl['sucursales'], $condicion);
		if(!$existe){
			$condicion = "id_sucursal = ".$data['id_sucursal']; 
			$update    = $this->update_item($tbl['sucursales'], $data, 'id_sucursal', $condicion);
			return $update;
		}else{
			return false;
		}
	}

	/*Inserta registro de sucursales*/
	public function db_insert_data($data = array()){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$existe = $this->row_exist($tbl['sucursales'], array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$insert = $this->insert_item($tbl['sucursales'], $data);
			return $insert;
		}else{
			return false;
		}
	}
}