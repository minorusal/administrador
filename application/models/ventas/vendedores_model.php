<?php
class vendedores_model extends Base_Model{
	function get_vendedores($limit, $offset, $filtro="", $aplicar_limit = true){
		$filtro = ($filtro=="") ? "" : "AND ( 	vv.nombre_vendedor  LIKE '%$filtro%' OR 
												vv.clave_corta  LIKE '%$filtro%' OR  
												vv.rfc  LIKE '%$filtro%'  
											)";
		$limit = ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "SELECT 
						vv.id_ventas_vendedores,
						vv.nombre_vendedor,
						vv.clave_corta,
						vv.rfc,
						vv.telefonos
					FROM 
						av_ventas_vendedores vv
					WHERE vv.activo = 1 $filtro
					ORDER BY vv.id_ventas_vendedores
				$limit;";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			 return $query->result_array();
		}	
	}
	function get_existencia_vendedor($clave_corta){
		$query = "SELECT * FROM av_ventas_vendedores vv WHERE vv.clave_corta = '$clave_corta'";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	function get_vendedor_unico($id_vendedor){
		$query = "SELECT * FROM av_ventas_vendedores vv WHERE vv.id_ventas_vendedores = $id_vendedor";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	function insert_vendedor($data){
		$query = $this->db->insert_string('av_ventas_vendedores', $data);
		$query = $this->db->query($query);

			return $query;
	}
	function catalogo_entidades(){
		$query = "SELECT * FROM av_administracion_entidades WHERE activo=1";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	function update_vendedor($data, $id_vendedor){
		$condicion = array('id_ventas_vendedores !=' => $id_vendedor, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_ventas_vendedores', $condicion);
		if(!$existe){
			$condicion = "id_ventas_vendedores = $id_vendedor"; 
			$query = $this->db->update_string('av_ventas_vendedores', $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}
	function cat_sucursales(){
		$query = "SELECT * FROM 00_av_system.sys_sucursales WHERE activo=1";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
}