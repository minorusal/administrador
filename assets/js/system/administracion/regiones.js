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
        }
    });
}

function agregar(){
	var btn     = jQuery("button[name='save_region']");
	btn.attr('disabled','disabled');
	jQuery('#mensajes').hide();
	var entidades = [];
	var objData = formData('#formulario');
	jQuery("[name='list'] option").each(function(){
	  entidades.push(jQuery(this).val());
	});
	objData['incomplete']  = values_requeridos();
	objData['region']      = jQuery('#txt_region').val();
	objData['clave_corta'] = jQuery('#txt_clave_corta').val();
	objData['descripcion'] = jQuery('#txt_descripcion').val();
	objData['entidades']   = entidades;
	
	jQuery.ajax({
		type:"POST",
		url: path()+"administracion/regiones/insert_region",
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
	});
}

function actualizar(){
	jQuery('#mensajes_update').hide();
	var btn             = jQuery("button[name='actualizar']");
	btn.attr('disabled','disabled');
	var entidades = [];
	var objData = formData('#formulario');
	jQuery("[name='list'] option").each(function(){
	  entidades.push(jQuery(this).val());
	});
	objData['incomplete']  = values_requeridos();
	objData['id_region']     = jQuery('#id_region').val();
	objData['region']      = jQuery('#txt_region').val();
	objData['clave_corta'] = jQuery('#txt_clave_corta').val();
	objData['descripcion'] = jQuery('#txt_descripcion').val();
	objData['entidades']   = entidades;
	jQuery.ajax({
		type:"POST",
		url: path()+"administracion/regiones/actualizar",
		dataType: "json",
		data: objData,
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

function buscar(){
	var filtro = jQuery('#search-query').val();
	jQuery.ajax({
		type:"POST",
		url: path()+"administracion/regiones/listado",
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

function detalle(id_region){
	jQuery('#ui-id-2').click();
	jQuery.ajax({
        type: "POST",
        url: path()+"administracion/regiones/detalle",
        dataType: 'json',
        data: {id_region : id_region},
        success: function(data){
        	jQuery('#a-0').html('');
        	jQuery('#a-2').html(data);
        	jQuery('#ui-id-2').show('slow');
        	var d      = jQuery('#dualselected').find('.ds_arrow button');	
			var select1 = jQuery('#dualselected select:first-child');		
			var select2 = jQuery('#dualselected select:last-child');			
			//select2.empty(); 
			d.click(function(){
				var times = (jQuery(this).hasClass('ds_prev'))? 0 : 1;	
				if(times){
					select1.find('option').each(function(){
					if(jQuery(this).is(':selected')){
						jQuery(this).attr('selected',false);
						var opc = select2.find('option:first-child');
						select2.append(jQuery(this));
					}
					});	
				}else{
					select2.find('option').each(function(){
						if(jQuery(this).is(':selected')){
							jQuery(this).attr('selected',false);
							select1.append(jQuery(this));
						}
					});
				}
				return false;
			});
        }
    });
}