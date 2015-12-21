<?php
class articulos_model extends Base_Model{

	public function insert_articulo($data){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$existe = $this->row_exist($tbl['compras_articulos'], array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$insert = $this->insert_item($tbl['compras_articulos'], $data);
			return $insert;
		}else{
			return false;
		}
	}
	public function update_articulo($data, $id_articulo){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$condicion = array('id_compras_articulo !=' => $id_articulo, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist($tbl['compras_articulos'], $condicion);
		if(!$existe){
			$condicion = "id_compras_articulo = $id_articulo"; 
			$update    = $this->update_item($tbl['compras_articulos'], $data, 'id_compras_articulo', $condicion);
			return $update;
		}else{
			return false;
		}
	}
	public function get_articulos($limit, $offset, $filtro="", $aplicar_limit = true){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$filtro = ($filtro=="") ? "" : "AND ( 	ca.articulo  LIKE '%$filtro%' OR 
												cl.linea  LIKE '%$filtro%' OR
												cm.marca  LIKE '%$filtro%' OR  
												cu.um  LIKE '%$filtro%' OR 
												ca.clave_corta  LIKE '%$filtro%' OR 
												ca.descripcion  LIKE '%$filtro%'
											)";
		$limit = ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "	SELECT 
						ca.id_compras_articulo
						,ca.articulo
						,cl.linea
						,cm.marca
						,cu.um
						,ca.clave_corta
						,ca.descripcion
					FROM $tbl[compras_articulos] ca
					LEFT JOIN $tbl[compras_lineas] cl on cl.id_compras_linea = ca.id_compras_linea 
					LEFT JOIN $tbl[compras_marcas] cm on cm.id_compras_marca = ca.id_compras_marca
					LEFT JOIN $tbl[compras_um] cu on cu.id_compras_um = ca.id_compras_um
					WHERE ca.activo = 1 $filtro
					ORDER BY ca.id_compras_articulo
				$limit";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	public function db_get_articulos_usados($id_articulo){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$query = "SELECT u.id_area
				  FROM $tbl[administracion_areas] a
				  LEFT JOIN $tbl[usuarios] u on a.id_administracion_areas = u.id_area
				  WHERE a.activo = 1 AND a.id_administracion_areas = $id_area ";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_articulo_unico($id_articulo){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[compras_articulos] ca WHERE ca.id_compras_articulo = $id_articulo";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

}
?>