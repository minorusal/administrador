jQuery(document).ready(function() {
	jQuery('#search-query').keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){  buscar_articulo(); 
		} 
	});
});
function agregar_articulo(){
	var articulo    = jQuery("#articulo").val();
	var clave_corta = jQuery("#clave_corta").val();
	var descripcion = jQuery("#descripcion").text();
	if(values_requeridos()==""){
		jQuery.ajax({
			type:"POST",
			url: path()+"inventario/catalogos/agregar_articulo",
			dataType: "json",
			data: {articulo : articulo, clave_corta:clave_corta, descripcion:descripcion},
			beforeSend : function(){
				jQuery("#registro_loader").html('Aplicando registro <img src="'+path()+'assets/images/loaders/loader27.gif"/>');
			},
			success : function(result){
				if(result==1){
					var msg = alertas_tpl('success' , '<strong>Done!</strong><br>El registro de dio de alta correctamente' ,true);
				}else{
					var msg = result;
				}
			    jQuery("#mensajes").html(msg).show('slow');
				jQuery("#registro_loader").html('');
			}
		});
	}else{
		jQuery("#mensajes").html(alertas_tpl('error' , ' <strong>Atencion!</strong><br>Los campos marcado con (*) son obligatorios, gracias' ,true)).show('slow');
	}
}
function editar_articulo(){
	var id_articulo = jQuery("#id_articulo").val();
	var articulo    = jQuery("#articulo").val();
	var clave_corta = jQuery("#clave_corta").val();
	var descripcion = jQuery("#descripcion").text();
	if(values_requeridos()==""){
		jQuery.ajax({
			type:"POST",
			url: path()+"inventario/catalogos/actualizar_articulo",
			dataType: "json",
			data: {id_articulo:id_articulo, articulo : articulo, clave_corta:clave_corta, descripcion:descripcion},
			beforeSend : function(){
				jQuery("#registro_loader").html('Guardando cambios <img src="'+path()+'assets/images/loaders/loader27.gif"/>');
			},
			success : function(result){
				
				if(result==1){
					var msg = alertas_tpl('success' , '<strong>Done!</strong><br>Los cambios se guardaron correctamente' ,true);
				}else{
					var msg = result;
				}
				jQuery("#mensajes").html(msg).show('slow');
				jQuery("#registro_loader").html('');
			}
		});
	}else{
		jQuery("#mensajes").html(alertas_tpl('error' , ' <strong>Atencion!</strong><br>Los campos marcado con (*) son obligatorios, gracias' ,true)).show('slow');
	}
}
function buscar_articulo(){
	var filtro = jQuery('#search-query').val();
	if(filtro !== ''){
		jQuery.ajax({
	        type: "POST",
	        url: path()+"inventario/catalogos/articulos",
	        dataType: 'json',
	        data: {filtro: filtro},
	        beforeSend : function(){
	        	jQuery("#loader").html('Buscando<img src="'+path()+'assets/images/loaders/loader27.gif"/>');
	        },
	        success: function(view){
	        	var funcion = 'buscar_articulo';
	        	jQuery("#loader").html('');
	        	jQuery('#a-1').html(view+input_keypress('search-query', funcion));				
	        	tool_tips();
	        }
	    });
	}
}
function detalle_articulo(id_articulo){
	jQuery('#ui-id-2').click();
	jQuery.ajax({
        type: "POST",
        url: path()+"inventario/catalogos/detalle_articulo",
        dataType: 'json',
        data: {id_articulo: id_articulo},
      
        success: function(view){
        	//jQuery('#ui-id-2').show();
        	jQuery('#a-2').html(view);
        }
    });
}