<?php
class listado_precios_model extends Base_Model{

	private $db1;
	private $tbl1, $tbl2, $tbl3, $tbl4, $tbl5, $tbl6,$tbl7;
	
	public function __construct(){
		parent::__construct();
		$this->db1    = $this->dbinfo[1]['db'];
		$this->tbl1   = $this->dbinfo[1]['tbl_compras_articulos_precios'];
		$this->tbl2   = $this->dbinfo[1]['tbl_compras_articulos'];
		$this->tbl3   = $this->dbinfo[1]['tbl_compras_proveedores'];
		$this->tbl4   = $this->dbinfo[1]['tbl_compras_marcas'];
		$this->tbl5   = $this->dbinfo[1]['tbl_compras_presentaciones'];
		$this->tbl6   = $this->dbinfo[1]['tbl_compras_embalaje'];
		$this->tbl7   = $this->dbinfo[1]['tbl_compras_um'];
	}
	public function db_get_data($data=array()){
		$tbl1  = $this->tbl1;
		$tbl2  = $this->tbl2;
		$tbl3  = $this->tbl3;
		$tbl4  = $this->tbl4;
		$tbl5  = $this->tbl5;
		$tbl6  = $this->tbl6;
		// Filtro

		$filtro 		= (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$filtro = ($filtro!="") ? "AND (a.cantidad_presentacion_embalaje LIKE '$filtro%' OR
										a.cantidad_um_presentacion 		 LIKE '$filtro%' OR
										b.articulo  	   LIKE '$filtro%' OR
										c.nombre_comercial LIKE '$filtro%' OR
										d.marca 		   LIKE '$filtro%' OR
										e.presentacion 	   LIKE '$filtro%' OR
										f.embalaje 	       LIKE '$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		// Query
		$query="SELECT 
					a.id_compras_articulo_precios
					,a.id_articulo
					,a.id_proveedor
					,a.id_marca
					,a.id_presentacion
					,a.id_embalaje
					,a.cantidad_presentacion_embalaje
					,a.cantidad_um_presentacion
					,a.precio_proveedor
					,b.articulo
					,c.nombre_comercial
					,d.marca
					,e.presentacion
					,f.embalaje
				from $tbl1 a 
				LEFT JOIN $tbl2 b on a.id_articulo  	= b.id_compras_articulo
				LEFT JOIN $tbl3 c on a.id_proveedor 	= c.id_compras_proveedor
				LEFT JOIN $tbl4 d on a.id_marca			= d.id_compras_marca
				LEFT JOIN $tbl5 e on a.id_presentacion	= e.id_compras_presentacion
				LEFT JOIN $tbl6 f on a.id_embalaje    	= f.id_compras_embalaje
				WHERE a.activo = 1 AND 1  $filtro
				GROUP BY a.id_compras_articulo_precios ASC
				$limit";
      	// Execute querie
				//echo $query;
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function db_insert_data($data = array()){
		$tbl1  = $this->tbl1;
		$insert = $this->insert_item($tbl1, $data);
		return $insert;
	}
	public function get_data_unico($id_compras_articulo_precio){
		$tbl1  = $this->tbl1;
		$query = "SELECT * FROM $tbl1 WHERE id_compras_articulo_precios = $id_compras_articulo_precio";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function db_update_data($data=array()){
		$tbl1  = $this->tbl1;
		$condicion = "id_compras_articulo_precios = ".$data['id_compras_articulo_precios'];
		$update = $this->update_item($tbl1, $data, 'id_compras_articulo_precios', $condicion);
		return $update;
	}
	public function get_articulos_um($id_compras_articulos){
		$tbl2  = $this->tbl2;
		$tbl7  = $this->tbl7;
		
		$query="SELECT 
					a.id_compras_articulo,
					a.clave_corta,
					a.id_compras_um as id_unidad_medida,
					b.id_compras_um,
					b.um,
					b.clave_corta as cv_um
				FROM 
					$tbl2 a
				LEFT JOIN $tbl7 b
					ON 
						a.id_compras_um = b.id_compras_um
				WHERE 
					a.id_compras_articulo= $id_compras_articulos ";
		//echo $query;
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
}
?>