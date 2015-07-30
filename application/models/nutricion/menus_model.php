<?php
class menus_model extends Base_Model{
	public function get_lts_recetas( $id_sucursal ){	
		$tbl = $this->tbl;
		$query = "  SELECT 
						r.id_nutricion_receta
						,r.receta
						,r.clave_corta		
					FROM 
						$tbl[nutricion_recetas] r
					WHERE r.id_sucursal = $id_sucursal ";

		$query = $this->db->query($query);
		if($query->num_rows >=1){
			return $query->result_array();
		}else{
			return false;
		}
	}
	public function get_lts_articulos( $id_sucursal ){
		$tbl =$this->tbl;

		$query = "  SELECT
						s.sucursal
						,s.id_region
						,a.id_articulo
						,a.id_compras_articulo_precios
						,CONCAT_WS(' ', ar.articulo, p.presentacion, a.um_x_presentacion, u.um) as articulo
					FROM
						$tbl[sucursales] s
					LEFT JOIN $tbl[compras_articulos_precios] a on a.id_administracion_region = s.id_region
					LEFT JOIN $tbl[compras_articulos] ar on ar.id_compras_articulo = a.id_articulo
					LEFT JOIN $tbl[compras_presentaciones] p on p.id_compras_presentacion = a.id_presentacion
					LEFT JOIN $tbl[compras_um]  u on u.id_compras_um = ar.id_compras_um
					WHERE
						ar.id_articulo_tipo = 3
					AND	s.id_sucursal = $id_sucursal";

		//print_debug($query);
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}else{
			return false;
		}
	}

	/*Inserta registro de sucursales*/
	public function db_insert_data($data = array()){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$existe = $this->row_exist($tbl['nutricion_menu'], array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$insert = $this->insert_item($tbl['nutricion_menu'], $data,true);
			return $insert;
		}else{
			return false;
		}
	}

	public function db_insert_receta($data = array()){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$insert = $this->insert_item($tbl['nutricion_programacion_receta_menu'], $data);
		if($insert){
			return $insert;
		}else{
			return false;
		}
	}

	public function db_insert_articulo($data = array()){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$insert = $this->insert_item($tbl['nutricion_programacion_articulo_menu'], $data);
		if($insert){
			return $insert;
		}else{
			return false;
		}
	}

}
?>