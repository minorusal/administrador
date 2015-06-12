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
	})
}
function load_content(uri, id_content){
	jQuery('#ui-id-2').hide('slow');
	jQuery('#ui-id-3').hide('slow');
	var filtro = jQuery('#search-query').val();
	var functions = [];
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
          	 	functions.push('jQuery(".chzn-select").chosen();');
          	 	functions.push('calendar_dual("orden_fecha","entrega_fecha")');
           		jQuery('#a-'+id_content).html(data+include_script(functions));
           }
        }
    });
}
function detalle(id_compras_orden){	
	var functions = [];
	jQuery.ajax({
        type: "POST",
        url: path()+"compras/ordenes/detalle",
        dataType: 'json',
        data: {id_compras_orden : id_compras_orden},
        success: function(data){
        	jQuery('#a-0').html('');
        	//var chosen = 'jQuery(".chzn-select").chosen();';
        	//var timepiker='jQuery(".fecha").datepicker();';
        	functions.push('jQuery(".chzn-select").chosen();');
          	functions.push('calendar_dual_detalle("orden_fecha","entrega_fecha")');
        	jQuery('#a-2').html(data+include_script(functions));
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
		})
}
function eliminar(id){	
	id = (!id)?false:id;
	if(id)if(!confirm('Esta seguro de eliminar el registro: '+id)) return false; 
	jQuery('#mensajes_update').hide();		
	var btn = jQuery("button[name='eliminar']");
	btn.attr('disabled','disabled');
		// Obtiene campos en formulario
		var objData = formData('#formulario');
		objData['id_compras_orden'] = (!objData['id_compras_orden'])?id:objData['id_compras_orden'];
		objData['msj_grid'] = (id)?1:0;
	jQuery.ajax({
		type:"POST",
		url: path()+"compras/ordenes/eliminar",
		dataType: "json",			
		data : objData,
		beforeSend : function(){
			imgLoader("#update_loader");
		},
		success : function(data){
			if(data.msj_grid==1){
		    	jQuery("#mensajes_grid").html(data.contenido).show('slow');
		    	jQuery('#ico-eliminar_'+id).closest('tr').fadeOut(function(){
					jQuery(this).remove();
				});
			}else{
				jQuery("#update_loader").html('');				
			    jQuery("#mensajes_update").html(data.contenido).show('slow');
			}

		}
	});
}
function insert(){		
	var btn   = jQuery("button[name='save']");
	//btn.attr('disabled','disabled');
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

function articulos(id_compras_orden){	
	var functions = [];
	jQuery.ajax({
        type: "POST",
        url: path()+"compras/ordenes/articulos",
        dataType: 'json',
        data: {id_compras_orden : id_compras_orden},
        success: function(data){
        	jQuery('#a-0').html('');
        	//var chosen = 'jQuery(".chzn-select").chosen();';
        	//var timepiker='jQuery(".fecha").datepicker();';
        	functions.push('jQuery(".chzn-select").chosen();');
          	functions.push('calendar_dual_detalle("orden_fecha","entrega_fecha")');
        	jQuery('#a-3').html(data+include_script(functions));
        	jQuery('#ui-id-3').show('slow');
        	jQuery('#ui-id-3').click();
        	var db   = jQuery('#dualselected').find('.ds_arrow button');	
				var sel1 = jQuery('#dualselected select:first-child');		
				var sel2 = jQuery('#dualselected select:last-child');			
				//sel2.empty(); 
				db.click(function(){
					var t = (jQuery(this).hasClass('ds_prev'))? 0 : 1;	
					if(t){
						sel1.find('option').each(function(){
						if(jQuery(this).is(':selected')){
							jQuery(this).attr('selected',false);
							var op = sel2.find('option:first-child');
							sel2.append(jQuery(this));
						}
						});	
					}else{
						sel2.find('option').each(function(){
							if(jQuery(this).is(':selected')){
								jQuery(this).attr('selected',false);
								sel1.append(jQuery(this));
							}
						});
					}
					return false;
				});
        }
    });
}
function test(id_articulo){
	if(jQuery('#'+id_articulo).is(":visible")){
		jQuery('#'+id_articulo).hide();
	}else{
		jQuery('#'+id_articulo).show();
	}
	jQuery('#dyntable2').show();
}
function agregar_articulos(){
	var btn   = jQuery("button[name='save']");
	//btn.attr('disabled','disabled');
	jQuery('#mensajes').hide();	
	// Obtiene campos en formulario
  	var objData = formData('#formulario');
  	objData['incomplete'] = values_requeridos();
	jQuery.ajax({
		type:"POST",
		url: path()+"compras/ordenes/registrar_articulos",
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
	/*var array= new Array();
	var array2= new Array();
	var arrayfinal= new Array();
		jQuery('input[name="id_compras_articulo_precios[]"]').each(function(index,valor) {
			array=valor;
		});
		jQuery('input[name="text1[]"]').each(function() {
			array2.push(jQuery(this).val());
		});
		jQuery(array).each(function(index,value){
			arrayfinal[index]=value;
		});
		alert(dump_var(array));*/
	
}