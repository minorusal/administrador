<?php
class listado_precios_model extends Base_Model{

	private $vars;
	private $db1,$db2;
	private $tbl;

	public function __construct(){
		parent::__construct();		
		$this->vars		= new config_vars();
        $this->vars->load_vars('assets/cfg/dbmodel.cfg');
		$this->db1 = $this->vars->db['db1'];
		$this->tbl['sucursales'] = $this->db1.'.'.$this->vars->db['db1_tbl_sucursales'];
        $this->db2 = $this->vars->db['db2'];	
        $this->tbl['administracion_entidades'] = $this->db2.'.'.$this->vars->db['db2_tbl_administracion_entidades'];	
		$this->tbl['compras_articulos'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_articulos'];
		$this->tbl['compras_articulos_precios'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_articulos_precios'];
		$this->tbl['compras_lineas'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_lineas'];
		$this->tbl['compras_marcas'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_marcas'];
		$this->tbl['compras_ordenes_tipo'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_ordenes_tipo'];
		$this->tbl['compras_ordenes'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_ordenes'];
		$this->tbl['compras_ordenes_articulos'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_ordenes_articulos'];
		$this->tbl['compras_ordenes_estatus'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_ordenes_estatus'];
		$this->tbl['compras_presentaciones'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_presentaciones'];
		$this->tbl['compras_proveedores'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_proveedores'];
		$this->tbl['compras_proveedores_articulos'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_proveedores_articulos'];
		$this->tbl['compras_um'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_um'];
		$this->tbl['compras_embalaje'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_embalaje'];
		$this->tbl['vw_compras_orden_proveedores'] = $this->db2.'.'.$this->vars->db['db2_vw_compras_orden_proveedores'];
		$this->tbl['vw_articulos'] = $this->db2.'.'.$this->vars->db['db2_vw_articulos'];
		$this->tbl['vw_proveedores_articulos'] = $this->db2.'.'.$this->vars->db['db2_vw_proveedores_articulos'];
	}

	public function db_get_data($data=array()){
		// DB Info
		$tbl = $this->tbl;
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
					,a.presentacion_x_embalaje
					,a.um_x_embalaje
					,a.um_x_presentacion
					,a.costo_sin_impuesto
					,b.articulo
					,c.nombre_comercial
					,d.marca
					,e.presentacion
					,f.embalaje
				from $tbl[compras_articulos_precios] a 
				LEFT JOIN $tbl[compras_articulos] b on a.id_articulo  	= b.id_compras_articulo
				LEFT JOIN $tbl[compras_proveedores] c on a.id_proveedor 	= c.id_compras_proveedor
				LEFT JOIN $tbl[compras_marcas] d on a.id_marca			= d.id_compras_marca
				LEFT JOIN $tbl[compras_presentaciones] e on a.id_presentacion	= e.id_compras_presentacion
				LEFT JOIN $tbl[compras_embalaje] f on a.id_embalaje    	= f.id_compras_embalaje
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
		// DB Info
		$tbl = $this->tbl;
		// Query
		$insert = $this->insert_item($tbl['compras_articulos_precios'], $data);
		return $insert;
	}
	public function get_data_unico($id_compras_articulo_precio){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[compras_articulos_precios] WHERE id_compras_articulo_precios = $id_compras_articulo_precio";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function db_update_data($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$condicion = "id_compras_articulo_precios = ".$data['id_compras_articulo_precios'];
		$update = $this->update_item($tbl['compras_articulos_precios'], $data, 'id_compras_articulo_precios', $condicion);
		return $update;
	}
	public function get_articulos_um($id_compras_articulos){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query="SELECT 
					a.id_compras_articulo,
					a.clave_corta,
					a.id_compras_um as id_unidad_medida,
					b.id_compras_um,
					b.um,
					b.clave_corta as cv_um
				FROM $tbl[compras_articulos] a
				LEFT JOIN $tbl[compras_um] b ON a.id_compras_um = b.id_compras_um
				WHERE a.id_compras_articulo= $id_compras_articulos ";
		//echo $query;
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
}
?>