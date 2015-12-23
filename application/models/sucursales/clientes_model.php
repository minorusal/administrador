<?php
class clientes_model extends Base_Model{

	function insert_cliente($data){	
		// DB Info
		$tbl = $this->tbl;
		// Query
		$existe = $this->row_exist($tbl['ventas_clientes'], array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$insert = $this->insert_item($tbl['ventas_clientes'], $data,true);
			return $insert;
		}else{
			return false;
		}	
	}

	function listado_clientes($data = array()){
		// DB Info
		$tbl = $this->tbl;
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND ( 	sc.nombre LIKE '%$filtro%' OR 
									    sc.paterno LIKE '%$filtro%' OR 
									    sc.materno LIKE '%$filtro%' OR 
									    sc.razon_social   LIKE '%$filtro%' OR 
									    sc.clave_corta    LIKE '%$filtro%' OR  
									    sc.rfc            LIKE '%$filtro%' OR 
									    su.sucursal       LIKE '%$filtro%' 
											)" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		// Query
		$query = "SELECT 
						sc.id_sucursales_clientes,
						sc.nombre,
						sc.apellido_paterno,
						sc.apellido_materno,
						CONCAT_WS(' ',sc.nombre,sc.apellido_paterno,sc.apellido_materno) AS name,
						sc.razon_social,
						sc.clave_corta AS cv_cliente,
						sc.rfc,
						sc.telefono,
						sc.calle,
						sc.numero_interior,
						sc.numero_exterior,
						sc.colonia,
						sc.municipio,
						sc.codigo_postal,
						sc.email,
						sc.timestamp,
						su.sucursal
					FROM $tbl[sucursales_clientes] sc
					LEFT JOIN $tbl[sucursales]  su on sc.id_sucursal = su.id_sucursal
					WHERE sc.activo = 1 $filtro
					ORDER BY sc.id_sucursales_clientes
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
		$query = "SELECT 
						 vc.id_ventas_clientes
						,vc.nombre
						,vc.paterno
						,vc.materno
						,vc.razon_social
						,vc.clave_corta as cv_cliente
						,vc.rfc
						,vc.calle
						,vc.num_int
						,vc.num_ext
						,vc.colonia
						,vc.municipio
						,vc.id_entidad
						,vc.id_sucursal
						,vc.cp
						,vc.telefonos
						,vc.*
						,pv.* 
				  FROM $tbl[ventas_clientes] vc
				  LEFT JOIN $tbl[sucursales_cliente_venta] cv on cv.id_cliente = vc.id_ventas_clientes
				  LEFT JOIN $tbl[sucursales_punto_venta] pv on pv.id_sucursales_punto_venta = cv.id_punto_venta 
				  WHERE vc.id_ventas_clientes = $id_cliente";
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

	public function delete_cliente_venta($id_cliente){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$query = "DELETE 
				  FROM $tbl[sucursales_cliente_venta]
				  WHERE id_cliente =".$id_cliente;
		$query = $this->db->query($query);
		if($query){
			return $query;
		}
	}

	public function db_update_data_cliente($data = array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$insert = $this->insert_item($tbl['sucursales_cliente_venta'], $data);
		return $insert;
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

	function sucursales_cliente_venta($id_cliente){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query = "SELECT COUNT(*) num_clientes FROM (
			      SELECT id_cliente FROM $tbl[sucursales_cliente_venta] WHERE id_cliente = $id_cliente) c";
		
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	public function bd_delete_data($data = array()){
		// DB Info
		$tbl = $this->tbl;
		
		$condicion = array('id_ventas_clientes ='=> $data['id_ventas_clientes']);
		$update = $this->update_item($tbl['ventas_clientes'],$data,'id_ventas_clientes',$condicion);
		if($update){
			return $update;
		}else{
			return false;
		}
	}
}