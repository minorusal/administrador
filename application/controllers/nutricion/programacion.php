<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class programacion extends Base_Controller{
	private $modulo;
	private $submodulo;
	private $seccion;
	private $view_content;
	private $path;
	private $icon;
	private $view_modal;
	private $offset, $limit_max;
	private $tab, $tab1, $tab2, $tab3;

	public function __construct(){
		parent::__construct();
		$this->modulo 			= 'nutricion';
		$this->seccion		    = 'programacion';
		$this->icon 			= 'fa fa-calendar'; 
		$this->path 			= $this->modulo.'/'.$this->seccion.'/'; 
		$this->view_content 	= 'content';
		$this->tab1 			= 'config_programacion';
		$this->tab2 			= 'ver_calendario';

		$this->load->model($this->modulo.'/'.$this->seccion.'_model','db_model');
		$this->load->model('administracion/sucursales_model','sucursales');
		$this->lang->load($this->modulo.'/'.$this->seccion,"es_ES");
	}
	public function config_tabs(){
		$tab_1 	= $this->tab1;
		$tab_2 	= $this->tab2;
		$path  	= $this->path;
		$config_tab['names']         = array($this->lang_item($tab_1) ,$this->lang_item($tab_2)); 
		$config_tab['links']         = array($path.$tab_1 ,$path.$tab_2); 
		$config_tab['action']        = array('','');
		$config_tab['attr']          = array('','');
		$config_tab['style_content'] = array('','');
		return $config_tab;
	}
	private function uri_view_principal(){
		return $this->modulo.'/'.$this->view_content;
	}

	function index(){
		$tabl_inicial 			  = 1;
		$view_agregar    		  = $this->inicio_config_programacion();	
		$contenidos_tab           = $view_agregar;
		$data['titulo_seccion']   = $this->lang_item($this->seccion);
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		
		$js['js'][]  = array('name' => $this->seccion, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal(), $data, $js);
	}

	function inicio_config_programacion(){
		$tab['lbl_sucursal']               = $this->lang_item('lbl_sucursal');
		$sqlData = array(
			 'buscar' => 0
			,'offset' => 0
			,'limit'  => 0
			);
		$dropdown_sucursales = array(
									 'data'		=> $this->sucursales->db_get_data($sqlData)
									,'value' 	=> 'id_sucursal'
									,'text' 	=> array('clave_corta','sucursal')
									,'name' 	=> "lts_sucursales"
									,'class' 	=> "requerido"
									,'leyenda' 	=> "-----"
									,'event'    => array('event'      => 'onchange', 
														'function'    => 'load_programacion', 
														'params'      => array('this.value'), 
														'params_type' => array(false))
								);
		$sucursales = dropdown_tpl($dropdown_sucursales);
		$tab['dropdow_sucursales'] = $sucursales;
		$uri_view = $this->modulo.'/'.$this->seccion.'/header_config_programacion';
		return $this->load_view_unique($uri_view , $tab, true);
	}
	function form_config_programacion(){

		$id_sucursal = ($this->ajax_post('id_sucursal')) ? $this->ajax_post('id_sucursal') : false;
		
		if($id_sucursal){
			$params_ciclo     = $this->db_model->get_params_ciclos($id_sucursal);
			$dias_descartados = $this->db_model->get_dias_descartados($id_sucursal);
			/*Periodo Programado*/
			if(is_array($params_ciclo)){
				$tab['value_fecha_inicio']     = $params_ciclo[0]['fecha_inicio'];
				$tab['value_fecha_termino']    = $params_ciclo[0]['fecha_termino'];
			}else{
				$tab['value_fecha_inicio']     = '';
				$tab['value_fecha_termino']    = '';
			}

			/*Dias Descartados*/
			if(is_array($dias_descartados)){
				foreach ($dias_descartados as $key => $value) {
					$dias_index[] = $value['dia_index'];
				}
			}else{
				$dias_index = array();
			}

			foreach ($this->days() as $key => $value) {
				$value = '<span>'.ucwords($value).'</span>';
				if(in_array($key, $dias_index)){
					$checked =  true;
				}else{
					$checked =  false;
				}
				$dias_descartados_checkbox[]=  form_checkbox('dias_descartados', $key, $checked).'&nbsp;'.$value;
			}

			/*recuperacion de Ciclos en programacion*/
			$ciclos               = $this->db_model->get_ciclos($id_sucursal);
			$ciclos_programados   = $this->db_model->get_ciclos_programados($id_sucursal);

			if(is_array($ciclos_programados)){
				foreach ($ciclos_programados as $key => $value) {
					$li_ciclos .= "<li id='".$value['id_nutricion_ciclos']."' class='onclick_on'><span class='icon-chevron-right'></span>&nbsp;".$value['id_nutricion_ciclos'].'-'.$value['ciclo']."</li>";
				}
			}else{
				$li_ciclos = '';
				$ciclos_programados = null;
			}

			if(!is_null($ciclos)){
				$multiselect_ciclos = dropMultiselect_tpl(array(
															 'data'		        => $ciclos
															,'data_seleted' 	=> $ciclos_programados
															,'value' 	        => 'id_nutricion_ciclos'
															,'text' 	        => array('id_nutricion_ciclos','ciclo')
															,'name' 	        => "multiselect_ciclos"
															,'name2' 	        => "multiselect_ciclos_agregados"
															,'prev' 	        => "quitar_ciclo()"
															,'next' 	        => "agregar_ciclo()"
															)
														);
				$dropdown_ciclos = dropdown_tpl(array(
								 'data'		=> $ciclos
								,'value' 	=> 'id_nutricion_ciclos'
								,'text' 	=> array('id_nutricion_ciclos','ciclo')
								,'name' 	=> "dropdown_ciclos"
								,'event'      => array('event'    => 'onchange',
											   						 'function' => 'load_contenido_ciclo',
							   										 'params'   => array('this.value'),
							   										 'params_type' => array(false)
							   										)
							));

				$btn_guardar_parametros = form_button(array( 'content'  => 'Guardar Cambios',
														'class' => 'btn btn-primary',
														'name'  => 'guardar_programacion',
														'onclick'=> 'guardar_configuracion_programacion()'
											));
			}else{
				$btn_guardar_parametros = form_button(array( 'content'  => 'Guardar Cambios',
														'class' => 'btn btn-primary',
														'disabled' =>'disabled',
														'name'  => 'guardar_programacion'
											));
				$dropdown_ciclos = alertas_tpl('', $this->lang_item('msg_ciclos_null'),false);
				$multiselect_ciclos = alertas_tpl('', $this->lang_item('msg_ciclos_null'),false);


			}

			
			
			$tab['btn_guardar_parametros']  = $btn_guardar_parametros;
			$tab['values_dias_descartados'] = implode('&nbsp;', $dias_descartados_checkbox);
			$tab['multiselect_ciclos']      = $multiselect_ciclos;
			$tab['ciclos_programados']      = '<ol id="ciclos_programados" class="list-ordered">'.$li_ciclos.'</ol>';
			$tab['dropdown_ciclos']         = $dropdown_ciclos;
		}else{
			$tab['btn_guardar_parametros']  = '';
			$tab['value_fecha_inicio']      = '';
			$tab['value_fecha_termino']     = '';
			$tab['values_dias_descartados'] = '';
			$tab['multiselect_ciclos']      = '';
			$tab['ciclos_programados']      = '';
			$tab['dropdown_ciclos']         = '';
		}
		
		$tab['lbl_config_programacion']    = $this->lang_item('lbl_config_programacion');
		$tab['lbl_input_fecha_inicio']     = $this->lang_item('lbl_input_fecha_inicio');
		$tab['lbl_input_fecha_termino']    = $this->lang_item('lbl_input_fecha_termino');
		$tab['lbl_dias_descartados']       = $this->lang_item('lbl_dias_descartados');
		$tab['lbl_dias_festivos']          = $this->lang_item('lbl_dias_festivos');
		$tab['lbl_info_dias_descartados']  = $this->lang_item('lbl_info_dias_descartados');
		$tab['lbl_input_fecha_descartada'] = $this->lang_item('lbl_input_fecha_descartada');
		$tab['lbl_agregar_ciclos']         = $this->lang_item('lbl_agregar_ciclos');
		$tab['lbl_info_agregar_ciclos']    = $this->lang_item('lbl_info_agregar_ciclos');
		$tab['lbl_orden_ciclos']           = $this->lang_item('lbl_orden_ciclos');
		$tab['lbl_info_orden_ciclos']      = $this->lang_item('lbl_info_orden_ciclos');
		$tab['lbl_cantidad_recetas']       = $this->lang_item('lbl_cantidad_recetas');
		$tab['lbl_info_cantidad_recetas']  = $this->lang_item('lbl_info_cantidad_recetas');
		
		$uri_view = $this->modulo.'/'.$this->seccion.'/content_config_programacion';
		echo json_encode( $this->load_view_unique($uri_view , $tab, true) );
		
	}

	public function ciclo_cantidad_recetas(){
		echo json_encode('hola');
	}
}