<?php
class Catalogos_model extends CI_Model{
		
	function get_articulos($limit, $offset){
		/*$query  = "	SELECT * from av_cat_articulos;";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result();
		}	*/

		$query = $this->db->get('av_cat_articulos', $limit, $offset);
        if ($query->num_rows() > 0) 
        {
        	
        	return $query->result_array();
		
		}	
	}

	/*function get_articulos($porpagina,$segmento){
	    $query = $this->db->get('av_cat_articulos',$porpagina,$segmento);
	    if( $query->num_rows > 0 )
	      return $query->result();
	    else
	      return FALSE;
	}*/
	 
	
	 public function get_total_articulos() 
    {
    	
        $consulta = $this->db->get('av_cat_articulos');
        return $consulta->num_rows();
		
    }
}
?>