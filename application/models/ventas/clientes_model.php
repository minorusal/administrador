<?php
class clientes_model extends Base_Model{

	function insert_cliente($data){	
		$tbl1 	= $this->dbinfo[1]['tbl_ventas_clientes'];	
		$insert = $this->insert_item($tbl1, $data);
		return $insert;
	}
	function consulta_clientes($limit, $offset, $filtro="", $aplicar_limit = true){
		
		$tbl1 	= $this->dbinfo[1]['tbl_ventas_clientes'];
		$tbl2 	= $this->dbinfo[1]['tbl_administracion_entidades'];
		$tbl3 	= $this->dbinfo[0]['tbl_sucursales'];
		$bd 	= $this->dbinfo[0]['db'];

		$filtro = ($filtro=='') ? "" : "AND ( 	vc.nombre_cliente LIKE '%$filtro%' OR 
												vc.razon_social   LIKE '%$filtro%' OR 
												vc.clave_corta    LIKE '%$filtro%' OR  
												vc.rfc            LIKE '%$filtro%' OR 
												e.entidad         LIKE '%$filtro%' OR
												su.sucursal       LIKE '%$filtro%' 
											)";
		$limit = ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "SELECT 
						vc.id_ventas_clientes,
						vc.nombre_cliente,
						vc.razon_social,
						vc.clave_corta,
						vc.rfc,
						vc.telefonos,
						e.entidad,
						su.sucursal
					FROM 
						$tbl1 vc
					LEFT JOIN $tbl2 e on vc.id_entidad = e.id_administracion_entidad
					LEFT JOIN $bd.$tbl3  su on vc.id_sucursal = su.id_sucursal
					WHERE vc.activo = 1 $filtro
					ORDER BY vc.id_ventas_clientes
				$limit;";
		$query = $this->db->query($query);

		if($query->num_rows >= 1){
			 return $query->result_array();
		}	
	}
	function get_cliente_unico($id_cliente){
		$tbl1 	= $this->dbinfo[1]['tbl_ventas_clientes'];
		$query = "SELECT * FROM $tbl1 vc WHERE vc.id_ventas_clientes = $id_cliente";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	function update_cliente($data, $id_cliente){
		$tbl1 	= $this->dbinfo[1]['tbl_ventas_clientes'];

		$condicion = array('id_ventas_clientes !=' => $id_cliente, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist($tbl1, $condicion);
		if(!$existe){
			$condicion = "id_ventas_clientes = $id_cliente"; 
			$data['id_ventas_clientes'] = $id_cliente;
			$update    = $this->update_item($tbl1, $data, 'id_ventas_clientes', $condicion);
			return $update;
		}else{
			return false;
		}
	}
	function get_existencia_cliente($clave_corta){
		$tbl1 	= $this->dbinfo[1]['tbl_ventas_clientes'];
		$query = "SELECT * FROM $tbl1 vc WHERE vc.clave_corta = '$clave_corta'";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
}