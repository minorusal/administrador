<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	if(!function_exists('table_tpl')){
		function table_tpl($titles, $data){
			foreach ($data as $key => $value) {
			//	$row . = '<tr>';
			}
		}
	}

	if(!function_exists('tabbed_tpl')){
		function tabbed_tpl($config, $uri_string, $segment = 1, $content = ""){
			$link         = "";
			$tabs         = "";
			$tabbed       = "";
			$tabs_li      = "";
			$tabs_content = "";
			$total_tabs   = count($config['names']);

			for ($i=0; $i < $total_tabs; $i++) { 
				$activate = ($i==($segment-1)) ? 'ui-tabs-active ui-state-active' : "";
				$selected = ($i==($segment-1)) ? 'true' : "";
				$display  = ($i==($segment-1)) ? 'display: block' : "";
				if(is_array($content)){
					$data = (array_key_exists($i, $content) ) ? $content[$i] : '';
				}else{
					$data     = ($i==($segment-1)) ? $content : "";
				}
				$link     = '"'.trim($uri_string.'/'.$config['links'][$i],"/").'"';
				$action   = $config['action'][$i];
				$attr     = array_2_string_format($config['attr'][$i]);
				$tabs_li .= "<li   class='ui-state-default ui-corner-top $activate' role='tab' tabindex='$i' aria-controls='a-$i' aria-labelledby='ui-id-$i' aria-selected='$selected'>
								<a $attr href='#a-$i' onclick='$action($link, $i);' class='ui-tabs-anchor' role='presentation' tabindex='$i' id='ui-id-$i'>
									".$config['names'][$i]."
								</a>
							</li>";

				$tabs_content .= "<div id='a-$i' aria-labelledby='ui-id-$i' class='ui-tabs-panel ui-widget-content ui-corner-bottom' role='tabpanel' aria-expanded='$selected' aria-hidden='false' style='overflo $display'>
        							$data
    								</div>";
			}

			$tabbed .= "<div class='tabbedwidget tab-primary ui-tabs ui-widget ui-widget-content ui-corner-all' style='overflow:visible;'>";
    		$tabbed .= "<ul class='ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all' role='tablist'>";
    		$tabbed .= $tabs_li;
    		$tabbed .= "</ul>";
    		$tabbed .= $tabs_content;
    		$tabbed .= "</div>";

    	return $tabbed;

		}
	}

	if(!function_exists('alertas_tpl')){
		function alertas_tpl($type = '', $mensaje = '' ,$close = false){
			$alert = "";
			$button_close = "";
			if($type == ""){
				$type = "alert";
			}else{
				$type = "alert-$type";
			}
			if($close){
				$button_close = "button data-dismiss='alert' class='close' type='button'>×</button>";
			}
			
			$alert ="<div class='alert $type'> $mensaje </div>";

			return $alert;
		}

	if(!function_exists('text_format_tpl')){
		function text_format_tpl($string){
			if($string==''){
				return $string;
			}
			return ucwords(strtolower($string));
		}
		
	}

	if(!function_exists('tool_tips_tpl')){
		function tool_tips_tpl($value, $tool_tip = '', $pocision = 'right', $attr = array()){
			$attr = array_2_string_format($attr);
			$tool_tip ="<ul class='tooltipsample'>
                       		<a $attr data-placement='$pocision' data-rel='tooltip'  data-original-title='$tool_tip' >$value</a></li>
                    	</ul>";
			
			return $tool_tip ;
		}
	}

	if(!function_exists('dropdown_tpl')){
		function dropdown_tpl($data = array(), $value, $text, $name = "", $class = "" ,$leyenda = "" ){
			
			$name         = ($name=="")?"selected": $name;
			$count        = 0;
			
			foreach ($data as $option => $item) {
				$option_value = "";
				if($count==0){
					$options[0]= ($leyenda=="") ? '-----' : $leyenda;
				}
				if(is_array($text)){
					foreach ($text as $string) {
						$option_value .= $item[$string].'-';
					}
					$options[$item[$value]] = trim($option_value, '-');
				}else{
					$options[$item[$value]]= $item[$text];
				}
				
				$count++;
			}

			$selected = '<span class="formwrapper">'.form_dropdown($name, $options, '', " class='chzn-select $class'")."</span>";
			
			return $selected;
		}
	}


	
	
}
		
?>