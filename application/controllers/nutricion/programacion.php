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
		$this->load->model('nutricion/ciclos_model','ciclos');
		$this->lang->load($this->modulo.'/'.$this->seccion,"es_ES");
	}
	public function config_tabs(){
		$tab_1 						 = $this->tab1;
		$tab_2 						 = $this->tab2;
		$path  						 = $this->path;
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
	public function index(){
		$tabl_inicial 			  = 1;
		$view_agregar    		  = $this->inicio_config_programacion();	
		$contenidos_tab           = array($view_agregar,$this->calendario());
		$data['titulo_seccion']   = $this->lang_item($this->seccion);
		$data['titulo_submodulo'] = $this->lang_item($this->modulo);
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		
		$js['js'][]  = array('name' => $this->seccion, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal(), $data, $js);
	}
	public function inicio_config_programacion(){
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
	public function form_config_programacion(){

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
			$li_ciclos            = '';
			if(is_array($ciclos_programados)){
				foreach ($ciclos_programados as $key => $value) {
					$li_ciclos .= "<li id='".$value['id_nutricion_ciclos']."' class='onclick_on'><h4><span class='icon-chevron-right'></span>&nbsp;".$value['id_nutricion_ciclos'].'-'.$value['ciclo']."</h4></li>";
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
		$id_ciclo        = $this->ajax_post('id_ciclo');
		$list            = '';
		$contenido_ciclo = $this->db_model->get_contenido_ciclo($id_ciclo);
		if(!is_null($contenido_ciclo)){
			foreach ($contenido_ciclo as $key => $value) {
					$servicios['s-'.$value['servicio']]['t-'.$value['tiempo']]['f-'.$value['familia']][] =array(
																															 'recetas'     => $value['receta'] , 
																															 'porciones'   => $value['porciones'],
																															 'id_vinculo'  => $value['id_nutricion_ciclo_receta']
																													);

			}
			$list  ='<br><div id="sidetreecontrol"><a href="?#">'.$this->lang_item('collapse').'</a> | <a href="?#">'.$this->lang_item('expand').'</a></div>';
			$list .= $this->make_list($servicios);	
		}else{
			$list = alertas_tpl('', $this->lang_item('msg_sin_recetas'),false);
		}
		echo json_encode($list);
	}
	public function make_list($items, $inicio = true, $inputs = true) {
	    $ret = ($inicio) ? '<ul id="treeview_ciclos" class=" treeview-gray">': '<ul>';
	    foreach ($items as $item => $subitems) {
	        if (!is_numeric($item)) {
	        	$nivel = explode('-', $item);
	        	if(count($nivel)>1){
	        		$icon   = $nivel[0];
		        	$tittle = $nivel[1];
		        	switch ($icon) {
		        		case 's':
		        			$icon = ' iconfa-time';
		        			$tipo = strtoupper( $this->lang_item('servicio') );
		        			break;
		        		case 't':
		        			$icon = 'iconfa-sitemap';
		        			$tipo = strtoupper( $this->lang_item('tiempo') );
		        			break;
		        		case 'f':
		        			$icon = 'iconfa-certificate';
		        			$tipo = strtoupper( $this->lang_item('familia') );
		        			break;
		        		default:
		        			$icon = 'iconfa-fire';
		        			break;
		        	}
		            $ret .= "<li><span class='$icon'></span>".$tipo.':&nbsp;'.strtoupper($tittle);
	        	}
	        }

	        if (is_array($subitems)) {
	        	if(array_key_exists('recetas', $subitems)){
	        	$data_input = array(
	        				  'id'     => 'cantidad_'.$subitems['id_vinculo'],
				              'class'  => 'input-mini numerico',
				              'type'   => 'text',
				              'value'  => $subitems['porciones']
				            );
	        	$save_disk = '<a onclick="guardar_cantidad_receta_ciclo('.$subitems['id_vinculo'].')">
	        					<span style ="font-size:1.6em;" class="  iconfa-save"></span>
	        				 </a>';
	        	if($inputs){
	        		$cantidades = form_input($data_input).$save_disk;
	        	}else{
	        		$cantidades = '';
	        	}
	            $ret .= "<li>".$cantidades."<span class=' iconfa-bookmark'></span>".$subitems['recetas'];
		         
		        }else{
		        	$ret .= $this->make_list($subitems, false);
		        }
	        }

	        $ret .= "</li>";
	    }
	    $ret .= "</ul>";
	    return($ret);
	}
	public function guardar_parametros_programacion(){
		$values              = '';
		$params_programacion = $this->ajax_post('params');
		$id_sucursal         = $params_programacion['id_sucursal'];
		$fecha_inicio        = $params_programacion['fecha_inicio'];
		$fecha_termino       = $params_programacion['fecha_termino'];
		$dias_descartados    = (array_key_exists('dias_descartados', $params_programacion)) ? $params_programacion['dias_descartados'] : false;
		$orden_ciclos        = (array_key_exists('orden_ciclos', $params_programacion)) ? $params_programacion['orden_ciclos'] : false;


		$fecha_inicio      = explode('/', $fecha_inicio);
		$fecha_inicio      = $fecha_inicio[2].'-'.$fecha_inicio[1].'-'.$fecha_inicio[0];
		$fecha_termino     = explode('/', $fecha_termino);
		$fecha_termino     = $fecha_termino[2].'-'.$fecha_termino[1].'-'.$fecha_termino[0];

		$this->db_model->delete_paramas_programacion($id_sucursal);

		$data_insert = array(
							  'fecha_inicio'   => $fecha_inicio
							 ,'fecha_termino'  => $fecha_termino
							 ,'id_sucursal'    => $id_sucursal
							 ,'id_usuario'     => $this->session->userdata('id_usuario')
							 ,'timestamp'      => $this->timestamp()
						);

		$this->db_model->insert_params_programacion($data_insert);
		
		if($dias_descartados){
			foreach ($dias_descartados as $value) {
				$dia = $this->days($value, true);
				$values[] = array(
								  'dia_index'      => $value
								 ,'dia_name'       => $dia
								 ,'id_sucursal'    => $id_sucursal
								 ,'id_usuario'     => $this->session->userdata('id_usuario')
								 ,'timestamp'      => $this->timestamp()
							);
			}	
			$this->db_model->insert_dias_descartados($values);
		}
		if($orden_ciclos){
			$values  = '';
			foreach ($orden_ciclos as $key => $value) {
				$index    = $value['index'];
				$id_ciclo = $value['ciclo_id'];
				$values[] = array(
								  'id_nutricion_ciclos'   => $id_ciclo
								 ,'orden'                 => $index
								 ,'id_sucursal'           => $id_sucursal
								 ,'id_usuario'            => $this->session->userdata('id_usuario')
								 ,'timestamp'             => $this->timestamp()
							);
			}
			$this->db_model->insert_ciclos_menus($values);
		}
		echo json_encode( 'exito');
	}
	public function actuaizar_cantidad_receta(){
		$cantidad    = $this->ajax_post('cantidad');
		$id_vinculo  = $this->ajax_post('id_vinculo');

		$data = array(
						'porciones' => $cantidad,
						'id_nutricion_ciclo_receta' => $id_vinculo
						,'edit_timestamp' => $this->timestamp()
						,'edit_id_usuario' => $this->session->userdata('id_usuario')
						);
		$update = $this->db_model->update_cantidad_ciclo_receta($data);

		if($update==''){
			$m = 'exito';
		}else{
			$m = 'error';
		}
		return $m;
	}
	public function calendario(){
		$data['lbl_sucursal'] = $this->lang_item('lbl_sucursal');
		$sqlData = array(
			 'buscar' => 0
			,'offset' => 0
			,'limit'  => 0
			);
		$dropdown = array(
						 'data'		=> $this->sucursales->db_get_data($sqlData)
						,'value' 	=> 'id_sucursal'
						,'text' 	=> array('clave_corta','sucursal')
						,'name' 	=> "lts_sucursales_calendario"
						,'event'    => array('event'      => 'onchange', 
											'function'    => 'load_calendario', 
											'params'      => array('this.value'), 
											'params_type' => array(false))
					);

		$data['dropdpwn_sucursales'] = dropdown_tpl($dropdown);
		$view = $this->load_view_unique($this->modulo.'/'.$this->seccion.'/calendario',$data, true);
		return $view ;
	}
	public function cargar_calendario(){
		$index                = 0;
		$descartados          =array();
		$id_sucursal          = $this->ajax_post('id_sucursal');
		$params_ciclo         = $this->db_model->get_params_ciclos($id_sucursal);
		$dias_descartados     = $this->db_model->get_dias_descartados($id_sucursal);
		$ciclos_programados   = $this->db_model->get_programacion_contenido_ciclo($id_sucursal);
		
		
		if(is_array($ciclos_programados)){
			
			foreach ($ciclos_programados as $key => $value) {
				$ciclos[$value['orden']]['nombre'] = $value['ciclo'];
				$ciclos[$value['orden']]['servicios']['s-'.$value['servicio']]['t-'.$value['tiempo']]['f-'.$value['familia']][] =array(
																														 'recetas'     => $value['receta'] , 
																														 'porciones'   => $value['porciones'],
																														 'id_vinculo'  => $value['id_nutricion_ciclo_receta']
																												);

			}

			//print_debug($ciclos);
			
			if(is_array($dias_descartados)){
				foreach ($dias_descartados as $key => $value) {
					$descartados[] = $value['dia_index'];
				}
			}

			$fechaInicio = strtotime(str_replace('/', '-', $params_ciclo[0]['fecha_inicio']));
			$fechaFin    = strtotime(str_replace('/', '-', $params_ciclo[0]['fecha_termino']));
			
			for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
				$day = (date('N', $i) == 7) ? 0 : date('N', $i);
				if(!array_key_exists($index, $ciclos)){
					$index = 0;
				}
				if(!in_array($day, $descartados)){
					$dia  =  date('j', $i);
					$mes  = (date('n', $i)-1);
					$anio =  date('Y', $i); 
					
					$json['title'] = "<span class=\"iconfa-glass\"></span>&nbsp;-&nbsp;<span>".$ciclos[$index]['nombre'].'</span><hr>'.
						$this->make_list($ciclos[$index]['servicios']);
					$json['start'] = "new Date($anio, $mes, $dia)";
					$json['allDay'] = 1;

					/*$json[] = "{
								title: '<span class=\"iconfa-glass\"></span>&nbsp;-&nbsp;<span>".$ciclos[$index]['nombre'].'</span><hr>'.$this->make_list($ciclos[$index]['servicios'])."',
								start: new Date($anio, $mes, $dia),
			                    allDay: true
							}";*/
					$index++;
				}
			}

			$response = array('success' => 1, 'json' => $json);

		}else{
			$response = array('success' => 0, 'msg' => alertas_tpl('', $this->lang_item('msg_ciclos_null') ,false));
		}
		//return $view ;
		echo json_encode( $response);
	}
	public function enlistar_contenido($array){
		$list .= '<ul  class="list-nostyle ">';
		foreach ($array as $item => $content) {
			$list .= '<li><span class=" iconfa-fire"></span><span style="font-size:12px;">'.$item.'</span>';
			if(is_array($content)){
				$list .= '<ul>';
				foreach ($content as $value) {
					$list .= '<li><span class=" iconfa-bookmark"></span>'.$value['recetas'].'</li>';
				}
				$list .= '</ul>';
			}
		}
		$list .= '</ul>';
		return $list;
	}
}
