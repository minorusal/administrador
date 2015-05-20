<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Base_Controller extends CI_Controller {
 
    public function __construct(){
        parent::__construct();
        $this->removeCache();
        $this->lang_load("system","es_ES");
    }

    /**
    * Carga la base de datos de acuerdo al pais de origen
    * del usuario (mx,cr,etc)
    * @param string $db
    * @return void
    */
    public function load_database($bd){
    	if($bd!=""){
    		$load = $this->load->database($bd,TRUE);
	    	if(!$load){
	    		return true;
	    	}
    	}
    }

    /**
    * unifica las vistas header & footer con las vistas parseadas
    * de la seccion seleccionada
    * @param string $view
    * @param array $data
    * @param array $data_includes
    * @param string $ext
    * @return void
    */
    public function load_view($view, $data = array(), $data_includes = array() ,$ext = '.html'){
		$this->lang_load("navigate");
		$ext      = ($ext!='.html') ? '': $ext;

		$nivel_1  = $this->session->userdata('id_menu_n1');
		$nivel_2  = $this->session->userdata('id_menu_n2');
		$nivel_3  = $this->session->userdata('id_menu_n3');
		$perfil   = $this->session->userdata('perfil');
		
		$uri      = $this->uri->segment_array();
		$includes = $this->load_scripts($data_includes);

		$this->load->database('global_system',TRUE);
		$this->load->model('users_model');

		$user_root    = (md5(strtolower($perfil))=='63a9f0ea7bb98050796b649e85481845') ? true : false;
		$data_modulos = $this->users_model->search_modules_for_user($nivel_1, $nivel_2, $nivel_3,$user_root);
		
		if((is_array($data_modulos))){
			$data_modulos   = $this->build_array_navigator($data_modulos);
			$navigate_items =  $data_modulos[1];
			$this->session->set_userdata('sites_availables', $data_modulos[0]);
			$panel_navigate = $this->build_panel_navigate($navigate_items,$uri );
		}else{
			$data_modulos   = "";
			$navigate_items = "";
			$panel_navigate = "";
		}

		$img_path     = './assets/avatar/users/';
		$img_path_    = base_url().'assets/avatar/users/';
		$avatar_image = $this->session->userdata('avatar_user');
		
		
		$dataheader['data_js']        = (!empty($includes)) ? $includes['js']  : '';
		$dataheader['data_css']       = (!empty($includes)) ? $includes['css'] : '';
		$dataheader['base_url']       = base_url();
		$dataheader['panel_navigate'] = $panel_navigate;
		$dataheader['avatar_user']    = (file_exists($img_path.$avatar_image))? $img_path_.$avatar_image : $img_path_.'sin_foto.png';
		
		$dataheader['avatar_pais']    = $this->session->userdata('avatar_pais');
		$dataheader['user_mail']      = $this->session->userdata('mail');
		$dataheader['user_name']      = $this->session->userdata('name');
		$dataheader['user_perfil']    = $this->session->userdata('perfil');
		$dataheader['close_session']  = $this->lang_item('close_session');
		$dataheader['date']           = date('d/m/Y');
		$dataheader['fecha_hoy']	  = $this->timestamp_complete();

		$uri_nav                      = $this->array2string_lang(explode('/', $this->uri->uri_string()),array("navigate","es_ES"),' » ');
		$dataheader['uri_string']     = $uri_nav;

		$datafooter = array('anio' => date('Y'));
		
		$data = (empty($data)) ? array() : $data;
		$this->parser->parse('includes/header.html', $dataheader);
		$this->parser->parse($view.$ext, $data);
		$this->parser->parse('includes/footer.html',$datafooter); 
	}

	/**
    * Carga una vista unica sin integrar el header 
    * ni el footer, puede servir para la carga de 
    * paginas de error
    * @param string $view
    * @param array $data
    * @param boolean $autoload
    * @param array $data_includes
    * @param string $ext
    * @return void
    */
	public function load_view_unique($view, $data = array(), $autoload = false ,$data_includes = array() ,$ext = '.html'){
		$ext      = ($ext!='.html') ? '': $ext;
		$includes = $this->load_scripts($data_includes);

		$data['data_js']  = (!empty($includes)) ? $includes['js']  : '';
		$data['data_css'] = (!empty($includes)) ? $includes['css'] : '';
		$data['base_url'] = base_url();
		
		return $this->parser->parse($view.$ext, $data, $autoload);
	}

	/**
    * Carga la vista de login
    * @return void
    */
	public function load_view_login(){
		$att_fopen = array('id' => 'login');
		$att_hiden = array(
                            'name'    => 'id_user',
                            'id'      => 'id_user',
                            'type'    => 'hidden'
                        );  
		$att_user = array(
                            'name'    => 'user',
                            'id'      => 'user'
                        );  
		$att_pwd = array(
                            'name'    => 'pwd',
                            'id'      => 'pwd'
                        ); 
		$att_btn = array(
                            'name'    => 'button',
                            'id'      => 'button_login',
                            'value'   => 'true',
                            'content' => $this->lang_item('lang_btn_ingresar')
                        );

		$data['base_url']          = base_url();
		$data['form_open']         = form_open('', $att_fopen);
		$data['form_input_hidden'] = form_input($att_hiden);
		$data['form_input_user']   = form_input($att_user, '', 'placeholder="'.$this->lang_item('lang_ph_user').'"');
		$data['form_input_pwd']    = form_password($att_pwd, '', 'placeholder="'.$this->lang_item('lang_ph_pwd').'"');
		$data['form_button']       = form_button($att_btn);
		$data['form_close']        = form_close();
		
		$this->parser->parse('login.html', $data);
	}

	/**
    * Carga archivos js & css en el header
    * @param array $data
    * @return array
    */
	public function load_scripts($data){
		if(empty($data)){
			return '';
		}
		$files_js  = '';
		$files_css = '';
		$url_js    = base_url().'assets/js/system';
		$url_css   = base_url().'assets/css/system';
		if (array_key_exists('js', $data)) {
			foreach ($data['js'] as $key => $value) {
				$js_name  = $value['name'];
				$js_dir   = $value['dirname'];
				$files_js.= "<script type='text/javascript' src='$url_js/$js_dir/$js_name.js'></script>";
			}
		}
		if (array_key_exists('css', $data)) {
			foreach ($data['css'] as $key => $value) {
				$css_name  = $value['name'];
				$css_dir   = $value['dirname'];
				$files_css.= "<link rel='stylesheet' href='$url_css/$css_dir/$css_name.css' type='text/css'  />";
			}
		}

		$data_load['js']  = $files_js;
		$data_load['css'] = $files_css;

		return $data_load;
	}

	/**
	* Prepara un array para la construccion
	* del panel de navegacion 
	* @param array $array_navigator
	* @return array
	*/
	function build_array_navigator($navigator){
		foreach ($navigator as $key => $value) {
			$route = "";
			if(!is_null($value['menu_n2'])){
				if(!is_null($value['menu_n3'])){
					$route = $value['menu_n3_routes'];
					$data_navigator[$value['menu_n1']]['content'][$value['menu_n2']]['content'][$value['menu_n3']] = array( 'menu_n3'=> $value['menu_n3'] , 'icon' => $value['menu_n3_icon'],'routes'=> $route);
					$data_navigator[$value['menu_n1']]['content'][$value['menu_n2']]['icon'] = $value['menu_n2_icon'];
					$data_navigator[$value['menu_n1']]['icon'] = $value['menu_n1_icon'];
				}else{
					$route = $value['menu_n2_routes'];
					$data_navigator[$value['menu_n1']]['content'][$value['menu_n2']] = array('icon' => $value['menu_n2_icon'] , 'routes' => $route);
					$data_navigator[$value['menu_n1']]['icon'] = $value['menu_n1_icon'];
				}
			}else{
				$route = $value['menu_n1_routes'];
				$data_navigator[$value['menu_n1']] = array('icon'=>$value['menu_n1_icon'], 'routes' => $route);
			}
			$sites_availables[] = $route;
		}
		$data = array($sites_availables, $data_navigator);

		return $data;
	}

	/**
    * Contruye el Panel de navegacion
    * @param array $items
    * @param array $uri
    * @param bolean $sub
    * @return string
    */
	public function build_panel_navigate($items, $uri, $sub = false, $bool = false) {
		
	    $panel    = "";
	    $style_ul = "";
	    if($sub){ if($bool){ $style_ul = "style='display: block;'";} $panel .= "<ul class='' $style_ul>";}
	    foreach ($items as $item => $subitems) {
	        $mod_dropdown = "";
	       	$content      = "";	
	       	$routes       = "";
	       	$active       = "";
	       	$icon         = "";
	       	$sub_nivel    = "";
	       	$bool         = false;
	       	$lang_item    = "";
	       	$class_clik   = "";
	       	if(in_array(strtolower(str_replace(' ','_', $item)), $uri)){
	        	$active  = "active";
	        	$bool    = true;
	        } 
	        if(array_key_exists('content', $subitems)){
	        	$mod_dropdown = "dropdown";
	        	$content     .= $this->build_panel_navigate($subitems['content'],$uri,$sub = true, $bool);
	        	$routes       = base_url();
	        	$icon         = $subitems['icon'];
	        	$class_clik   = "";
	        	$sub_nivel    = "<span class='iconfa-circle-arrow-down' style='float:right;'></span>";
	        }else{
	        	$routes       = base_url().$subitems['routes'];
	        	$icon         = $subitems['icon'] ;
	        	$class_clik   = "load_controller";
	        }
	        $lang_item = $this->lang_item(str_replace(' ','_', $item));//<--Si se activa esta funcion se alentiza la carga de la vista "OPTIMIZAR!!!!!!!";
    		$panel .= "<li class='$mod_dropdown $active $class_clik'><a href='$routes'><span class='$icon'></span>".text_format_tpl($lang_item)." $sub_nivel </a>";
	        $panel .= $content;
	       	$panel .= "</li>";
	    }
	    if($sub){$panel .= "</ul>";}
	    return $panel;
	}         		

	/**
	* convierte un objeto a un arreglo
	* @param object $obj
	* @return array
	*/
	public function object_to_array($obj){
		$reaged = (array)$obj;
		foreach($reaged as $key => &$field){
			if(is_object($field))
				$field = $this->object_to_array($field);
		}
		return $reaged;
	}

	/**
	* elimina el cache almacenado
	* @return void
	*/
	public function removeCache(){
        $this->output->set_header('Last-Modified:'.gmdate('D, d M Y H:i:s').'GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0',false);
        $this->output->set_header('Pragma: no-cache');
    }

    /**
    * Devuleve la cantidad de segmentos contenidos en la URL
    * contabilizando a partir del dominio
    * Ejemplo http://domain.com/sitio/pagina/3
    * el valor devuleto sera 4
    * @return int
    */
    public function uri_segment(){
    	return $this->uri->total_segments();
    }

    /**
    * Devuleve el ultimo segmentos contenido en la URL
    * @return int
    */
    public function uri_segment_end($prev = 0){
    	return $this->uri->segment($this->uri->total_segments()-$prev);
    }

    /**
    * Devuleve la URL actual
    * @return string
    */
    public function uri_string(){
    	return $this->uri->uri_string();
    }

    /**
    * Si $post es false devuleve un arreglo con el total de items 
    * recibidos por el metodo POST[]
    * de lo contrario devolvera el item con respecto al index $post
    * @param int $post
    * @return array
    */
    public function ajax_post($post){
    	if($post===false){
    		return $this->input->post();
    	}
    	return $this->input->post($post);
    }


    /**
    * Si $get es false devuleve un arreglo con el total de items 
    * recibidos por el metodo GET[]
    * de lo contrario devolvera el item con respecto al index $get
    * @param int $get
    * @return array
    */
    public function ajax_get($get){
    	if($get===false){
    		return $this->input->get();
    	}
    	return $this->input->get($get);
    }

    /**
    * devuleve el tiempo unix actual
    * @return string
    */
    public function timestamp(){
    	return date('Y-m-d H:m:s');
    }

    public function days($index = false){
		$days[0]= $this->lang_item('lunes',false);
		$days[1]= $this->lang_item('martes',false);
		$days[2]= $this->lang_item('miercoles',false);
		$days[3]= $this->lang_item('jueves',false);
		$days[4]= $this->lang_item('viernes',false);
		$days[5]= $this->lang_item('sabado',false);
		$days[6]= $this->lang_item('domingo',false);

		if($index){
			return $days[ltrim($index,'0')];
		}
		return $days;
    }
    public function months($index = false){
		$months[0]  = $this->lang_item('enero', false);
		$months[1]  = $this->lang_item('febrero', false);
		$months[2]  = $this->lang_item('marzo', false);
		$months[3]  = $this->lang_item('abril', false);
		$months[4]  = $this->lang_item('mayo', false);
		$months[5]  = $this->lang_item('junio', false);
		$months[6]  = $this->lang_item('julio', false);
		$months[7]  = $this->lang_item('agosto', false);
		$months[8]  = $this->lang_item('septiembre', false);
		$months[9]  = $this->lang_item('octubre', false);
		$months[10] = $this->lang_item('noviembre', false);
		$months[11] = $this->lang_item('diciembre	', false);

		if($index){
			return $months[ltrim($index,'0')];
		}
		return $months;
		
		
    }
    public function timestamp_complete($timestamp = "" , $time = false){
	// Crea fecha larga i.e: Miércoles 06 de Mayo del 2015
    	if($timestamp==""){
			$dia=date("l");
			if ($dia=="Monday") $dia=$this->lang_item('lunes', false);
			if ($dia=="Tuesday") $dia=$this->lang_item('martes', false);
			if ($dia=="Wednesday") $dia=$this->lang_item('miercoles', false);
			if ($dia=="Thursday") $dia=$this->lang_item('jueves', false);
			if ($dia=="Friday") $dia=$this->lang_item('viernes', false);
			if ($dia=="Saturday") $dia=$this->lang_item('sabado', false);
			if ($dia=="Sunday") $dia=$this->lang_item('domingo', false);
			$dia2=date("d");
			$mes=date("F");
			if ($mes=="January") $mes=$this->lang_item('enero', false);
			if ($mes=="February") $mes=$this->lang_item('febrero', false);
			if ($mes=="March") $mes=$this->lang_item('marzo', false);
			if ($mes=="April") $mes=$this->lang_item('abril', false);
			if ($mes=="May") $mes=$this->lang_item('mayo', false);
			if ($mes=="June") $mes=$this->lang_item('junio', false);
			if ($mes=="July") $mes=$this->lang_item('julio', false);
			if ($mes=="August") $mes=$this->lang_item('agosto', false);
			if ($mes=="September")$mes=$this->lang_item('septiembre', false);
			if ($mes=="October") $mes=$this->lang_item('octubre', false);
			if ($mes=="November") $mes=$this->lang_item('noviembre', false);
			if ($mes=="December") $mes=$this->lang_item('diciembre', false);
			$anio=date("Y");

			if($time){
				$time = date('H:m:s');
				return "$dia $dia2 ". sprintf($this->lang_item('timestamp_string', false),$mes, $anio, $time);
			}
			return "$dia $dia2 ". sprintf($this->lang_item('fecha_actual', false),$mes, $anio);
		}else{
			
			$days      = $this->days();
			$timestamp = explode(' ', $timestamp);
			$time      = $timestamp[1];
			$date      = explode('-', $timestamp[0]);
			$day       = $days[intval(  (date("w",mktime(0,0,0,$date[1],$date[2],$date[0])))-1)];
			$month     = $this->months($date[1]-1);
			//$time      = date('H:m:s');
			$fecha     =  "$day ".$date[2]." ". sprintf($this->lang_item('timestamp_string', false),$month, $date[0], $time);
			
			return $fecha;
		}
	}

    /**
    * Devuelve el item de idioma con respecto al indice $index
    * @param int $index
    * @return string
    */

    public function lang_item($index, $format = true){
    	$index = str_replace('lang_', '', $index);
    	$lang_item = ($this->lang->line($index)) ? $this->lang->line($index) : $index;
    	
    	if($format==true){
    		$lang_item = text_format_tpl($lang_item);
    	}
    	return $lang_item;
    }

    /**
    * carga el archivo Lang de idioma
    * @param string $name
    * @param string $lang
    * @return void
    */

    public function lang_load($name, $lang = "es_ES"){
    	$this->lang->load(trim($name,'/'),$lang);
    }
    
    /**
    * convierte un arreglo a su respectivo item lang
    * @param array $input
    * @param string $lang_
    * @param string $separator
    * @return string
    */

	public function array2string_lang($input = array(), $lang_ =array(), $separator = " "){
		$string = "";
		$this->lang_load($lang_[0],$lang_[1]);
		foreach ($input as $item) {
			$lang_item = $this->lang_item($item);
			$string =  $string.$separator. $lang_item;
		}
		return trim($string, $separator);
	}
}
?>