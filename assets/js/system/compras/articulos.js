jQuery(document).ready(function(){
	jQuery('#search-query').focus();
	jQuery('#search-query').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){  
			buscar_articulo();
		} 
	});
})

function buscar_articulo(){
	var filtro = jQuery('#search-query').val();
	jQuery.ajax({
		type:"POST",
		url: path()+"compras/articulos/listado_articulos",
		dataType: "json",
		data: {filtro : filtro},
		beforeSend : function(){
			jQuery("#loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
		},
		success : function(data){
			var funcion = 'buscar_articulo';
        	jQuery("#loader").html('');
        	jQuery('#a-1').html(data+input_keypress('search-query', funcion));
			jQuery('#search-query').val(filtro).focus();
			tool_tips();
		}
	})
}

function load_content(uri, id_content){
	jQuery('#ui-id-2').hide('slow');
	var filtro = jQuery('#search-query').val();
    jQuery.ajax({
        type: "POST",
        url: uri,
        dataType: 'json',
        data: {filtro : filtro, tabs:1},
        success: function(data){
           if(id_content==1){
           		var funcion = 'buscar_articulo';
           		jQuery('#a-1').html(data+input_keypress('search-query', funcion));
           		jQuery('#search-query').val(filtro).focus();
           		tool_tips();
           }else{
          	 	var chosen  = 'jQuery(".chzn-select").chosen();';
           		jQuery('#a-'+id_content).html(data+include_script(chosen));
           }
        }
    });
}

function detalle_articulo(id_articulo){
	jQuery('#ui-id-2').click();
	jQuery.ajax({
        type: "POST",
        url: path()+"compras/articulos/detalle_articulo",
        dataType: 'json',
        data: {id_articulo : id_articulo},
        success: function(data){
        	var chosen = 'jQuery(".chzn-select").chosen();';
        	jQuery('#a-0').html('');
        	jQuery('#a-2').html(data+include_script(chosen));
        	jQuery('#ui-id-2').show('slow');
        }
    });
}

function update_articulo(){
	jQuery('#mensajes_update').hide();
	var btn          = jQuery("button[name='update_articulo']");
	btn.attr('disabled','disabled');
	var objData = formData('#formulario');
		objData['incomplete'] = values_requeridos();    
	jQuery.ajax({
		type:"POST",
		url: path()+"compras/articulos/update_articulo",
		dataType: "json",
		data: objData,
		beforeSend : function(){
			jQuery("#update_loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
		},
		success : function(data){
			btn.removeAttr('disabled');
			var data = data.split('|');
			if(data[0]==1){
			}
			jQuery("#update_loader").html('');
		    jQuery("#mensajes_update").html(data[1]).show('slow');
		}
	});
}

function insert_articulo(){
		
	var btn          = jQuery("button[name='save_articulo']");
	btn.attr('disabled','disabled');
	jQuery('#mensajes').hide();
	
	var objData = formData('#formulario');
  	objData['incomplete'] = values_requeridos();

	jQuery.ajax({
		type:"POST",
		url: path()+"compras/articulos/insert_articulo",
		dataType: "json",
		data: objData,
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
	})
}