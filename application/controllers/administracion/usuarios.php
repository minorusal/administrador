<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class usuarios extends Base_Controller { 

	private $modulo;
	private $submodulo;
	private $view_content;
	private $path;
	private $icon;
	private $offset, $limit_max;
	private $tab = array(), $tab_indice = array();

	public function __construct(){
		parent::__construct();
		$this->modulo 			= 'administracion';
		$this->submodulo		= 'control_de_usuarios';
		$this->seccion          = 'usuarios';
		$this->icon 			= 'fa fa-user'; #Icono de modulo
		$this->path 			= $this->modulo.'/'.$this->seccion.'/';
		$this->view_agregar     = $this->modulo.'/'.$this->seccion.'/'.$this->seccion.'_agregar';
		$this->view_detalle    = $this->modulo.'/'.$this->seccion.'/'.$this->seccion.'_detalle';
		$this->view_content 	= 'content';
		$this->limit_max		= 10;
		$this->offset			= 0;

		$this->tab1 			= 'agregar';
		$this->tab2 			= 'listado';
		$this->tab3 			= 'detalle';
		/*for($i=0; $i<=count($this->tab_indice)-1; $i++){
			$this->tab[$this->tab_indice[$i]] = $this->tab_indice[$i];
		}*/
		$this->load->model('users_model','db_model');
		$this->load->model($this->modulo.'/perfiles_model','perfiles');
		$this->load->model($this->modulo.'/areas_model','areas');
		$this->load->model($this->modulo.'/puestos_model','puestos');
		$this->lang->load($this->modulo.'/'.$this->seccion,"es_ES");
	}
	public function config_tabs(){
		/*for($i=1; $i<=count($this->tab); $i++){
			${'tab_'.$i} = $this->tab [$this->tab_indice[$i-1]];
		}*/
		$tab_1 	= $this->tab1;
		$tab_2 	= $this->tab2;
		$tab_3 	= $this->tab3;
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
		$config_tab['style_content'] = array('','','');
		//print_debug($config_tab);
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
		$js['js'][]               = array('name' => 'usuarios', 'dirname' => 'administracion');
		$this->load_view($this->uri_view_principal(), $data, $js);
	}
	public function listado($offset = 0){
		$seccion 		= '/listado';
		$filtro         = ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";
		//$accion 		= $this->tab['listado'];
		$tab_detalle	= $this->tab3;	
		$limit 			= $this->limit_max;
		$uri_view 		= $this->modulo.$seccion;
		$url_link 		= $this->path.'listado';		
		$sqlData = array(
						'user'         => $this->session->userdata('id_usuario')
						,'buscar'      	=> $filtro
						,'offset' 		=> $offset
						,'limit'      	=>  $limit
						,'aplicar_limit'=> true
					);
		$uri_segment  = $this->uri_segment(); 
		$total_rows	  = count($this->db_model->get_users($sqlData));
		$list_content = $this->db_model->get_users($sqlData);
		$url          = base_url($url_link);
		$paginador    = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));
		//print_debug($paginador);
		if($total_rows>0){
			foreach ($list_content as $value) {
				// Evento de enlace
				$atrr = array(
								'href' => '#',
							  	'onclick' => $tab_detalle.'('.$value['id_personal'].')'
						);
				$btn_acciones['ficha'] 	= '<span style="color:blue;"  class="ico_acciones ico_articulos fa fa-user" onclick="asignar_perfil('.$value['id_personal'].','.$value['id_usuario'].','.$value['id_perfil'].')" title="'.$this->lang_item("lbl_asignar_perfil").'"></span>';
				$btn_acciones['email'] 	= '<span style="color:green;"  class="ico_acciones ico_articulos fa fa-envelope" onclick="enviar_email()" title="'.$this->lang_item("lbl_enviar_email").'"></span>';

				$acciones = implode('&nbsp;&nbsp;&nbsp;',$btn_acciones);
				// Datos para tabla
				$tbl_data[] = array('id'               => $value['id_usuario'],
									'nombre'           => tool_tips_tpl($value['name'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'nombre_de_usuario'=> $value['user'],
									'perfil'           => $value['perfil'],
									'area'             => $value['area'],
									'puesto'           => $value['puesto'],
									'acciones'		   => $acciones
									);
			}

			$tbl_plantilla = set_table_tpl();
			
			$this->table->set_heading(	$this->lang_item("id"),
										$this->lang_item("lbl_nombre"),
										$this->lang_item("lbl_user"),
										$this->lang_item("lbl_perfil"),
										$this->lang_item("lbl_area"),
										$this->lang_item("lbl_puesto"),
										$this->lang_item("lbl_acciones")

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

	public function asignar_perfil(){
		$id_perfil   = $this->ajax_post('id_perfil');
		$id_personal = $this->ajax_post('id_personal');
		$id_usuario  = $this->ajax_post('id_usuario');
		$perfiles    = $this->db_model->search_data_perfil($id_personal);

		$sqlData = array(
						'buscar'      	 => $id_usuario
						,'offset' 		 => 0
						,'limit'      	 => 10
						,'aplicar_limit' => true
					);
		$detalle = $this->db_model->get_users($sqlData);
		//print_debug($detalle);	
		$tbl_data[] = array('id'                => $detalle[0]['id_usuario'],
							'nombre'            => '<strong>'.$this->lang_item("lbl_nombre_usuario").':</strong>',
							'nombre_bd'         => $detalle[0]['name'],
							'clave_corta'       => '<strong>'.$this->lang_item("lbl_telefono").':</strong>',
							'clave_corta_bd'    => $detalle[0]['telefono']);

		$tbl_data[] = array('id'                => $detalle[0]['id_usuario'],
							'nombre'            => '<strong>'.$this->lang_item("lbl_email").':</strong>',
							'nombre_bd'         => $detalle[0]['mail'],
							'clave_corta'       => '<strong>'.$this->lang_item("lbl_user").':</strong>',
							'clave_corta_bd'    => $detalle[0]['user']);

		$tbl_data[] = array('id'                => $detalle[0]['id_usuario'],
							'nombre'            => '<strong>'.$this->lang_item("lbl_perfil").':</strong>',
							'nombre_bd'         => $detalle[0]['perfil'],
							'clave_corta'       => '<strong>'.$this->lang_item("lbl_area").':</strong>',
							'clave_corta_bd'    => $detalle[0]['area']);

		$tbl_data[] = array('id'                => $detalle[0]['id_usuario'],
							'nombre'            => '<strong>'.$this->lang_item("lbl_puesto").':</strong>',
							'nombre_bd'         => $detalle[0]['puesto'],
							'clave_corta'       => '',
							'clave_corta_bd'    => '');
		// Plantilla
		$tbl_plantilla = set_table_resumen_tpl();
		// Titulos de tabla
		$this->table->set_heading(	$this->lang_item("lbl_id"),
									$this->lang_item("lbl_informacion_general")
									/*$this->lang_item("lbl_nada"),
									$detalle[0]['sucursal'],
									$this->lang_item("lbl_nada")*/
									);
		// Generar tabla
		$this->table->set_template($tbl_plantilla);
		$tabla = $this->table->generate($tbl_data);
		
		if($perfiles){
			$boton = array(
						'class'   => 'btn btn-primary'
					   ,'name'    => 'agregar_perfil'
					   ,'onclick' => 'agregar_perfil()'
					   ,'content' => $this->lang_item("btn_guardar"));
			$btn_save   = form_button($boton);
			$perfil_array      = array(
								 'data'		=> $perfiles
								,'value' 	=> 'id_perfil'
								,'text' 	=> array('clave_corta','perfil')
								,'name'  	=> "lts_perfiles"
								,'class' 	=> "requerido"
								,'selected' => $id_perfil
								,'event'    => array('event'       => 'onchange',
							   						 'function'    => 'load_tree_view_perfil_usuario',
							   						 'params'      => array($id_personal,'this.value'),
							   						 'params_type' => array(0,0)
			   										));
		    $list_perfiles            =  dropdown_tpl($perfil_array);

		    $tabData['tabla']   = $tabla;
		    //print_debug($tabData['tabla']);
		    $tabData['lbl_perfiles']   = $this->lang_item("lbl_perfiles");
		    $tabData['id_personal']     = $id_personal;
		    $tabData['list_perfiles']  = $list_perfiles;
		    $tabData['tree_view']      = $this->treeview_perfiles_usuarios($id_personal,$id_perfil);
		    $tabData['button_save']    = $btn_save;
			$uri_view = $this->modulo.'/'.$this->seccion.'/ficha_asignar_perfiles';
			echo json_encode( $this->load_view_unique($uri_view ,$tabData, true));
		}
	}

	public function insert_perfiles(){
		$objData  	= $this->ajax_post('objData');
		if($objData['incomplete']>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode( array( 'success'=>'false', 'mensaje' => alertas_tpl('error', $msg ,false)));
		}else{
			$sqlData = array(
							 'id_personal'  => $objData['id_personal']
							,'id_perfil'    => $objData['lts_perfiles']
							,'id_menu_n1'   => (isset($objData['nivel_1'])) ? $objData['nivel_1']:''/*.','.$id_menu_n1:$id_menu_n1*/
							,'id_menu_n2'   => (isset($objData['nivel_2'])) ? $objData['nivel_2']:''/*.','.$id_menu_n2:$id_menu_n2*/
							,'id_menu_n3'   => (isset($objData['nivel_3'])) ? $objData['nivel_3']:''/*.','.$id_menu_n3:$id_menu_n3*/
							,'edit_id_usuario'  => $this->session->userdata('id_usuario')
							,'edit_timestamp'   => $this->timestamp()
							);
			$insert = $this->db_model->insert_perfiles_usuario($sqlData);
			if($insert){
				$msg = $this->lang_item("msg_update_success",false);
				echo json_encode(array(  'success'=>'true', 'mensaje' => $msg ));
			}else{
				$msg = $this->lang_item("msg_err_clv",false);
				echo json_encode( array( 'success'=>'false', 'mensaje' =>alertas_tpl('', $msg ,false)));
			}
		}
	}
	public function detalle(){
		$id_personal = $this->ajax_post('id_personal');
		$boton = array(
						'class'   => 'btn btn-primary'
					   ,'name'    => 'actualizar'
					   ,'onclick' => 'actualizar()'
					   ,'content' => $this->lang_item("btn_guardar"));
		$btn_save   = form_button($boton);
		$tabData['id_personal']  = $id_personal;
		$tabData['lbl_usuario']  = $this->lang_item("lbl_usuario");
		$tabData['lbl_paterno']  = $this->lang_item("lbl_paterno");
		$tabData['lbl_materno']  = $this->lang_item("lbl_materno");
		$tabData['lbl_telefono'] = $this->lang_item("lbl_telefono");
		$tabData['lbl_email']    = $this->lang_item("lbl_email");
		$tabData['lbl_area']     = $this->lang_item("lbl_area");
		$tabData['lbl_puesto']   = $this->lang_item("lbl_puesto");
		$tabData['lbl_perfil']   = $this->lang_item("lbl_perfil");

		$detalle = $this->db_model->get_user_detalle($id_personal);
		//print_debug($detalle);
		foreach ($detalle as $value){
			$id_perfil[]  = $value['id_perfil'];
		}
		//print_debug($id_perfil);
		$tabData['txt_nombre']    = $detalle[0]['nombre'];
		$tabData['txt_paterno']   = $detalle[0]['paterno'];
		$tabData['txt_materno']   = $detalle[0]['materno'];
		$tabData['txt_telefono']  = $detalle[0]['telefono'];
		$tabData['txt_email']     = $detalle[0]['mail'];
		
		$areas_array      = array(
								 'data'		=> $this->areas->db_get_data()
								,'value' 	=> 'id_administracion_areas'
								,'text' 	=> array('area')
								,'name' 	=> "lts_areas"
								,'class' 	=> "requerido"
								,'selected' => $detalle[0]['id_area']);
		$list_area            =  dropdown_tpl($areas_array);
		$tabData['dropdown_area']   = $list_area;
		$puestos_array      = array(
								 'data'		=> $this->puestos->db_get_data()
								,'value' 	=> 'id_administracion_puestos'
								,'text' 	=> array('puesto')
								,'name' 	=> "lts_puestos"
								,'class' 	=> "requerido"
								,'selected' => $detalle[0]['id_puesto']);
		$list_puesto            =  dropdown_tpl($puestos_array);
		$tabData['dropdown_puesto'] = $list_puesto;
		$perfiles_array  = array(
						 'data'		=> $this->perfiles->db_get_data()
						,'value' 	=> 'id_perfil'
						,'text' 	=> array('clave_corta','perfil')
						,'name' 	=> "lts_perfiles"
						,'class' 	=> "requerido"
						,'selected' => $id_perfil);
		$perfiles  = multi_dropdown_tpl($perfiles_array);
		$tabData['dropdown_perfil'] = $perfiles;
		//$tabData['tree_view']       =  $this->treeview_perfiles_usuarios($id_usuario);

		$tabData['lbl_ultima_modificacion'] = $this->lang_item('lbl_ultima_modificacion');
        $tabData['val_fecha_registro']      = $detalle[0]['timestamp'];
		$tabData['lbl_fecha_registro']      = $this->lang_item('lbl_fecha_registro');
		$tabData['lbl_usuario_registro']    = $this->lang_item('lbl_usuario_registro');

		$usuario_registro                  = $this->users_model->search_user_for_id($id_personal);
	    $usuario_name	                   = text_format_tpl($usuario_registro[0]['name'],"u");
	    $tabData['val_usuarios_registro']  = $usuario_name;

	    if($detalle[0]['edit_id_usuario']){
	    	$usuario_registro = $this->users_model->search_user_for_id($detalle[0]['edit_id_usuario']);
			$usuario_name     = text_format_tpl($usuario_registro[0]['name'],"u");
			$tabData['val_ultima_modificacion'] = sprintf($this->lang_item('val_ultima_modificacion',false), $this->timestamp_complete($detalle[0]['edit_timestamp']), $usuario_name);
	    }else{
	    	$usuario_name = '';
			$tabData['val_ultima_modificacion'] = $this->lang_item('lbl_sin_modificacion', false);
	    }
	    $tabData['button_save']      = $btn_save;
		$tabData['registro_por']     = $this->lang_item('registro_por', false);
		$tabData['usuario_registro'] = $usuario_name;

		$uri_view = $this->view_detalle;
		echo json_encode($this->load_view_unique($uri_view,$tabData,true));
	}

	public function actualizar(){
		$objData  	= $this->ajax_post('objData');
		//print_debug($objData);
		if($objData['incomplete']>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode(array(  'success'=>'false', 'mensaje' => alertas_tpl('error', $msg ,false)));
		}else{
			$sqlData = array(
				 'id_personal' => $objData['id_personal']
				,'nombre'      => $objData['txt_nombre']
				,'paterno'     => $objData['txt_paterno']
				,'materno'     => $objData['txt_materno']
				,'telefono'    => $objData['txt_telefono']
				,'mail'		   => $objData['txt_email']
				,'edit_id_usuario'  => $this->session->userdata('id_usuario')
				,'edit_timestamp'   => $this->timestamp()
				);

			$insert = $this->db_model->db_update_personal($sqlData);

			$sqlData = array(
				 'id_personal'      => $objData['id_personal']
				,'id_area'          => $objData['lts_areas']
				,'id_puesto'        => $objData['lts_puestos']
				,'edit_id_usuario'  => $this->session->userdata('id_usuario')
				,'edit_timestamp'   => $this->timestamp()
				);
			$insert = $this->db_model->db_update_usuarios($sqlData);

			$arr_perfil  = explode(',',$objData['lts_perfiles']);

			//print_debug($arr);
			if(!empty($arr_perfil)){
				$sqlData = array();
				foreach($arr_perfil as $key => $value_perfil){
					$data = $this->db_model->get_user_detalle($objData['id_personal']);
					foreach ($data as $key => $value) {	
						print_debug($value[]);
						$sqlData = array(
						 'id_personal'     => $objData['id_personal']
						,'id_perfil_tabla' => $value
						,'id_perfil'       => $value_perfil
						,'edit_id_usuario' => $this->session->userdata('id_usuario')
						,'edit_timestamp'  => $this->timestamp()
						);
						$update = $this->db_model->insert_perfiles_usuario($sqlData);
					}
				}
			}

			if($insert){
				$msg = $this->lang_item("msg_update_success",false);
				echo json_encode(array(  'success'=>'true', 'mensaje' => $msg ));
			}else{
				$msg = $this->lang_item("msg_err_clv",false);
				echo json_encode( array( 'success'=>'false', 'mensaje' =>alertas_tpl('', $msg ,false)));
			}
		}
	}
	public function agregar(){
		$seccion 		= '';
		$uri_view   	= $this->view_agregar;
		$btn_save       = form_button(array('class'=>"btn btn-primary",'name' => 'save_usuario','onclick'=>'insert()' , 'content' => $this->lang_item("btn_guardar") ));
		$btn_reset      = form_button(array('class'=>"btn btn-primary",'name' => 'reset','value' => 'reset','onclick'=>'clean_formulario()','content' => $this->lang_item("btn_limpiar")));
		
		$areas_array      = array(
								 'data'		=> $this->areas->db_get_data()
								,'value' 	=> 'id_administracion_areas'
								,'text' 	=> array('area')
								,'name' 	=> "lts_areas"
								,'class' 	=> "requerido");
		$areas            =  dropdown_tpl($areas_array);
		$puestos_array      = array(
								 'data'		=> $this->puestos->db_get_data()
								,'value' 	=> 'id_administracion_puestos'
								,'text' 	=> array('puesto')
								,'name' 	=> "lts_puestos"
								,'class' 	=> "requerido");
		$puestos            =  dropdown_tpl($puestos_array);

		$perfiles_array  = array(
						 'data'		=> $this->perfiles->db_get_data()
						,'value' 	=> 'id_perfil'
						,'text' 	=> array('clave_corta','perfil')
						,'name' 	=> "lts_perfiles"
						,'class' 	=> "requerido"
						
					);
		$perfiles  = multi_dropdown_tpl($perfiles_array);

		$tabData['base_url']         =  base_url();
		$tabData['lbl_nombre_usuario']  = $this->lang_item("lbl_nombre_usuario");
		$tabData['lbl_no_disponible']  = $this->lang_item("lbl_no_disponible");
		$tabData['lbl_nombre']       =  $this->lang_item('lbl_nombre', false);
		$tabData['lbl_paterno']      =  $this->lang_item('lbl_paterno', false);
		$tabData['lbl_materno']      =  $this->lang_item('lbl_materno', false);
		$tabData['lbl_telefono']     =  $this->lang_item('lbl_telefono', false);
		$tabData['lbl_email']        =  $this->lang_item('lbl_email', false);
		$tabData['lbl_area']         =  $this->lang_item('lbl_area', false);
		$tabData['lbl_puesto']       =  $this->lang_item('lbl_puesto', false);
		$tabData['lbl_perfil']       =  $this->lang_item('lbl_perfil', false);
		$tabData['dropdown_area']    =  $areas;
		$tabData['dropdown_puesto']  =  $puestos;
		$tabData['dropdown_perfil']  =  $perfiles;
		$tabData['button_save']      =  $btn_save;
		$tabData['button_reset']     =  $btn_reset;
		$tabData['tree_view']        =  '';
		
		

        if($this->ajax_post(false)){
				echo json_encode($this->load_view_unique($uri_view , $tabData, true));
		}else{
			return $this->load_view_unique($uri_view , $tabData, true);
		}
	}
	public function load_tree_view_perfil(){
		$id_perfil         = $this->ajax_post('id_perfil');
		$treeview_perfiles = $this->treeview_perfiles($id_perfil, true);
		echo json_encode($treeview_perfiles);
	}

	public function load_tree_view_perfil_usuario(){
		$id_perfil         = $this->ajax_post('id_perfil');
		$id_personal       = $this->ajax_post('id_personal');
		//print_debug($id_personal);
		$treeview_perfiles = $this->treeview_perfiles_usuarios($id_personal,$id_perfil);
		echo json_encode($treeview_perfiles);
	}
	public function insert(){
		$objData  	= $this->ajax_post('objData');
		if($objData['incomplete']>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode( array( 'success'=>'false', 'mensaje' => alertas_tpl('error', $msg ,false)));
		}else{
			
			$sqlData = array(
				'nombre'       => $objData['txt_nombre']
				,'paterno'     => $objData['txt_paterno']
				,'materno'     => $objData['txt_materno']
				,'telefono'    => $objData['txt_telefono']
				,'mail'        => $objData['txt_mail']
				,'id_usuario'  => $this->session->userdata('id_usuario')
				,'timestamp'   => $this->timestamp());
			$insert_personal = $this->db_model->db_insert_personal($sqlData);

			$sqlData = array(
				// 'user' => $objData['txt_nombre_usuario']
				 'user' => ''
				,'pwd'  => ''
				,'id_usuario'  => $this->session->userdata('id_usuario')
				,'timestamp'   => $this->timestamp());
			$insert_claves = $this->db_model->db_insert_claves($sqlData);

			$arr_perfil  = explode(',',$objData['lts_perfiles']);
			if(!empty($arr_perfil)){
				$sqlData = array();
				foreach ($arr_perfil as $key => $value){
					$sqlData = array(
						 'id_personal'     => $insert_personal
						,'id_clave'        => $insert_claves
						,'id_perfil'       => $value
						,'id_pais'	       => $this->session->userdata('id_pais')
						,'id_empresa'      => $this->session->userdata('id_empresa')
						,'id_sucursal'     => $this->session->userdata('id_sucursal')
						,'id_puesto'       => $objData['lts_puestos']
						,'id_area'         => $objData['lts_areas']
						,'id_menu_n1'      => ''//($objData['nivel_1'])?$objData['nivel_1']:''
						,'id_menu_n2'      => ''//($objData['nivel_2'])?$objData['nivel_2']:''
						,'id_menu_n3'      => ''//($objData['nivel_3'])?$objData['nivel_3']:''
						,'id_usuario_reg'  => $this->session->userdata('id_usuario')
						,'timestamp'       => $this->timestamp()
						);
					$insert_usuarios = $this->db_model->db_insert_usuarios($sqlData);
				}
			}
			if($insert_claves){
				$msg = $this->lang_item("msg_insert_success",false);
				echo json_encode(array(  'success'=>'true', 'mensaje' => $msg));
			}else{
				$msg = $this->lang_item("msg_err_clv",false);
				echo json_encode(array(  'success'=>'false', 'mensaje' => alertas_tpl('', $msg ,false)));
			}
					
		}
	}
	public function export_xlsx($offset=0){
		$filtro      = ($this->ajax_get('filtro')) ?  base64_decode($this->ajax_get('filtro') ): "";
		$limit 		 = $this->limit_max;
		$sqlData     = array(
			 'buscar'      	=> $filtro
			,'offset' 		=> $offset
			,'limit'      	=> $limit
		);
		$lts_content = $this->db_model->get_users($sqlData);
		if(count($lts_content)>0){
			foreach ($lts_content as $value) {
				$set_data[] = array(
									 $value['nombre'],
									 $value['usuario'],
									 $value['perfil'],
									 $value['area'],
									 $value['puesto']
									 );
			}
			
			$set_heading = array(
									$this->lang_item("lbl_nombre"),
									$this->lang_item("lbl_user"),
									$this->lang_item("lbl_perfil"),
									$this->lang_item("lbl_area"),
									$this->lang_item("lbl_puesto")
									);
	
		}

		$params = array(	'title'   => $this->lang_item("lbl_excel"),
							'items'   => $set_data,
							'headers' => $set_heading
						);
		
		$this->excel->generate_xlsx($params);
	}

	public function find_string(){
		$string = $this->ajax_post('item');
		$campo = $this->ajax_post('nom');
		$detalle = $this->db_model->get_user_by_userName($string);
		if($detalle){
			echo json_encode(array('existe' => 0, 'string' => $string, 'campo' => $campo));
		}else{
			echo json_encode(array('existe' => 1, 'string' => '', 'campo' => $campo));
		}
	}
}
