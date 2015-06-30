<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ciclos extends Base_Controller{

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
		$this->seccion		    = 'ciclos';
		$this->icon 			= 'fa fa-cutlery'; 
		$this->path 			= $this->modulo.'/'.$this->seccion.'/'; 
		$this->view_content 	= 'content';
		//$this->view_modal       = 'modal_cropper';
		$this->limit_max		= 10;
		$this->offset			= 0;
		// Tabs
		$this->tab1 			= 'agregar';
		$this->tab2 			= 'listado';
		$this->tab3 			= 'detalle';
		// DB Model
		$this->load->model($this->modulo.'/'.$this->seccion.'_model','ciclos');
		$this->load->model('administracion/sucursales_model','sucursales');
		$this->load->model('administracion/servicios_model','servicios');
		$this->load->model('nutricion/tiempos_model','tiempos');
		$this->load->model('nutricion/recetario_model','recetas');
		// Diccionario
		$this->lang->load($this->modulo.'/'.$this->seccion,"es_ES");
	}

	public function config_tabs()
	{
		$tab_1 	= $this->tab1;
		$tab_2 	= $this->tab2;
		$tab_3 	= $this->tab3;
		$path  	= $this->path;
		$pagina =(is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		// Nombre de Tabs
		$config_tab['names']    = array(
										 $this->lang_item($tab_1) #agregar
										,$this->lang_item($tab_2) #listado
										,$this->lang_item($tab_3) #detalle
								); 
		// Href de tabs
		$config_tab['links']    = array(
										 $path.$tab_1             
										,$path.$tab_2.'/'.$pagina 
										,$tab_3                   
								); 
		// Accion de tabs
		$config_tab['action']   = array(
										 'load_content'
										,'load_content'
										,''
								);
		// Atributos 
		$config_tab['attr']     = array('','', array('style' => 'display:none'));

		$config_tab['style_content'] = array('');


		return $config_tab;
	}
	private function uri_view_principal()
	{
		return $this->modulo.'/'.$this->view_content;
	}

	public function index(){
		$tabl_inicial 			  = 2;
		$view_listado    		  = $this->listado();	
		$contenidos_tab           = $view_listado;
		$data['titulo_seccion']   = $this->lang_item($this->seccion);
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		
		$js['js'][]  = array('name' => $this->seccion, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal(), $data, $js);
	}
	public function listado($offset=0){
		// Crea tabla con listado de elementos capturados 
		$seccion 		= '/listado';
		$tab_detalle	= $this->tab3;	
		$limit 			= $this->limit_max;
		$uri_view 		= $this->modulo.$seccion;
		$url_link 		= $this->path.'listado';
		$filtro      	= ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";
	}

	public function agregar(){
		//Carga formulario de Agregar Ciclos con el archivo ciclos_save.html
		$seccion = $this->modulo.'/'.$this->seccion.'/'.$this->seccion.'_save';
		$sqlData = array(
			 'buscar' => 0
			,'offset' => 0
			,'limit'  => 0
			);
		//Combo box que muestra las sucursales
		$dropdown_sucursales = array(
						 'data'		=> $this->sucursales->db_get_data($sqlData)
						,'value' 	=> 'id_sucursal'
						,'text' 	=> array('clave_corta','sucursal')
						,'name' 	=> "lts_sucursales"
						,'event'    => array('event'      => 'onchange', 
											'function'    => 'load_ciclos', 
											'params'      => array('this.value'), 
											'params_type' => array(false))
					);
		$sucursales = dropdown_tpl($dropdown_sucursales);

		$dropdown_ciclos = array(
					'value' 	=> 'id_nutricion_ciclos'
					,'text' 	=> array('ciclo')
					,'name' 	=> "lts_ciclos"
			   									);
			$ciclos = dropdown_tpl($dropdown_ciclos);

		$data_servicio = $this->servicios->db_get_data($sqlData);
		$dropdown_servicios = array(
				 'data'     => $data_servicio
				,'value' 	=> 'id_administracion_servicio'
				,'text' 	=> array('servicio')
				,'name' 	=> "lts_servicios"
		   									);
		$servicios = dropdown_tpl($dropdown_servicios);

		$data_tiempo = $this->tiempos->db_get_data($sqlData);
		$dropdown_tiempos = array(
				 'data'     => $data_tiempo
				,'value' 	=> 'id_nutricion_tiempo'
				,'text' 	=> array('tiempo')
				,'name' 	=> "lts_tiempos"
				,'event'      => array('event'       => 'onchange',
				   					   'function'    => 'buscar_recetas',
				   					   'params'      => array('this.value'),
			   						   'params_type' => array(false)
   										)
					);						
		$tiempos = dropdown_tpl($dropdown_tiempos);

		$recetas  = array(
						 'data'		=> $this->recetas->get_data($sqlData)
						,'value' 	=> 'id_nutricion_receta'
						,'text' 	=> array('receta')
						,'name' 	=> "lts_recetas"
						,'class' 	=> "requerido  "
					);

		$list_recetas  = multi_dropdown_tpl($recetas);


		$btn_save  = form_button(array('class'=>'btn btn-primary', 'name'=>'save', 'onclick'=>'agregar()','content'=>$this->lang_item("btn_guardar")));
		$btn_reset = form_button(array('class'=>'btn btn_primary', 'name'=>'reset','onclick'=>'clean_formulario()','content'=>$this->lang_item('btn_limpiar')));
		
		$tab_1['lbl_ciclos']     		= $this->lang_item('lbl_ciclos');
		$tab_1['lbl_servicios']  		= $this->lang_item('lbl_servicios');
		$tab_1['lbl_tiempos']    		= $this->lang_item('lbl_tiempos');
		$tab_1['lbl_asignar_recetas']   = $this->lang_item('lbl_recetas');

		$tab_1['btn_save']     	   		= $btn_save;
		$tab_1['btn_reset']    	   		= $btn_reset;
		$tab_1['list_ciclos']  		    = $ciclos;
		$tab_1['list_sucursales']  		= $sucursales;
		$tab_1['list_servicios']   		= $servicios;
		$tab_1['list_tiempos']     		= $tiempos;
		$tab_1['multiselect_recetas']   = $list_recetas;
		$tab_1['lbl_sucursal']     = $this->lang_item('lbl_sucursal');
		if($this->ajax_post(false)){
			echo json_encode($this->load_view_unique($seccion,$tab_1,true));
		}else{
			return $this->load_view_unique($seccion, $tab_1, true);
		}
	}

	public function ciclo_receta(){
		
		$seccion   = $this->modulo.'/'.$this->seccion.'/'.$this->seccion.'_content';
		$id_tiempo   = $this->ajax_post('id_tiempo');
		$id_sucursal = $this->ajax_post('id_sucursal');
		if($id_tiempo){
			$sqlData = array(
							 'buscar' => $id_tiempo
							,'offset' => 0
							,'limit' => 0
							);
			$recetas  = array(
							 'data'		=> $this->recetas->get_data($sqlData)
							,'value' 	=> 'id_nutricion_receta'
							,'text' 	=> array('receta')
							,'name' 	=> "lts_recetas"
							,'class' 	=> "requerido"
						);

			$list_recetas  = multi_dropdown_tpl($recetas);
			$tab['multiselect_recetas']   = $list_recetas;
			if($this->ajax_post(false)){
				echo json_encode($this->load_view_unique($seccion,$tab,true));
			}else{
				return $this->load_view_unique($seccion, $tab, true);
			}
		}
	}
	public function cargar_ciclos(){
		$seccion   = $this->modulo.'/'.$this->seccion.'/'.$this->seccion.'_content';
		$id_sucursal = $this->ajax_post('id_sucursal');
		if($id_sucursal){
			$sqlData = array(
							 'buscar' => $id_sucursal
							,'offset' => 0
							,'limit' => 0
							);
			$data_ciclo = $this->ciclos->db_get_data($sqlData);
			$dropdown_ciclos = array(
					 'data'     => $data_ciclo
					,'value' 	=> 'id_nutricion_ciclos'
					,'text' 	=> array('ciclo')
					,'name' 	=> "lts_ciclos"
					,'event'    => array('event' => 'onchange',
							   						'function' => 'load_contenido_ciclo',
			   										'params'   => array('this.value'),
			   										'params_type' => array(false)
			   									));
			$ciclos = dropdown_tpl($dropdown_ciclos);

			$data['list_ciclos'] = $ciclos;
		}
		
		if($this->ajax_post(false)){
			echo json_encode($this->load_view_unique($seccion,$data,true));
		}else{
			return $this->load_view_unique($seccion, $data, true);
		}
	}

	public function ciclo_detalle($id_ciclo = false){
		$id_ciclo  = $this->ajax_post('id_ciclo');
		$nom_ciclo = $this->ajax_post('nombre_ciclo');
		$list = '';
		$contenido_ciclo = $this->ciclos->get_ciclo_contenido($id_ciclo);
		if(!is_null($contenido_ciclo)){
			foreach ($contenido_ciclo as $key => $value) {
				$servicio[$value['servicio']][] = array('id_servicio' => $value['id_servicio'],
														 'receta'     => $value['receta'] , 
														 'id_vinculo' => $value['id_nutricion_receta']) ;
			}
			$list ='<br><div id="sidetreecontrol"><a href="?#">Colapsar</a> | <a href="?#">Extender</a></div>';
			$list .= '<ul id="treeview_ciclos" class=" treeview-gray">';
			foreach ($servicio as $item => $recetas) {
				$m = '<a class ="onclick_on" onclick ="eliminar_servicio('.$recetas[0]['id_servicio'].','.$id_ciclo.')"><span class=" iconfa-trash"></span></a>';
				$list .= '<li><span class=" iconfa-fire"></span>'.$item.$m;
				if(is_array($recetas)){
					$list .= '<ul>';
					
					foreach ($recetas as $value) {
						$m = '<a class ="onclick_on" onclick ="eliminar_receta('.$value['id_vinculo'].','.$id_ciclo.')"><span class=" iconfa-trash"></span></a>';
						$list .= '<li><span class=" iconfa-bookmark"></span>'.$value['receta'].$m.'</li>';
					}
					$list .= '</ul>';
				}
			}
			$list .= '</ul>';

			$m = '<a class ="onclick_on" onclick="eliminar_servicio(0,'.$id_ciclo.')"">Eliminar todo <span class=" iconfa-trash"></span></a>';
		}else{
			$m = '<a class ="onclick_on">No se tienen recetas vinculadas a este menu</a>';
		}
		$detalle = widgetbox_tpl($nom_ciclo, $m.$list);
		echo json_encode($detalle);
	}
}