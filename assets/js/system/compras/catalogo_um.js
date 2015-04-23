jQuery(document).ready(function() {
	jQuery('#search-query').keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){  buscar_um(); 
		} 
	});
});
function agregar_um(){
	var um    = jQuery("#um").val();
	var clave_corta = jQuery("#clave_corta").val();
	var descripcion = jQuery("#descripcion").text();
	jQuery.ajax({
		type:"POST",
		url: path()+"compras/catalogos/agregar_um",
		dataType: "json",
		data: {ajax : 1, um : um, clave_corta:clave_corta, descripcion:descripcion},
		beforeSend : function(){
			jQuery("#registro_loader").html('<img src="'+path()+'assets/images/loaders/loader27.gif"/>');
		},
		success : function(msg){
			
		    jQuery("#mensajes").html(msg).show('slow');
			jQuery("#registro_loader").html('');
		}
	});

}
function editar_um(){
	var id_um = jQuery("#id_um").val();
	var um    = jQuery("#um").val();
	var clave_corta = jQuery("#clave_corta").val();
	var descripcion = jQuery("#descripcion").text();
	
	jQuery.ajax({
		type:"POST",
		url: path()+"compras/catalogos/actualizar_um",
		dataType: "json",
		data: {id_um:id_um, um : um, clave_corta:clave_corta, descripcion:descripcion},
		beforeSend : function(){
			jQuery("#registro_loader").html('<img src="'+path()+'assets/images/loaders/loader27.gif"/>');
		},
		success : function(msg){
			
			jQuery("#mensajes").html(msg).show('slow');
			jQuery("#registro_loader").html('');
		}
	});

}
function buscar_um(){
	var filtro = jQuery('#search-query').val();
	if(filtro !== ''){
		jQuery.ajax({
	        type: "POST",
	        url: path()+"compras/catalogos/um",
	        dataType: 'json',
	        data: {filtro: filtro},
	        beforeSend : function(){
	        	jQuery("#loader").html('<img src="'+path()+'assets/images/loaders/loader27.gif"/>');
	        },
	        success: function(view){
	        	var funcion = 'buscar_um';
	        	jQuery("#loader").html('');
	        	jQuery('#a-1').html(view+input_keypress('search-query', funcion));				
	        	tool_tips();
	        }
	    });
	}
}
function detalle_um(id_um){
	jQuery('#ui-id-2').click();
	jQuery.ajax({
        type: "POST",
        url: path()+"compras/catalogos/detalle_um",
        dataType: 'json',
        data: {id_um: id_um},
      
        success: function(view){
        	//jQuery('#ui-id-2').show();
        	jQuery('#a-2').html(view);
        }
    });
}