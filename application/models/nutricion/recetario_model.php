<?php
class recetario_model extends Base_Model{

	public function get_data($data = array()){	
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		
		$filtro = ($filtro) ? "AND (f.familia like '%$filtro%' OR
									r.receta like '%$filtro%' OR
									r.clave_corta like '%$filtro%' OR
									r.porciones like '%$filtro%' OR
									r.preparacion like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "	SELECT *
					FROM $tbl[nutricion_recetas] r
					LEFT JOIN  $tbl[nutricion_familias] f ON f.id_nutricion_familia  = r.id_nutricion_familia
					WHERE r.activo = 1 $filtro
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
}

?>