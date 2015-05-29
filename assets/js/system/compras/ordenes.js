jQuery(document).ready(function(){
	var Buscar = jQuery('#search-query');
	Buscar.focus();
	Buscar.keyup(function(e) { if(e.which == 13) buscar(); });
});

function buscar(){
	var filtro = jQuery('#search-query').val();
	jQuery.ajax({
		type:"POST",
		url: path()+"compras/ordenes/listado",
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
	});
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
           		var funcion = 'buscar';
           		jQuery('#a-1').html(data+input_keypress('search-query', funcion));
           		jQuery('#search-query').val(filtro).focus();
           		tool_tips();
           }else{
          	 	var chosen  = 'jQuery(".chzn-select").chosen();';
          	 	var timepicker = 'datepicker(".fecha");';
           		jQuery('#a-'+id_content).html(data+include_script(chosen+timepicker));
           }
        }
    });
}
function detalle(id_compras_orden){	
	jQuery.ajax({
        type: "POST",
        url: path()+"compras/ordenes/detalle",
        dataType: 'json',
        data: {id_compras_orden : id_compras_orden},
        success: function(data){
        	var chosen = 'jQuery(".chzn-select").chosen();';
        	jQuery('#a-0').html('');
        	jQuery('#a-2').html(data+include_script(chosen));
        	jQuery('#ui-id-2').show('slow');
        	jQuery('#ui-id-2').click();
        }
    });
}
function actualizar(){	
		jQuery('#mensajes_update').hide();		
		var btn          = jQuery("button[name='update']");
		btn.attr('disabled','disabled');
  		// Obtiene campos en formulario
  		var objData = formData('#formulario');
  		objData['incomplete'] = values_requeridos();
		jQuery.ajax({
			type:"POST",
			url: path()+"compras/ordenes/actualizar",
			dataType: "json",			
			data : objData,
			beforeSend : function(){
				imgLoader("#update_loader");
			},
			success : function(data){
				btn.removeAttr('disabled');
				// if(data.id==1){	}
				jQuery("#update_loader").html('');
			    jQuery("#mensajes_update").html(data.contenido).show('slow');
			}
		});
}
function eliminar(){	
		jQuery('#mensajes_update').hide();		
		var btn = jQuery("button[name='eliminar']");
		btn.attr('disabled','disabled');
  		// Obtiene campos en formulario
  		var objData = formData('#formulario');
  		//objData['incomplete'] = values_requeridos();
		jQuery.ajax({
			type:"POST",
			url: path()+"compras/ordenes/eliminar",
			dataType: "json",			
			data : objData,
			beforeSend : function(){
				imgLoader("#update_loader");
			},
			success : function(data){
				//btn.removeAttr('disabled');
				// if(data.id==1){	}
				jQuery("#update_loader").html('');
			    jQuery("#mensajes_update").html(data.contenido).show('slow');
			}
		})
}
function insert(){		
	var btn   = jQuery("button[name='save']");
	btn.attr('disabled','disabled');
	jQuery('#mensajes').hide();	
	// Obtiene campos en formulario
  	var objData = formData('#formulario');
  	objData['incomplete'] = values_requeridos();
	jQuery.ajax({
		type:"POST",
		url: path()+"compras/ordenes/insert",
		dataType: "json",
		data : objData,
		beforeSend : function(){
			jQuery("#registro_loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
		},
		success : function(data){
			btn.removeAttr('disabled');
			if(data.id==1){
				clean_formulario();
			}
			jQuery("#registro_loader").html('');
		    jQuery("#mensajes").html(data.contenido).show('slow');
			
		}
	});
}
function show_proveedor(id_tipo){
	if(id_tipo==1){
		jQuery('#proveedores').show('slow');
		jQuery('#prefactura').show('slow');
		jQuery('[name=id_proveedor]').addClass('requerido');
		jQuery('#prefactura_num').addClass('requerido');
	}else{
		jQuery('#proveedores').hide('slow');
		jQuery('#prefactura').hide('slow');
		jQuery('[name=id_proveedor]').removeClass('requerido');
		jQuery('#prefactura_num').removeClass('requerido');
	}
}
function show_direccion(id_sucursal){
	jQuery.ajax({
        type: "POST",
        url: path()+"compras/ordenes/show_direccion",
        dataType: 'json',
        data: {id_sucursal : id_sucursal},
        success: function(data){
          jQuery('#entrega_direccion').html(data);
        }
    });
}
