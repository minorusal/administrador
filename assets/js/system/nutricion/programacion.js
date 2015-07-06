jQuery(document).ready(function(){
	calendar_dual("fecha_inicio", "fecha_termino");
});
function load_programacion(id_sucursal){

	var calendar     = 'calendar_dual("fecha_inicio", "fecha_termino");';
	var dual_select  = 'dual_select();';
	var festivos     = 'calendar("fechas_festivas");';
	var chosen       = 'jQuery(".chzn-select").chosen();';
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
        	jQuery('#configuracion_programacion').html(data+include_script(calendar+dual_select+festivos+chosen));
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
	jQuery('#mensajes').hide();
	var btn              = jQuery("button[name='guardar_programacion']");
	var fecha_inicio     = jQuery('#fecha_inicio').val(); 
	var fecha_termino    = jQuery('#fecha_termino').val(); 
	var dias_descartados = [];
	var orden_ciclos     = [];
	var params           = new Array();

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

		if(orden_ciclos.length===0){
			alert('Es necesario definir almenos un ciclo para comenzar con la programación, gracias');
			return false;
		}
		params = {	id_sucursal      : jQuery('select[name=lts_sucursales]').val(),
					fecha_inicio     : fecha_inicio,
					fecha_termino    : fecha_termino,
					dias_descartados : dias_descartados,
					orden_ciclos     : orden_ciclos
				};

		jQuery.ajax({
	        type: "POST",
	        url: path()+"nutricion/programacion/guardar_parametros_programacion",
	        dataType: 'json',
	        data: { params : params },
	        beforeSend : function(){
	        	imgLoader('#registro_loader');
	        	btn.attr('disabled',true);
	        },
	        success: function(data){
	        	imgLoader_clean('#registro_loader');
	        	btn.attr('disabled',false);
	        	jQuery('#mensajes').html(data).show('slow');
	        }
	    });
		//
		//alert(dump_var(params_ciclo));
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
	if(cantidad==''){
		alert('Es necesario que se defina una cantidad');
	}else{
		jQuery.ajax({
	        type: "POST",
	        url: path()+"nutricion/programacion/actuaizar_cantidad_receta",
	        dataType: 'json',
	        data: { id_vinculo: id_vinculo, cantidad : cantidad },
	        beforeSend : function(){
	        	imgLoader('#loader_cantidades');
	        },
	        success: function(data){
	        	jQuery('#loader_cantidades').html('');
	        }
	    });
	}
}
function load_calendario(id_sucursal){
	jQuery('#mensajes').html('');
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
	        	jQuery('#form_calendario').append("<div id='calendar'></div>");
	        	if(data.success == 1){
	        		config_calendar('calendar', data.json);
	        	}else{
	        		jQuery('#mensajes').html(data.msg);
	        	}
	        	
	        }
	    });
	}else{
	}
}
//config_calendar