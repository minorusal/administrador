<?php
class clientes_model extends Base_Model{

	function insert_cliente($data){		
		$query = $this->db->insert_string('av_ventas_clientes', $data);
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
	function consulta_clientes($limit, $offset, $filtro="", $aplicar_limit = true){
		$filtro = ($filtro=="") ? "" : "AND ( 	vc.nombre_cliente  LIKE '%$filtro%' OR 
												vc.razon_social  LIKE '%$filtro%' OR 
												vc.clave_corta  LIKE '%$filtro%' OR  
												vc.rfc  LIKE '%$filtro%'  
											)";
		$limit = ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "SELECT 
						vc.id_ventas_clientes,
						vc.nombre_cliente,
						vc.razon_social,
						vc.clave_corta,
						vc.rfc,
						vc.telefonos
					FROM 
						av_ventas_clientes vc
					WHERE vc.activo = 1 $filtro
					ORDER BY vc.id_ventas_clientes
				$limit;";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			 return $query->result_array();
		}	
	}
	function get_cliente_unico($id_cliente){
		$query = "SELECT * FROM av_ventas_clientes vc WHERE vc.id_ventas_clientes = $id_cliente";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	function update_cliente($data, $id_cliente){
		$condicion = array('id_ventas_clientes !=' => $id_cliente, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_ventas_clientes', $condicion);
		if(!$existe){
			$condicion = "id_ventas_clientes = $id_cliente"; 
			$query = $this->db->update_string('av_ventas_clientes', $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}
	function get_existencia_cliente($clave_corta){
		$query = "SELECT * FROM av_ventas_clientes vc WHERE vc.clave_corta = '$clave_corta'";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
}