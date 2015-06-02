<?php
class catalogos_model extends Base_Model{

	private $db1;
	private $tbl_presentaciones, $tbl_lineas, $tbl_marcas,$tbl_um, $tbl_embalaje;
	
	public function __construct()
	{
		parent::__construct();
		$this->db1                = $this->dbinfo[1]['db'];
		$this->tbl_presentaciones = $this->dbinfo[1]['tbl_compras_presentaciones'];
		$this->tbl_lineas         = $this->dbinfo[1]['tbl_compras_lineas'];
		$this->tbl_marcas         = $this->dbinfo[1]['tbl_compras_marcas'];
		$this->tbl_um        	  = $this->dbinfo[1]['tbl_compras_um'];
		$this->tbl_embalaje       = $this->dbinfo[1]['tbl_compras_embalaje'];
	}


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
			$insert = $this->insert_item('av_compras_presentaciones', $data);
			return $insert;
		}else{
			return false;
		}
	}
	public function update_presentaciones($data, $id_presentacion){
		$condicion = array('id_compras_presentacion !=' => $id_presentacion, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_compras_presentaciones', $condicion);
		if(!$existe){
			$condicion = "id_compras_presentacion = $id_presentacion"; 
			$data['id_compras_presentacion'] =  $id_presentacion;
			$update    = $this->update_item('av_compras_presentaciones', $data, 'id_compras_presentacion', $condicion);
			return $update;
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
			$insert = $this->insert_item('av_compras_lineas', $data);
			return $insert;
		}else{
			return false;
		}
	}
	public function update_linea($data, $id_linea){
		$condicion = array('id_compras_linea !=' => $id_linea, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_compras_lineas', $condicion);
		if(!$existe){
			$condicion = "id_compras_linea = $id_linea"; 
			$data['id_compras_linea'] =  $id_linea;
			$update    = $this->update_item('av_compras_lineas', $data, 'id_compras_linea', $condicion);
			return $update;
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
			$insert = $this->insert_item('av_compras_marcas', $data);
			return $insert;
		}else{
			return false;
		}
	}
	public function update_marca($data, $id_marca){
		$condicion = array('id_compras_marca !=' => $id_marca, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_compras_marcas', $condicion);
		if(!$existe){
			$condicion = "id_compras_marca = $id_marca"; 
			$data['id_compras_marca'] =  $id_marca;
			$update    = $this->update_item('av_compras_marcas', $data, 'id_compras_marca', $condicion);
			return $update;
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
			$insert = $this->insert_item('av_compras_um', $data);
			return $insert;
		}else{
			return false;
		}
	}
	public function update_um($data, $id_um){
		$condicion = array('id_compras_um !=' => $id_um, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_compras_um', $condicion);
		if(!$existe){
			$condicion = "id_compras_um = $id_um"; 
			$data['id_compras_um'] =  $id_um;
			$update    = $this->update_item('av_compras_um', $data, 'id_compras_um', $condicion);
			return $update;
		}else{
			return false;
		}
	}

	/*EMBALAJE*/
	public function get_embalaje($data=array()){

		$tbl_embalaje  = $this->db1.'.'.$this->tbl_embalaje;

		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;


		$filtro = ($filtro=="") ? "" : "AND (ce.embalaje like '%$filtro%' OR 
											 ce.clave_corta like '%$filtro%'OR
										     ce.descripcion like '%$filtro%') ";
		$limit = ($aplicar_limit) ?  "LIMIT $offset ,$limit " : "";
		$query = "	SELECT 
						ce.id_compras_embalaje
						,ce.embalaje
						,ce.clave_corta
						,ce.descripcion
					FROM
						$tbl_embalaje ce
					WHERE ce.activo = 1 $filtro
					ORDER BY ce.id_compras_embalaje
					$limit";
      	
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function get_embalaje_unico($id_embalaje){
		$tbl_embalaje  = $this->db1.'.'.$this->tbl_embalaje;
		$query = "SELECT * FROM $tbl_embalaje ce WHERE ce.id_compras_embalaje = $id_embalaje";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function insert_embalaje($data=array()){
		$tbl_embalaje  = $this->db1.'.'.$this->tbl_embalaje;		
		$existe = $this->row_exist($tbl_embalaje, array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string($tbl_embalaje, $data);
			$query = $this->db->query($query);

			return $query;
		}else{
			return false;
		}
	}
	public function update_embalaje($data, $id_embalaje){		
		$tbl_embalaje  = $this->db1.'.'.$this->tbl_embalaje;		

		$condicion = array('id_compras_embalaje !=' => $id_embalaje, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist($tbl_embalaje, $condicion);
		if(!$existe){
			$condicion = "id_compras_embalaje = $id_embalaje"; 
			$query = $this->db->update_string($tbl_embalaje, $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}

	/*ARTICULOS*/
	public function insert_articulo($data){
		$existe = $this->row_exist('av_compras_articulos', array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string('av_compras_articulos', $data);
			$query = $this->db->query($query);

			return $query;
		}else{
			return false;
		}
	}
	public function update_articulo($data, $id_articulo){
		$condicion = array('id_compras_articulo !=' => $id_articulo, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_compras_articulos', $condicion);
		if(!$existe){
			$condicion = "id_compras_articulo = $id_articulo"; 
			$query = $this->db->update_string('av_compras_articulos', $data, $condicion);
			//print_debug($query);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}
	public function get_articulos($limit, $offset, $filtro="", $aplicar_limit = true){
		$filtro = ($filtro=="") ? "" : "AND ( 	ca.articulo  LIKE '%$filtro%' OR 
												cl.linea  LIKE '%$filtro%' OR 
												cu.um  LIKE '%$filtro%' OR 
												ca.clave_corta  LIKE '%$filtro%' OR 
												ca.descripcion  LIKE '%$filtro%'
											)";
		$limit = ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "	SELECT 
						ca.id_compras_articulo
						,ca.articulo
						,cl.linea
						,cu.um
						,ca.clave_corta
						,ca.descripcion
					FROM
						av_compras_articulos ca
					LEFT JOIN av_compras_lineas cl on cl.id_compras_linea = ca.id_compras_linea 
					LEFT JOIN av_compras_um cu on cu.id_compras_um = ca.id_compras_um
					WHERE ca.activo = 1 $filtro
					ORDER BY ca.id_compras_articulo
				$limit
					";
      	//print_debug($query);
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function get_articulo_unico($id_articulo){
		$query = "SELECT * FROM av_compras_articulos ca WHERE ca.id_compras_articulo = $id_articulo";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
} 
?>