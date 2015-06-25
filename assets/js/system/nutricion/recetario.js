jQuery(document).ready(function(){
	jQuery('#search-query').focus();
	jQuery('#search-query').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){  
			buscar();
		} 
	});
});
function load_content(uri, id_content){
	jQuery('#ui-id-2').hide('slow');
	var filtro = jQuery('#search-query').val();
    jQuery.ajax({
        type: "POST",
        url: uri,
        dataType: 'json',
        data: {filtro : filtro, tabs:1},
        beforeSend : function(){
        	if(id_content!==1){
        		imgLoader('#a-'+id_content);
        	}
		},
        success: function(data){
			if(id_content==1){
			  var funcion = 'buscar';
			  jQuery('#a-1').html(data+input_keypress('search-query', funcion));
			  jQuery('#search-query').val(filtro).focus();
			  tool_tips();
			}else{
				imgLoader_clean('#a-'+id_content);
				var chosen = 'jQuery(".chzn-select").chosen();';
				jQuery('#a-'+id_content).html(data+include_script(chosen));

			}
        }
    });
}

function buscar(){
	var filtro = jQuery('#search-query').val();
	jQuery.ajax({
		type:"POST",
		url: path()+"nutricion/recetario/listado",
		dataType: "json",
		data: {filtro : filtro},
		beforeSend : function(){
			imgLoader("#loader");
		},
		success : function(data){
			var funcion = 'buscar';
        	imgLoader_clean("#loader");
        	jQuery('#a-1').html(data+input_keypress('search-query', funcion));
			jQuery('#search-query').val(filtro).focus();
			tool_tips();
		}
	});
}
