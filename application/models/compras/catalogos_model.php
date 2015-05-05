<?php
class catalogos_model extends Base_Model{
	/*PRESENTACIONES*/
	/*public function get_presentacion_unico($id_presentacion){
		$query = "SELECT * FROM av_cat_presentaciones cp WHERE cp.id_cat_presentaciones = $id_presentacion";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_presentaciones($limit, $offset, $filtro="", $aplicar_limit = true){
		$filtro = ($filtro=="") ? "" : "AND (
												cp.presentaciones like '%$filtro%'
											OR 
												cp.clave_corta like '%$filtro%'
											OR
												cp.descripcion like '%$filtro%'
										) ";
		$limit = ($aplicar_limit) ?  "LIMIT $offset ,$limit " : "";
		$query = "	SELECT 
						cp.id_cat_presentaciones
						,cp.presentaciones
						,cp.clave_corta
						,cp.descripcion
					FROM
						av_cat_presentaciones cp
					WHERE cp.activo = 1 $filtro
					ORDER BY cp.id_cat_presentaciones
					$limit";
      	
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function insert_presentacion($data){
		$existe = $this->row_exist('av_cat_presentaciones', array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string('av_cat_presentaciones', $data);
			$query = $this->db->query($query);

			return $query;
		}else{
			return false;
		}
	}
	public function update_presentaciones($data, $id_presentacion){
		$condicion = array('id_cat_presentaciones !=' => $id_presentacion, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_cat_presentaciones', $condicion);
		if(!$existe){
			$condicion = "id_cat_presentaciones = $id_presentacion"; 
			$query = $this->db->update_string('av_cat_presentaciones', $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}*/
	public function get_presentacion_unico($id_presentacion){
		$query = "SELECT * FROM av_compras_presentaciones cp WHERE cp.id_compras_presentacion = $id_presentacion";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_presentaciones($limit, $offset, $filtro="", $aplicar_limit = true){
		$filtro = ($filtro=="") ? "" : "AND (
												cp.presentacion like '%$filtro%'
											OR 
												cp.clave_corta like '%$filtro%'
											OR
												cp.descripcion like '%$filtro%'
										) ";
		$limit = ($aplicar_limit) ?  "LIMIT $offset ,$limit " : "";
		$query = "	SELECT 
						cp.id_compras_presentacion
						,cp.presentacion
						,cp.clave_corta
						,cp.descripcion
					FROM
						av_compras_presentaciones cp
					WHERE cp.activo = 1 $filtro
					ORDER BY cp.id_compras_presentacion
					$limit";
      	
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function insert_presentacion($data){
		$existe = $this->row_exist('av_compras_presentaciones', array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string('av_compras_presentaciones', $data);
			$query = $this->db->query($query);

			return $query;
		}else{
			return false;
		}
	}
	public function update_presentaciones($data, $id_presentacion){
		$condicion = array('id_compras_presentacion !=' => $id_presentacion, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_compras_presentaciones', $condicion);
		if(!$existe){
			$condicion = "id_compras_presentacion = $id_presentacion"; 
			$query = $this->db->update_string('av_compras_presentaciones', $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}

	/*LINEAS*/
	public function get_linea_unico($id_linea){
		$query = "SELECT * FROM av_compras_lineas cl WHERE cl.id_compras_linea = $id_linea";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_lineas($limit, $offset, $filtro="", $aplicar_limit = true){
		$filtro = ($filtro=="") ? "" : "AND (
												cl.linea like '%$filtro%'
											OR 
												cl.clave_corta like '%$filtro%'
											OR
												cl.descripcion like '%$filtro%'
										) ";
		$limit = ($aplicar_limit) ?  "LIMIT $offset ,$limit " : "";
		$query = "	SELECT 
						cl.id_compras_linea
						,cl.linea
						,cl.clave_corta
						,cl.descripcion
					FROM
						av_compras_lineas cl
					WHERE cl.activo = 1 $filtro
					ORDER BY cl.id_compras_linea
					$limit";
      	
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function insert_linea($data){
		$existe = $this->row_exist('av_compras_lineas', array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string('av_compras_lineas', $data);
			$query = $this->db->query($query);

			return $query;
		}else{
			return false;
		}
	}
	public function update_linea($data, $id_linea){
		$condicion = array('id_compras_linea !=' => $id_linea, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_compras_lineas', $condicion);
		if(!$existe){
			$condicion = "id_compras_linea = $id_linea"; 
			$query = $this->db->update_string('av_compras_lineas', $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}

	/*U.M.*/
	public function filtrar_um($data){
		$query = "SELECT 
					c.id_cat_um
					,c.um
					,c.clave_corta
					,c.descripcion
				FROM
					av_cat_um c
				WHERE 
					c.activo = 1
				AND (
					c.um like '%$data%'
				OR 
					c.clave_corta like '%$data%'
				OR
					c.descripcion like '%$data%')";

       $query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function detalle_um($data){
		$query = "SELECT 
					c.id_cat_um
					,c.um
					,c.clave_corta
					,c.descripcion
					,c.timestamp
					,c.id_usuario
				FROM
					av_cat_um c
				WHERE 
					c.id_cat_um = $data";

       $query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_um($limit, $offset, $aplicar_limit = true){
		$limit = ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "SELECT 
					c.id_cat_um
					,c.um
					,c.clave_corta
					,c.descripcion
				FROM
					av_cat_um c
				WHERE c.activo = 1

				$limit";
       
       $query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_total_um(){
		$query = "SELECT 
					*
				FROM
					av_cat_um c
				WHERE c.activo = 1";
       	$query = $this->db->query($query);
        return $query->num_rows();
    }
	public function insert_um($data){
		$existe = $this->row_exist('av_cat_um', array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string('av_cat_um', $data);
			$query = $this->db->query($query);

			return $query;
		}else{
			return false;
		}
	}
	public function update_um($data, $id_usuario){
		$condicion = array('id_cat_um !=' => $id_usuario, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_cat_um', $condicion );
		if(!$existe){
			$condicion = "id_cat_um = $id_usuario"; 
			$query = $this->db->update_string('av_cat_um', $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}

	/*MARCAS*/
	public function filtrar_marcas($data){
		$query = "SELECT 
					c.id_cat_marcas
					,c.marcas
					,c.clave_corta
					,c.descripcion
				FROM
					av_cat_marcas c
				WHERE 
					c.activo = 1
				AND (
					c.marcas like '%$data%'
				OR 
					c.clave_corta like '%$data%'
				OR
					c.descripcion like '%$data%')";

       $query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function detalle_marcas($data){
		$query = "SELECT 
					c.id_cat_marcas
					,c.marcas
					,c.clave_corta
					,c.descripcion
					,c.timestamp
					,c.id_usuario
				FROM
					av_cat_marcas c
				WHERE 
					c.id_cat_marcas = $data";

       $query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_marcas($limit, $offset, $aplicar_limit = true){
		$limit = ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "SELECT 
					c.id_cat_marcas
					,c.marcas
					,c.clave_corta
					,c.descripcion
				FROM
					av_cat_marcas c
				WHERE c.activo = 1

				$limit";
       
       $query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_total_marcas(){
		$query = "SELECT 
					*
				FROM
					av_cat_marcas c
				WHERE c.activo = 1";
       	$query = $this->db->query($query);
        return $query->num_rows();
    }
	public function insert_marcas($data){
		$existe = $this->row_exist('av_cat_marcas', array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string('av_cat_marcas', $data);
			$query = $this->db->query($query);

			return $query;
		}else{
			return false;
		}
	}
	public function update_marcas($data, $id_usuario){
		$condicion = array('id_cat_marcas !=' => $id_usuario, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_cat_marcas', $condicion );
		if(!$existe){
			$condicion = "id_cat_marcas = $id_usuario"; 
			$query = $this->db->update_string('av_cat_marcas', $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}
} 
?>