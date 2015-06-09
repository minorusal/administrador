<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class valores_nutricionales extends Base_Controller {
	private $modulo;
	private $submodulo;
	private $seccion;
	private $view_content;
	private $path;
	private $icon;

	private $offset, $limit_max;
	private $tab, $tab1, $tab2, $tab3;

	public function __construct()
	{
		parent::__construct();
		$this->modulo 			= 'nutricion';
		$this->submodulo		= 'valores_nutricionales';
		//$this->seccion          = 'areas';
		$this->icon 			= 'fa fa-sort-amount-asc'; 
		$this->path 			= $this->modulo.'/'.$this->submodulo.'/'; #administracion/areas
		$this->view_content 	= 'content';
		$this->limit_max		= 10;
		$this->offset			= 0;
		// Tabs
		$this->tab1 			= 'listado';
		$this->tab2 			= 'detalle';
		//$this->tab3 			= 'detalle';
		// DB Model
		$this->load->model($this->modulo.'/'.$this->submodulo.'_model','db_model');
		$this->load->model('compras/catalogos_model','db_model_articulos');
		// Diccionario
		$this->lang->load($this->modulo.'/'.$this->submodulo,"es_ES");
	}

	public function config_tabs()
	{
		$tab_1 	= $this->tab1;
		$tab_2 	= $this->tab2;
		//$tab_3 	= $this->tab3;
		$path  	= $this->path;
		$pagina =(is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		// Nombre de Tabs
		$config_tab['names']    = array(
										 $this->lang_item($tab_1) 
										,$this->lang_item($tab_2) 
										//,$this->lang_item($tab_3) 
								); 
		// Href de tabs
		$config_tab['links']    = array(
										 //$path.$tab_1      
										$path.$tab_1.'/'.$pagina #listado
										,$tab_2                  #detalle
								); 
		// Accion de tabs
		$config_tab['action']   = array(
										 'load_content'
										//,'load_content'
										,''
								);
		// Atributos 
		$config_tab['attr']     = array('', array('style' => 'display:none'));
		return $config_tab;
	}

	private function uri_view_principal()
	{
		return $this->modulo.'/'.$this->view_content;
	}

	public function index()
	{
		$tabl_inicial 			  = 1;
		$view_listado    		  = $this->listado();	
		$contenidos_tab           = $view_listado;
		$data['titulo_seccion']   = $this->lang_item($this->submodulo);
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		
		$js['js'][]  = array('name' => $this->submodulo, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal(), $data, $js);
	}

	public function listado($offset=0)
	{
		// Crea tabla con listado de elementos capturados 
		$seccion 		= '/listado';
		$tab_detalle	= $this->tab2;	
		$limit 			= $this->limit_max;
		$uri_view 		= $this->modulo.$seccion;
		$url_link 		= $this->path.'listado';
		$filtro      	= ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";
		$sqlData = array(
			 'buscar'      	=> $filtro
			,'offset' 		=> $offset
			,'limit'      	=> $limit
		);
		$uri_segment  = $this->uri_segment(); 
		$total_rows	  = count($this->db_model_articulos->get_articulos($sqlData['limit'],$sqlData['offset'],$sqlData['buscar'],true));
		$sqlData['aplicar_limit'] = false;
		$list_content = $this->db_model_articulos->get_articulos($sqlData['limit'],$sqlData['offset'],$sqlData['buscar'],$sqlData['aplicar_limit']);
		$url          = base_url($url_link);
		$paginador    = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));

		if($total_rows)
		{
			foreach ($list_content as $value)
			{
				// Evento de enlace
				$atrr = array(
								'href' => '#',
							  	'onclick' => $tab_detalle.'('.$value['id_compras_articulo'].')'
						);
				// Datos para tabla
				$tbl_data[] = array('id'     => $value['id_compras_articulo'],
									'articulo'   => tool_tips_tpl($value['articulo'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'clave_corta'  => $value['clave_corta'],
									'descripcion'  => $value['descripcion']
									);
			}
			// Plantilla
			$tbl_plantilla = array('table_open'  => '<table class="table table-bordered responsive ">');
			// Titulos de tabla
			$this->table->set_heading(	$this->lang_item("ID"),
										$this->lang_item("articulo"),
										$this->lang_item("clave_corta"),
										$this->lang_item("descripcion"));
			// Generar tabla
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
			$buttonTPL = array( 'text'   => $this->lang_item("btn_xlsx"), 
								'iconsweets' => 'iconsweets-excel',
								'href'       => base_url($this->path.'export_xlsx?filtro='.base64_encode($filtro))
								);
		}
		else
		{
			$buttonTPL = "";
			$msg   = $this->lang_item("msg_query_null");
			$tabla = alertas_tpl('', $msg ,false);
		}
		$tabData['filtro']    = (isset($filtro) && $filtro!="") ? sprintf($this->lang_item("msg_query_search",false),$total_rows , $filtro) : "";
		$tabData['tabla']     = $tabla;
		$tabData['export']    = button_tpl($buttonTPL);
		$tabData['paginador'] = $paginador;
		$tabData['item_info'] = $this->pagination_bootstrap->showing_items($limit, $offset, $total_rows);
		if($this->ajax_post(false))
		{
			echo json_encode( $this->load_view_unique($uri_view , $tabData, true));
		}
		else
		{
			return $this->load_view_unique($uri_view , $tabData, true);
		}
	}

	public function detalle()
	{
		$id_articulo = $this->ajax_post('id_articulo');
		$detalle = $this->db_model_articulos->get_articulo_unico($id_articulo);
		$seccion = $this->tab2;
		$tab_detalle = $this->tab2;
		$detalle_nutricional = $this->db_model->get_valores_nutricionales_unico($id_articulo);
		//print_debug($detalle_nutricional);
		$btn_save = form_button(array('class' => 'btn btn-primary' , 'name' => 'actualizar', 'onclick' => 'actualizar()', 'content' => $this->lang_item("btn_guardar")));
		$tabData['id_compras_articulos'] = $id_articulo;
		$tabData['nombre_articulo'] = $this->lang_item("articulo");
		$tabData['cantidad_sugerida'] = $this->lang_item("cantidad_sugerida");
		$tabData['peso_bruto'] = $this->lang_item("peso_bruto");
		$tabData['peso_neto'] = $this->lang_item("peso_neto");
		$tabData['energia'] = $this->lang_item("energia");
		$tabData['proteina'] = $this->lang_item("proteina");
		$tabData['lipidos'] = $this->lang_item("lipidos");
		$tabData['hidratos_carbono'] = $this->lang_item("hidratos_carbono");
		$tabData['fibra'] = $this->lang_item("fibra");
		$tabData['vitamina_a'] = $this->lang_item("vitamina_a");
		$tabData['acido_ascorbico'] = $this->lang_item("acido_ascorbico");
		$tabData['acido_folico'] = $this->lang_item("acido_folico");
		$tabData['hierro_nohem'] = $this->lang_item("hierro_nohem");
		$tabData['potasio'] = $this->lang_item("potasio");
		$tabData['azucar'] = $this->lang_item("azucar");
		$tabData['indice_glicemico'] = $this->lang_item("indice_glicemico");
		$tabData['carga_glicemica'] = $this->lang_item("carga_glicemica");
		$tabData['calcio'] = $this->lang_item("calcio");
		$tabData['sodio'] = $this->lang_item("sodio");
		$tabData['selenio'] = $this->lang_item("selenio");
		$tabData['fosforo'] = $this->lang_item("fosforo");
		$tabData['colesterol'] = $this->lang_item("colesterol");
		$tabData['ag_saturados'] = $this->lang_item("ag_saturados");
		$tabData['ag_mono'] = $this->lang_item("ag_mono");
		$tabData['ag_poli'] = $this->lang_item("ag_poli");

		$tabData['cant_sugerida'] = (isset($detalle_nutricional[0]['cantidad_sugerida']))?$detalle_nutricional[0]['cantidad_sugerida']:0;
		$tabData['p_bruto'] = (isset($detalle_nutricional[0]['peso_bruto']))?$detalle_nutricional[0]['peso_bruto']:0;
		$tabData['p_neto'] = (isset($detalle_nutricional[0]['peso_neto']))?$detalle_nutricional[0]['peso_neto']:0;
		$tabData['ener'] = (isset($detalle_nutricional[0]['energia']))?$detalle_nutricional[0]['energia']:0;
		$tabData['prot'] = (isset($detalle_nutricional[0]['proteina']))?$detalle_nutricional[0]['proteina']:0;
		$tabData['lipids'] = (isset($detalle_nutricional[0]['lipidos']))?$detalle_nutricional[0]['lipidos']:0;
		$tabData['h_carbono'] = (isset($detalle_nutricional[0]['hidratos_carbono']))?$detalle_nutricional[0]['hidratos_carbono']:0;
		$tabData['fib'] = (isset($detalle_nutricional[0]['fibra']))?$detalle_nutricional[0]['fibra']:0;
		$tabData['vit_a'] = (isset($detalle_nutricional[0]['vitamina_a']))?$detalle_nutricional[0]['vitamina_a']:0;
		$tabData['a_ascorbico'] = (isset($detalle_nutricional[0]['acido_ascorbico']))?$detalle_nutricional[0]['acido_ascorbico']:0;
		$tabData['a_folico'] = (isset($detalle_nutricional[0]['acido_folico']))?$detalle_nutricional[0]['acido_folico']:0;
		$tabData['h_nohem'] = (isset($detalle_nutricional[0]['hierro_nohem']))?$detalle_nutricional[0]['hierro_nohem']:0;
		$tabData['pot'] = (isset($detalle_nutricional[0]['potasio']))?$detalle_nutricional[0]['potasio']:0;
		$tabData['azu'] = (isset($detalle_nutricional[0]['azucar']))?$detalle_nutricional[0]['azucar']:0;
		$tabData['i_glicemico'] = (isset($detalle_nutricional[0]['indice_glicemico']))?$detalle_nutricional[0]['indice_glicemico']:0;
		$tabData['c_glicemica'] = (isset($detalle_nutricional[0]['carga_glicemica']))?$detalle_nutricional[0]['carga_glicemica']:0;
		$tabData['calc'] = (isset($detalle_nutricional[0]['calcio']))?$detalle_nutricional[0]['calcio']:0;
		$tabData['sod'] = (isset($detalle_nutricional[0]['sodio']))?$detalle_nutricional[0]['sodio']:0;
		$tabData['sel'] = (isset($detalle_nutricional[0]['selenio']))?$detalle_nutricional[0]['selenio']:0;
		$tabData['fos'] = (isset($detalle_nutricional[0]['fosforo']))?$detalle_nutricional[0]['fosforo']:0;
		$tabData['coles'] = (isset($detalle_nutricional[0]['colesterol']))?$detalle_nutricional[0]['colesterol']:0;
		$tabData['saturados'] = (isset($detalle_nutricional[0]['ag_saturados']))?$detalle_nutricional[0]['ag_saturados']:0;
		$tabData['mono'] = (isset($detalle_nutricional[0]['ag_mono']))?$detalle_nutricional[0]['ag_mono']:0;
		$tabData['poli'] = (isset($detalle_nutricional[0]['ag_poli']))?$detalle_nutricional[0]['ag_poli']:0;

		$tabData['lbl_ultima_modificacion'] = $this->lang_item('lbl_ultima_modificacion');
        $tabData['val_fecha_registro']     = $detalle_nutricional[0]['timestamp'];
		$tabData['lbl_fecha_registro']     = $this->lang_item('lbl_fecha_registro');
		$tabData['lbl_usuario_registro']    = $this->lang_item('lbl_usuario_registro');

		$this->load_database('global_system');
        $this->load->model('users_model');

		$usuario_registro                  = $this->users_model->search_user_for_id($detalle_nutricional[0]['id_usuario']);
	    $usuario_name	                   = text_format_tpl($usuario_registro[0]['name'],"u");
	    $tabData['val_usuarios_registro']  = $usuario_name;

		if($detalle_nutricional[0]['edit_id_usuario'])
		{
			$usuario_registro = $this->users_model->search_user_for_id($detalle_nutricional[0]['edit_id_usuario']);
			$usuario_name = text_format_tpl($usuario_registro[0]['name'],"u");
			$tabData['val_ultima_modificacion'] = sprintf($this->lang_item('val_ultima_modificacion',false), $this->timestamp_complete($detalle_nutricional[0]['edit_timestamp']), $usuario_name);
		}
		else
		{
			$usuario_name = '';
			$tabData['val_ultima_modificacion'] = $this->lang_item('lbl_sin_modificacion', false);
		}
		$tabData['button_save'] = $btn_save;
		$tabData['registro_por'] = $this->lang_item('registro´_por', false);
		$tabData['usuario_registro'] = $usuario_name;
		$uri_view = $this->modulo.'/'.$this->submodulo.'/valores_'.$seccion;

		echo json_encode($this->load_view_unique($uri_view,$tabData,true));
	}
}