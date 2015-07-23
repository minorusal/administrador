<?php
class clientes_model extends Base_Model{

	function insert_cliente($data){	
		// DB Info
		$tbl = $this->tbl;
		// Query	
		$insert = $this->insert_item($tbl['ventas_clientes'], $data);
		return $insert;
	}
	function consulta_clientes($limit, $offset, $filtro="", $aplicar_limit = true){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$filtro = ($filtro=='') ? "" : "AND ( 	vc.nombre LIKE '%$filtro%' OR 
												vc.paterno LIKE '%$filtro%' OR 
												vc.materno LIKE '%$filtro%' OR 
												vc.razon_social   LIKE '%$filtro%' OR 
												vc.clave_corta    LIKE '%$filtro%' OR  
												vc.rfc            LIKE '%$filtro%' OR 
												e.entidad         LIKE '%$filtro%' OR
												su.sucursal       LIKE '%$filtro%' 
											)";
		$limit = ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "SELECT 
						vc.id_ventas_clientes,
						vc.nombre,
						vc.paterno,
						vc.materno,
						vc.razon_social,
						vc.clave_corta,
						vc.rfc,
						vc.telefonos,
						vc.calle,
						vc.num_int,
						vc.num_ext,
						vc.colonia,
						vc.municipio,
						vc.cp,
						vc.email,
						vc.timestamp,
						e.entidad,
						su.sucursal
					FROM $tbl[ventas_clientes] vc
					LEFT JOIN $tbl[administracion_entidades] e on vc.id_entidad = e.id_administracion_entidad
					LEFT JOIN $tbl[sucursales]  su on vc.id_sucursal = su.id_sucursal
					WHERE vc.activo = 1 $filtro
					ORDER BY vc.id_ventas_clientes
				$limit;";
		$query = $this->db->query($query);

		if($query->num_rows >= 1){
			 return $query->result_array();
		}	
	}
	function get_cliente_unico($id_cliente){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[ventas_clientes] vc WHERE vc.id_ventas_clientes = $id_cliente";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	function update_cliente($data, $id_cliente){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$condicion = "id_ventas_clientes = $id_cliente"; 
		$data['id_ventas_clientes'] = $id_cliente;
		$update    = $this->update_item($tbl['ventas_clientes'], $data, 'id_ventas_clientes', $condicion);
		return $update;
	}
	function get_existencia_cliente($clave_corta){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[ventas_clientes] vc WHERE vc.clave_corta = '$clave_corta'";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
}