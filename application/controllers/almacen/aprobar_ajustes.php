<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('stock.php');
class aprobar_ajustes extends stock{
	/**
	* Nombre:		Ajustes
	* Ubicación:	Almacen>Ajustes
	* Descripción:	Funcionamiento para quitar cantidad de stock
	* @author:		Alejandro Enciso
	* Creación: 	2015-05-19
	*/
	private $modulo;
	private $submodulo;
	private $seccion;
	private $view_content, $uri_view_principal;
	private $path;
	private $icon;
	private $offset, $limit_max;
	private $tab_inicial, $tab = array(), $tab_indice = array();

	public function __construct(){
		parent::__construct();
		$this->vars = new config_vars();
        $this->vars->load_vars();
		$this->modulo 			= 'almacen';
		$this->submodulo        = 'aprobar_ajustes';
		$this->seccion          = 'ajustes';
		$this->icon 			= 'fa fa-thumbs-up'; //Icono de modulo
		$this->path 			= $this->modulo.'/'.$this->submodulo.'/'; //almacen/entradas_recepcion/
		$this->view_content 	= 'content';
		$this->limit_max		= 10;
		$this->offset			= 0;
		// Tabs
		//$this->tab1 			= 'agregar';
		$this->tab1 			= 'listado';
		$this->tab2 			= 'detalle';
		$this->tab3 			= 'listado_afectado';
		// DB Model
		$this->load->model($this->modulo.'/'.$this->seccion.'_model','db_model');		
		$this->load->model($this->modulo.'/catalogos_model','catalogos_model');
		$this->load->model('stock_model','stock_model');

		// Diccionario
		$this->lang->load($this->modulo.'/'.$this->seccion,"es_ES");
		// Tabs
		$this->tab_inicial 		= 2;
		$this->tab_indice 		= array(
									 $this->tab1
									,$this->tab2
									,$this->tab3
								);
		for($i=0; $i<=count($this->tab_indice)-1; $i++){
			$this->tab[$this->tab_indice[$i]] = $this->tab_indice[$i];
		}
	}
	public function config_tabs(){
		$tab_1 	= $this->tab1;
		$tab_2 	= $this->tab2;
		$tab_3 	= $this->tab3;
		$path  	= $this->path;
		$pagina =(is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		// Nombre de Tabs
		$config_tab['names']    = array(
										 $this->lang_item($tab_1) //agregar
										,$this->lang_item($tab_2) //listado
										,$this->lang_item($tab_3) //listado_afectado
								); 
		// Href de tabs
		$config_tab['links']    = array(
										$path.$tab_1.'/'.$pagina //almacen/ajustes/listado/pagina
										,$path.$tab_2            //almacen/ajustes/agregar
										,$path.$tab_3            //almacen/ajustes/listado_afectado
								); 
		// Accion de tabs
		$config_tab['action']   = array(
										'load_content'
										,''
										,''
								);
		// Atributos 
		$config_tab['attr']     = array('', array('style' => 'display:none'), array('style' => 'display:none'));
		return $config_tab;
	}
	private function uri_view_principal(){
		return $this->modulo.'/'.$this->view_content;
	}
	public function index(){
		$tabl_inicial 			  = $this->tab_inicial;
		$view_listado    		  = $this->listado();
		$contenidos_tab           = $view_listado;
		$data['titulo_submodulo'] = $this->lang_item($this->modulo);
		$data['titulo_seccion']   = $this->lang_item($this->submodulo);
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	

		$js['js'][]  = array('name' => $this->submodulo, 'dirname' => $this->modulo);
		$js['js'][]  = array('name' => 'numeral', 'dirname' => '');
		$this->load_view($this->uri_view_principal(), $data, $js);
	}
	public function listado($offset=0){
		// Crea tabla con listado de ordenes aprobadas 
		$accion 		= $this->tab['listado'];
		$limit 			= $this->limit_max;
		$uri_view 		= $this->modulo.'/'.$accion;
		$url_link 		= $this->modulo.'/'.$this->submodulo.'/'.$accion;
		$buttonTPL 		= '';
		$filtro  = ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";
		$sqlData = array(
			 'buscar' => $filtro
			,'offset' => $offset
			,'limit'  => $limit
		);
		$uri_segment  			  = $this->uri_segment(); 
		$total_rows   			  = count($this->db_model->db_get_data($sqlData));
		$sqlData['aplicar_limit'] = false;
		$list_content 			  = $this->db_model->db_get_data($sqlData);
		$url          			  = base_url($url_link);
		$paginador    			  = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));
		if($total_rows){
			foreach ($list_content as $value) {
				// Acciones
				$accion_id 						= $value['id_almacen_ajuste'];
				$btn_acciones['agregar'] 		= '<span id="ico-articulos_'.$accion_id.'" class="ico_detalle fa fa-search-plus" onclick="detalle('.$accion_id.')" title="'.$this->lang_item("agregar_articulos").'"></span>';
				$acciones = implode('&nbsp;&nbsp;&nbsp;',$btn_acciones);
				// Datos para tabla
				$tbl_data[] = array('id'             	=> $value['id_almacen_ajuste'],
									'articulo'  	 	=> $value['articulo'],
									'stock_mov'   	 	=> $value['stock_mov'].'-'.$value['cl_um'],
									'stock_um_mov'   	=> $value['stock_um_mov'].'-'.$value['cl_um'],
									'timestamp'  	 	=> $value['timestamp'],
									'acciones' 		 	=> $acciones
									);
			}

			// Plantilla
			$tbl_plantilla = set_table_tpl();
			// Titulos de tabla
			$this->table->set_heading(	$this->lang_item("id_almacen_ajuste"),
										$this->lang_item("articulo"),										
										$this->lang_item("stock_mov"),
										$this->lang_item("stock_um_mov"),
										$this->lang_item("fecha_registro"),
										$this->lang_item("acciones")
									);
			// Generar tabla
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
			// XLS
			$buttonTPL = array( 'text'   => $this->lang_item("btn_xlsx"), 
							'iconsweets' => 'iconsweets-excel',
							'href'       => base_url($this->modulo.'/'.$this->submodulo).'/export_xlsx?filtro='.base64_encode($filtro)
							);
		}else{
			$msg   = $this->lang_item("msg_query_null");
			$tabla = alertas_tpl('', $msg ,false);
		}
		$tabData['filtro']    = (isset($filtro) && $filtro!="") ? sprintf($this->lang_item("msg_query_search",false),$total_rows , $filtro) : "";
		$tabData['tabla']     = $tabla;
		$tabData['paginador'] = $paginador;
		$tabData['item_info'] = $this->pagination_bootstrap->showing_items($limit, $offset, $total_rows);
		$tabData['export']    = button_tpl($buttonTPL);

		if($this->ajax_post(false)){
			echo json_encode( $this->load_view_unique($uri_view , $tabData, true));
		}else{
			return $this->load_view_unique($uri_view , $tabData, true);
		}
	}
	public function detalle(){
		$id_almacen_ajuste = $this->ajax_post('id_almacen_ajuste');
		$view 			   = $this->tab['detalle'];
		$uri_view  		   = $this->modulo.'/'.$this->seccion.'/'.$this->submodulo.'/'.$view;
		$detalle  		   = $this->db_model->get_data_unico($id_almacen_ajuste);

		$id_articulo   =   $detalle[0]['id_articulo'];
		$id_almacen    =  ($detalle[0]['id_almacen']!=0)?$detalle[0]['id_almacen']:'';
		$id_pasillo    =  ($detalle[0]['id_pasillo']!=0)?$detalle[0]['id_pasillo']:'';
		$id_gaveta     =  ($detalle[0]['id_gaveta']!=0)?$detalle[0]['id_gaveta']:'';
		$stock_mov     =   $detalle[0]['stock_mov'];
		$stock_um_mov  =   $detalle[0]['stock_um_mov'];
		$sqlData=array(
					'id_almacen'  => $id_almacen,
					'id_pasillo'  => $id_pasillo,
					'id_gaveta'   => $id_gaveta,
					'id_articulo' => $id_articulo
				);

		$articulo_detalle = $this->db_model->get_data_stock($sqlData);
		$stock_total      = 0;
		$stock_um_total   = 0;
		for($i=0;count($articulo_detalle)>$i;$i++){
			$stock_total   	= $stock_total + $articulo_detalle[$i]['stock'];
			$stock_um_total = $stock_um_total + $articulo_detalle[$i]['stock_um'];
		}
		$accion_id				 	 = $detalle[0]['id_almacen_ajuste'];
		$btn_save       		 	 = form_button(array('class'=>"btn btn-primary",'name' => 'ajuste_save','onclick'=>'agregar('.$accion_id.')' , 'content' => $this->lang_item("btn_guardar") ));
		$tabData['stock_total']	     = $stock_total;
		$tabData['stock_um_total']	 = $stock_um_total;
		$tabData['stock_mov']	     = $stock_mov;
		$tabData['stock_um_mov']	 = $stock_um_mov;
		$tabData['articulo']	 	 = $detalle[0]['articulo'];
		$tabData['cl_almacen']	 	 = $detalle[0]['cl_almacen'];
		$tabData['cl_gaveta']	 	 = $detalle[0]['cl_gaveta'];
		$tabData['cl_pasillo']	 	 = $detalle[0]['cl_pasillo'];
		$tabData['cl_um']			 = $detalle[0]['cl_um'];
		$tabData['id_articulo'] 	 = $id_articulo;
		$tabData['id_almacen'] 		 = $id_almacen;
		$tabData['id_pasillo'] 		 = $id_pasillo;
		$tabData['id_gaveta'] 		 = $id_gaveta;
		$tabData['button_save']      = $btn_save;
		//DIC
		$tabData['lbl_articulo']	 = $this->lang_item("articulo",false);
		$tabData['lbl_cl_almacen']	 = $this->lang_item("almacen_lbl",false);
		$tabData['lbl_cl_gaveta']	 = $this->lang_item("gaveta_lbl",false);
		$tabData['lbl_cl_pasillo']	 = $this->lang_item("pasillo_lbl",false);
		$tabData['lbl_stock_mov']	 = $this->lang_item("stock_mov",false);
		$tabData['lbl_stock_um_mov'] = $this->lang_item("stock_um_mov",false);
		$tabData['lbl_stock']		 = $this->lang_item("lblstock",false);
		$tabData['lbl_stock_um']	 = $this->lang_item("stock_um_lbl",false);

		echo json_encode($this->load_view_unique($uri_view ,$tabData, true));
	}
	public function agregar(){
		$id_articulo  	   = $this->ajax_post('id_articulo');
		$id_almacen   	   = $this->ajax_post('id_almacen');
		$id_pasillo   	   = $this->ajax_post('id_pasillo');
		$id_gaveta    	   = $this->ajax_post('id_gaveta');
		$stock_mov 	  	   = $this->ajax_post('stock_mov');
		$stock_um_mov 	   = $this->ajax_post('stock_um_mov');
		$id_almacen_ajuste = $this->ajax_post('id_almacen_ajuste');
		$view 			   = $this->tab['listado_afectado'];
		$view_lineas 	   = 'listado_afectado_lineas';
		$uri_view  		   = $this->modulo.'/'.$this->seccion.'/'.$this->submodulo.'/'.$view;
		$uri_view_lineas   = $this->modulo.'/'.$this->seccion.'/'.$this->submodulo.'/'.$view_lineas;
		
		$sqlData=array(
					'id_almacen'  => $id_almacen,
					'id_pasillo'  => $id_pasillo,
					'id_gaveta'   => $id_gaveta,
					'id_articulo' => $id_articulo
				);

		$articulo_detalle  = $this->db_model->get_data_stock($sqlData);
		for($i=0;count($articulo_detalle)>$i;$i++){
			$muestra_tabla = true;
			if($i==0){
				$cantidad  = $articulo_detalle[$i]['stock']-$stock_mov;
				if($cantidad<=0){
					$stock    = 0;
					$stock_um = 0;
				}else{
					$stock    = $cantidad;
					$stock_um = $this->regla_de_tres($articulo_detalle[$i]['stock'], $articulo_detalle[$i]['stock_um'], $cantidad);
					($articulo_detalle[$i]['id_articulo_tipo']==2)?$stock_um=$stock_um:$stock_um=$cantidad;
				}
			}else{
				if($cantidad<=0){
					$cantidad  	  = $cantidad*-1;
					$stock_mov 	  = $cantidad;
					$cantidad  	  = $articulo_detalle[$i]['stock']-$cantidad;
					$stock_um  	  = $this->regla_de_tres($articulo_detalle[$i]['stock'], $articulo_detalle[$i]['stock_um'], $cantidad);
					$stock_um_mov = $stock_um;
					if($cantidad<=0){
						$stock        = 0;
						$stock_um_mov = $articulo_detalle[$i]['stock_um'];
						$stock_um     = 0;
					}else{
						$stock    = $cantidad;							
						$stock_um = $this->regla_de_tres($articulo_detalle[$i]['stock'], $articulo_detalle[$i]['stock_um'], $cantidad);
						($articulo_detalle[$i]['id_articulo_tipo']==2)?$stock_um=$stock_um:$stock_um=$cantidad;
					}
				}else{
					$muestra_tabla = false;
				}
			}
			if($muestra_tabla){
				if($stock==0){
					$id_estatus=0;
				}else{
					$id_estatus=1;
				}
				$slqDatastock = array(
									'id_stock'        => $articulo_detalle[$i]['id_stock'],
									'stock'  	      => $stock,
									'stock_um'        => $stock_um,
									'activo'      	  => $id_estatus,
									'edit_timestamp'  => $this->timestamp(),
									'edit_id_usuario' => $this->session->userdata('id_usuario')
								);
				$stock_update = $this->stock_model->update_data_stock($slqDatastock);
				if($stock==0){
					$delete = array(
								'fisico'   => true, 
								'id_stock' => $articulo_detalle[$i]['id_stock']
								);
					$this->eliminar_stock_en_cero($delete);
				}
				$slqDatastockLogs = array(
								'id_accion'  					 => 4,//Ajuste
								'id_stock'  					 => $articulo_detalle[$i]['id_stock'],
								'id_compras_orden_articulo'  	 => $articulo_detalle[$i]['id_compras_orden_articulo'],
								'id_almacen_entradas_recepcion'  => $articulo_detalle[$i]['id_almacen_entradas_recepcion'],
								//'id_articulo_tipo'  			 => $articulo_detalle[$i]['id_articulo_tipo'],
								'log_id_almacen_origen'  		 => $id_almacen,
								'log_id_pasillo_origen'  		 => $id_pasillo,
								'log_id_gaveta_origen'  		 => $id_gaveta,
								'log_stock_origen'  			 => $articulo_detalle[$i]['stock'],
								'log_stock_um_origen'  			 => $articulo_detalle[$i]['stock_um'],
								'log_id_almacen_destino'  		 => $id_almacen,
								'log_id_pasillo_destino'  		 => $id_pasillo,
								'log_id_gaveta_destino'  		 => $id_gaveta,
								'log_stock_destino'  			 => $stock,
								'log_stock_um_destino'  		 => $stock_um,
								'log_lote'  					 => $articulo_detalle[$i]['lote'],
								'log_caducidad'  				 => $articulo_detalle[$i]['caducidad'],
								'timestamp'  	 		         => $this->timestamp(),
								'id_usuario'   		   		     => $this->session->userdata('id_usuario')
							);
				//tabla a mostrar
				$this->stock_model->insert_stock_log($slqDatastockLogs);
				//DATA
				$tbl_data[] = array('id'            => $articulo_detalle[$i]['articulo'],
									'articulo'  	=> $articulo_detalle[$i]['articulo'],
									'almacenes'   	=> $articulo_detalle[$i]['almacenes'],
									'pasillos'   	=> $articulo_detalle[$i]['pasillos'],
									'gavetas'   	=> $articulo_detalle[$i]['gavetas'],
									'clave_corta'   => $articulo_detalle[$i]['clave_corta'],
									'stock'   	 	=> $articulo_detalle[$i]['stock'],
									'stock_um'   	=> $articulo_detalle[$i]['stock_um']
									);
			}
		}
			$tbl_plantilla = set_table_tpl();
			// Titulos de tabla
			$this->table->set_heading(	$this->lang_item("lbl_articulo"),
										$this->lang_item("lbl_articulo"),										
										$this->lang_item("lbl_cl_almacen"),
										$this->lang_item("lbl_cl_pasillo"),
										$this->lang_item("lbl_cl_gaveta"),
										$this->lang_item("lbl_clave_corta"),
										$this->lang_item("lbl_stock"),
										$this->lang_item("lbl_stock_um")
									);
			// Generar tabla
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);

		$tabData['tabla']     		= $tabla;
		$data = $this->load_view_unique($uri_view ,$tabData, true);

		$slqDataajuste = array(
									'id_almacen_ajuste' => $id_almacen_ajuste,
									'estatus'      	  	=> 2,//ajuste aprobado
									'edit_timestamp'  	=> $this->timestamp(),
									'edit_id_usuario' 	=> $this->session->userdata('id_usuario')
								);

		$insert=$this->db_model->update_data($slqDataajuste);
		if($insert){
				$msg = $this->lang_item("msg_update_success",false);
				echo json_encode(array(  'success'=>'true', 'mensaje' => $msg, 'table' => $data ));
			}else{
				$msg = $this->lang_item("msg_campos_obligatorios",false);
				echo json_encode( array( 'success'=>'false', 'mensaje' => alertas_tpl('error', $msg ,false)) );
			}
	}
	public function export_xlsx(){
		$filtro      = ($this->ajax_get('filtro')) ?  base64_decode($this->ajax_get('filtro') ): "";
		$sqlData = array(
			 'buscar'     => $filtro
		);
		$list_content 			  = $this->db_model->db_get_data($sqlData);

		if(count($list_content)>0){
			foreach ($list_content as $value) {
				$set_data[] = array(
									$value['id_almacen_ajuste'],
									$value['articulo'],
									$value['stock_mov'].'-'.$value['cl_um'],
									$value['stock_um_mov'].'-'.$value['cl_um'],
									$value['timestamp']
									);
			}
			$set_heading = array(	$this->lang_item("id_almacen_ajuste"),
										$this->lang_item("articulo"),										
										$this->lang_item("stock_mov"),
										$this->lang_item("stock_um_mov"),
										$this->lang_item("fecha_registro")
									);
	
		}

		$params = array(	'title'   => $this->lang_item("xlsx_aprobar_ajustes"),
							'items'   => $set_data,
							'headers' => $set_heading
						);
		$this->excel->generate_xlsx($params);
	}
}
?>