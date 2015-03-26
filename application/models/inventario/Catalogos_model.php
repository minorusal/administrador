<?php
class Catalogos_model extends CI_Model{
		
	function articulos($user){
		$query  = "	SELECT * from av_cat_articulos;";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result();
		}		
	}
}
?>