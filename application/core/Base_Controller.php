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
		$items    = $this->session->userdata('id_modulo');
		$uri      = $this->uri->segment_array();
		$includes = $this->load_scripts($data_includes);
		
		$this->load->database('global_system',TRUE);
		$this->load->model('users_model');
		$data_modulos = $this->users_model->search_modules_for_user($items);
		$data_modulos = $this->buil_array_navigator($data_modulos);
		

		$this->session->set_userdata('sites_availables', $data_modulos[0]);
		$items    =  $data_modulos[1];

		$dataheader['data_js']        = (!empty($includes)) ? $includes['js']  : '';
		$dataheader['data_css']       = (!empty($includes)) ? $includes['css'] : '';
		$dataheader['base_url']       = base_url();
		$dataheader['panel_navigate'] = $this->buil_panel_navigate($items,$uri);
		$dataheader['avatar_user']    = $this->session->userdata('avatar_user');
		$dataheader['avatar_pais']    = $this->session->userdata('avatar_pais');
		$dataheader['user_mail']      = $this->session->userdata('mail');
		$dataheader['user_name']      = $this->session->userdata('name');
		$dataheader['user_perfil']    = $this->session->userdata('perfil');
		$dataheader['close_session']  = $this->lang_item('close_session');
		$dataheader['date']           = date('d/m/Y');
		
		$uri_nav                      = $this->array2string_lang(explode('/', $this->uri->uri_string()),array("navigate","es_ES"),' » ');
		$dataheader['uri_string']     = $uri_nav;

		$datafooter = array();
		
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
	function buil_array_navigator($navigator){
		foreach ($navigator as $key => $value) {
			$route = "";
			if(!is_null($value['submodulo'])){
				if(!is_null($value['seccion'])){
					$route = $value['modulo'].'/'.$value['submodulo'].'/'.$value['seccion_routes'];
					$data_navigator[$value['modulo']]['content'][$value['submodulo']]['content'][$value['seccion']] = array( 'seccion'=> $value['seccion'] , 'icon' => $value['seccion_icon'],'routes'=> $route);
					$data_navigator[$value['modulo']]['content'][$value['submodulo']]['icon'] = $value['submodulo_icon'];
					$data_navigator[$value['modulo']]['icon'] = $value['modulo_icon'];
				}else{
					$route = $value['modulo'].'/'.$value['submodulo_routes'];
					$data_navigator[$value['modulo']]['content'][$value['submodulo']] = array('icon' => $value['submodulo_icon'] , 'routes' => $route);
					$data_navigator[$value['modulo']]['icon'] = $value['modulo_icon'];
				}
			}else{
				$route = $value['modulo_routes'];
				$data_navigator[$value['modulo']] = array('icon'=>$value['modulo_icon'], 'routes' => $route);
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
	public function buil_panel_navigate($items, $uri, $sub = false, $bool = false) {
		$this->lang_load("navigate");
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
	       	if(in_array(strtolower($item), $uri)){
	        	$active  = "active";
	        	$bool    = true;
	        } 
	        if(array_key_exists('content', $subitems)){
	        	$mod_dropdown = "dropdown";
	        	$content     .= $this->buil_panel_navigate($subitems['content'],$uri,$sub = true, $bool);
	        	$icon         = $subitems['icon'];
	        	$sub_nivel    = "<span class='iconfa-circle-arrow-down' style='float:right;'></span>";
	        }else{
	        	$routes = base_url().$subitems['routes'];
	        	$icon   = $subitems['icon'] ;
	        }
	        $lang_item = $this->lang_item($item);

    		$panel .= "<li class='$mod_dropdown $active '><a href='$routes'><span class='$icon'></span>".text_format_tpl($lang_item)." $sub_nivel </a>";
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
    * @return void
    */
    public function uri_segment(){
    	return $this->uri->total_segments();
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
    * devuleve el tiempo unix actual
    * @return string
    */
    public function timestamp(){
    	return date('Y-m-d H:m:s',now());
    }

    /**
    * Devuelve el item de idioma con respecto al indice $index
    * @param int $index
    * @return string
    */

    public function lang_item($index, $format = true){
    	$index = str_replace('lang_', '', $index);
    	$lang_item = ($this->lang->line('lang_'.$index)) ? $this->lang->line('lang_'.$index) : $index;
    	
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