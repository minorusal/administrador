<?php
class proveedores_model extends Base_Model{
	public function get_proveedores($limit, $offset, $filtro="", $aplicar_limit = true){
		$filtro = ($filtro=="") ? "" : "AND ( 	ca.articulo  LIKE '%$filtro%' OR 
												cl.linea  LIKE '%$filtro%' OR 
												cm.marca  LIKE '%$filtro%' OR  
												cp.presentacion  LIKE '%$filtro%' 
												OR cu.um  LIKE '%$filtro%' 
												OR ca.clave_corta  LIKE '%$filtro%' 
												OR ca.descripcion  LIKE '%$filtro%'
											)";
		$limit = ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "	SELECT *
					FROM
						av_compras_articulos ca
					LEFT JOIN av_compras_lineas cl on cl.id_compras_linea = ca.id_compras_linea 
					LEFT JOIN av_compras_marcas cm on cm.id_compras_marca = ca.id_compras_marca
					LEFT JOIN av_compras_presentaciones cp on cp.id_compras_presentacion = ca.id_compras_presentacion
					LEFT JOIN av_compras_um cu on cu.id_compras_um = ca.id_compras_um
					WHERE ca.activo = 1 $filtro
					ORDER BY ca.id_compras_articulo
				$limit
					";
      
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	public function get_entidades(){
		$query = "SELECT * FROM av_administracion_entidades WHERE 1";
		$query = $this->db->query($query);
		if($query->num_rows>=1){
			return $query->result_array();
		}
	}
}