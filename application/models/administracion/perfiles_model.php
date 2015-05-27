<?php
class perfiles_model extends Base_Model
{		
	public function get_id_menu_1($menu)
	{
		$tbl =  $this->dbinfo[0]['db'].'.'. $this->dbinfo[0]['tbl_menu_n1'];
		$query = "SELECT * FROM $tbl WHERE menu_n1 like '%$menu%'";
		$query = $this->db->query($query);
		if($query->num_rows == 1){
			return $query->result_array();
			//return true;
		}
		else
		{
			return false;
		}
	}
	public function get_id_menu_2($menu)
	{
		$tbl =  $this->dbinfo[0]['db'].'.'. $this->dbinfo[0]['tbl_menu_n2'];
		$query = "SELECT * FROM $tbl WHERE menu_n2 like '%$menu%'";
		$query = $this->db->query($query);
		if($query->num_rows == 1){
			//return $query->result_array();
			return true;
		}
		else
		{
			return false;
		}
	}
	public function get_id_menu_3($menu)
	{
		$tbl =  $this->dbinfo[0]['db'].'.'. $this->dbinfo[0]['tbl_menu_n3'];
		$query = "SELECT * FROM $tbl WHERE menu_n3 like '%$menu%'";
		$query = $this->db->query($query);
		if($query->num_rows == 1){
			//return $query->result_array();
			return true;
		}
		else
		{
			return false;
		}
	}
}
