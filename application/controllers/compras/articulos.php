<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class articulos extends Base_Controller { 
	
	var $uri_string  = 'compras/articulos';
	var $view        = 'catalogos'
	public function __construct(){
		parent::__construct();
	}

	public function index(){
		
	}
}