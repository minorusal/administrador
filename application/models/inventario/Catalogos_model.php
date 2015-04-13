<?php
class Catalogos_model extends CI_Model{
		
	function get_articulos($limit, $offset){
		$query = $this->db->get('av_cat_articulos', $limit, $offset);
        if ($query->num_rows() > 0){
        	return $query->result_array();
		}	
	}
	
	public function get_total_articulos(){
        $consulta = $this->db->get('av_cat_articulos');
        return $consulta->num_rows();
    }
}
?>