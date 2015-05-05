<?php
class articulos_model extends Base_Model{

	public function insert_articulo($data){
		$existe = $this->row_exist('av_compras_articulos', array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string('av_compras_articulos', $data);
			$query = $this->db->query($query);

			return $query;
		}else{
			return false;
		}
	}
	public function update_articulo($data, $id_articulo){
		$condicion = array('id_compras_articulo !=' => $id_articulo, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_compras_articulos', $condicion);
		if(!$existe){
			$condicion = "id_compras_articulo = $id_articulo"; 
			$query = $this->db->update_string('av_compras_articulos', $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}
	public function get_articulos($limit, $offset, $filtro="", $aplicar_limit = true){
		$filtro = ($filtro=="") ? "" : "AND ( 	ca.articulos  LIKE '%$filtro%' OR 
												cl.linea  LIKE '%$filtro%' OR 
												cm.marcas  LIKE '%$filtro%' OR  
												cp.presentacion  LIKE '%$filtro%' 
												OR cu.um  LIKE '%$filtro%' 
												OR ca.clave_corta  LIKE '%$filtro%' 
												OR ca.descripcion  LIKE '%$filtro%'
											)";
		$limit = ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "	SELECT 
						ca.id_compras_articulo
						,ca.articulos
						,cl.linea
						,cm.marcas
						,cp.presentacion
						,cu.um
						,ca.clave_corta
						,ca.descripcion
					FROM
						av_compras_articulos ca
					LEFT JOIN av_compras_lineas cl on cl.id_compras_linea = ca.id_compras_linea 
					LEFT JOIN av_cat_marcas cm on cm.id_cat_marcas = ca.id_cat_marcas
					LEFT JOIN av_compras_presentaciones cp on cp.id_compras_presentacion = ca.id_compras_presentacion
					LEFT JOIN av_cat_um cu on cu.id_cat_um = ca.id_cat_um
					WHERE ca.activo = 1 $filtro
					ORDER BY ca.id_compras_articulo
				$limit
					";
      
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function get_articulo_unico($id_articulo){
		$query = "SELECT * FROM av_compras_articulos ca WHERE ca.id_compras_articulo = $id_articulo";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	
}
?>