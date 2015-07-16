<?php
class recetario_model extends Base_Model{
	public function get_data($data = array()){	
		$tbl = $this->tbl;
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
									s.sucursal like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "	SELECT 
						f.familia
						,f.id_nutricion_familia
						,r.*
						,s.sucursal
					FROM $tbl[nutricion_recetas] r
					LEFT JOIN  $tbl[nutricion_familias] f ON f.id_nutricion_familia  = r.id_nutricion_familia
					LEFT JOIN $tbl[sucursales] s ON s.id_sucursal = r.id_sucursal
					WHERE r.activo = 1 $unique $filtro 
					$limit 
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}


	public function get_data_receta_vnutricion($id_receta){
		$tbl = $this->tbl;
		$query = "SELECT 
					 s.sucursal
					,nf.familia
					,nr.receta
					,nr.porciones
				  FROM $tbl[nutricion_recetas] nr
				  LEFT JOIN $tbl[sucursales] s on s.id_sucursal = nr.id_sucursal
				  LEFT JOIN $tbl[nutricion_familias] nf on nf.id_nutricion_familia = nr.id_nutricion_familia
				  
				  WHERE nr.id_nutricion_receta = $id_receta";
		print_debug($query);
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	public function get_data_recetas_x_familia($id_familia){
			

		$tbl = $this->tbl;
		
		$query="SELECT * FROM $tbl[nutricion_recetas] r WHERE r.id_nutricion_familia = $id_familia $filtro";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	//,vn.cantidad_sugerida
	public function get_data_unique($data = array()){	
				
		$tbl = $this->tbl;
		
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
									s.sucursal like '%$filtro%')" : "";
		$limit  = ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query  = "	SELECT 
						f.familia
						,f.id_nutricion_familia
						,r.*
						,ri.id_compras_articulo
						,ri.porciones as porciones_articulo
						,ca.articulo
						,ap.costo_x_um
						,cu.um
						,s.id_sucursal
						,s.sucursal
					FROM $tbl[nutricion_recetas] r
					LEFT JOIN  $tbl[nutricion_familias] f ON f.id_nutricion_familia  = r.id_nutricion_familia
					LEFT JOIN  $tbl[nutricion_recetas_articulos] ri on r.id_nutricion_receta = ri.id_nutricion_receta
					LEFT JOIN  $tbl[compras_articulos] ca ON ca.id_compras_articulo = ri.id_compras_articulo
					LEFT JOIN  $tbl[compras_um] cu on cu.id_compras_um = ca.id_compras_um
					LEFT JOIN  $tbl[sucursales] s ON s.id_sucursal = r.id_sucursal
					LEFT JOIN  (SELECT 
									ap.id_articulo, 
									ap.costo_x_um,
									ap.id_administracion_region
								FROM 
									$tbl[compras_articulos_precios] ap
								WHERE 
									ap.articulo_default = 1
					) ap ON (ap.id_articulo = ca.id_compras_articulo AND ap.id_administracion_region = s.id_region)

					WHERE r.activo = 1 $unique $filtro";

					
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
		
		$tbl = $this->tbl;
		$existe = $this->row_exist($tbl['nutricion_recetas'], array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$insert = $this->insert_item($tbl['nutricion_recetas'], $data, true);
			return $insert;
		}else{
			return false;
		}
	}
	public function insert_receta_articulos($data= array(), $id_receta = false){
		$tbl = $this->tbl;
		if($id_receta){
			$condicion = array("id_nutricion_receta" => $id_receta);
			$this->db->where($condicion);
			$query = $this->db->delete($tbl['nutricion_recetas_articulos']);	
		}
		$query = $this->db->insert_batch($tbl['nutricion_recetas_articulos'], $data);
	}
	public function update_receta($data=array()){
			
		$tbl = $this->tbl;
		
		$condicion = array('id_nutricion_receta !=' => $data['id_nutricion_receta'], 'clave_corta = '=> $data['clave_corta']); 
		$existe    = $this->row_exist($tbl['nutricion_recetas'], $condicion);
		if(!$existe){
			$condicion = "id_nutricion_receta = ".$data['id_nutricion_receta']; 
			$update    = $this->update_item($tbl['nutricion_recetas'], $data, 'id_nutricion_receta', $condicion);
			return $update;
		}else{
			return false;
		}
	}
}
?>