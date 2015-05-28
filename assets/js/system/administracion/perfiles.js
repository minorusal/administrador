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
        	var treeview = 'load_treeview("treeview-modules")';
           if(id_content==1){
           		var funcion = 'buscar';
           		jQuery('#a-1').html(data+input_keypress('search-query', funcion));
           		jQuery('#search-query').val(filtro).focus();
           		tool_tips();
           }else{
           		jQuery('#a-'+id_content).html(data);
           		var chosen  = 'jQuery(".chzn-select").chosen();';
           		jQuery('#a-'+id_content).html(data+include_script(treeview));
           }
        }
    });
}
function buscar(){
	var filtro = jQuery('#search-query').val();
	jQuery.ajax({
		type:"POST",
		url: path()+"administracion/perfiles/listado",
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

function detalle(id_perfil){
	jQuery('#ui-id-2').click();
	jQuery.ajax({
        type: "POST",
        url: path()+"administracion/perfiles/detalle",
        dataType: 'json',
        data: {id_perfil : id_perfil},
        success: function(data){
        	var treeview = 'load_treeview("treeview-modules")';
        	jQuery('#a-0').html('');
        	jQuery('#a-2').html(data);
        	jQuery('#a-2').html(data+include_script(treeview));
        	jQuery('#ui-id-2').show('slow');
        }
    });
}
function actualizar(){
	jQuery('#mensajes_update').hide();
	var btn             = jQuery("button[name='actualizar']");
	btn.attr('disabled','disabled');
	var btn_text        = btn.html();	
	var nivel_1 = [];
	var nivel_2 = [];
	var nivel_3 = [];
	var objData = formData('#formulario');
	//objData['incomplete']  = incomplete;

	jQuery("input[name='nivel_1']:checked").each(function(){
	  nivel_1.push(jQuery(this).val());
	});
	
	jQuery("input[name='nivel_2']:checked").each(function(){
	  nivel_2.push(jQuery(this).val());
	});
	
	jQuery("input[name='nivel_3']:checked").each(function(){
	  nivel_3.push(jQuery(this).val());
	});

	objData['incomplete']  = values_requeridos();
	objData['id_perfil']   = jQuery('#id_perfil').val();
	objData['perfil']      = jQuery('#txt_perfil').val();
	objData['clave_corta'] = jQuery('#txt_clave_corta').val();
	objData['descripcion'] = jQuery('#txt_descripcion').val();
	objData['nivel_1']     = (nivel_1.length>0) ? nivel_1.join(',') : nivel_1;
	objData['nivel_2']     = (nivel_2.length>0) ? nivel_2.join(',') : nivel_2;
	objData['nivel_3']     = (nivel_3.length>0) ? nivel_3.join(',') : nivel_3;
	
	jQuery.ajax({
		type:"POST",
		url: path()+"administracion/perfiles/actualizar",
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
	});
}


function agregar(){
	var btn          = jQuery("button[name='save_perfil']");
	btn.attr('disabled','disabled');
	jQuery('#mensajes').hide();
	var incomplete = values_requeridos();
	var nivel_1 =  [];
	var nivel_2 =  [];
	var nivel_3 =  [];
	
	var objData = formData('#formulario');
	objData['incomplete']  = incomplete;
  	jQuery("input[name='nivel_1']:checked").each(function(){
	  nivel_1.push(jQuery(this).val());
	});
	
	jQuery("input[name='nivel_2']:checked").each(function(){
	  nivel_2.push(jQuery(this).val());
	});
	
	jQuery("input[name='nivel_3']:checked").each(function(){
	  nivel_3.push(jQuery(this).val());
	});
	objData['perfil']      = jQuery('#txt_perfil').val();
	objData['descripcion'] = jQuery('#txt_descripcion').val();
	objData['clave_corta'] = jQuery('#txt_clave_corta').val();
	objData['nivel_1'] = (nivel_1.length>0) ? nivel_1.join(',') : nivel_1;
	objData['nivel_2'] = (nivel_2.length>0) ? nivel_2.join(',') : nivel_2;
	objData['nivel_3'] = (nivel_3.length>0) ? nivel_3.join(',') : nivel_3;
	jQuery.ajax({
		type:"POST",
		url: path()+"administracion/perfiles/insert_perfil",
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




