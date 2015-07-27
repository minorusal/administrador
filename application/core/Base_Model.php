<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Base_Model extends CI_Model {

	private $vars, $db1,$db2; #dbmodel
	public $tbl; #dbmodel

	public function __construct(){
		parent::__construct();	
		/*INICIO dbmodel*/
		// Crea arreglo $tbl[] con nombre de tablas dentro del archivo dbmodel.cfg
		$this->vars		= new config_vars();
        $this->vars->load_vars('assets/cfg/dbmodel.cfg');
		$this->db1 = $this->vars->db['db1'];
		$this->tbl['claves']       = $this->db1.'.'.$this->vars->db['db1_tbl_claves']; 
		$this->tbl['empresas']     = $this->db1.'.'.$this->vars->db['db1_tbl_empresas']; 
		$this->tbl['modulos']      = $this->db1.'.'.$this->vars->db['db1_tbl_modulos']; 		
		$this->tbl['paises']       = $this->db1.'.'.$this->vars->db['db1_tbl_paises']; 
		$this->tbl['perfiles']     = $this->db1.'.'.$this->vars->db['db1_tbl_perfiles']; 
		$this->tbl['personales']   = $this->db1.'.'.$this->vars->db['db1_tbl_personales']; 
		$this->tbl['secciones']    = $this->db1.'.'.$this->vars->db['db1_tbl_secciones']; 
		$this->tbl['submodulos']   = $this->db1.'.'.$this->vars->db['db1_tbl_submodulos']; 
		$this->tbl['sucursales']   = $this->db1.'.'.$this->vars->db['db1_tbl_sucursales']; 
		$this->tbl['usuarios']     = $this->db1.'.'.$this->vars->db['db1_tbl_usuarios']; 
		$this->tbl['menu1']        = $this->db1.'.'.$this->vars->db['db1_tbl_menu_n1']; 
		$this->tbl['menu2']        = $this->db1.'.'.$this->vars->db['db1_tbl_menu_n2']; 
		$this->tbl['menu3']        = $this->db1.'.'.$this->vars->db['db1_tbl_menu_n3']; 
		$this->tbl['vw_personal']  = $this->db1.'.'.$this->vars->db['db1_vw_personal']; 
		
        $this->db2 = $this->vars->db['db2'];
		$this->tbl['administracion_areas']                         = $this->db2.'.'.$this->vars->db['db2_tbl_administracion_areas'];
		$this->tbl['administracion_creditos']                      = $this->db2.'.'.$this->vars->db['db2_tbl_administracion_creditos'];
		$this->tbl['administracion_descuentos']                    = $this->db2.'.'.$this->vars->db['db2_tbl_administracion_descuentos'];
		$this->tbl['administracion_entidad_region']                = $this->db2.'.'.$this->vars->db['db2_tbl_administracion_entidad_region'];
		$this->tbl['administracion_entidades']                     = $this->db2.'.'.$this->vars->db['db2_tbl_administracion_entidades'];
		$this->tbl['administracion_forma_pago']                    = $this->db2.'.'.$this->vars->db['db2_tbl_administracion_forma_pago'];
		$this->tbl['administracion_impuestos']                     = $this->db2.'.'.$this->vars->db['db2_tbl_administracion_impuestos'];
		$this->tbl['administracion_movimientos']                   = $this->db2.'.'.$this->vars->db['db2_tbl_administracion_movimientos'];
		$this->tbl['administracion_puestos']                       = $this->db2.'.'.$this->vars->db['db2_tbl_administracion_puestos'];
		$this->tbl['administracion_regiones']                      = $this->db2.'.'.$this->vars->db['db2_tbl_administracion_regiones'];
		$this->tbl['administracion_servicios']                     = $this->db2.'.'.$this->vars->db['db2_tbl_administracion_servicios'];
		$this->tbl['administracion_variables']                     = $this->db2.'.'.$this->vars->db['db2_tbl_administracion_variables'];

		$this->tbl['almacen_ajustes']                              = $this->db2.'.'.$this->vars->db['db2_tbl_almacen_ajustes'];
		$this->tbl['almacen_almacenes']                            = $this->db2.'.'.$this->vars->db['db2_tbl_almacen_almacenes'];
		$this->tbl['almacen_entradas_recibir']                     = $this->db2.'.'.$this->vars->db['db2_tbl_almacen_entradas_recibir'];
		$this->tbl['almacen_entradas_partidas']                    = $this->db2.'.'.$this->vars->db['db2_tbl_almacen_entradas_partidas'];
		$this->tbl['almacen_gavetas']                              = $this->db2.'.'.$this->vars->db['db2_tbl_almacen_gavetas'];
		$this->tbl['almacen_pasillos']                             = $this->db2.'.'.$this->vars->db['db2_tbl_almacen_pasillos'];
		$this->tbl['almacen_tipos']                                = $this->db2.'.'.$this->vars->db['db2_tbl_almacen_tipos'];
		$this->tbl['almacen_transportes']                          = $this->db2.'.'.$this->vars->db['db2_tbl_almacen_transportes'];
		$this->tbl['compras_articulos']                            = $this->db2.'.'.$this->vars->db['db2_tbl_compras_articulos'];
		$this->tbl['compras_articulos_precios']                    = $this->db2.'.'.$this->vars->db['db2_tbl_compras_articulos_precios'];
		$this->tbl['compras_lineas']                               = $this->db2.'.'.$this->vars->db['db2_tbl_compras_lineas'];
		$this->tbl['compras_marcas']                               = $this->db2.'.'.$this->vars->db['db2_tbl_compras_marcas'];
		$this->tbl['compras_ordenes_tipo']                         = $this->db2.'.'.$this->vars->db['db2_tbl_compras_ordenes_tipo'];
		$this->tbl['compras_ordenes']                              = $this->db2.'.'.$this->vars->db['db2_tbl_compras_ordenes'];
		$this->tbl['compras_ordenes_articulos']                    = $this->db2.'.'.$this->vars->db['db2_tbl_compras_ordenes_articulos'];
		$this->tbl['compras_ordenes_estatus']                      = $this->db2.'.'.$this->vars->db['db2_tbl_compras_ordenes_estatus'];
		$this->tbl['compras_presentaciones']                       = $this->db2.'.'.$this->vars->db['db2_tbl_compras_presentaciones'];
		$this->tbl['compras_proveedores']                          = $this->db2.'.'.$this->vars->db['db2_tbl_compras_proveedores'];
		$this->tbl['compras_proveedores_articulos']                = $this->db2.'.'.$this->vars->db['db2_tbl_compras_proveedores_articulos'];
		$this->tbl['compras_um']                                   = $this->db2.'.'.$this->vars->db['db2_tbl_compras_um'];
		$this->tbl['compras_embalaje']                             = $this->db2.'.'.$this->vars->db['db2_tbl_compras_embalaje'];
		
		$this->tbl['nutricion_ciclo_receta']                       = $this->db2.'.'.$this->vars->db['db2_tbl_nutricion_ciclo_receta'];
		$this->tbl['nutricion_ciclos']                             = $this->db2.'.'.$this->vars->db['db2_tbl_nutricion_ciclos'];
		$this->tbl['nutricion_familias']                           = $this->db2.'.'.$this->vars->db['db2_tbl_nutricion_familias'];
		$this->tbl['nutricion_recetas']                            = $this->db2.'.'.$this->vars->db['db2_tbl_nutricion_recetas'];
		$this->tbl['nutricion_recetas_articulos']                  = $this->db2.'.'.$this->vars->db['db2_tbl_nutricion_recetas_articulos'];
		$this->tbl['nutricion_tiempos']                            = $this->db2.'.'.$this->vars->db['db2_tbl_nutricion_tiempos'];
		$this->tbl['nutricion_valores_nutricionales']              = $this->db2.'.'.$this->vars->db['db2_tbl_nutricion_valores_nutricionales'];
		
		$this->tbl['nutricion_programacion']                       = $this->db2.'.'.$this->vars->db['db2_tbl_nutricion_programacion'];
		$this->tbl['nutricion_programacion_ciclos']                = $this->db2.'.'.$this->vars->db['db2_tbl_nutricion_programacion_ciclos'];
		$this->tbl['nutricion_programacion_dias_descartados']      = $this->db2.'.'.$this->vars->db['db2_tbl_nutricion_programacion_dias_descartados'];
		$this->tbl['nutricion_programacion_dias_especiales']       = $this->db2.'.'.$this->vars->db['db2_tbl_nutricion_programacion_dias_especiales'];
		$this->tbl['nutricion_programacion_dias_festivos']         = $this->db2.'.'.$this->vars->db['db2_tbl_nutricion_programacion_dias_festivos'];

		$this->tbl['sucursales_esquema_pago']                      = $this->db2.'.'.$this->vars->db['db2_tbl_sucursales_esquema_pago'];
		$this->tbl['sucursales_esquema_venta']                     = $this->db2.'.'.$this->vars->db['db2_tbl_sucursales_esquema_venta'];
		$this->tbl['sucursales_pago']                              = $this->db2.'.'.$this->vars->db['db2_tbl_sucursales_pago'];
		$this->tbl['sucursales_venta']                             = $this->db2.'.'.$this->vars->db['db2_tbl_sucursales_venta'];

		$this->tbl['ventas_clientes']                              = $this->db2.'.'.$this->vars->db['db2_tbl_ventas_clientes'];
		$this->tbl['ventas_vendedores']                            = $this->db2.'.'.$this->vars->db['db2_tbl_ventas_vendedores'];
		$this->tbl['vw_compras_orden_articulos']                   = $this->db2.'.'.$this->vars->db['db2_vw_compras_orden_articulos'];
		$this->tbl['vw_compras_orden_proveedores']                 = $this->db2.'.'.$this->vars->db['db2_vw_compras_orden_proveedores'];
		$this->tbl['vw_articulos']                                 = $this->db2.'.'.$this->vars->db['db2_vw_articulos'];
		$this->tbl['vw_proveedores_articulos']                     = $this->db2.'.'.$this->vars->db['db2_vw_proveedores_articulos'];		
		$this->tbl['compras_articulos_tipo']                       = $this->db2.'.'.$this->vars->db['db2_tbl_compras_articulos_tipo'];
		$this->tbl['almacen_stock']                                = $this->db2.'.'.$this->vars->db['db2_tbl_almacen_stock'];
		$this->tbl['almacen_stock_deleted'] 					   = $this->db2.'.'.$this->vars->db['db2_tbl_almacen_stock_deleted'];
		$this->tbl['almacen_stock_estatus']                        = $this->db2.'.'.$this->vars->db['db2_tbl_almacen_stock_estatus'];
		$this->tbl['almacen_stock_logs']                           = $this->db2.'.'.$this->vars->db['db2_tbl_almacen_stock_logs'];
		$this->tbl['almacen_stock_logs_acciones']                  = $this->db2.'.'.$this->vars->db['db2_tbl_almacen_stock_logs_acciones'];
		/*FIN dbmodel*/
	}


	public function last_id(){
		return $this->db->insert_id();
	}
	public function row_exist($table, $row, $debug=false){
    	$this->db->select();
		$this->db->from($table);
		$this->db->where($row);
		$query = $this->db->get();
		if($debug){
			print_debug($query->result_array());
		}
		if($query->num_rows >= 1){
			return true;
		}else{
			return false;
		}
    }

    public function enabled_item($table, $clauses){ 	
    	$item  = array('activo' => 0);
		$query = $this->db->update_string($table, $item, $clauses);
		$query = $this->db->query($query);
		return $query;
    }
    public function update_item($tbl, $data, $id_row, $condicion = '') {
    	if(array_key_exists($id_row, $data)){
	    	$route = $this->uri->uri_string();
	    	$log   = array(	 'route'      => $route,
		    				 'type'       => 'UPDATE',
		    				 'tabla'      => $tbl,
		    				 'id_row'     => $data[$id_row],
		    				 'data_row'   => array_2_string_format($data,'=',','),
		    				 'id_usuario' => $data['edit_id_usuario'],
		    				 'timestamp'  => $data['edit_timestamp']
		    			);
	    	$log = $this->db->insert_string($this->tbl['administracion_movimientos'], $log);
	    	$log = $this->db->query($log);
	    	if($log){
	    		$update = $this->db->update_string($tbl, $data, $condicion);
	    		$update = $this->db->query($update);
	    	}else{
	    		$update = false;
	    	}
	    	return $update;
	    }else{
	    	return false;
	    }
    }
    public function insert_item($tbl, $data = array(), $last_id = false){
   		if(isset($data['id_usuario_reg'],$data)){unset($data['id_usuario']);}else{$data['id_usuario'];}
    	$insert  = $this->db->insert_string($tbl, $data);
    	$insert  = $this->db->query($insert);
    	if($insert){
    		$id_row  = $this->db->insert_id();
	    	$route   = $this->uri->uri_string();
	    	$log     = array(	 'route'      => $route,
			    				 'type'       => 'INSERT',
			    				 'tabla'      => $tbl,
			    				 'id_row'     => $id_row,
			    				 'data_row'   => array_2_string_format($data,'=',','),
			    				 'id_usuario' => (isset($data['id_usuario']))?$data['id_usuario']:$data['id_usuario_reg'],
			    				 'timestamp'  => $data['timestamp']
			    			);
	    	$log   = $this->db->insert_string($this->tbl['administracion_movimientos'], $log);
	    	$log   = $this->db->query($log);
    	}else{
    		$insert = false;
    	}
    	if($last_id){
    		return $id_row;
    	}
    	return $insert;
    }
    public function logs(){

    }
}

?>