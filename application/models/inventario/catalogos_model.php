<?php
class catalogos_model extends Base_Model{
	
	/*Articulos*/
	public function filtrar_articulos($data){
		$query = "SELECT 
					ca.id_cat_articulo
					,ca.articulo
					,ca.clave_corta
					,ca.descripcion
				FROM
					av_cat_articulos ca
				WHERE 
					ca.activo = 1
				AND (
					ca.articulo like '%$data%'
				OR 
					ca.clave_corta like '%$data%'
				OR
					ca.descripcion like '%$data%')";

       $query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function detalle_articulos($data){
		$query = "SELECT 
					ca.id_cat_articulo
					,ca.articulo
					,ca.clave_corta
					,ca.descripcion
					,ca.timestamp
					,ca.id_usuario
				FROM
					av_cat_articulos ca
				WHERE 
					ca.id_cat_articulo = $data";

       $query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_articulos($limit, $offset){
		$query = "SELECT 
					ca.id_cat_articulo
					,ca.articulo
					,ca.clave_corta
					,ca.descripcion
				FROM
					av_cat_articulos ca
				WHERE ca.activo = 1

				LIMIT $offset ,$limit";
       
       $query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_total_articulos(){
		$query = "SELECT 
					*
				FROM
					av_cat_articulos ca
				WHERE ca.activo = 1";
       	$query = $this->db->query($query);
        return $query->num_rows();
    }
	public function insert_articulos($data){
		$existe = $this->row_exist('av_cat_articulos', array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string('av_cat_articulos', $data);
			$query = $this->db->query($query);

			return $query;
		}else{
			return false;
		}
	}
	public function update_articulos($data, $id_usuario){
		$condicion = array('id_cat_articulo !=' => $id_usuario, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_cat_articulos', $condicion );
		if(!$existe){
			$condicion = "id_cat_articulo = $id_usuario"; 
			$query = $this->db->update_string('av_cat_articulos', $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}


	/*Lineas*/
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
	public function get_lineas($limit, $offset){
		$query = "SELECT 
					c.id_cat_linea
					,c.linea
					,c.clave_corta
					,c.descripcion
				FROM
					av_cat_lineas c
				WHERE c.activo = 1

				LIMIT $offset ,$limit";
       
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
	  
}
?>