<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class proveedores extends Base_Controller { 

	private $modulo;
	private $submodulo;
	private $view_content;
	private $path;
	private $icon;
	private $offset, $limit_max;
	private $tab = array(), $tab_indice = array();


	public function __construct(){
		parent::__construct();
		$this->modulo 			= 'compras';
		$this->submodulo		= 'proveedores';
		$this->icon 			= 'fa fa-users'; #Icono de modulo
		$this->path 			= $this->modulo.'/'.$this->submodulo.'/';
		$this->view_content 	= 'content';
		$this->limit_max		= 5;
		$this->offset			= 0;

		$this->tab_indice 		= array(
									 'agregar'
									,'listado'
									,'detalle'
								);
		for($i=0; $i<=count($this->tab_indice)-1; $i++){
			$this->tab[$this->tab_indice[$i]] = $this->tab_indice[$i];
		}

		$this->load->model($this->modulo.'/'.$this->submodulo.'_model','db_model');
		$this->load->model('administracion/entidades_model','entidad');
		$this->lang->load($this->modulo.'/'.$this->submodulo,"es_ES");
	}
	public function config_tabs(){
		for($i=1; $i<=count($this->tab); $i++){
			${'tab_'.$i} = $this->tab [$this->tab_indice[$i-1]];
		}
		$path  	= $this->path;
		$pagina =(is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		
		$config_tab['names']    = array(
										 $this->lang_item($tab_1)
										,$this->lang_item($tab_2)
										,$this->lang_item($tab_3)
								); 
		$config_tab['links']    = array(
										 $path.$tab_1
										,$path.$tab_2.'/'.$pagina
										,$tab_3
								); 
		$config_tab['action']   = array(
										 'load_content'
										,'load_content'
										,''
								);
		$config_tab['attr']     = array('','', array('style' => 'display:none'));
		return $config_tab;
	}
	private function uri_view_principal(){
		return $this->modulo.'/'.$this->view_content;
	}
	public function index(){
		$tabl_inicial 			  = 2;
		$view_listado    		  = $this->listado();		
		$contenidos_tab           = $view_listado;
		$data['titulo_seccion']   = $this->lang_item("seccion");
		$data['titulo_submodulo'] = $this->lang_item("submodulo");
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		$js['js'][]               = array('name' => $this->submodulo, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal(), $data, $js);
	}
	public function listado($offset=0){
		$seccion 		= '';
		$filtro         = ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";
		$accion 		= $this->tab['listado'];
		$tab_detalle	= $this->tab['detalle'];
		$limit 			= $this->limit_max;
		$uri_view 		= $this->modulo.'/'.$accion;
		$url_link 		= $this->path.$seccion.$accion;		
		$sqlData = array(
						 'buscar'      	=> $filtro
						,'offset' 		=> $offset
						,'limit'      	=> $limit
						,'aplicar_limit'=> true
					);
		$uri_segment  = $this->uri_segment(); 
		$total_rows	  = $this->db_model->db_get_total_rows($sqlData);
		$list_content = $this->db_model->db_get_data($sqlData);
		$url          = base_url($url_link);
		$paginador    = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));


		if($total_rows>0){
			foreach ($list_content as $value) {
				// Evento de enlace
				$atrr = array(
								'href' => '#',
							  	'onclick' => $tab_detalle.'('.$value['id_compras_proveedor'].')'
						);
				// Datos para tabla
				$tbl_data[] = array('id'                => $value['razon_social'],
									'razon_social'      => tool_tips_tpl($value['razon_social'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'nombre_comercial'  => $value['nombre_comercial'],
									'rfc'               => $value['rfc'],
									'clave_corta'       => $value['clave_corta'],
									'entidad'           => $value['entidad']
									);
			}

			$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
			
			$this->table->set_heading(	$this->lang_item("id"),
										$this->lang_item("lbl_rsocial"),
										$this->lang_item("lbl_nombre"),
										$this->lang_item("lbl_rfc"),
										$this->lang_item("lbl_clv"),
										$this->lang_item("lbl_entidad")

									);
			$buttonTPL = array( 'text'       => $this->lang_item("btn_xlsx"), 
								'iconsweets' => 'iconsweets-excel',
								'href'       => base_url($this->path.'export_xlsx?filtro='.base64_encode($filtro))
								);

			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
		}else{
			$buttonTPL = "";
			$msg       = $this->lang_item("msg_query_null");
			$tabla     = alertas_tpl('', $msg ,false);
		}
			$tabData['filtro']    = (isset($filtro) && $filtro!="") ? sprintf($this->lang_item("msg_query_search"),$total_rows , $filtro) : "";
			$tabData['tabla']     = $tabla;
			$tabData['export']    = button_tpl($buttonTPL);
			$tabData['paginador'] = $paginador;
			$tabData['item_info'] = $this->pagination_bootstrap->showing_items($limit, $offset, $total_rows);

			if($this->ajax_post(false)){
				echo json_encode( $this->load_view_unique($uri_view , $tabData, true));
			}else{
				return $this->load_view_unique($uri_view , $tabData, true);
			}
	}
	public function export_xlsx(){
		$filtro  = ($this->ajax_get('filtro')) ?  base64_decode($this->ajax_get('filtro') ): "";
		$sqlData = array(
						 'buscar'      	 => $filtro
						,'aplicar_limit' => 0
					);
		$lts_content = $this->db_model->db_get_data($sqlData);
		if(count($lts_content)>0){
			foreach ($lts_content as $value) {
				$set_data[] = array(
									$value['razon_social'],
									$value['nombre_comercial'],
									$value['clave_corta'],
									$value['rfc'],
									$value['calle'],
									$value['num_int'],
									$value['num_ext'],
									$value['colonia'],
									$value['municipio'],
									$value['entidad'],
									$value['cp'],
									$value['telefonos'],
									$value['email'],
									$value['contacto'],
									$value['comentarios']
							);
			}

			$set_heading = array(
									$this->lang_item("lbl_rsocial"),
									$this->lang_item("lbl_nombre"),
									$this->lang_item("lbl_clv"),
									$this->lang_item("lbl_rfc"),
									$this->lang_item("lbl_calle"),
									$this->lang_item("lbl_num_int"),
									$this->lang_item("lbl_num_ext"),
									$this->lang_item("lbl_colonia"),
									$this->lang_item("lbl_municipio"),
									$this->lang_item("lbl_entidad"),
									$this->lang_item("lbl_cp"),
									$this->lang_item("lbl_telefono"),
									$this->lang_item("lbl_email"),
									$this->lang_item("lbl_contacto"),
									$this->lang_item("lbl_comentario")
								);
	
		}
		$params = array(	'tittle'  => $this->lang_item("catalogo", false).$this->lang_item("seccion"),
							'items'   => $set_data,
							'headers' => $set_heading
						);
		
		$this->excel->generate_xlsx($params);
	}

	public function detalle(){
		// Crea formulario de detalle y edición
		$seccion 			    = '';
		$accion 			    = $this->tab['detalle'];
		$id_compras_proveedor 	= $this->ajax_post('id_compras_proveedor');
		$detalle  			    = $this->db_model->get_orden_unico($id_compras_proveedor);
		$btn_save       	    = form_button(array('class'=>"btn btn-primary",'name' => 'actualizar' , 'onclick'=>'actualizar()','content' => $this->lang_item("btn_guardar") ));


		$dropArray = array(
					'data'		=> $this->entidad->get_entidades_default(array('aplicar_limit'=> false))
					,'selected' => $detalle[0]['id_administracion_entidad'] 
					,'value' 	=> 'id_administracion_entidad'
					,'text' 	=> array('ent_abrev','entidad')
					,'name' 	=> "id_administracion_entidad"
					,'class' 	=> "requerido"
				);
		//$entidades  				 = 

		$tabData['lbl_rsocial']            =  $this->lang_item('lbl_rsocial', false);
		$tabData['lbl_nombre']             =  $this->lang_item('lbl_nombre', false);
		$tabData['lbl_clv']                =  $this->lang_item('lbl_clv', false);
		$tabData['lbl_rfc']                =  $this->lang_item('lbl_rfc', false);
		$tabData['lbl_calle']              =  $this->lang_item('lbl_calle', false);
		$tabData['lbl_num_int']            =  $this->lang_item('lbl_num_int', false);
		$tabData['lbl_num_ext']            =  $this->lang_item('lbl_num_ext', false);
		$tabData['lbl_colonia']            =  $this->lang_item('lbl_colonia', false);
		$tabData['lbl_municipio']          =  $this->lang_item('lbl_municipio', false);
		$tabData['lbl_entidad']            =  $this->lang_item('lbl_entidad', false);
		$tabData['dropdown_entidad']       = dropdown_tpl($dropArray);
		$tabData['lbl_cp']                 = $this->lang_item('lbl_cp', false);
		$tabData['lbl_telefono']           = $this->lang_item('lbl_telefono', false);
		$tabData['lbl_email']              = $this->lang_item('lbl_email', false);
		$tabData['lbl_contacto']           = $this->lang_item('lbl_contacto', false);
		$tabData['lbl_comentario']         = $this->lang_item('lbl_comentario', false);
		
		$tabData['lbl_ultima_modiciacion'] = $this->lang_item('lbl_ultima_modificacion', false);
		$tabData['lbl_ultima_modiciacion'] = $this->lang_item('lbl_ultima_modificacion', false);
		$tabData['lbl_fecha_registro']     = $this->lang_item('lbl_fecha_registro', false);
		$tabData['lbl_usuario_regitro']    = $this->lang_item('lbl_usuario_regitro', false);

		


		$tabData['button_save']      = $btn_save;


               
        /*if($detalle[0]['id_usuario']){
        	$usuario_registro           = $this->users_model->search_user_for_id($detalle[0]['id_usuario']);
        	$usuario_name 				= text_format_tpl($usuario_registro[0]['name'],"u");
    	}else{
    		$usuario_name = '';
    	}*/
        //$tabData['registro_por']    	= $this->lang_item("registro_por",false);
      //  $tabData['usuario_registro']	= $usuario_name;
		$uri_view   					= $this->path.$this->submodulo.'_'.$accion;
		echo json_encode( $this->load_view_unique($uri_view ,$tabData, true));
	}
}