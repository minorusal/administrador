jQuery(document).ready(function() {
	jQuery('#search-query').keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){  buscar_presentaciones(); 
		} 
	});
});
function agregar_presentaciones(){
	var presentaciones    = jQuery("#presentaciones").val();
	var clave_corta = jQuery("#clave_corta").val();
	var descripcion = jQuery("#descripcion").text();
	jQuery.ajax({
		type:"POST",
		url: path()+"compras/catalogos/agregar_presentaciones",
		dataType: "json",
		data: {ajax : 1, presentaciones : presentaciones, clave_corta:clave_corta, descripcion:descripcion},
		beforeSend : function(){
			jQuery("#registro_loader").html('<img src="'+path()+'assets/images/loaders/loader27.gif"/>');
		},
		success : function(msg){
			
		    jQuery("#mensajes").html(msg).show('slow');
			jQuery("#registro_loader").html('');
		}
	});

}
function editar_presentaciones(){
	var id_presentaciones = jQuery("#id_presentaciones").val();
	var presentaciones    = jQuery("#presentaciones").val();
	var clave_corta = jQuery("#clave_corta").val();
	var descripcion = jQuery("#descripcion").text();
	
	jQuery.ajax({
		type:"POST",
		url: path()+"compras/catalogos/actualizar_presentaciones",
		dataType: "json",
		data: {id_presentaciones:id_presentaciones, presentaciones : presentaciones, clave_corta:clave_corta, descripcion:descripcion},
		beforeSend : function(){
			jQuery("#registro_loader").html('<img src="'+path()+'assets/images/loaders/loader27.gif"/>');
		},
		success : function(msg){
			
			jQuery("#mensajes").html(msg).show('slow');
			jQuery("#registro_loader").html('');
		}
	});

}
function buscar_presentaciones(){
	var filtro = jQuery('#search-query').val();
	if(filtro !== ''){
		jQuery.ajax({
	        type: "POST",
	        url: path()+"compras/catalogos/presentaciones",
	        dataType: 'json',
	        data: {filtro: filtro},
	        beforeSend : function(){
	        	jQuery("#loader").html('<img src="'+path()+'assets/images/loaders/loader27.gif"/>');
	        },
	        success: function(view){
	        	var funcion = 'buscar_presentaciones';
	        	jQuery("#loader").html('');
	        	jQuery('#a-1').html(view+input_keypress('search-query', funcion));				
	        	tool_tips();
	        }
	    });
	}
}
function detalle_presentaciones(id_presentaciones){
	jQuery('#ui-id-2').click();
	jQuery.ajax({
        type: "POST",
        url: path()+"compras/catalogos/detalle_presentaciones",
        dataType: 'json',
        data: {id_presentaciones: id_presentaciones},
      
        success: function(view){
        	//jQuery('#ui-id-2').show();
        	jQuery('#a-2').html(view);
        }
    });
}