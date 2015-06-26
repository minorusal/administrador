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
function agregar(){
	var btn                 = jQuery("button[name='save_receta']");
	var objData             = formData('#formulario');
	objData['incomplete']   = values_requeridos();
	alert(dump_var(objData));

	btn.attr('disabled','disabled');
	/*var btn          = jQuery("button[name='save_area']");
	btn.attr('disabled','disabled');
	jQuery('#mensajes').hide();
	var incomplete       = values_requeridos();
	var id_area       = jQuery('#id_area').val();
    var area          = jQuery('#area').val();
    var clave_corta      = jQuery('#clave_corta').val();
    var descripcion      = jQuery('#descripcion').val();
	
	jQuery.ajax({
		type:"POST",
		url: path()+"nutricion/recetario/insert",
		dataType: "json",
		data: {incomplete :incomplete, id_area:id_area, area:area, clave_corta:clave_corta, descripcion:descripcion},
		beforeSend : function(){
			jQuery("#registro_loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
		},
		success : function(data){
			btn.removeAttr('disabled');

			var data = data.split('|');
			if(data[0]==1){
				clean_formulario();
			}
			jQuery("#registro_loader").html('');
		    jQuery("#mensajes").html(data[1]).show('slow');
		}
	});*/
	btn.removeAttr('disabled');
}
