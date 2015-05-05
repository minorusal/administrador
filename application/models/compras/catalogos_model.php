<?php
class catalogos_model extends Base_Model{
	public function get_presentacion_unico($id_presentacion){
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
	}
	
	

	/*PRESENTACIONES*/
	/*public function filtrar_presentaciones($data){
		$query = "SELECT 
					ca.id_cat_presentaciones
					,ca.presentaciones
					,ca.clave_corta
					,ca.descripcion
				FROM
					av_cat_presentaciones ca
				WHERE 
					ca.activo = 1
				AND (
					ca.presentaciones like '%$data%'
				OR 
					ca.clave_corta like '%$data%'
				OR
					ca.descripcion like '%$data%')";

       $query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function detalle_presentaciones($data){
		$query = "SELECT 
					ca.id_cat_presentaciones
					,ca.presentaciones
					,ca.clave_corta
					,ca.descripcion
					,ca.timestamp
					,ca.id_usuario
				FROM
					av_cat_presentaciones ca
				WHERE 
					ca.id_cat_presentaciones = $data";

       $query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_presentaciones($limit, $offset, $aplicar_limit = true){
		$limit = ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "SELECT 
					ca.id_cat_presentaciones
					,ca.presentaciones
					,ca.clave_corta
					,ca.descripcion
				FROM
					av_cat_presentaciones ca
				WHERE ca.activo = 1

				$limit";
       
       $query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_total_presentaciones(){
		$query = "SELECT 
					*
				FROM
					av_cat_presentaciones ca
				WHERE ca.activo = 1";
       	$query = $this->db->query($query);
        return $query->num_rows();
    }
	public function insert_presentaciones($data){
		$existe = $this->row_exist('av_cat_presentaciones', array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string('av_cat_presentaciones', $data);
			$query = $this->db->query($query);

			return $query;
		}else{
			return false;
		}
	}
	public function update_presentaciones($data, $id_usuario){
		$condicion = array('id_cat_presentaciones !=' => $id_usuario, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_cat_presentaciones', $condicion );
		if(!$existe){
			$condicion = "id_cat_presentaciones = $id_usuario"; 
			$query = $this->db->update_string('av_cat_presentaciones', $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}
	*/
	/*LINEAS*/
	public function filtrar_lineas($data){
		$query = "SELECT 
					c.id_cat_linea
					,c.linea
					,c.clave_corta
					,c.descripcion
				FROM
					av_cat_lineas c
				WHERE 
					c.activo = 1
				AND (
					c.linea like '%$data%'
				OR 
					c.clave_corta like '%$data%'
				OR
					c.descripcion like '%$data%')";

       $query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function detalle_lineas($data){
		$query = "SELECT 
					c.id_cat_linea
					,c.linea
					,c.clave_corta
					,c.descripcion
					,c.timestamp
					,c.id_usuario
				FROM
					av_cat_lineas c
				WHERE 
					c.id_cat_linea = $data";

       $query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_lineas($limit, $offset, $aplicar_limit = true){
		
		$limit = ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";

		$query = "SELECT 
					c.id_cat_linea
					,c.linea
					,c.clave_corta
					,c.descripcion
				FROM
					av_cat_lineas c
				WHERE c.activo = 1

				$limit";
       
       $query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_total_lineas(){
		$query = "SELECT 
					*
				FROM
					av_cat_lineas c
				WHERE c.activo = 1";
       	$query = $this->db->query($query);
        return $query->num_rows();
    }
	public function insert_lineas($data){
		$existe = $this->row_exist('av_cat_lineas', array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string('av_cat_lineas', $data);
			$query = $this->db->query($query);

			return $query;
		}else{
			return false;
		}
	}
	public function update_lineas($data, $id_usuario){
		$condicion = array('id_cat_linea !=' => $id_usuario, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_cat_lineas', $condicion );
		if(!$existe){
			$condicion = "id_cat_linea = $id_usuario"; 
			$query = $this->db->update_string('av_cat_lineas', $data, $condicion);
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