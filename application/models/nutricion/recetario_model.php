<?php
class recetario_model extends Base_Model{

	public function get_data($data = array()){	
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (array_key_exists('aplicar_limit', $data)) ? $data['aplicar_limit'] : false;
		$unique         = (array_key_exists('unique', $data) ? $data['unique'] : false);

		
		$unique = ($unique) ? "AND r.id_nutricion_receta = $unique" : "";
		$filtro = ($filtro) ? "AND (f.familia like '%$filtro%' OR
									r.receta like '%$filtro%' OR
									r.clave_corta like '%$filtro%' OR
									r.porciones like '%$filtro%' OR
									r.preparacion like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "	SELECT 
						f.familia
						,f.id_nutricion_familia
						,r.*
					FROM $tbl[nutricion_recetas] r
					LEFT JOIN  $tbl[nutricion_familias] f ON f.id_nutricion_familia  = r.id_nutricion_familia
					WHERE r.activo = 1 $unique $filtro 
					$limit 
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	//Función que obtiene las recetas filtradas por familias
	public function get_data_recetas_x_familia($id_familia){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$query="SELECT * FROM $tbl[nutricion_recetas] r WHERE r.id_nutricion_familia = $id_familia";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	public function get_data_unique($data = array()){	
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (array_key_exists('aplicar_limit', $data)) ? $data['aplicar_limit'] : false;
		$unique         = (array_key_exists('unique', $data) ? $data['unique'] : false);

		
		$unique = ($unique) ? "AND r.id_nutricion_receta = $unique" : "";
		$filtro = ($filtro) ? "AND (f.familia like '%$filtro%' OR
									r.receta like '%$filtro%' OR
									r.clave_corta like '%$filtro%' OR
									r.porciones like '%$filtro%' OR
									r.preparacion like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "	SELECT 
						f.familia
						,f.id_nutricion_familia
						,r.*
						,ri.id_compras_articulo
					FROM $tbl[nutricion_recetas] r
					LEFT JOIN  $tbl[nutricion_familias] f ON f.id_nutricion_familia  = r.id_nutricion_familia
					LEFT JOIN $tbl[nutricion_recetas_articulos] ri on r.id_nutricion_receta = ri.id_nutricion_receta
					WHERE r.activo = 1 $unique $filtro 
					$limit 
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	public function get_insumos($data = array()){
		$tbl = $this->tbl;
		$filtro = (empty($data)) ? '' : ' AND '.array_2_string_format($data);



		$query = "	SELECT 
						 ca.id_compras_articulo
						,ca.articulo
						,cl.linea
						,cu.um
						,ca.clave_corta
						,ca.descripcion
					FROM $tbl[compras_articulos] ca
					LEFT JOIN $tbl[compras_lineas] cl on cl.id_compras_linea = ca.id_compras_linea 
					LEFT JOIN $tbl[compras_um] cu on cu.id_compras_um = ca.id_compras_um
					WHERE ca.activo = 1 AND ca.id_articulo_tipo = 2 $filtro
					ORDER BY ca.id_compras_articulo";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function insert_receta($data = array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$existe = $this->row_exist($tbl['nutricion_recetas'], array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$insert = $this->insert_item($tbl['nutricion_recetas'], $data, true);
			return $insert;
		}else{
			return false;
		}
	}
	public function insert_receta_articulos($data= array())	{
		$tbl = $this->tbl;
		$query = $this->db->insert_batch($tbl['nutricion_recetas_articulos'], $data);
		
	}
}

?>