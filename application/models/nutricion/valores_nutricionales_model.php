<?php
class valores_nutricionales_model extends Base_Model{

	public function get_valores_nutricionales_default($data = array()){
		// DB Info		
		$tbl = $this->tbl;
		// Filtro
		$buscar = (array_key_exists('buscar',$data))?$data['buscar']:false;
		$filtro = ($buscar) ? "AND ( 	ar.articulo  LIKE '%$buscar%')" : "";
		
		$limit 			= (array_key_exists('limit',$data)) ?$data['limit']:0;
		$offset 		= (array_key_exists('offset',$data))?$data['offset']:0;
		$aplicar_limit 	= (array_key_exists('aplicar_limit',$data)) ? $data['aplicar_limit'] : false;
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		
		// Query
		$query = "	SELECT *
					FROM $tbl[nutricion_valores_nutricionales] va,
					     $tbl[compras_articulos] ar
					WHERE  va.id_compras_articulos = ar.id_compras_articulo AND
						   va.activo = 1 $filtro
					GROUP BY va.id_compras_articulos ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	public function get_valores_nutricionales_unico($id_articulo){
		// DB Info
		$tbl = $this->tbl;
		//print_debug($tbl['nutricion_valores_nutricionales']);
		$condicion = array('id_compras_articulos =' => $id_articulo); 
		$existe    = $this->row_exist($tbl['nutricion_valores_nutricionales'], $condicion);
		if($existe){
			$query = "SELECT * FROM $tbl[nutricion_valores_nutricionales] WHERE id_compras_articulos = $id_articulo";
			$query = $this->db->query($query);
			if($query->num_rows >= 1){
				return $query->result_array();
			}
		}
		else
		{
			return false;
		}
	}

	/*Actualiza la información en el formuladio de edición de la tabla nutricion_valores_nutricionales*/
	public function db_update_data($data=array()){
		//print_debug($data);
		// DB Info
		$tbl = $this->tbl;
		// Query
		$condicion = array('id_compras_articulos =' => $data['id_compras_articulos']); 
		$existe    = $this->row_exist($tbl['nutricion_valores_nutricionales'], $condicion);
		if(!$existe){		
			$insert = $this->insert_item($tbl['nutricion_valores_nutricionales'], $data);
			return $insert;
		}else{
			$insert = $this->update_item($tbl['nutricion_valores_nutricionales'], $data, 'id_compras_articulos', $condicion);
			return $insert;
		}
	}
}