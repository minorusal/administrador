<?php
class catalogos_model extends Base_Model
{
	private $db1, $db2;
	private $tbl_almacenes, $tbl_sucursales, $tbl_tipos;
	private $tbl_pasillos;
	
	public function __construct()
	{
		parent::__construct();
		$this->db1            = $this->dbinfo[1]['db'];
		$this->tbl_almacenes  = $this->dbinfo[1]['tbl_almacen_almacenes'];
		$this->tbl_tipos      = $this->dbinfo[1]['tbl_almacen_tipos'];
		$this->tbl_pasillos   = $this->dbinfo[1]['tbl_almacen_pasillos'];
		$this->tbl_gavetas    = $this->dbinfo[1]['tbl_almacen_gavetas'];
		
		$this->db2            = $this->dbinfo[0]['db'];
		$this->tbl_sucursales = $this->dbinfo[0]['tbl_sucursales'];
	}
	/*ALMACENES*/

	/*Traer información para el listado de los almacenes*/
	public function db_get_data_almacen($data=array())
	{
		$tbl_almacenes  = $this->db1.'.'.$this->tbl_almacenes;
		$tbl_tipos      = $this->db1.'.'.$this->tbl_tipos;
		$tbl_sucursales = $this->db2.'.'.$this->tbl_sucursales;
		
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (av.almacenes like '%$filtro%' OR 
												  av.clave_corta like '%$filtro%' OR
												  av.descripcion like '%$filtro%' OR
												  su.sucursal like '%$filtro%' OR
												  ti.tipos like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "	SELECT 
						 av.id_almacen_almacenes
						,av.clave_corta
						,av.descripcion
						,av.almacenes
						,av.id_sucursal
						,su.sucursal
						,ti.tipos
					FROM $tbl_almacenes av
					
					LEFT JOIN $tbl_sucursales su on su.id_sucursal = av.id_sucursal
					LEFT JOIN $tbl_tipos ti on ti.id_almacen_tipos = av.id_almacen_tipos
					WHERE av.activo = 1 $filtro
					GROUP BY av.id_almacen_almacenes ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1)
		{
			return $query->result_array();
		}	
	}

	/*Trae la información para el formulario de edición de almacen*/
	public function get_orden_unico_almacen($id_almacen_almacenes)
	{
		$tbl_almacenes  = $this->db1.'.'.$this->tbl_almacenes;
		$query = "SELECT * FROM $tbl_almacenes WHERE id_almacen_almacenes = $id_almacen_almacenes";
		$query = $this->db->query($query);
		if($query->num_rows >= 1)
		{
			return $query->result_array();
		}
	}


	/*Actualliza la información en el formuladio de edición de almacen*/
	public function db_update_data($data=array())
	{
		$tbl_almacenes  = $this->db1.'.'.$this->tbl_almacenes;
		$condicion = array('id_almacen_almacenes !=' => $data['id_almacen_almacenes'], 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist($tbl_almacenes, $condicion);
		if(!$existe)
		{
			$condicion = "id_almacen_almacenes = ".$data['id_almacen_almacenes']; 
			$query = $this->db->update_string($tbl_almacenes, $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}
		else
		{
			return false;
		}
	}
	/*Inserta registro de almacenes*/
	public function db_insert_data($data = array())
	{
		$tbl_almacenes  = $this->db1.'.'.$this->tbl_almacenes;
		$existe = $this->row_exist($tbl_almacenes, array('clave_corta'=> $data['clave_corta']));
		if(!$existe)
		{
			$query = $this->db->insert_string($tbl_almacenes, $data);
			$query = $this->db->query($query);
			return $query;
		}
		else
		{
			return false;
		}
	}

	public function db_get_data_tipos($data=array())
	{
		$tbl_tipos      = $this->db1.'.'.$this->tbl_tipos;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$limit 	= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query  = "SELECT * FROM $tbl_tipos at";
      	$query  = $this->db->query($query);
		if($query->num_rows >= 1)
		{
			return $query->result_array();
		}	
	}

	/*PASILLOS*/
	public function db_get_data_pasillo($data=array())
	{
		$tbl_pasillos   = $this->db1.'.'.$this->tbl_pasillos;
		$tbl_almacenes  = $this->db1.'.'.$this->tbl_almacenes;
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (av.pasillos like '%$filtro%' OR
									av.descripcion like '%$filtro%' OR  
									av.clave_corta like '%$filtro%' OR
									al.almacenes like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "	SELECT 
						 av.id_almacen_pasillos
						,av.clave_corta
						,av.descripcion
						,av.pasillos
						,al.id_almacen_almacenes
						,al.almacenes
					FROM $tbl_pasillos av
					LEFT JOIN $tbl_almacenes al on al.id_almacen_almacenes = av.id_almacen_almacenes
					WHERE av.activo = 1 $filtro
					GROUP BY av.id_almacen_pasillos ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1)
		{
			return $query->result_array();
		}	
	}

	/*Trae la información para el formulario de edición de pasillo*/
	public function get_orden_unico_pasillo($id_almacen_pasillos)
	{
		$tbl_pasillos   = $this->db1.'.'.$this->tbl_pasillos;
		$query = "SELECT * FROM $tbl_pasillos WHERE id_almacen_pasillos = $id_almacen_pasillos";
		$query = $this->db->query($query);
		if($query->num_rows >= 1)
		{
			return $query->result_array();
		}
	}

	/*Actualliza la información en el formuladio de edición de pasillos*/
	public function db_update_data_pasillo($data=array())
	{
		$tbl_pasillos   = $this->db1.'.'.$this->tbl_pasillos;
		$condicion = array('id_almacen_pasillos !=' => $data['id_almacen_pasillos'], 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist($tbl_pasillos, $condicion);
		if(!$existe)
		{
			$condicion = "id_almacen_pasillos = ".$data['id_almacen_pasillos']; 
			$query = $this->db->update_string($tbl_pasillos, $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}
		else
		{
			return false;
		}
	}

	/*Inserta registro de pasillos*/
	public function db_insert_data_pasillos($data = array())
	{
		$tbl_pasillos   = $this->db1.'.'.$this->tbl_pasillos;
		$existe = $this->row_exist($tbl_pasillos, array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string($tbl_pasillos, $data);
			$query = $this->db->query($query);
			return $query;
		}
		else
		{
			return false;
		}
	}

	/*GAVETAS*/

	public function db_get_data_gaveta($data=array())
	{
		$tbl_gavetas    = $this->db1.'.'.$this->tbl_gavetas;
		$tbl_pasillos   = $this->db1.'.'.$this->tbl_pasillos;
		$tbl_almacenes  = $this->db1.'.'.$this->tbl_almacenes;
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro) ? "AND (am.almacenes like '%$filtro%' OR
									al.pasillos like '%$filtro%' OR
									av.gavetas like '%$filtro%' OR 
									av.clave_corta like '%$filtro%' OR
									av.descripcion like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "	SELECT 
						 av.id_almacen_gavetas
						,al.pasillos
						,av.id_almacen_pasillos
						,am.almacenes
						,av.id_almacen_almacenes
						,av.clave_corta
						,av.descripcion
						,av.gavetas
					FROM $tbl_gavetas av
					LEFT JOIN $tbl_pasillos al on al.id_almacen_pasillos = av.id_almacen_pasillos
					LEFT JOIN $tbl_almacenes am on am.id_almacen_almacenes = av.id_almacen_almacenes
					WHERE av.activo = 1 $filtro
					GROUP BY av.id_almacen_gavetas ASC
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1)
		{
			return $query->result_array();
		}	
	}

	/*Trae la información para el formulario de edición de gaveta*/
	public function get_orden_unico_gaveta($id_almacen_gavetas)
	{
		$tbl_gavetas    = $this->db1.'.'.$this->tbl_gavetas;
		$query = "SELECT * FROM $tbl_gavetas WHERE id_almacen_gavetas = $id_almacen_gavetas";
		$query = $this->db->query($query);
		if($query->num_rows >= 1)
		{
			return $query->result_array();
		}
	}

	/*Actualliza la información en el formuladio de edición de gavetas*/
	public function db_update_data_gaveta($data=array())
	{
		$tbl_gavetas    = $this->db1.'.'.$this->tbl_gavetas;
		$condicion = array('id_almacen_gavetas !=' => $data['id_almacen_gavetas'], 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist($tbl_gavetas, $condicion);
		if(!$existe)
		{
			$condicion = "id_almacen_gavetas = ".$data['id_almacen_gavetas']; 
			$query = $this->db->update_string($tbl_gavetas, $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}
		else
		{
			return false;
		}
	}

	/*Inserta registro de gavetas*/
	public function db_insert_data_gavetas($data = array())
	{
		$tbl_gavetas    = $this->db1.'.'.$this->tbl_gavetas;
		$existe = $this->row_exist($tbl_gavetas, array('clave_corta'=> $data['clave_corta']));
		if(!$existe)
		{
			$query = $this->db->insert_string($tbl_gavetas, $data);
			$query = $this->db->query($query);
			return $query;
		}
		else
		{
			return false;
		}
	}
}

//WHERE cp.activo = 1 $filtro