<?php
class catalogos_model extends CI_Model{
	function filtrar_articulos($data){
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

	function detalle_articulos($data){
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

	function get_articulos($limit, $offset){
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
	  
}
?>