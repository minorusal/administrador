<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	if(!function_exists('tabbed_widgets')){
		
		function tabbed_widgets($config, $uri_string, $segment = 1, $content = ""){
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
				$data     = ($i==($segment-1)) ? $content : "";

				$link = '"'.trim($uri_string.'/'.$config['links'][$i],"/").'"';

				$tabs_li .= "<li class='ui-state-default ui-corner-top $activate' role='tab' tabindex='$i' aria-controls='a-$i' aria-labelledby='ui-id-$i' aria-selected='$selected'>
								<a href='#a-$i' onclick='load_contetn_tab($link, $i);' class='ui-tabs-anchor' role='presentation' tabindex='$i' id='ui-id-$i'>
									".ucwords(strtolower($config['names'][$i]))."
								</a>
							</li>";

				$tabs_content .= "<div id='a-$i' aria-labelledby='ui-id-$i' class='ui-tabs-panel ui-widget-content ui-corner-bottom' role='tabpanel' aria-expanded='$selected' aria-hidden='false' style='$display'>
        							$data
    								</div>";
			}

			$tabbed .= "<div class='tabbedwidget tab-primary ui-tabs ui-widget ui-widget-content ui-corner-all'>";
    		$tabbed .= "<ul class='ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all' role='tablist'>";
    		$tabbed .= $tabs_li;
    		$tabbed .= "</ul>";
    		$tabbed .= $tabs_content;
    		$tabbed .= "</div>";

    	return $tabbed;

		}
	}	
?>