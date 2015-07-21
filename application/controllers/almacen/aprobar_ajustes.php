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
		$this->icon 			= 'fa fa-wrench'; //Icono de modulo
		$this->path 			= $this->modulo.'/'.$this->submodulo.'/'; //almacen/entradas_recepcion/
		$this->view_content 	= 'content';
		$this->limit_max		= 10;
		$this->offset			= 0;
		// Tabs
		//$this->tab1 			= 'agregar';
		$this->tab1 			= 'listado';
		$this->tab2 			= 'detalle';
		// DB Model
		$this->load->model($this->modulo.'/'.$this->seccion.'_model','db_model');		
		$this->load->model($this->modulo.'/catalogos_model','catalogos_model');
		$this->load->model('stock_model','stock_model');

		// Diccionario
		//$this->lang->load($this->modulo.'/'.$this->submodulo,"es_ES");
		// Tabs
		$this->tab_inicial 		= 2;
		$this->tab_indice 		= array(
									 $this->tab1
									,$this->tab2
								);
		for($i=0; $i<=count($this->tab_indice)-1; $i++){
			$this->tab[$this->tab_indice[$i]] = $this->tab_indice[$i];
		}
	}
	public function config_tabs(){
		$tab_1 	= $this->tab1;
		$tab_2 	= $this->tab2;
		//$tab_3 	= $this->tab3;
		$path  	= $this->path;
		$pagina =(is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		// Nombre de Tabs
		$config_tab['names']    = array(
										 $this->lang_item($tab_1) //agregar
										,$this->lang_item($tab_2) //listado
								); 
		// Href de tabs
		$config_tab['links']    = array(
										$path.$tab_1.'/'.$pagina //compras/listado_precios/listado/pagina
										,$path.$tab_2            //compras/listado_precios/agregar
								); 
		// Accion de tabs
		$config_tab['action']   = array(
										'load_content'
										,''
								);
		// Atributos 
		$config_tab['attr']     = array('', array('style' => 'display:none'));
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
				// Evento de enlace
				// Acciones
				$accion_id 						= $value['id_almacen_ajuste'];
				$btn_acciones['agregar'] 		= '<span id="ico-articulos_'.$accion_id.'" class="ico_detalle fa fa-search-plus" onclick="detalle('.$accion_id.')" title="'.$this->lang_item("agregar_articulos").'"></span>';
				$acciones = implode('&nbsp;&nbsp;&nbsp;',$btn_acciones);

				//$peso_unitario = (substr($value['peso_unitario'], strpos($value['peso_unitario'], "." ))=='.000')?number_format($value['peso_unitario'],0):$value['peso_unitario'];
				//$presentacion_x_embalaje = (substr($value['presentacion_x_embalaje'], strpos($value['presentacion_x_embalaje'], "." ))=='.000')?number_format($value['presentacion_x_embalaje'],0):$value['presentacion_x_embalaje'];
				//$embalaje = ($value['embalaje'])?$value['embalaje'].' CON ':'';
				//$stock = (substr($value['stock'], strpos($value['stock'], "." ))=='.000' && $value['articulo_tipo']!=strtoupper('INSUMO'))?number_format($value['stock'],0).' '.$this->lang_item("pieza_abrev"):$value['stock'].' '.$value['unidad_minima_cve'];
				// Datos para tabla
				$tbl_data[] = array('id'             	=> $value['id_stock'],
									'articulo'  	 	=> $value['articulo'],
									'stock_origen'   	=> $value['stock_origen'].'-'.$value['cl_um'],
									'stock_um_origen'   => $value['stock_um_origen'].'-'.$value['cl_um'],
									'stock_mov'   	 	=> $value['stock_mov'].'-'.$value['cl_um'],
									'stock_um_mov'   	=> $value['stock_um_mov'].'-'.$value['cl_um'],
									'stock_final'   	=> $value['stock_final'].'-'.$value['cl_um'],
									'stock_um_final'   	=> $value['stock_um_final'].'-'.$value['cl_um'],
									//'almacen'   	 	=> $value['cl_almacen'],
									//'pasillo'   	 	=> $value['cl_pasillo'],
									//'gavetas'   	 	=> $value['cl_gaveta'],
									'acciones' 		 	=> $acciones
									);


			}

			// Plantilla
			$tbl_plantilla = array ('table_open'  => '<table id="tbl_grid" class="table table-bordered responsive ">');
			// Titulos de tabla
			$this->table->set_heading(	$this->lang_item("id_stock"),
										$this->lang_item("articulo"),										
										$this->lang_item("stock_origen"),
										$this->lang_item("stock_um_origen"),
										$this->lang_item("stock_mov"),
										$this->lang_item("stock_um_mov"),
										$this->lang_item("stock_final"),
										$this->lang_item("stock_um_final"),
										//$this->lang_item("almacen"),
										//$this->lang_item("pasillo"),
										//$this->lang_item("gaveta"),
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
		$detalle  		   = $this->db_model->get_data_unico($id_almacen_ajuste);
		$view 			   = $this->tab['detalle'];
		//DATA
		$accion_id 						 = $detalle[0]['id_almacen_ajuste'];
		$btn_save          				 = form_button(array('class'=>"btn btn-primary",'name' => 'ajuste_save','onclick'=>'agregar('.$accion_id.')' , 'content' => $this->lang_item("btn_guardar") ));
		$tabData['articulo']	         = $detalle[0]['articulo'];
		$tabData['cl_almacen']	         = $detalle[0]['cl_almacen'];
		$tabData['cl_gaveta']	         = $detalle[0]['cl_gaveta'];
		$tabData['cl_pasillo']	         = $detalle[0]['cl_pasillo'];
		$tabData['stock_origen']	     = $detalle[0]['stock_origen'];
		$tabData['stock_um_origen']      = $detalle[0]['stock_um_origen'];
		$tabData['stock_mov']	         = $detalle[0]['stock_mov'];
		$tabData['stock_um_mov']	     = $detalle[0]['stock_um_mov'];
		$tabData['stock_final']	         = $detalle[0]['stock_final'];
		$tabData['stock_um_final']	     = $detalle[0]['stock_um_final'];
		$tabData['cl_um']			     = $detalle[0]['cl_um'];
		$tabData['button_save']			 = $btn_save;
		
		//DIC
		$tabData['lbl_articulo']	     = $this->lang_item("articulo",false);
		$tabData['lbl_cl_almacen']	     = $this->lang_item("cl_almacen",false);
		$tabData['lbl_cl_gaveta']	     = $this->lang_item("cl_gaveta",false);
		$tabData['lbl_cl_pasillo']	     = $this->lang_item("cl_pasillo",false);
		$tabData['lbl_stock_origen']	 = $this->lang_item("stock_origen",false);
		$tabData['lbl_stock_um_origen']  = $this->lang_item("stock_um_origen",false);
		$tabData['lbl_stock_mov']	     = $this->lang_item("stock_mov",false);
		$tabData['lbl_stock_um_mov']	 = $this->lang_item("stock_um_mov",false);
		$tabData['lbl_stock_final']	     = $this->lang_item("stock_final",false);
		$tabData['lbl_stock_um_final']	 = $this->lang_item("stock_um_final",false);
		$tabData['lbl_cl_um']			 = $this->lang_item("cl_um",false);
		
		$uri_view  = $this->modulo.'/'.$this->seccion.'/'.$this->submodulo.'/'.$view;

		echo json_encode( $this->load_view_unique($uri_view ,$tabData, true));
	}
	public function agregar(){
		$id_almacen_ajuste = $this->ajax_post('id_almacen_ajuste');
		dump_var($id_almacen_ajuste);
	}
	/*public function agregar(){
		$view = $this->tab['agregar'];
		$btn_save      = form_button(array('class'=>"btn btn-primary",'name' => 'ajuste_save','onclick'=>'agregar()' , 'content' => $this->lang_item("btn_guardar") ));
		$listado_almacen=$this->catalogos_model->db_get_data_almacen();
		$dropArray = array(
				 'data'		=> $listado_almacen
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

		$lts_almacen  		    = dropdown_tpl($dropArray);
		$tabData['lts_almacen'] = $lts_almacen;
		$tabData['button_save'] = $btn_save;

		$uri_view  = $this->modulo.'/'.$this->seccion.'/'.$this->submodulo.'/'.$view;
		if($this->ajax_post(false)){
			echo json_encode( $this->load_view_unique($uri_view ,$tabData, true));
		}else{
			echo json_encode( $this->load_view_unique($uri_view ,$tabData, true,$includes));
		}
	}
	
	public function load_stock(){
		$id_articulo   =  $this->ajax_post('id_articulo');
		$id_almacen    =  ($this->ajax_post('id_almacen')!=0)?$this->ajax_post('id_almacen'):'';
		$id_pasillo    =  ($this->ajax_post('id_pasillo')!=0)?$this->ajax_post('id_pasillo'):'';
		$id_gavetas    =  ($this->ajax_post('id_gavetas')!=0)?$this->ajax_post('id_gavetas'):'';

		$slqdata=array(
					'id_articulo'=> $id_articulo,
					'id_almacen' => $id_almacen,
					'id_pasillo' => $id_pasillo, 
					'id_gavetas' => $id_gavetas);

		$detalle  = $this->db_model->db_get_data_x_articulo($slqdata);
		$stock=0;
		$stock_um=0;
		for($i=0; count($detalle)>$i;$i++){
			$stock+=$detalle[$i]['stock'];
			$stock_um+=$detalle[$i]['stock_um'];
		}
		$um= $detalle[0]['unidad_minima_cve'];
		$data=array('stock'=> $stock,'stock_um'=>$stock_um, 'u_m_cv'=>$um);
		echo json_encode($data);
	}
	public function load_gaveta_pas(){
		$id_almacen  = $this->ajax_post('id_almacen');
		$vent 		 ='load_gaveta';
		$name 		 = 'lts_pasillos';
		$name_gav 	 = 'lts_gavetas';
		$event_2	 ='load_articulos';

		$datasql=array('id_almacen'=>$id_almacen);
		$dropArray = array(
				 'data'		=> $this->catalogos_model->db_get_data_pasillos_por_almacen($datasql)
				,'value' 	=> 'id_almacen_pasillos'
				,'text' 	=> array('clave_corta','pasillos')
				,'name' 	=> $name
				,'event'    => array('event'       => 'onchange',
			   						 'function'    => $vent,
			   						 'params'      => array('this.value'),
			   						 'params_type' => array(0)
								)
			);
		$dropArray2 = array(
				 'data'		=> $this->catalogos_model->db_get_data_gavetas_por_almacen($datasql)
				,'value' 	=> 'id_almacen_gavetas'
				,'text' 	=> array('clave_corta','gavetas')
				,'name' 	=> $name_gav
				,'event'    => array('event'       => 'onchange',
			   						 'function'    => $event_2,
			   						 'params'      => array('this.value'),
			   						 'params_type' => array(0)
									)
			);
		//se cargar el select de lso articulos para modificar su cantidad
		$datasql=array(
				'id_almacen' => $id_almacen,
				'id_pasillo' => '',
				'id_gaveta'  => '');
		$dropArray3 = array(
				 'data'		=> $this->db_model->db_get_data_articulos($datasql)
				,'value' 	=> 'id_articulo'
				,'text' 	=> array('cl_um','articulo')
				,'name' 	=> "lts_ajustes"
				,'event'    => array('event'       => 'onchange',
				   						 'function'    => 'load_stock',
				   						 'params'      => array('this.value'),
				   						 'params_type' => array(0)
   									)
			);
		$lts_pasillo  = dropdown_tpl($dropArray);
		$lts_gavetas  = dropdown_tpl($dropArray2);
		$lts_ajustes  = dropdown_tpl($dropArray3);

		$data['pasillos']    = $lts_pasillo;
		$data['gavetas']     = $lts_gavetas;
		$data['lts_ajustes'] = $lts_ajustes;
		echo json_encode($data);
	}
	public function load_gaveta(){
		$id_almacen    =  ($this->ajax_post('id_almacen')!=0)?$this->ajax_post('id_almacen'):'';
		$id_pasillo    =  ($this->ajax_post('id_pasillo')!=0)?$this->ajax_post('id_pasillo'):'';
		$id_gaveta     =  ($this->ajax_post('id_gaveta')!=0)?$this->ajax_post('id_gaveta'):'';
		$event='load_articulos';
		$name = 'lts_gavetas';

		if($id_pasillo==0){
			$datasql=array('id_almacen'=>$id_almacen);
			$dropArray = array(
							 'data'		=> $this->catalogos_model->db_get_data_gavetas_por_almacen($datasql)
							,'value' 	=> 'id_almacen_gavetas'
							,'text' 	=> array('clave_corta','gavetas')
							,'name' 	=> $name
							,'event'    => array('event'       => 'onchange',
						   						 'function'    => $event,
						   						 'params'      => array('this.value'),
						   						 'params_type' => array(0)
		   									)
						);
		}else{
			$datasql=array('id_pasillo'=>$id_pasillo);
			$dropArray = array(
							 'data'		=> $this->catalogos_model->db_get_data_gavetas_por_pasillo($datasql)
							,'value' 	=> 'id_almacen_gavetas'
							,'text' 	=> array('clave_corta','gavetas')
							,'name' 	=> $name
							,'event'    => array('event'       => 'onchange',
						   						 'function'    => $event,
						   						 'params'      => array('this.value'),
						   						 'params_type' => array(0)
	   										)
						);
		}
		$datasql=array(
					'id_almacen' => $id_almacen,
					'id_pasillo' => $id_pasillo,
					'id_gaveta'  => '');
		$dropArray3 = array(
						 'data'		=> $this->db_model->db_get_data_articulos($datasql)
						,'value' 	=> 'id_articulo'
						,'text' 	=> array('cl_um','articulo')
						,'name' 	=> "lts_ajustes"
						,'class' 	=> "requerido"
						,'event'    => array('event'       => 'onchange',
						   						 'function'    => 'load_stock',
						   						 'params'      => array('this.value'),
						   						 'params_type' => array(0)
		   									)
					);
		$lts_ajustes  = dropdown_tpl($dropArray3);
		$lts_gavetas  = dropdown_tpl($dropArray);
		$data['lts_gavetas'] = $lts_gavetas;
		$data['lts_ajustes'] = $lts_ajustes;
		echo json_encode($data);
	}
	public function load_articulos(){
		$id_almacen   =  ($this->ajax_post('id_almacen')!=0)?$this->ajax_post('id_almacen'):'';
		$id_pasillo   =  ($this->ajax_post('id_pasillo')!=0)?$this->ajax_post('id_pasillo'):'';
		$id_gaveta    =  ($this->ajax_post('id_gaveta')!=0)?$this->ajax_post('id_gaveta'):'';

		$datasql=array(
					'id_almacen' => $id_almacen,
					'id_pasillo' => $id_pasillo,
					'id_gaveta'  => $id_gaveta);

		$dropArray3 = array(
						 'data'	 => $this->db_model->db_get_data_articulos($datasql)
						,'value' => 'id_articulo'
						,'text'  => array('cl_um','articulo')
						,'name'  => "lts_ajustes"
						,'class' => "requerido"
						,'event' => array(
										'event'       => 'onchange',
				   						 'function'    => 'load_stock',
				   						 'params'      => array('this.value'),
				   						 'params_type' => array(0)
									)
			);
		$lts_ajustes  = dropdown_tpl($dropArray3);
		echo json_encode($lts_ajustes);
	}
	public function update(){
		$incomplete	 = $this->ajax_post('incomplete');
		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode( array( 'success'=>'false', 'mensaje' => alertas_tpl('error', $msg ,false)) );
		}else{
			$stock_mov 	  = $this->ajax_post('stock');
			$stock_um_mov = $this->ajax_post('stock_um_destino');
			$id_articulo  = $this->ajax_post('id_articulo');
			$id_almacen   = $this->ajax_post('id_almacen');
			$id_pasillo   = $this->ajax_post('id_pasillo');
			$id_gaveta 	  = $this->ajax_post('id_gavetas');
			$cont=0;
			$datasql=array(
					'id_articulo' => $id_articulo,
					'id_almacen'  => $id_almacen,
					'id_pasillo'  => $id_pasillo,
					'id_gaveta'   => $id_gaveta);

			$data=$this->db_model->get_data_stock($datasql);
			//realiza la resta de la cantidad a mover
			for($i=0;count($data)>$i;$i++){
				$realizar_insert = true;
				if($i==0){
					$cantidad = $data[$i]['stock']-$stock_mov;
					if($cantidad<=0){
						$stock    = 0;
						$stock_um = 0;
						//$status=0;
					}else{
						$stock    = $cantidad;
						$stock_um = $this->regla_de_tres($data[$i]['stock'], $data[$i]['stock_um'], $cantidad);
						($data[$i]['id_articulo_tipo']==2)?$stock_um=$stock_um:$stock_um=$cantidad;
					}
				}else{
					if($cantidad<=0){
						$cantidad  = $cantidad*-1;
						$stock_mov = $cantidad;
						$cantidad  = $data[$i]['stock']-$cantidad;
						$stock_um  = $this->regla_de_tres($data[$i]['stock'], $data[$i]['stock_um'], $cantidad);
						$stock_um_mov=$stock_um;
						if($cantidad<=0){
							$stock        = 0;
							$stock_um_mov = $data[$i]['stock_um'];
							$stock_um     = 0;
						}else{
							$stock    = $cantidad;							
							$stock_um = $this->regla_de_tres($data[$i]['stock'], $data[$i]['stock_um'], $cantidad);
							($data[$i]['id_articulo_tipo']==2)?$stock_um=$stock_um:$stock_um=$cantidad;
						}
					}else{
						$realizar_insert=false;
					}
				}
				if($realizar_insert){
					$slqData = array(
									'id_stock'  	  => $data[$i]['id_stock'],
									'stock_origen'    => $data[$i]['stock'],
									'stock_um_origen' => $data[$i]['stock_um'],
									'stock_mov'  	  => $stock_mov,
									'stock_um_mov'    => $stock_um_mov,
 									'stock_final'  	  => $stock,
									'stock_um_final'  => $stock_um,
									'id_articulo'     => $id_articulo,
									'id_almacen'      => $id_almacen,
									'id_pasillo'      => $id_pasillo,
									'id_gaveta'       => $id_gaveta,
									'estatus'         => 1,//en espera de aprobacion 
									'timestamp'  	  => $this->timestamp(),
									'id_usuario' 	  =>$this->session->userdata('id_usuario')
								);
					$insert=$this->db_model->insert($slqData);
				}
				
				 	/*($data_update_stock)?$cont=0:$cont++;
				if($data_update_stock){
					$insert_stock_logs=array(
						'id_accion'   					  =>	4,
						'id_almacen_entradas_recepcion'   =>	$data[$i]['id_almacen_entradas_recepcion'],
						'id_compras_orden_articulo'       =>	$data[$i]['id_compras_orden_articulo'],
						'id_stock'   					  =>	$data[$i]['id_stock'],
						'log_id_almacen_origen'   		  =>	$data[$i]['id_almacen'],
						'log_id_pasillo_origen'   		  =>	$data[$i]['id_pasillo'],
						'log_id_gaveta_origen'   		  =>	$data[$i]['id_gaveta'],
						'log_stock_origen'   			  =>	$data[$i]['stock'],
						'log_stock_um_origen'   		  =>	$data[$i]['stock_um'],
						'log_id_almacen_destino'   	      =>	$data[$i]['id_almacen'],
						'log_id_pasillo_destino'   	      =>	$data[$i]['id_pasillo'],
						'log_id_gaveta_destino'   		  =>	$data[$i]['id_gaveta'],
						'log_stock_destino'   			  =>	$stock,
						'log_stock_um_destino'   		  =>	$stock_um,
						'log_lote'   					  =>	$data[$i]['lote'],
						'log_caducidad'   		  		  =>	$data[$i]['caducidad'],
						'timestamp'   					  =>	$this->timestamp(),
						'id_usuario'   					  =>	$this->session->userdata('id_usuario')
						);
					$data_insert_stock_logs=$this->stock_model->insert_stock_log($insert_stock_logs);
					($data_insert_stock_logs)?$cont=0:$cont++;
				}
			}
			if($cont>0){
				$msg = $this->lang_item("msg_campos_obligatorios",false);
				echo json_encode( array( 'success'=>'false', 'mensaje' => alertas_tpl('error', $msg ,false)) );
			}else{
				$delArray = array('fisico'=>true);
				$this->eliminar_stock_en_cero($delArray);
				$msg = $this->lang_item("msg_update_success",false);
				echo json_encode(array(  'success'=>'true', 'mensaje' => $msg ));*/
		/*	}	
			//dump_var($slqData);
			if($insert){
				$msg = $this->lang_item("msg_update_success",false);
				echo json_encode(array(  'success'=>'true', 'mensaje' => $msg ));
			}else{
				$msg = $this->lang_item("msg_campos_obligatorios",false);
				echo json_encode( array( 'success'=>'false', 'mensaje' => alertas_tpl('error', $msg ,false)) );
			}	
		}
	}*/
}
?>