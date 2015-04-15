<?php
class catalogos_model extends CI_Model{
		
	function get_articulos($limit, $offset){
		
		$query = "SELECT 
					ca.id_cat_articulo
					,ca.articulo
					,ca.clave_corta
					,if(ca.descripcion = '' , 'Sin Descripción', ca.descripcion) as descripcion
					,ca.timestamp
				FROM
					av_cat_articulos ca
				WHERE ca.activo = 1

				LIMIT $offset ,$limit";
       
       $query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	
	public function insert_articulo($data){
		$existe = $this->row_exist('av_cat_articulos', array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string('av_cat_articulos', $data);
			$query = $this->db->query($query);

			return $query;
		}else{
			return false;
		}
		
	}
	
	public function get_total_articulos(){
        $consulta = $this->db->get('av_cat_articulos');
        return $consulta->num_rows();
    }

    
}
?>