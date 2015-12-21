<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('stock.php');
class entradas_almacen extends stock{
		/**
	* Nombre:		Historial Ordenes
	* Ubicación:	Compras>Ordenes/historial ordenes
	* Descripción:	Funcionamiento para la sección de ordenes de compra
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
		$this->submodulo        = 'entradas';
		$this->seccion          = 'entradas_almacen';
		$this->icon 			= 'fa fa-tags'; //Icono de modulo
		$this->path 			= $this->modulo.'/'.$this->seccion.'/'; //almacen/entradas_recepcion/
		$this->view_content 	= 'content';
		$this->limit_max		= 10;
		$this->offset			= 0;
		// DB Model
		//almacen/entradas_almacen/listado
		$this->load->model($this->modulo.'/'.$this->seccion.'_model','db_model');
		$this->load->model($this->modulo.'/catalogos_model','catalogos_model');
		$this->load->model('stock_model','stock_model');

		// Diccionario
		$this->lang->load($this->modulo.'/'.$this->submodulo,"es_ES");
		// Tabs
		$this->tab_inicial 			= 2;
		$this->tab_indice 		= array(
									'agregar',
									'listado',
									'detalle'
								);
		for($i=0; $i<=count($this->tab_indice)-1; $i++){
			$this->tab[$this->tab_indice[$i]] = $this->tab_indice[$i];
		}
	}
	public function config_tabs(){
		$tab_1 	= $this->tab['agregar'];
		$tab_2 	= $this->tab['listado'];
		$tab_3 	= $this->tab['detalle'];
		/*$tab_4 	= $this->tab4;
		$tab_5 	= $this->tab5;*/
		$path  	= $this->path;
		$pagina =(is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		// Nombre de Tabs
		$config_tab['names']    = array(
										 $this->lang_item($tab_1) //listado
										,$this->lang_item($tab_2) //detalle
										,$this->lang_item($tab_3) //detalle
								); 
		// Href de tabs
		$config_tab['links']    = array(
										$path.$tab_1
										,$path.$tab_2.'/'.$pagina //almacen/entradas_recepcion/listado/pagina
										,$tab_2                   //detalle
								); 
		// Accion de tabs
		$config_tab['action']   = array(
										 'load_content'
										,'load_content'
										,''
								);
		// Atributos 
		$config_tab['attr']     = array(array('style' => 'display:none'),'', array('style' => 'display:none'));
		return $config_tab;
	}
	private function uri_view_principal(){
		return $this->modulo.'/'.$this->view_content; //almacen/content
	}
	public function index(){
		$tabl_inicial 			  = 2;
		$view_listado    		  = $this->listado();
		//$view_listado    		  = 'tab';
		$contenidos_tab           = $view_listado;
		$data['titulo_submodulo'] = $this->lang_item($this->submodulo);
		$data['titulo_seccion']   = $this->lang_item($this->seccion);
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		//$data['modal']            = $this->modal();

		$js['js'][]  = array('name' => $this->seccion, 'dirname' => $this->modulo);
		$js['js'][]  = array('name' => 'numeral', 'dirname' => '');
		$this->load_view($this->uri_view_principal(), $data, $js);
	}
	public function listado($offset=0){
		// Crea tabla con listado de ordenes aprobadas 
		$accion 		= $this->tab['listado'];
		$limit 			= $this->limit_max;
		$uri_view 		= $this->modulo.'/'.$accion;
		$url_link 		= $this->modulo.'/'.$this->seccion.'/'.$accion;
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
				// Evento de enlace
				// Acciones
				$accion_id 						= $value['id_compras_orden_articulo'];
				$btn_acciones['agregar'] 		= '<span id="ico-articulos_'.$accion_id.'" class="ico_detalle fa fa-search-plus" onclick="detalle('.$accion_id.')" title="'.$this->lang_item("agregar_articulos").'"></span>';
				$acciones = implode('&nbsp;&nbsp;&nbsp;',$btn_acciones);

				// Datos para tabla
				$tbl_data[] = array('id'             	=> $value['id_stock'],
									'articulo'  	 	=> $value['articulo'],
									'presentacion'   	=> $value['presentacion'],
									'stock'      		=> $value['stock'],
									'clave_corta'   	=> $value['clave_corta'],
									'fecha_recepcion'   => $value['fecha_recepcion'],
									'almacenes'   	 	=> $value['almacenes'],
									'gavetas'   	 	=> $value['gavetas'],
									'acciones' 		 	=> $acciones
									);
			}
			// Plantilla
			$tbl_plantilla = set_table_tpl();
			// Titulos de tabla
			$this->table->set_heading(	$this->lang_item("id_stock"),
										$this->lang_item("articulo"),										
										$this->lang_item("presentacion"),
										$this->lang_item("stock"),
										$this->lang_item("clave_corta"),
										$this->lang_item("fecha_recepcion"),
										$this->lang_item("almacenes"),
										$this->lang_item("gavetas"),
										$this->lang_item("acciones")
									);
			// Generar tabla
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
			// XLS
			$buttonTPL = array( 'text'   => $this->lang_item("btn_xlsx"), 
							'iconsweets' => 'fa fa-file-excel-o',
							'href'       => base_url($this->modulo.'/'.$this->seccion).'/export_xlsx?filtro='.base64_encode($filtro)
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
		$id_compras_orden_articulo    = $this->ajax_post('id_compras_orden_articulo');
		$view 			= $this->tab['detalle'];
		$detalle  		= $this->db_model->get_data_unico($id_compras_orden_articulo);
		$btn_save       = form_button(array('class'=>"btn btn-primary",'name' => 'save' , 'onclick'=>'save()','content' => $this->lang_item("btn_guardar") ));
		$dropArray = array(
				 'data'		=> $this->catalogos_model->db_get_data_almacen_excepcion($detalle[0]['id_almacen'])
				,'value' 	=> 'id_almacen_almacenes'
				,'text' 	=> array('clave_corta','almacenes')
				,'name' 	=> "lts_almacen"
				,'event'    => array('event'       => 'onchange',
			   						 'function'    => 'load_gaveta_pas',
			   						 'params'      => array('this.value'),
			   						 'params_type' => array(0)
								)
				,'class' 	=> "requerido"
			);
		$lts_almacen  = dropdown_tpl($dropArray);
		//DATA
		$tabData['id_compras_orden_articulo'] 		= $detalle[0]['id_compras_orden_articulo'];
		$tabData['id_stock']	     				= $detalle[0]['id_stock'];
		$tabData['upc']	 		     				= $detalle[0]['upc'];
		$tabData['sku']	 		 	 				= $detalle[0]['sku'];
		$tabData['marca']	 	     				= $detalle[0]['marca'];
		$tabData['presentacion']	 				= $detalle[0]['presentacion'];
		$tabData['lote']			 				= $detalle[0]['lote'];
		$tabData['stock']	 	 	 				= $detalle[0]['stock'];
		$tabData['stock_um'] 	 	 				= $detalle[0]['stock_um'];
		$tabData['caducidad']	     				= $detalle[0]['caducidad'];
		$tabData['almacenes']	     				= $detalle[0]['almacenes'];
		$tabData['pasillos']	     				= $detalle[0]['pasillos'];
		$tabData['gavetas']	     	 				= $detalle[0]['gavetas'];
		$tabData['id_almacen_origen']				= $detalle[0]['id_almacen'];
		$tabData['id_pasillo_origen']				= $detalle[0]['id_pasillo'];
		$tabData['id_gaveta_origen'] 				= $detalle[0]['id_gaveta'];
		$tabData['id_almacen_entradas_recepcion'] 	= $detalle[0]['id_almacen_entradas_recepcion'];
		$tabData['lts_almacen']	     				= $lts_almacen;
		$tabData['button_save']      				= $btn_save;
		//DIC
		$tabData['upc_lbl']			 				= $this->lang_item("upc_lbl",false);
		$tabData['sku_lbl']			 				= $this->lang_item("sku_lbl",false);
		$tabData['marca_lbl']		 				= $this->lang_item("marca_lbl",false);
		$tabData['presentacion_lbl'] 				= $this->lang_item("presentacion_lbl",false);
		$tabData['lote_lbl']		 				= $this->lang_item("lote_lbl",false);
		$tabData['stock_lbl']		 				= $this->lang_item("stock_lbl",false);
		$tabData['stock_um_lbl']	 				= $this->lang_item("stock_um_lbl",false);		
		$tabData['caducidad_lbl']	 				= $this->lang_item("caducidad_lbl",false);
		$tabData['almacen_lbl']		 				= $this->lang_item("almacen_lbl",false);
		$tabData['pasillo_lbl']		 				= $this->lang_item("pasillo_lbl",false);
		$tabData['gaveta_lbl']		 				= $this->lang_item("gaveta_lbl",false);
		$tabData['almacen_origen_lbl']		 		= $this->lang_item("almacen_origen_lbl",false);
		$tabData['pasillo_origen_lbl']		 		= $this->lang_item("pasillo_origen_lbl",false);
		$tabData['gaveta_origen_lbl']		 		= $this->lang_item("gaveta_origen_lbl",false);
		$tabData['origen']		 					= $this->lang_item("origen",false);
		$tabData['destino']		 					= $this->lang_item("destino",false);
		
		$uri_view  = $this->modulo.'/'.$this->submodulo.'/'.$this->seccion.'/'.$view;

		echo json_encode( $this->load_view_unique($uri_view ,$tabData, true));
	}
	public function load_gaveta_pas(){
		$id_almacen    = $this->ajax_post('id_almacen');
		$datasql=array('id_almacen'=>$id_almacen);
			$dropArray = array(
					 'data'		=> $this->catalogos_model->db_get_data_pasillos_por_almacen($datasql)
					,'value' 	=> 'id_almacen_pasillos'
					,'text' 	=> array('clave_corta','pasillos')
					,'name' 	=> "lts_pasillos"
					,'event'    => array('event'       => 'onchange',
				   						 'function'    => 'load_gaveta',
				   						 'params'      => array('this.value'),
				   						 'params_type' => array(0)
									)
				);
			$dropArray2 = array(
					 'data'		=> $this->catalogos_model->db_get_data_gavetas_por_almacen($datasql)
					,'value' 	=> 'id_almacen_gavetas'
					,'text' 	=> array('clave_corta','gavetas')
					,'name' 	=> "lts_gavetas"
					,'class' 	=> "requerido"
				);
			$lts_pasillo  = dropdown_tpl($dropArray);
			$lts_gavetas  = dropdown_tpl($dropArray2);
			$data['pasillos']=$lts_pasillo;
			$data['gavetas']=$lts_gavetas;

		echo json_encode($data);
	}
	public function load_gaveta(){
		$id_pasillo    = $this->ajax_post('id_pasillo');
		$id_almacen    = $this->ajax_post('id_almacen');
		if($id_pasillo==0){
			$datasql=array('id_almacen'=>$id_almacen);
			$dropArray = array(
						 'data'		=> $this->catalogos_model->db_get_data_gavetas_por_almacen($datasql)
						,'value' 	=> 'id_almacen_gavetas'
						,'text' 	=> array('clave_corta','gavetas')
						,'name' 	=> "lts_gavetas"
						,'class' 	=> "requerido"
					);
			$lts_gavetas  = dropdown_tpl($dropArray);
		}else{
			$datasql=array('id_pasillo'=>$id_pasillo);
			$dropArray = array(
						 'data'		=> $this->catalogos_model->db_get_data_gavetas_por_pasillo($datasql)
						,'value' 	=> 'id_almacen_gavetas'
						,'text' 	=> array('clave_corta','gavetas')
						,'name' 	=> "lts_gavetas"
						,'class' 	=> "requerido"
					);
			$lts_gavetas  = dropdown_tpl($dropArray);
		}
		echo json_encode($lts_gavetas);
	}
	public function update_almacen(){
		$id_almacen_origen				= $this->ajax_post('id_almacen_origen'); #origen
		$id_pasillo_origen				= $this->ajax_post('id_pasillo_origen'); #origen
		$id_pasillo_origen 				= ($id_pasillo_origen==0)?null:$id_pasillo_origen; #Validacion de nulo
		$id_gaveta_origen				= $this->ajax_post('id_gaveta_origen'); #origen
		$id_almacen						= $this->ajax_post('lts_almacen'); #destino
		$id_pasillo						= $this->ajax_post('lts_pasillos'); #destino
		$id_pasillo 					= ($id_pasillo==0)?null:$id_pasillo; #Validacion de nulo
		$id_gaveta 						= $this->ajax_post('lts_gavetas'); #destino
		$id_stock 						= $this->ajax_post('id_stock');
		$id_compras_orden_articulo 		= $this->ajax_post('id_compras_orden_articulo');
		$id_almacen_entradas_recepcion 	= $this->ajax_post('id_almacen_entradas_recepcion');
		$stock 							= $this->ajax_post('stock');
		$stock_um 						= $this->ajax_post('stock_um');
		$lote 							= $this->ajax_post('lote');
		$caducidad 						= $this->ajax_post('caducidad');

		$arrayData = array(
						 'id_accion' 					=> $this->vars->cfg['id_accion_almacen_entrada'] #2 => ENTRADA
						,'id_stock' 					=> $id_stock
						,'id_compras_orden_articulo' 	=> $id_compras_orden_articulo
						,'id_almacen_entradas_recepcion'=> $id_almacen_entradas_recepcion
						,'id_almacen_origen'			=> $id_almacen_origen
						,'id_pasillo_origen'			=> $id_pasillo_origen
						,'id_gaveta_origen'				=> $id_gaveta_origen
						,'stock_origen'					=> $stock
						,'stock_um_origen'				=> $stock_um
						,'id_almacen_destino'			=> $id_almacen
						,'id_pasillo_destino'			=> $id_pasillo
						,'id_gaveta_destino'			=> $id_gaveta
						,'stock_destino'				=> $stock
						,'stock_um_destino'				=> $stock_um
						,'lote'							=> $lote
						,'caducidad'					=> $caducidad
					);
		$update = $this->stock_update($arrayData);
		if($update){
			$msg = $this->lang_item("msg_update_success",false);
			echo json_encode('1|'.alertas_tpl('success', $msg ,false));
		}else{
			$msg = $this->lang_item("msg_err_clv",false);
			echo json_encode('0|'.alertas_tpl('', $msg ,false));
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
									$value['id_stock'],
									$value['articulo'],
									$value['presentacion'],
									$value['stock'],
									$value['clave_corta'],
									$value['fecha_recepcion'],
									$value['almacenes'],
									$value['gavetas']
								);
			}

			$set_heading = array(		
								$this->lang_item("id"),
								$this->lang_item("articulo"),										
								$this->lang_item("presentacion"),
								$this->lang_item("stock"),
								$this->lang_item("clave_corta"),
								$this->lang_item("fecha_recepcion"),
								$this->lang_item("almacenes"),
								$this->lang_item("gavetas")
							);
		}

		$params = array(	'title'   => $this->lang_item("xlsx_entradas_almacen"),
							'items'   => $set_data,
							'headers' => $set_heading
						);
		$this->excel->generate_xlsx($params);
	}
}