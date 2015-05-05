<?php
class catalogos_model extends Base_Model{
	/*PRESENTACIONES*/
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

	/*MARCAS*/

	public function get_marca_unico($id_marca){
		$query = "SELECT * FROM av_compras_marcas cm WHERE cm.id_compras_marca = $id_marca";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_marcas($limit, $offset, $filtro="", $aplicar_limit = true){
		$filtro = ($filtro=="") ? "" : "AND (
												cm.marca like '%$filtro%'
											OR 
												cm.clave_corta like '%$filtro%'
											OR
												cm.descripcion like '%$filtro%'
										) ";
		$limit = ($aplicar_limit) ?  "LIMIT $offset ,$limit " : "";
		$query = "	SELECT 
						cm.id_compras_marca
						,cm.marca
						,cm.clave_corta
						,cm.descripcion
					FROM
						av_compras_marcas cm
					WHERE cm.activo = 1 $filtro
					ORDER BY cm.id_compras_marca
					$limit";
      	
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function insert_marca($data){
		$existe = $this->row_exist('av_compras_marcas', array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string('av_compras_marcas', $data);
			$query = $this->db->query($query);

			return $query;
		}else{
			return false;
		}
	}
	public function update_marca($data, $id_marca){
		$condicion = array('id_compras_marca !=' => $id_marca, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_compras_marcas', $condicion);
		if(!$existe){
			$condicion = "id_compras_marca = $id_marca"; 
			$query = $this->db->update_string('av_compras_marcas', $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}
	/*U.M.*/
	
	public function get_um_unico($id_um){
		$query = "SELECT * FROM av_compras_um cu WHERE cu.id_compras_um = $id_um";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_um($limit, $offset, $filtro="", $aplicar_limit = true){
		$filtro = ($filtro=="") ? "" : "AND (
												cu.um like '%$filtro%'
											OR 
												cu.clave_corta like '%$filtro%'
											OR
												cu.descripcion like '%$filtro%'
										) ";
		$limit = ($aplicar_limit) ?  "LIMIT $offset ,$limit " : "";
		$query = "	SELECT 
						cu.id_compras_um
						,cu.um
						,cu.clave_corta
						,cu.descripcion
					FROM
						av_compras_um cu
					WHERE cu.activo = 1 $filtro
					ORDER BY cu.id_compras_um
					$limit";
      	
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function insert_um($data){
		$existe = $this->row_exist('av_compras_um', array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string('av_compras_um', $data);
			$query = $this->db->query($query);

			return $query;
		}else{
			return false;
		}
	}
	public function update_um($data, $id_um){
		$condicion = array('id_compras_um !=' => $id_um, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_compras_um', $condicion);
		if(!$existe){
			$condicion = "id_compras_um = $id_um"; 
			$query = $this->db->update_string('av_compras_um', $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}
	
} 
?>