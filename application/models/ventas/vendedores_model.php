<?php
class vendedores_model extends Base_Model{
	function get_vendedores($limit, $offset, $filtro="", $aplicar_limit = true){

		$tbl1 	= $this->dbinfo[1]['tbl_ventas_vendedores'];
		$tbl2 	= $this->dbinfo[1]['tbl_administracion_entidades'];
		$tbl3 	= $this->dbinfo[0]['tbl_sucursales'];
		$bd 	= $this->dbinfo[0]['db'];

		$filtro = ($filtro=="") ? "" : "AND ( 	vv.nombre_vendedor  LIKE '%$filtro%' OR 
												vv.clave_corta  LIKE '%$filtro%' OR
												vv.rfc            LIKE '%$filtro%' OR 
												e.entidad         LIKE '%$filtro%' OR
												su.sucursal       LIKE '%$filtro%' 
											)";
		$limit = ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "SELECT 
						vv.id_ventas_vendedores,
						vv.nombre_vendedor,
						vv.clave_corta,
						vv.rfc,
						vv.telefonos,
						e.entidad,
						su.sucursal
					FROM 
						$tbl1 vv
					LEFT JOIN $tbl2  e on vv.id_entidad = e.id_administracion_entidad
					LEFT JOIN $bd.$tbl3 su on vv.id_sucursal = su.id_sucursal
					WHERE vv.activo = 1 $filtro
					ORDER BY vv.id_ventas_vendedores
				$limit;";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			 return $query->result_array();
		}	
	}
	function get_existencia_vendedor($clave_corta){
		$tbl1 	= $this->dbinfo[1]['tbl_ventas_vendedores'];

		$query = "SELECT * FROM $tbl1 vv WHERE vv.clave_corta = '$clave_corta'";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	function get_vendedor_unico($id_vendedor){
		$tbl1 	= $this->dbinfo[1]['tbl_ventas_vendedores'];

		$query = "SELECT * FROM $tbl1 vv WHERE vv.id_ventas_vendedores = $id_vendedor";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	function insert_vendedor($data){
		$tbl1 	= $this->dbinfo[1]['tbl_ventas_vendedores'];
		$insert = $this->insert_item($tbl1, $data);
		return $insert;
	}
	function update_vendedor($data, $id_vendedor){
		$tbl1 	= $this->dbinfo[1]['tbl_ventas_vendedores'];

		$condicion = array('id_ventas_vendedores !=' => $id_vendedor, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist($tbl1 , $condicion);
		if(!$existe){
			$condicion = "id_ventas_vendedores = $id_vendedor"; 
			$update    = $this->update_item($tbl1, $data, 'id_ventas_vendedores', $condicion);
			return $update;
		}else{
			return false;
		}
	}
}