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
		url: path()+"compras/ordenes/listado_ordenes",
		dataType: "json",
		data: {filtro : filtro},
		beforeSend : function(){
			jQuery("#loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
		},
		success : function(data){
			var funcion = 'buscar_orden';
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
           		var funcion = 'buscar_orden';
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

function ordenes_detalle(id_compras_orden){	
	jQuery.ajax({
        type: "POST",
        url: path()+"compras/ordenes/ordenes_detalle",
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

function update_orden(){	
		jQuery('#mensajes_update').hide();		
		var btn          = jQuery("button[name='update_orden']");
		btn.attr('disabled','disabled');
  		// Obtiene campos en formulario
  		var objData = formData('#formulario');
  		objData['incomplete'] = values_requeridos();
		jQuery.ajax({
			type:"POST",
			url: path()+"compras/ordenes/update_orden",
			dataType: "json",			
			data : objData,
			beforeSend : function(){
				imgLoader("#update_loader");
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

function insert_articulo(){
		
	var btn          = jQuery("button[name='save_orden']");
	btn.attr('disabled','disabled');
	jQuery('#mensajes').hide();
	
	var incomplete   = values_requeridos();
    var articulo     = jQuery('#articulo').val();
    var clave_corta  = jQuery('#clave_corta').val();
    var descripcion  = jQuery('#descripcion').val();
   
    var presentacion = jQuery("select[name='lts_presentaciones'] option:selected").val();
    var linea        = jQuery("select[name='lts_lineas'] option:selected").val();
    var um           = jQuery("select[name='lts_um'] option:selected").val();
    var marca        = jQuery("select[name='lts_marcas'] option:selected").val();


	jQuery.ajax({
		type:"POST",
		url: path()+"compras/ordenes/insert_orden",
		dataType: "json",
		data: {incomplete :incomplete, articulo:articulo, clave_corta:clave_corta, descripcion:descripcion,presentacion:presentacion,linea:linea,um:um,marca:marca },
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