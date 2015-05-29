jQuery(document).ready(function(){
	jQuery('#search-query').focus();
	jQuery('#search-query').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){  
			buscar();
		} 
	});
})
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
           		var funcion = 'buscar';
           		jQuery('#a-1').html(data+input_keypress('search-query', funcion));
           		jQuery('#search-query').val(filtro).focus();
           		tool_tips();
           }else{
           		jQuery('#a-'+id_content).html(data);
           }
        }
    });
}
function buscar(){
	var filtro = jQuery('#search-query').val();
	jQuery.ajax({
		type:"POST",
		url: path()+"administracion/descuentos/listado",
		dataType: "json",
		data: {filtro : filtro},
		beforeSend : function(){
			jQuery("#loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
		},
		success : function(data){
			var funcion = 'buscar';
        	jQuery("#loader").html('');
        	jQuery('#a-1').html(data+input_keypress('search-query', funcion));
			jQuery('#search-query').val(filtro).focus();
			tool_tips();
		}
	})
}

function detalle(id_descuento){
	jQuery('#ui-id-2').click();
	jQuery.ajax({
        type: "POST",
        url: path()+"administracion/descuentos/detalle",
        dataType: 'json',
        data: {id_descuento : id_descuento},
        success: function(data){
        	jQuery('#a-0').html('');
        	jQuery('#a-2').html(data);
        	jQuery('#ui-id-2').show('slow');
        }
    });
}
function actualizar(){
	jQuery('#mensajes_update').hide();
	var btn             = jQuery("button[name='actualizar']");
	btn.attr('disabled','disabled');
	var btn_text        = btn.html();	
	var incomplete      = values_requeridos();
	var id_descuento    = jQuery('#id_descuento').val();
	var valor_descuento = jQuery('#valor_descuento').val();
    var descuento       = jQuery('#descuento').val();
    var clave_corta     = jQuery('#clave_corta').val();
    var descripcion     = jQuery('#descripcion').val();
	jQuery.ajax({
		type:"POST",
		url: path()+"administracion/descuentos/actualizar",
		dataType: "json",
		data: {incomplete:incomplete, id_descuento:id_descuento, valor_descuento:valor_descuento, descuento:descuento, clave_corta:clave_corta, descripcion:descripcion},
		beforeSend : function(){
			jQuery("#update_loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
		},
		success : function(data){
			btn.removeAttr('disabled');		
			jQuery("#mensajes_update").html(data.contenido).show('slow');
			jQuery("#update_loader").html('');
		}
	})
}


function agregar(){
	var btn          = jQuery("button[name='save_descuento']");
	btn.attr('disabled','disabled');
	jQuery('#mensajes').hide();
	var incomplete       = values_requeridos();
	var id_descuento     = jQuery('#id_descuento').val();
    var descuento        = jQuery('#descuento').val();
    var valor_descuento  = jQuery('#valor_descuento').val();
    var clave_corta      = jQuery('#clave_corta').val();
    var descripcion      = jQuery('#descripcion').val();
	jQuery.ajax({
		type:"POST",
		url: path()+"administracion/descuentos/insert_descuento",
		dataType: "json",
		data: {incomplete :incomplete, id_descuento:id_descuento ,descuento:descuento, valor_descuento:valor_descuento, clave_corta:clave_corta, descripcion:descripcion},
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




