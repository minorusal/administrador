jQuery(document).ready(function(){
	jQuery('#search-query').focus();
	jQuery('#search-query').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){  
			buscar_almacen();
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
           		var funcion = 'buscar_almacen';
           		jQuery('#a-1').html(data+input_keypress('search-query', funcion));
           		jQuery('#search-query').val(filtro).focus();
           		tool_tips();
           }else{
           		jQuery('#a-'+id_content).html(data);
           }
        }
    });
}
function buscar_almacen(){
	var filtro = jQuery('#search-query').val();
	jQuery.ajax({
		type:"POST",
		url: path()+"almacen/almacenes/listado_almacenes",
		dataType: "json",
		data: {filtro : filtro},
		beforeSend : function(){
			jQuery("#loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
		},
		success : function(data){
			var funcion = 'buscar_almacen';
        	jQuery("#loader").html('');
        	jQuery('#a-1').html(data+input_keypress('search-query', funcion));
			jQuery('#search-query').val(filtro).focus();
			tool_tips();
		}
	})
}
function detalle_almacenes(id_almacen){
	jQuery('#ui-id-2').click();
	jQuery.ajax({
        type: "POST",
        url: path()+"almacen/almacenes/detalle_almacenes",
        dataType: 'json',
        data: {id_almacen : id_almacen},
        success: function(data){
        	jQuery('#a-0').html('');
        	jQuery('#a-2').html(data);
        	jQuery('#ui-id-2').show('slow');
        }
    });
}
function update_almacenes(){
	jQuery('#mensajes_update').hide();
	var btn          = jQuery("button[name='update_almacenes']");
	btn.attr('disabled','disabled');
	var btn_text     = btn.html();	
	var incomplete       = values_requeridos();
	var id_almacen  = jQuery('#id_almacen').val();
    var almacen     = jQuery('#almacen').val();
    var clave_corta      = jQuery('#clave_corta').val();
    var descripcion      = jQuery('#descripcion').val();
  
	jQuery.ajax({
		type:"POST",
		url: path()+"almacen/almacenes/update_almacen",
		dataType: "json",
		data: {incomplete :incomplete,id_almacen:id_almacen, almacen:almacen, clave_corta:clave_corta, descripcion:descripcion },
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
	})
}

function insert_almacen(){
	var btn          = jQuery("button[name='save_almacen']");
	btn.attr('disabled','disabled');
	jQuery('#mensajes').hide();
	var incomplete   = values_requeridos();
    var almacen      = jQuery('#almacenes').val();
    var clave_corta  = jQuery('#clave_corta').val();
    var descripcion  = jQuery('#descripcion').val();
  
	jQuery.ajax({
		type:"POST",
		url: path()+"almacen/almacenes/insert_almacen",
		dataType: "json",
		data: {incomplete :incomplete, almacenes:almacen, clave_corta:clave_corta, descripcion:descripcion },
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


