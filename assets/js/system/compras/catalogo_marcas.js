jQuery(document).ready(function() {
	jQuery('#search-query').keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){  buscar_marcas(); 
		} 
	});
});
function agregar_marcas(){
	jQuery('#mensajes').hide();
	var marcas    = jQuery("#marcas").val();
	var clave_corta = jQuery("#clave_corta").val();
	var descripcion = jQuery("#descripcion").text();
	jQuery.ajax({
		type:"POST",
		url: path()+"compras/catalogos/agregar_marcas",
		dataType: "json",
		data: {ajax : 1, marcas : marcas, clave_corta:clave_corta, descripcion:descripcion},
		beforeSend : function(){
			jQuery("#registro_loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
		},
		success : function(msg){
			
		    jQuery("#mensajes").html(msg).show('slow');
			jQuery("#registro_loader").html('');
		}
	});

}
function editar_marcas(){
	jQuery('#mensajes').hide();
	var id_marcas = jQuery("#id_marcas").val();
	var marcas    = jQuery("#marcas").val();
	var clave_corta = jQuery("#clave_corta").val();
	var descripcion = jQuery("#descripcion").text();
	
	jQuery.ajax({
		type:"POST",
		url: path()+"compras/catalogos/actualizar_marcas",
		dataType: "json",
		data: {id_marcas:id_marcas, marcas : marcas, clave_corta:clave_corta, descripcion:descripcion},
		beforeSend : function(){
			jQuery("#registro_loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
		},
		success : function(msg){
			
			jQuery("#mensajes").html(msg).show('slow');
			jQuery("#registro_loader").html('');
		}
	});

}
function buscar_marcas(){
	var filtro = jQuery('#search-query').val();
	if(filtro !== ''){
		jQuery.ajax({
	        type: "POST",
	        url: path()+"compras/catalogos/marcas",
	        dataType: 'json',
	        data: {filtro: filtro},
	        beforeSend : function(){
	        	jQuery("#loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
	        },
	        success: function(view){
	        	var funcion = 'buscar_marcas';
	        	jQuery("#loader").html('');
	        	jQuery('#a-1').html(view+input_keypress('search-query', funcion));				
	        	tool_tips();
	        }
	    });
	}
}
function detalle_marcas(id_marcas){
	jQuery('#ui-id-2').click();
	jQuery.ajax({
        type: "POST",
        url: path()+"compras/catalogos/detalle_marcas",
        dataType: 'json',
        data: {id_marcas: id_marcas},
      
        success: function(view){
        	//jQuery('#ui-id-2').show();
        	jQuery('#a-2').html(view);
        }
    });
}