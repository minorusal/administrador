jQuery(document).ready(function(){
	calendar_dual("fecha_inicio", "fecha_termino");
});
function load_programacion(id_sucursal){
	var calendar          = 'calendar_dual("fecha_inicio", "fecha_termino");';
	var dual_select       = 'dual_select();';
	var input_calendar    = 'calendar("input_calendar");';
	var chosen            = 'jQuery(".chzn-select").chosen();';
	var remove_e            = 'remove_option("multidropdown_ciclos_especiales");';
	var remove_f            = 'remove_option("multidropdown_festivos");';
	jQuery.ajax({
        type: "POST",
        url: path()+"nutricion/programacion/form_config_programacion",
        dataType: 'json',
        data: { id_sucursal : id_sucursal },
        beforeSend : function(){
        	imgLoader('#loader_programacion');
        },
        success: function(data){
        	imgLoader_clean("#loader_programacion");
        	jQuery('#configuracion_programacion').html(data+include_script(calendar+dual_select+input_calendar+chosen+remove_e+remove_f));
        	
        }
    });
}
function agregar_festivo(){
	var multidropdown   = jQuery('select[name="multidropdown_festivos"]');
	var fechas_festivas = jQuery('#fechas_festivas').val();
	var existe          = false;
	multidropdown.find('option').each(function(){
        var v  = jQuery(this).val(); 
        if(v==fechas_festivas){
        	existe = true;
        }
    }); 
	if(existe){
	}else{
		if(fechas_festivas!=''){
			multidropdown.append(jQuery('<option></option>').attr('value',fechas_festivas).text(fechas_festivas));
			multidropdown.find('option[value="'+fechas_festivas+'"]').attr('selected', true).trigger('liszt:updated');
			multidropdown.on('change', function(evt, params){
			remove_option(multidropdown);
			});
			
		}else{
			alert('Es necesario definir una fecha, gracias');
		}
	}
}
function agregar_ciclo_especial(){
	var multidropdown  = jQuery('select[name="multidropdown_ciclos_especiales"]');
	var fecha_especial = jQuery('#fechas_especiales').val();
	var ciclo_especial = jQuery('select[name="dropdown_ciclos_especiales"] option:selected');
	var value          = ciclo_especial.val()+'|'+fecha_especial;
	var text           = fecha_especial+'-'+ciclo_especial.text();
	var existe = false;
	multidropdown.find('option').each(function(){
        var v  = jQuery(this).val().split('|'); 
        if(v[1]==fecha_especial){
        	existe = true;
        }
    }); 
	if(existe){
		alert('Solo se acepta un ciclo por fecha, gracias');
	}else{
		if((ciclo_especial.val()>0)&&(fecha_especial!='')){
			multidropdown.append(jQuery('<option></option>').attr('value',value).text(text));
			multidropdown.find('option[value="'+value+'"]').attr('selected', true).trigger('liszt:updated');
			remove_option(multidropdown);
			
		}else{
			alert('Es necesario definir una fecha y un ciclo, gracias');
		}
	}
}
function remove_option(name){
	var element = jQuery('select[name="'+name+'"]');
	element.on('change', function(evt, params){		
		if(params.deselected){
			element.find('option[value="'+params.deselected+'"]').remove();
			element.trigger('liszt:updated');
		}
	});
}
function quitar_ciclo(){
	var id = '';
	jQuery('select[name=multiselect_ciclos_agregados] option:selected').each(function(){
		id = jQuery(this).val();
		jQuery(jQuery('#'+id)).remove();
	});
}
function agregar_ciclo(){
	jQuery('select[name=multiselect_ciclos] option:selected').each(function(){
		jQuery('#ciclos_programados').append('<li id ="'+jQuery(this).val()+'" class="onclick_on "><h4><span class=" icon-chevron-right"></span>&nbsp;'+jQuery(this).text()+'</h4></li>');
	});
}
function load_contenido_ciclo(id_ciclo){
	var nombre_ciclo = jQuery('select[name=dropdown_ciclos] option:selected').text();
	var treeview     = 'load_treeview_collapse("treeview_ciclos");';
	var numeric      = "allow_only_numeric()";
	if(id_ciclo!=0){
		jQuery.ajax({
	        type: "POST",
	        url: path()+"nutricion/programacion/ciclo_cantidad_recetas",
	        dataType: 'json',
	        data: { id_ciclo : id_ciclo, nombre_ciclo: nombre_ciclo},
	        beforeSend : function(){
	        	imgLoader('#cantidades_ciclo_receta')
	        },
	        success: function(data){
	        	jQuery('#edicion_cantidades_receta').show('slow');
	        	jQuery('#cantidades_ciclo_receta').html(data+include_script(treeview+numeric));
	        }
	    });
	}else{
		jQuery('#edicion_cantidades_receta').hide();
		jQuery('#cantidades_ciclo_receta').html('');
	}
}
function guardar_configuracion_programacion(){
	
	var btn              = jQuery("button[name='guardar_programacion']");
	var multidropdown_f  = jQuery('select[name="multidropdown_festivos"]');
	var multidropdown_e  = jQuery('select[name="multidropdown_ciclos_especiales"]');
	var fecha_inicio     = jQuery('#fecha_inicio').val(); 
	var fecha_termino    = jQuery('#fecha_termino').val(); 
	var dias_descartados = [];
	var orden_ciclos     = [];
	var festivos         = [];
	var especiales       = [];
	var a                = new Array();
	var params           = new Array();
	var v;

	if((fecha_inicio!='')||(fecha_termino!='')){

		jQuery('input[name="dias_descartados"]:checked').each(function(){
			dias_descartados.push(jQuery(this).val());
		});
		jQuery('#ciclos_programados li').each(function(index, item){
			orden_ciclos.push({
								index     : index,
								ciclo_id   : jQuery(item).attr('id'),
								ciclo_name : jQuery(item).text()
							});
		});

		multidropdown_f.find('option').each(function(){
			festivos.push(jQuery(this).val());			
		});
		multidropdown_e.find('option').each(function(){
			
			v = jQuery(this).val().split('|');
			a = {
					id_ciclo :  v[0],
					fecha    :  v[1]
				}
			especiales.push(a);
		});

		if(orden_ciclos.length===0){
			alert('Es necesario definir almenos un ciclo para comenzar con la programación, gracias');
			return false;
		}else{
			var progress = progress_initialized('registro_loader');
			jQuery('#mensajes').hide();
			params = {	id_sucursal      : jQuery('select[name=lts_sucursales]').val(),
					fecha_inicio     : fecha_inicio,
					fecha_termino    : fecha_termino,
					dias_descartados : dias_descartados,
					orden_ciclos     : orden_ciclos,
					especiales       : especiales,
					festivos         : festivos
				};

			jQuery.ajax({
		        type: "POST",
		        url: path()+"nutricion/programacion/guardar_parametros_programacion",
		        dataType: 'json',
		        data: { params : params },
		        beforeSend : function(){
		        	btn.attr('disabled',true);
		        },
		        success: function(data){
		        	jgrowl(data);
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
	}else{
		alert('Es necesario definir una fecha de inicio y una fecha termino, gracias');
	}
}
function load_treeview_collapse(id){
    jQuery("#"+id).treeview({
        animated: "fast",
        control:"#sidetreecontrol",
        collapsed: true,
        unique: false
      });
}
function guardar_cantidad_receta_ciclo(id_vinculo){
	var cantidad = jQuery('#cantidad_'+id_vinculo).val();
	var progress = progress_initialized('loader_cantidades');
	if(cantidad==''){
		alert('Es necesario que se defina una cantidad');
	}else{
		jQuery.ajax({
	        type: "POST",
	        url: path()+"nutricion/programacion/actuaizar_cantidad_receta",
	        dataType: 'json',
	        data: { id_vinculo: id_vinculo, cantidad : cantidad },
	        beforeSend : function(){
	        },
	        success: function(data){
	        	jgrowl(data);
	        }
	    }).error(function(){
	       		progress.progressTimer('error', {
		            errorText:'ERROR!',
		            onFinish:function(){
		            }
	            });
	        }).done(function(){
		        progress.progressTimer('complete');
		    });
	}
}
function load_calendario(id_sucursal){
	jQuery('#mensajes').html('').hide();
	if(id_sucursal!=0){
		jQuery.ajax({
	        type: "POST",
	        url: path()+"nutricion/programacion/cargar_calendario",
	        dataType: 'json',
	        data: { id_sucursal : id_sucursal },
	        beforeSend : function(){
	        	imgLoader('#loader_calendario');
	        },
	        success: function(data){
	        	imgLoader_clean('#loader_calendario');
	        	jQuery('#contenedor_calendario').html(data.result);
	        	if(data.success == 1){
	        	}else{
	        		jQuery('#mensajes').html(data.msg).show('slow');
	        	}
	        	
	        }
	    });
	}else{
		imgLoader_clean('#loader_calendario');
		jQuery('#contenedor_calendario').html('');
	}
}
function load_calendario_tab(){
	load_calendario(jQuery('select[name=lts_sucursales_calendario] option:selected').val());
}