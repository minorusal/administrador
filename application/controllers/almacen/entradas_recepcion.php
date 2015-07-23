<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class entradas_recepcion extends Base_Controller{
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
	// private $vars;

	public function __construct(){
		parent::__construct();
		$this->vars = new config_vars();
        $this->vars->load_vars();
		$this->modulo 			= 'almacen';
		$this->seccion          = 'entradas';
		$this->submodulo        = 'entradas_recepcion';
		$this->icon 			= 'fa fa-book'; //Icono de modulo
		$this->path 			= $this->modulo.'/'.$this->seccion.'/'; //almacen/entradas_recepcion/
		$this->view_content 	= 'content';
		$this->limit_max		= 10;
		$this->offset			= 0;
		// Tabs
		$this->tab1 			= 'entradas_recepcion_save';
		$this->tab2 			= 'listado';
		$this->tab3 			= 'entradas_recepcion_edit';
		$this->tab4 			= 'articulos';
		$this->tab5 			= 'modal';
		// DB Model
		$this->load->model($this->modulo.'/'.$this->submodulo.'_model','db_model');
		$this->load->model('compras/ordenes_model','ordenes_model');
		$this->load->model('stock_model','stock_model');
		$this->load->model('compras/listado_precios_model','listado_precios_model');
		$this->load->model('administracion/sucursales_model','sucursales_model');
		$this->load->model('administracion/formas_de_pago_model','formas_de_pago_model');
		$this->load->model('administracion/creditos_model','creditos_model');

		// Diccionario
		$this->lang->load($this->modulo.'/'.$this->seccion,"es_ES");
		// Tabs
		$this->tab_inicial 			= 2;
		$this->tab_indice 		= array(
									 'entradas_recepcion_save'
									,'listado'
									,'entradas_recepcion_edit'
									,'articulos'
									,'modal'
								);
		for($i=0; $i<=count($this->tab_indice)-1; $i++){
			$this->tab[$this->tab_indice[$i]] = $this->tab_indice[$i];
		}

	}
	public function config_tabs(){
		$tab_1 	= $this->tab1;
		$tab_2 	= $this->tab2;
		$tab_3 	= $this->tab3;
		$tab_4 	= $this->tab4;
		$tab_5 	= $this->tab5;
		$path  	= $this->path;
		$pagina =(is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		// Nombre de Tabs
		$config_tab['names']    = array(
										 $this->lang_item($tab_1) //agregar
										,$this->lang_item($tab_2) //listado
										,$this->lang_item($tab_3) //detalle
										,$this->lang_item($tab_4) //articulos
										,$this->lang_item($tab_5) //modal
								); 
		// Href de tabs
		$config_tab['links']    = array(
										 $path.$tab_1            //almacen/entradas_recepcion/agregar
										,$this->modulo.'/'.$this->submodulo.'/'.$tab_2.'/'.$pagina //almacen/entradas_recepcion/listado/pagina
										,$tab_3                   //detalle
										,$tab_4                   //articulos
										,$tab_5                   //modal
								); 
		// Accion de tabs
		$config_tab['action']   = array(
										 'load_content'
										,'load_content'
										,''
										,''
										,''
								);
		// Atributos 
		$config_tab['attr']     = array(array('style' => 'display:none'),'', array('style' => 'display:none'), array('style' => 'display:none'), array('style' => 'display:none'));
		return $config_tab;
	}
	private function uri_view_principal(){
		return $this->modulo.'/'.$this->view_content; //compras/content
	}
	public function index(){
		$tabl_inicial 			  = 2;
		$view_listado    		  = $this->listado();
		$contenidos_tab           = $view_listado;
		$data['titulo_submodulo'] = $this->lang_item($this->seccion);
		$data['titulo_seccion']   = $this->lang_item($this->submodulo);
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		//$data['modal']            = $this->modal();

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
				// Evento de enlace
				// Acciones
				$accion_id 						= $value['id_compras_orden'];
				$btn_acciones['agregar'] 		= '<span id="ico-articulos_'.$accion_id.'" class="ico_detalle fa fa-search-plus" onclick="articulos('.$accion_id.')" title="'.$this->lang_item("agregar_articulos").'"></span>';
				$acciones = implode('&nbsp;&nbsp;&nbsp;',$btn_acciones);
				// Datos para tabla
				$tbl_data[] = array('id'             => $value['id_compras_orden'],
									'orden_num'      => $value['orden_num'],
									'descripcion'    => $value['descripcion'],
									'timestamp'  	 => $value['timestamp'],
									'entrega_fecha'  => $value['entrega_fecha'],
									'estatus'   	 => $value['estatus'],
									'acciones' 		 => $acciones
									);
			}
			// Plantilla
			// $tbl_plantilla = array ('table_open'  => '<table id="tbl_grid" class="table table-bordered responsive ">');
			$tbl_plantilla = set_table_tpl();
			// Titulos de tabla
			$this->table->set_heading(	$this->lang_item("id"),
										$this->lang_item("orden_num"),										
										$this->lang_item("descripcion"),
										$this->lang_item("fecha_registro"),
										$this->lang_item("entrega_fecha"),
										$this->lang_item("estatus"),
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
	public function articulos($id_compras_orden=false){
		// Agregar articulos a una orden de compra
		$table 				= '';
		$accion 			= $this->tab['articulos'];
		$uso_interno		= (!$id_compras_orden)?false:true;
		$id_compras_orden 	= (!$id_compras_orden)?$this->ajax_post('id_compras_orden'):$id_compras_orden;
		$detalle  			= $this->ordenes_model->get_orden_unico($id_compras_orden);
		//dump_var($detalle);

		$data_sql = array('id_compras_orden'=>$id_compras_orden);
		$data_listado=$this->db_model->db_get_data_orden_listado_registrado_unificado($data_sql);
		$moneda = $this->session->userdata('moneda');
		if(count($data_listado)>0){
				$style_table='display:block';				
			for($i=0;count($data_listado)>$i;$i++){
				// Lineas
				$peso_unitario = (substr($data_listado[$i]['peso_unitario'], strpos($data_listado[$i]['peso_unitario'], "." ))=='.000')?number_format($data_listado[$i]['peso_unitario'],0):$data_listado[$i]['peso_unitario'];
				$presentacion_x_embalaje = (substr($data_listado[$i]['presentacion_x_embalaje'], strpos($data_listado[$i]['presentacion_x_embalaje'], "." ))=='.000')?number_format($data_listado[$i]['presentacion_x_embalaje'],0):$data_listado[$i]['presentacion_x_embalaje'];
				$embalaje = ($data_listado[$i]['embalaje'])?$data_listado[$i]['embalaje'].' CON ':'';
				$table.='<tr id="'.$data_listado[$i]['id_compras_orden_articulo'].'">
							<td class="center">
								<span name="consecutivo">'.($i+1).'</span>
								<input type="hidden" id="id_compras_articulo" data-campo="id_compras_articulo['.$data_listado[$i]['id_compras_orden_articulo'].']" value="'.$data_listado[$i]['id_compras_articulo'].'">
								<input type="hidden" id="id_articulo_tipo" data-campo="id_articulo_tipo['.$data_listado[$i]['id_compras_orden_articulo'].']" value="'.$data_listado[$i]['id_articulo_tipo'].'">
								<input type="hidden" id="id_compras_um" data-campo="id_compras_um['.$data_listado[$i]['id_compras_orden_articulo'].']" value="'.$data_listado[$i]['id_compras_um'].'">
								<input type="hidden" data-campo="lote_val['.$data_listado[$i]['id_compras_orden_articulo'].']" name="lote_val" id="lote_val_'.$data_listado[$i]['id_compras_orden_articulo'].'"/>
								<input type="hidden" data-campo="caducidad_val['.$data_listado[$i]['id_compras_orden_articulo'].']" name="caducidad_val" id="caducidad_val_'.$data_listado[$i]['id_compras_orden_articulo'].'"/>
								<input type="hidden" data-campo="cantidad_lote['.$data_listado[$i]['id_compras_orden_articulo'].']" name="cantidad_lote" id="cantidad_lote_'.$data_listado[$i]['id_compras_orden_articulo'].'"/>
								<input type="hidden" id="um_x_embalaje" data-campo="um_x_embalaje['.$data_listado[$i]['id_compras_orden_articulo'].']" value="'.$data_listado[$i]['um_x_embalaje'].'">
								<input type="hidden" id="um_x_presentacion" data-campo="um_x_presentacion['.$data_listado[$i]['id_compras_orden_articulo'].']" value="'.$data_listado[$i]['um_x_presentacion'].'">								
								<input type="hidden" id="unidad_minima" data-campo="unidad_minima['.$data_listado[$i]['id_compras_orden_articulo'].']" value="'.$data_listado[$i]['unidad_minima'].'">
								<input type="hidden" id="cl_um" data-campo="cl_um['.$data_listado[$i]['id_compras_orden_articulo'].']" value="'.$data_listado[$i]['cl_um'].'">
								
							</td>
							<td>
								<span name="proveedor">'.$data_listado[$i]['nombre_comercial'].'</span>
								<input type="hidden" value="'.$data_listado[$i]['id_compras_orden_articulo'].'" data-campo="id_compras_orden_articulo['.$data_listado[$i]['id_compras_orden_articulo'].']" id="idarticuloprecios_'.$data_listado[$i]['id_compras_orden_articulo'].'"/>
								<input type="hidden" id="proveedor_'.$data_listado[$i]['id_compras_orden_articulo'].'" value="'.$data_listado[$i]['nombre_comercial'].'">
							</td>
							<td>
								<input type="hidden" id="articulo_'.$data_listado[$i]['id_compras_orden_articulo'].'" value="'.$data_listado[$i]['articulo'].' - '.$peso_unitario.' '.$data_listado[$i]['cl_um'].' '.$data_listado[$i]['upc'].'">
								<ul class="tooltips">
									<span>'.$data_listado[$i]['articulo'].' - '.$peso_unitario.' '.$data_listado[$i]['cl_um'].'<br/>'.$data_listado[$i]['upc'].'</span>
								</ul>
							</td>
							<td>
								'.$embalaje.$presentacion_x_embalaje.' '.$data_listado[$i]['presentacion'].'
								<input type="hidden" id="presentacion_'.$data_listado[$i]['id_compras_orden_articulo'].'" value="'.$embalaje.$presentacion_x_embalaje.' '.$data_listado[$i]['presentacion'].'">
							</td>
							<td class="right">
								<input type="hidden" id="costo_sin_impuesto_'.$data_listado[$i]['id_compras_orden_articulo'].'" value="'.$data_listado[$i]['costo_sin_impuesto'].'"/>
								<span class="add-on">'.$moneda.'</span> '.number_format($data_listado[$i]['costo_sin_impuesto'],2).'
							</td>
							<td class="right">
									<input type="hidden" id="cantidad_'.$data_listado[$i]['id_compras_orden_articulo'].'" value="'.$data_listado[$i]['cantidad'].'" data-campo="cantidad['.$data_listado[$i]['id_compras_orden_articulo'].']"/>
									<input type="hidden" id="cantidad_resta_'.$data_listado[$i]['id_compras_orden_articulo'].'" value="'.$data_listado[$i]['cantidad'].'" data-campo="cantidad_resta['.$data_listado[$i]['id_compras_orden_articulo'].']"/>
									<span>'.number_format($data_listado[$i]['cantidad'],2).' Pz</span>
							</td>
							<td class="right">
								<input type="hidden" name="costo_x_cantidad_hidden[]" id="costo_x_cantidad_hidden' .$data_listado[$i]['id_compras_orden_articulo'].'" value="'.$data_listado[$i]['costo_x_cantidad'].'" data-campo="costo_x_cantidad_hidden['.$data_listado[$i]['id_compras_orden_articulo'].']"/>
								<span class="add-on">'.$moneda.'</span> 
								<span id="costo_x_cantidad'.$data_listado[$i]['id_compras_orden_articulo'].'">'.number_format($data_listado[$i]['costo_x_cantidad'],2).'</span>
							</td>
							<td class="right">
				                  	<input type="hidden" name="descuento[]" id="descuento_'.$data_listado[$i]['id_compras_orden_articulo'].'" value="'.$data_listado[$i]['descuento'].'" data-campo="descuento['.$data_listado[$i]['id_compras_orden_articulo'].']"/>
				                 	<span>'.number_format($data_listado[$i]['descuento'],2).' %</span>
							</td>
							<td class="right">
								<input type="hidden" class="subtotal" name="subtotal__hidden[]" id="subtotal__hidden'.$data_listado[$i]['id_compras_orden_articulo'].'" value ="'.$data_listado[$i]['subtotal'].'"data-campo="subtotal__hidden['.$data_listado[$i]['id_compras_orden_articulo'].']"/>
				                  <span class="add-on">'.$moneda.'</span> 
				                  <span id="subtotal_'.$data_listado[$i]['id_compras_orden_articulo'].'">'.number_format($data_listado[$i]['subtotal'],2).'</span>
							</td>
							<td class="right">
								<input type="hidden" value ="'.$data_listado[$i]['impuesto_porcentaje'].'" data-campo="impuesto['.$data_listado[$i]['id_compras_orden_articulo'].']" id="impuesto_'.$data_listado[$i]['id_compras_orden_articulo'].'"name="impuesto['.$data_listado[$i]['id_compras_orden_articulo'].']" />
								'.number_format($data_listado[$i]['impuesto_porcentaje'],0).'
								<span class="add-on">%</span>
							</td>
							<td class="right">
								<input type="hidden" value="'.$data_listado[$i]['valor_impuesto'].'" name="valor_hidden_impuesto[]" id="valor_hidden_impuesto_'.$data_listado[$i]['id_compras_orden_articulo'].'" data-campo="valor_hidden_impuesto['.$data_listado[$i]['id_compras_orden_articulo'].']"/>
								<span class="add-on">'.$moneda.'</span> 
								<span id="valor_impuesto_'.$data_listado[$i]['id_compras_orden_articulo'].'">'.number_format($data_listado[$i]['valor_impuesto'],2).'</span>
							</td>
							<td class="right">
								<strong>
								<input type="hidden" value="'.$data_listado[$i]['total'].'" id="total_hidden_'.$data_listado[$i]['id_compras_orden_articulo'].'" data-campo="total_hidden['.$data_listado[$i]['id_compras_orden_articulo'].']"/>
								<span class="add-on">'.$moneda.'</span> 
								<span id="total_'.$data_listado[$i]['id_compras_orden_articulo'].'">'.number_format($data_listado[$i]['total'],2).'</span>
								</strong>
							</td>
							<td class="center"><input type="checkbox" class="requerido" id="listado_'.$data_listado[$i]['id_compras_orden_articulo'].'" data-campo="aceptar['.$data_listado[$i]['id_compras_orden_articulo'].']"  name="aceptar[]"  onclick="calculos('.$data_listado[$i]['id_compras_orden_articulo'].')"value="'.$data_listado[$i]['id_compras_orden_articulo'].'">
							</td>
						</tr>';
			}
		}
		else{
			$style_table='display:none';
			$table='';
		}
		$data='';
		$proveedores    = $this->ordenes_model->db_get_proveedores($data,$detalle[0]['id_proveedor']);
		$sucursales	    = $this->sucursales_model->get_orden_unico_sucursal($detalle[0]['id_sucursal']);
		$forma_pago	    = $this->formas_de_pago_model->get_orden_unico_formapago($detalle[0]['id_forma_pago']);
		$creditos	    = $this->creditos_model->get_orden_unico_credito($detalle[0]['id_credito']);
		$orden_tipo	    = $this->ordenes_model->db_get_tipo_orden($detalle[0]['id_orden_tipo']);
		
		$fec=explode('-',$detalle[0]['entrega_fecha']);
		$entrega_fecha=$fec[2].'/'.$fec[1].'/'.$fec[0];
		$fec2=explode('-',$detalle[0]['orden_fecha']);
		$orden_fecha=$fec2[2].'/'.$fec2[1].'/'.$fec2[0];
		$tabData['id_compras_orden']		 = $id_compras_orden;
		$tabData['orden_num']   			 = $this->lang_item("orden_num",false);
        $tabData['proveedor'] 	 			 = $this->lang_item("proveedor",false);
		$tabData['sucursal']     			 = $this->lang_item("sucursal",false);
        $tabData['orden_fecha']   		     = $this->lang_item("orden_fecha",false);
		$tabData['entrega_fecha']            = $this->lang_item("entrega_fecha",false);
        $tabData['observaciones']    	     = $this->lang_item("observaciones",false);
        $tabData['forma_pago']     			 = $this->lang_item("forma_pago",false);
		$tabData['articulo']  			 	 = $this->lang_item("articulo",false);
		$tabData['costo_unitario']	 		 = $this->lang_item("costo_unitario",false);
		$tabData['cantidad']  			 	 = $this->lang_item("cantidad",false);
		$tabData['costo_cantidad']  	     = $this->lang_item("costo_cantidad",false);
		$tabData['descuento']  			 	 = $this->lang_item("descuento",false);
		$tabData['subtotal']  			 	 = $this->lang_item("subtotal",false);
		$tabData['imp']  			 		 = $this->lang_item("imp",false);
		$tabData['valor_imp']  			 	 = $this->lang_item("valor_imp",false);
		$tabData['total']  			 		 = $this->lang_item("total",false);
		$tabData['accion']  				 = $this->lang_item("accion",false);
		$tabData['impuesto']  				 = $this->lang_item("impuesto",false);
		$tabData['a_pagar']  				 = $this->lang_item("a_pagar",false);
		$tabData['cerrar_orden']  		 	 = $this->lang_item("cerrar_orden",false);
		$tabData['cancelar_orden']			 = $this->lang_item("cancelar_orden",false);
		$tabData['presentacion']			 = $this->lang_item("presentacion",false);
		$tabData['consecutivo']				 = $this->lang_item("consecutivo",false);
		$tabData['moneda']				 	 = $moneda;
		$tabData['aceptar_orden']			 = $this->lang_item("aceptar_orden",false);
		$tabData['devolucion_orden']		 = $this->lang_item("devolucion_orden",false);
		$tabData['no_factura']		 		 = $this->lang_item("no_factura",false);
		$tabData['fecha_factura']		 	 = $this->lang_item("fecha_factura",false);
		$tabData['#']		 	 			 = $this->lang_item("#",false);
		$tabData['costo_unitario']		 	 = $this->lang_item("costo_unitario",false);
		$tabData['costo_cantidad']		 	 = $this->lang_item("costo_cantidad",false);
		$tabData['valor_imp']		 	 	 = $this->lang_item("valor_imp",false);
		$tabData['aceptar']		 	 		 = $this->lang_item("aceptar",false);
		$tabData['comentarios_entrada']		 = $this->lang_item("comentarios_entrada",false);
		$tabData['recibir_enetrada']		 = $this->lang_item("recibir_enetrada",false);
		$tabData['rechazar_entrada']		 = $this->lang_item("rechazar_entrada",false);
		//DATA
		$tabData['orden_num_value']	 		 = $detalle[0]['orden_num'];
		$tabData['estatus']	 		 		 = $detalle[0]['estatus'];
		$tabData['observaciones_value']	 	 = $detalle[0]['observaciones'];
		$tabData['fecha_registro']	 	 	 = $detalle[0]['timestamp'];
		$tabData['list_sucursales']			 = $sucursales[0]['sucursal'];
		$tabData['orden_fecha_value']	 	 = $orden_fecha;
		$tabData['entrega_fecha_value']	     = $entrega_fecha;
		$tabData['list_forma_pago']			 = $forma_pago[0]['forma_pago'];
		$tabData['table']					 = $table;
		$tabData['style_table']				 = $style_table;

		$uri_view  = $this->path.$this->submodulo.'/'.$accion;
		if(!$uso_interno){
			echo json_encode( $this->load_view_unique($uri_view ,$tabData, true));
		}else{
			$includes['css'][]  = array('name' => 'style.default', 'dirname' => '');
			$includes['css'][]  = array('name' => 'estilos-custom', 'dirname' => '');
			return $this->load_view_unique($uri_view ,$tabData, true, $includes);
		}
	}
	public function insert(){
		$id_almacen_lobby = $this->vars->cfg['id_almacen_lobby'];
		// $id_pasillo_lobby = $this->vars->cfg['id_pasillo_lobby'];
		$id_gaveta_lobby  = $this->vars->cfg['id_gaveta_lobby'];
		// Recibe datos de formulario e inserta un nuevo registro en la BD
		$incomplete  = $this->ajax_post('incomplete');
		$cont=0;
		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			$json_respuesta = array(
						 'id' 		=> 0
						,'contenido'=> alertas_tpl('error', $msg ,false)
						,'success' 	=> false
				);
		}else{
			$id_compras_orden = $this->ajax_post('id_compras_orden');
			$num_factura 	  = $this->ajax_post('no_factura');
			$fec 			  = explode('/',$this->ajax_post('fecha_factura'));
			$fecha_factura    = $fec[2].'-'.$fec[1].'-'.$fec[0];
			$comentario 	  = $this->ajax_post('comentario');
			$subtotal 		  = $this->ajax_post('subtotal');
			$descuento_total  = $this->ajax_post('descuento_total');
			$impuesto_total   = $this->ajax_post('impuesto_total');
			$total 			  = $this->ajax_post('total');
			$cont 			  = 0;
			$sqlData = array(
						'id_compras_orden'	 => $id_compras_orden
						,'num_factura' 		 => $num_factura
						,'fecha_factura' 	 => $fecha_factura
						,'subtotal' 	 	 => $subtotal
						,'descuento' 	 	 => $descuento_total
						,'impuesto' 	 	 => $impuesto_total
						,'total' 		 	 => $total
						,'comentario' 		 => $comentario
						,'id_usuario' 		 => $this->session->userdata('id_usuario')
						,'timestamp'  		 => $this->timestamp()
						);
			$id = $this->db_model->insert($sqlData);
			if($id){
				$id_articulo_tipo 	= $this->ajax_post('id_articulo_tipo');
				$um_x_embalaje 		= $this->ajax_post('um_x_embalaje');
				$um_x_presentacion 	= $this->ajax_post('um_x_presentacion');
				$unidad_minima 		= $this->ajax_post('unidad_minima');
				$lote_val 			= $this->ajax_post('lote_modal');
				$caducidad_val 		= $this->ajax_post('caducidad_modal');
				$candidad_modal 	= $this->ajax_post('candidad_modal');
				$chek_box         	= $this->ajax_post('aceptar');
				$values_lote=array_values($lote_val);
				$values_caducidad=array_values($caducidad_val);
				$values_cantidad=array_values($candidad_modal);

				$keys=array_keys($lote_val);
				//CONSTRUCCION DE ARRAY QUE GUARDA DATOS PARA INSERT
				for($i=0; count($lote_val)>$i;$i++){
					$data[]=explode('-',$keys[$i]);
					$array[][$data[$i][0]]=array(
											$data[$i][0],
											$values_lote[$i],
											$values_cantidad[$i],
											$values_caducidad[$i],
											$id_articulo_tipo[$data[$i][0]],
											$chek_box[$data[$i][0]],
											$um_x_presentacion[$data[$i][0]],
											$unidad_minima[$data[$i][0]]
										);
				}	

				for($j=0; count($lote_val)>$j;$j++){
						$data[]=explode('-',$keys[$j]);
						$fec=explode('/',$array[$j][$data[$j][0]][3]);
						$caducidad=$fec[2].'-'.$fec[1].'-'.$fec[0];
						if($array[$j][$data[$j][0]][5]=='true'){
							//SQL PARA INSERTAR EN ENTRASDAS RECEPCION PARTIDAS
							$sqldata= array(
											'id_almacen_entradas_recepcion' => $id,
											'id_compras_orden_articulo'   => $array[$j][$data[$j][0]][0],
											'lote'						  => $array[$j][$data[$j][0]][1],
											'cantidad'					  => $array[$j][$data[$j][0]][2],
											'caducidad'					  => $caducidad,
											'timestamp'  	 		      => $this->timestamp(),
											'id_usuario'   		   		  => $this->session->userdata('id_usuario')
											);
							$insert_partidas = $this->db_model->insert_entradas_partidas($sqldata);	
							if($insert_partidas){
								if($array[$j][$data[$j][0]][4]==2){//SE VALIDA EL TIPO DE ARITUCLO
									$um_presentacion = $array[$j][$data[$j][0]][2]*$array[$j][$data[$j][0]][6];//CANTIDAD*PRESENTACION
									$presentacion_UM = $um_presentacion*$array[$j][$data[$j][0]][7];//UM_PRESENTACION*UNIDAD_MINIMA
									$stock_um	     = $presentacion_UM;
								}else{
									$stock_um   = $array[$j][$data[$j][0]][2];//CANTIDAD
								}
								//SE CREA SQL PARA INSERTAR EN LA TABLA DE STOCK
								$sqldata_stock=array(
												'id_almacen'		   	   		=> $id_almacen_lobby,
												'id_gaveta'		   	   	   		=> $id_gaveta_lobby,
												'id_almacen_entradas_recepcion' => $id,
												'id_compras_orden_articulo' 	=> $array[$j][$data[$j][0]][0],
												'id_articulo_tipo' 				=> $array[$j][$data[$j][0]][4],
												'stock'							=> $array[$j][$data[$j][0]][2],
												'stock_um'						=> $stock_um,
												'lote'							=> $array[$j][$data[$j][0]][1],
												'caducidad'						=> $caducidad,
												'id_estatus' 					=> 1,
												'timestamp'  	 		    	=> $this->timestamp(),
												'id_usuario'   		   			=> $this->session->userdata('id_usuario'),
												'activo'						=> 1
												);
								$id_stock = $this->db_model->insert_entradas_stock($sqldata_stock);
								if($id_stock){
									$sqldata_stock_logs=array(
												'id_accion'			  		    => $this->vars->cfg['id_accion_almacen_recepcion'], #1 => RECEPCION
												'id_almacen_entradas_recepcion' => $id,
												'id_compras_orden_articulo'  	=> $array[$j][$data[$j][0]][0],
												'id_stock' 						=> $id_stock,
												'log_id_almacen_destino'		=> $id_almacen_lobby,
												'log_id_gaveta_destino'		   	=> $id_gaveta_lobby,
												'log_stock_origen'			    => $array[$j][$data[$j][0]][2],
												'log_stock_um_origen'			=> $stock_um,
												'log_lote'						=> $array[$j][$data[$j][0]][1],
												'log_caducidad'					=> $caducidad,
												'timestamp'  	 		    	=> $this->timestamp(),
												'id_usuario'   		   			=> $this->session->userdata('id_usuario'),
												'activo'						=> 1
												);
									$id_stock_logs = $this->stock_model->insert_stock_log($sqldata_stock_logs);
								}else{$cont++;}
							}else{$cont++;}					
						}else{//NO INSERTA EN PARTIDAS SI NO ESTA CHECKEADO EL LISTADO
						}
				}
				//dump_var($sqldata);
				//ACTUAIZAR A LA ORDEN
				if($cont==0){
					$sqldata3= array(
							'id_compras_orden' 			   => $id_compras_orden,
							'estatus'					   => 8,
							'edit_timestamp'  	 		   => $this->timestamp(),
							'edit_id_usuario'   		   => $this->session->userdata('id_usuario')
							);
					$update = $this->ordenes_model->db_update_data($sqldata3);
					if($update){
						$msg = $this->lang_item("msg_insert_success",false);
						$json_respuesta = array(
									 'id' 		=> 0
									,'contenido'=> alertas_tpl('success', $msg ,false)
									,'success' 	=> false
							);
					}
				}else{
					$msg = $this->lang_item("msg_query_insert",false);
					$json_respuesta = array(
							 'id' 		=> 0
							,'contenido'=> alertas_tpl('error', $msg ,false)
							,'success' 	=> false
					);
				}
			}else{
				$msg = $this->lang_item("msg_query_insert",false);
				$json_respuesta = array(
						 'id' 		=> 0
						,'contenido'=> alertas_tpl('error', $msg ,false)
						,'success' 	=> false
				);
			}
		}
		echo json_encode($json_respuesta);
	}
	public function modal_lote_caducidad(){		
		$id = $this->ajax_post('id');
		$caducidad = $this->ajax_post('caducidad_val');
		$lote = $this->ajax_post('lote_val');
		$cantidad_lote = $this->ajax_post('cantidad_lote');		
		$proveedor = $this->ajax_post('proveedor');
		$articulo = $this->ajax_post('articulo');
		$presentacion = $this->ajax_post('presentacion');
		
		// template html modal
		$tabData_modal = array('id'=>$id, 'caducidad'=>$caducidad, 'lote'=>$lote, 'cantidad_lote'=>$cantidad_lote, 'proveedor_val'=>$proveedor,'articulo_val'=>$articulo, 'presentacion_val'=>$presentacion);
		$tabData_modal['lbl_lote'] 			= $this->lang_item("lote",true);
		$tabData_modal['lbl_caducidad'] 	= $this->lang_item("caducidad",true);
		$tabData_modal['lbl_cantidad'] 		= $this->lang_item("cantidad",true);
		$tabData_modal['recibir_lote'] 	    = $this->lang_item("recibir_lote",true);
		$tabData_modal['devolver_lote'] 	= $this->lang_item("devolver_lote",true);
		$tabData_modal['proveedor'] 	 	= $this->lang_item("proveedor",false);
		$tabData_modal['articulo']  	    = $this->lang_item("articulo",false);
		$tabData_modal['presentacion']		= $this->lang_item("presentacion",false);
		
		$url_modal_tpl = $this->modulo.'/'.$this->seccion.'/'.$this->submodulo.'/'.'modal_lote_caducidad';
		$html = $this->load_view_unique($url_modal_tpl ,$tabData_modal, true);
		// Cargar modal
		$titulo_modal = $this->lang_item("modal_titulo",true);
		$arg_body   = array(
							'header'=>array('id'=> 1,'html'=>$titulo_modal),
							'body' =>array('id'=> 'test','html'=>$html), 
							'footer' => array('id'=> 'test','html'=>''));
		$html_modal = toggle_modal_tpl('lote', $arg_body);
		echo json_encode($html_modal);
	}
}
?>