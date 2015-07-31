<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class menus extends Base_Controller{

	private $modulo;
	private $submodulo;
	private $view_content;
	private $path;
	private $icon;
	private $offset, $limit_max;
	private $tab, $tab1, $tab2, $tab3;

	public function __construct(){
		parent::__construct();
		$this->modulo 			= 'nutricion';
		$this->submodulo		= 'menus';
		$this->icon 			= 'fa fa-cutlery';
		$this->tab1 			= 'agregar';
		$this->tab2 			= 'listado';
		$this->tab3             = 'detalle';
		$this->view_content 	= 'content';
		$this->path 			= $this->modulo;
		$this->path_view        = $this->path.'/'.$this->submodulo;
		$this->view_agregar     = $this->path_view.'/'.$this->submodulo.'_'.$this->tab1;
		$this->view_detalle     = $this->path_view.'/'.$this->submodulo.'_'.$this->tab3;
		$this->view_listado     = $this->path.'/'.$this->tab2;
		$this->limit_max		= 10;
		$this->offset			= 0;
		// DB Model
		$this->load->model($this->modulo.'/'.$this->submodulo.'_model','db_model' );
		$this->load->model('sucursales/listado_sucursales_model','sucursales');
		// Diccionario
		$this->lang->load( $this->modulo.'/'.$this->submodulo,"es_ES" );
	}

	public function config_tabs(){
		$tab_1 	= $this->tab1;
		$path  	= $this->path;

		$config_tab['names']    = array( $this->lang_item($tab_1) ); 
		$config_tab['links']    = array( $path.$tab_1 ); 
		$config_tab['action']   = array('');
		$config_tab['attr']     = array('');
		$config_tab['style_content'] = array('','');
		
		return $config_tab;
	}
	
	private function uri_view_principal(){
		return $this->modulo.'/'.$this->view_content;
	}

	public function index(){
		$tabl_inicial 			  = 1;	
		$contenidos_tab           = $this->agregar();
		$data['titulo_submodulo'] = $this->lang_item($this->modulo);
		$data['titulo_seccion']   = $this->lang_item('conformacion_menus');
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		
		$js['js'][]  = array('name' => $this->submodulo, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal(), $data, $js);
	}

	public function agregar(){
		$data['lbl_sucursal'] = $this->lang_item('lbl_sucursal');
		$sqlData = array(
							 'buscar' => 0
							,'offset' => 0
							,'limit'  => 0
						);
		$dropdown_sucursales = array(
						 'data'		=> $this->sucursales->db_get_data($sqlData)
						,'value' 	=> 'id_sucursal'
						,'text' 	=> array('cv_sucursal','sucursal')
						,'name' 	=> "lts_sucursales"
						,'leyenda' 	=> "-----"
						,'class' 	=> "requerido"
						,'event'    => array('event'      => 'onchange', 
											'function'    => 'load_dropdowns', 
											'params'      => array('this.value'), 
											'params_type' => array(false))
					);
		$btn_guardar = form_button(array( 
											'content'  => $this->lang_item('btn_guardar'),
											'class'    => 'btn btn-primary',
											//'disabled' => 'disabled',
											'onclick'  => 'conformar_menu()',
											'name'     => 'guardar_menu'
										));

		$sucursales                           = dropdown_tpl($dropdown_sucursales);
		$data['lbl_clave_corta']              = $this->lang_item('lbl_clave_corta', false);
		$data['lbl_nombre_menu']              = $this->lang_item('lbl_nombre_menu', false);
		$data['lbl_sucursal']                 = $this->lang_item('lbl_sucursal', false);
		$data['lbl_asigna_recetas']           = $this->lang_item('lbl_asigna_recetas', false);
		$data['lbl_list_recetas']             = $this->lang_item('lbl_list_recetas', false);
		$data['lbl_list_recetas_selected']    = $this->lang_item('lbl_list_recetas_selected', false);
		$data['lbl_asigna_articulos']         = $this->lang_item('lbl_asigna_articulos', false);
		$data['lbl_list_articulos']           = $this->lang_item('lbl_list_articulos', false);
		$data['lbl_list_articulos_selected']  = $this->lang_item('lbl_list_articulos_selected', false);
		$data['dropdown_sucursales']          = $sucursales;
		$data['dropdown_recetas']             = dropdown_tpl(array('data' => null,'name' => "lts_recetas", 'leyenda' => "-----"));
		$data['dropdown_articulos']           = dropdown_tpl(array('data' => null,'name' => "lts_articulos", 'leyenda' => "-----"));
		$data['btn_formato']                  = $btn_guardar;

		if($this->ajax_post(false)){
			echo json_encode($this->load_view_unique($this->view_agregar,$data,true));
		}
		else{
			return $this->load_view_unique($this->view_agregar, $data, true);
		}
	}

	public function load_dropdowns(){
		$id_sucursal    = ($this->ajax_post('id_sucursal')) ? $this->ajax_post('id_sucursal') : false;
		$json_recetas   = array();
		$json_articulos = array();
		if($id_sucursal){
			$lts_recetas = $this->db_model->get_lts_recetas( $id_sucursal );
			if(is_array($lts_recetas)){
				foreach ($lts_recetas as $key => $value) {
					$json_recetas[] = array(
											'key'  => $value['id_nutricion_receta'], 
											'item' => $value['clave_corta'].'-'.$value['receta']
									);
				}
			}
			
			$lts_articulos = $this->db_model->get_lts_articulos($id_sucursal);
			if(is_array($lts_articulos)){
				foreach ($lts_articulos as $key => $value) {
					$json_articulos[] = array(
											'key'  => $value['id_compras_articulo_precios'],
											'item' => $value['articulo']
										);
				}
			}
		}
		echo json_encode(array('recetas' => $json_recetas, 'articulos' => $json_articulos));
	}

	public function conformar_menu(){
		$objData  	= $this->ajax_post('objData');
		//print_debug($objData);
		if($objData['incomplete']>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode( array( 'success'=>'false', 'mensaje' => alertas_tpl('error', $msg ,false)));
		}else{
			$data_insert = array(
				  'menu'        => $objData['menu']
				 ,'clave_corta' => $objData['clave_corta']
				 ,'id_usuario'  => $this->session->userdata('id_usuario')
				 ,'timestamp'   => $this->timestamp()
				);
			$insert = $this->db_model->db_insert_data($data_insert);
			
			$arr_receta  = explode(',',$objData['recetas_selected']);
			
			if(!empty($arr_receta)){
				$sqlData = array();
				foreach ($arr_receta as $key => $value){
					$sqlData = array(
						 'id_sucursal'  => $insert
						,'id_receta'    => $value
						,'id_usuario'   => $this->session->userdata('id_usuario')
						,'timestamp'    => $this->timestamp()
						);
					$insert_pago = $this->db_model->db_insert_receta($sqlData);
				}
			}

			$arr_articulo  = explode(',',$objData['articulos_selected']);
			
			if(!empty($arr_articulo)){
				$sqlData = array();
				foreach ($arr_articulo as $key => $value){
					$sqlData = array(
						 'id_sucursal'  => $insert
						,'id_articulo'  => $value
						,'id_usuario'   => $this->session->userdata('id_usuario')
						,'timestamp'    => $this->timestamp()
						);
					$insert_pago = $this->db_model->db_insert_articulo($sqlData);
				}
			}

			if($insert){
				$msg = $this->lang_item("msg_insert_success",false);
				echo json_encode(array(  'success'=>'true', 'mensaje' => $msg));
			}else{
				$msg = $this->lang_item("msg_err_clv",false);
				echo json_encode(array(  'success'=>'false', 'mensaje' => alertas_tpl('', $msg ,false)));
			}
		}
	}
}