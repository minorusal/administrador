jQuery(document).ready(function(){
	jQuery('#search-query').focus();
	jQuery('#search-query').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){  
			buscar();
		} 
	});
});
function buscar(){
	var filtro = jQuery('#search-query').val();
	jQuery.ajax({
		type:"POST",
		url: path()+"nutricion/recetario/listado",
		dataType: "json",
		data: {filtro : filtro},
		beforeSend : function(){
			imgLoader("#loader");
		},
		success : function(data){
			var funcion = 'buscar';
        	imgLoader_clean("#loader");
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
        beforeSend : function(){
        	if(id_content!=1){
        		imgLoader('#a-'+id_content);
        	}else{
        		imgLoader('#loader');
        	}
		},
        success: function(data){
			if(id_content==1){
			  var funcion = 'buscar';
			  jQuery('#a-1').html(data+input_keypress('search-query', funcion));
			  jQuery('#search-query').val(filtro).focus();
			  tool_tips();
			}else{
				imgLoader_clean('#a-'+id_content);
				var numeric      = 'allow_only_numeric();';
				var chosen       = 'jQuery(".chzn-select").chosen();';
				var multi_chosen = "jQuery('select[name=\"lts_insumos_insert\"]').on('change', function(evt, params){edit_porciones(evt, params);});";
				jQuery('#a-'+id_content).html(data+include_script(chosen+multi_chosen+numeric));
			}
        }
    });
}
function edit_porciones(evt,params){
	if(params.selected){
		jQuery('#content_porciones_insert').show();
		jQuery.ajax({
	        type: "POST",
	        url: path()+"nutricion/recetario/detalle_articulo", 
	        dataType: 'json',
	        data: {id_articulo : params.selected},
	        beforeSend : function(){
	        	imgLoader('#loader_editar_porciones_insert');
			},
			success:function(data){
				imgLoader_clean('#loader_editar_porciones_insert');
				var numeric      = 'allow_only_numeric();';
				jQuery('#content_porciones_insert').append(data+include_script(numeric));
			}
		});
	}else{
		jQuery('#articulo_'+params.deselected).remove();
	}
}
function agregar(){
	var progress            = progress_initialized('registro_loader');
	var btn                 = jQuery("button[name='save_receta']");
	var objData             = formData('#formulario');
	objData['incomplete']   = values_requeridos();
	jQuery('#a-2').html('');
	jQuery('#mensajes').hide();
	jQuery.ajax({
		type:"POST",
		url: path()+"nutricion/recetario/insert",
		dataType: "json",
		data: {objData: objData},
		beforeSend : function(){
			btn.attr('disabled','disabled');
		},
		success : function(data){
			if(data.success == 'true' ){
				clean_formulario_recetas();
				jgrowl(data.mensaje);
			}else{
				jQuery("#mensajes").html(data.mensaje).show('slow');	
			}
		    
		}
	}).error(function(){
		       		progress.progressTimer('error', {
			            errorText:'ERROR!',
			            onFinish:function(){
			            }
		            });
		           btn.attr('disabled',false);
		        }).done(function(){
			        progress.progressTimer('complete');
			        btn.attr('disabled',false);
			    });
}
function actualizar(){
	var progress            = progress_initialized('update_loader');
	var btn                 = jQuery("button[name='update_receta']");
	var objData             = formData('#formulario_edicion');
	objData['incomplete']   = values_requeridos();
	jQuery("#mensajes_update").html('').hide('slow');
	jQuery('#mensajes').hide();
	jQuery.ajax({
		type:"POST",
		url: path()+"nutricion/recetario/update",
		dataType: "json",
		data: {objData: objData},
		beforeSend : function(){
			btn.attr('disabled','disabled');
		},
		success : function(data){
			if(data.success == 'true' ){
				jgrowl(data.mensaje);
			}else{
				jQuery("#mensajes_update").html(data.mensaje).show('slow');	
			}
		}
	}).error(function(){
		       		progress.progressTimer('error', {
			            errorText:'ERROR!',
			            onFinish:function(){
			            }
		            });
		           btn.attr('disabled',false);
		        }).done(function(){
			        progress.progressTimer('complete');
			        btn.attr('disabled',false);
			    });
}
function detalle(id_receta){  
  	jQuery('#a-0').html('');
  	jQuery('#ui-id-2').click();
  	jQuery.ajax({
        type: "POST",
        url: path()+"nutricion/recetario/detalle",
        dataType: 'json',
        data: {id_receta : id_receta},
        success: function(data){
        	var numeric      = 'allow_only_numeric();';
			var chosen       = 'jQuery(".chzn-select").chosen();';
			var multi_chosen = "jQuery('select[name=\"lts_insumos_update\"]').on('change', function(evt, params){edit_porciones(evt, params);});";
          jQuery('#a-2').html(data+include_script(chosen+multi_chosen+numeric));
          jQuery('#ui-id-2').show('slow');
          jQuery('#test').modal()
        }
    });
}
function clean_formulario_recetas(){
	clean_formulario();
	jQuery('#content_porciones_insert').html('').hide();
}