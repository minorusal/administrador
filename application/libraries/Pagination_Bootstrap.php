<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pagination_Bootstrap extends CI_Pagination{
	
	var $first_link			= '&larr; Primero';
	var $next_link			= 'Siguiente &raquo;';
	var $prev_link			= '&laquo; Anterior';
	var $last_link			= 'Ultimo &rarr;';

	var $full_tag_open		= '<div class="pagination" style="float:right;"><ul>';
	var $full_tag_close		= '</ul></div>';
	var $first_tag_open		= '<li class="prev  paginate_button page">';
	var $first_tag_close	= '</li>';
	var $last_tag_open		= '<li class="next  paginate_button page">';
	var $last_tag_close		= '</li>';
	var $cur_tag_open		= '<li class="active"><a href="">';
	var $cur_tag_close		= '</a></li>';
	var $next_tag_open		= '<li class="next  paginate_button  page">';
	var $next_tag_close		= '</li>';
	var $prev_tag_open		= '<li class="prev  paginate_button page">';
	var $prev_tag_close		= '</li>';
	var $num_tag_open		= '<li class="page">';
	var $num_tag_close		= '</li>';
	
	public function paginator_generate($total_rows = 5 , $url = '' , $per_page = 2 , $uri_segment = 4, $config = array()){
		
		if(!empty($config)){
			$this->initialize($config);
		}else{
			$config['total_rows']  = $total_rows;
			$config['base_url']    = $url;
			$config['per_page']    = $per_page;
			$config['uri_segment'] = $uri_segment;

			$this->initialize($config);
		}
		
		$links = $this->create_links();

		return $links;
	}

	public function showing_items($limit, $offset, $total_items){

		$item_firts = $offset + 1;
		$item_last  = $offset + $limit;
		$showing    = '<div style="float:left;"><p class="text-info">Resultado '.$item_firts.' - '.$item_last.' de '.$total_items.' registros</p></div>';

		return $showing;
	}
}