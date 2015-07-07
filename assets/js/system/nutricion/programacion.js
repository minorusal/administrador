jQuery(document).ready(function(){
	calendar_dual("fecha_inicio", "fecha_termino");
});
function load_programacion(id_sucursal){

	var calendar          = 'calendar_dual("fecha_inicio", "fecha_termino");';
	var dual_select       = 'dual_select();';
	var input_calendar    = 'calendar("input_calendar");';
	var chosen            = 'jQuery(".chzn-select").chosen();';
	
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
        	jQuery('#configuracion_programacion').html(data+include_script(calendar+dual_select+input_calendar+chosen));
        }
    });
}
function agregar_ciclo_especial(){
	var multidropdown  = jQuery('select[name="multidropdown_ciclos_especiales"]');
	var fecha_especial = jQuery('#fechas_especiales').val();
	var ciclo_especial = jQuery('select[name="dropdown_ciclos_especiales"] option:selected');
	var value          = ciclo_especial.text()+'|'+fecha_especial+'|'+ciclo_especial.text();
	var text           = fecha_especial+'-'+ciclo_especial.text();
	var existe = false
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
			jQuery('select[name="multidropdown_ciclos_especiales"] option[value="'+value+'"]').attr('selected', true).trigger('liszt:updated');
			multidropdown.on('change', function(evt, params){
				if(params.deselected){
					jQuery('select[name="multidropdown_ciclos_especiales"] option[value="'+params.deselected+'"]').remove();
					multidropdown.trigger('liszt:updated');
				}
			});
		}else{
			alert('Es necesario definir una fecha y un ciclo, gracias');
		}
	}
	
	
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
		jQuery('#contenedor_calendario').html('');
	}
}