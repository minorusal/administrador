jQuery(document).ready(function(){
	var Buscar = jQuery('#search-query');
	Buscar.focus();
	Buscar.keyup(function(e) { if(e.which == 13) buscar(); });
});

function buscar(){
	var filtro = jQuery('#search-query').val();
	jQuery.ajax({
		type:"POST",
		url: path()+"almacen/entradas_recepcion/listado",
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
          	 	functions.push('calendar_actual("fecha_factura")');
          	 	jQuery('#a-'+id_content).html(data+include_script(functions));

           }
        }
    });
}
function agregar(){
	var btn          = jQuery("button[name='save_entreda']");
	btn.attr('disabled','disabled');
	jQuery('#mensajes').hide();
	var incomplete   	 = values_requeridos();
    var no_orden      	 = jQuery('#no_orden').val();
    var no_factura  	 = jQuery('#no_factura').val();
    var fecha_factura  	 = jQuery('#fecha_factura').val();
    var fecha_recepcion  = jQuery('#fecha_recepcion').val();
    
	jQuery.ajax({
		type:"POST",
		url: path()+"almacen/entradas_recepcion/get_data_orden",
		dataType: "json",
		data: {incomplete :incomplete, no_orden:no_orden, no_factura:no_factura, fecha_factura:fecha_factura, fecha_recepcion:fecha_recepcion },
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
function articulos(id_compras_orden){	
	var functions=[];
	jQuery.ajax({
        type: "POST",
        url: path()+"almacen/entradas_recepcion/articulos",
        dataType: 'json',
        data: {id_compras_orden : id_compras_orden},
        success: function(data){
        	jQuery('#a-0').html('');
        	functions.push('jQuery(".chzn-select").chosen();');
          	functions.push('calendar_no_futuras("fecha_factura")');
        	jQuery('#a-3').html(data+include_script(functions));
        	jQuery('#ui-id-3').show('slow');
        	jQuery('#ui-id-3').click();
        }
    });
}
function calculos(id){
	var moneda = jQuery('#moneda').val();
	var subtotal=0;
	var descuento=0;
	var impuesto=0;
	var total =0;
	var cont=0;
	var valor_2=[];
	var valor_3=[];
	var valor_4=[];
	var varl;
	var result;
	var result_2;
	var result_3;
	var functions = [];
	//
	var cantidad = jQuery('#cantidad_'+id).val();
	var caducidad_val  	= jQuery('#caducidad_val_'+id).val();
	var lote_val		= jQuery('#lote_val_'+id).val();
	var cantidad_lote			= jQuery('#cantidad_lote_'+id).val();
	var proveedor		= jQuery('#proveedor_'+id).val();
	var articulo		= jQuery('#articulo_'+id).val();
	var presentacion    = jQuery('#presentacion_'+id).val();
	jQuery('input[name="aceptar[]"]:checked').each(function() {
		valor_2.push(parseFloat(jQuery('#descuento_'+jQuery(this).val()).val()));
		valor_3.push(parseFloat(jQuery('#costo_x_cantidad_hidden'+jQuery(this).val()).val()));
		valor_4.push(parseFloat(jQuery('#valor_hidden_impuesto_'+jQuery(this).val()).val()));
	});
	jQuery(valor_3).each(function(index,value){
		result=parseFloat(value);
		subtotal= subtotal+result;
	});
	//CALCULA EL DESCUENTO
	jQuery(valor_2).each(function(index,value){
		result_2=parseFloat((parseFloat(valor_3[index])*parseFloat(valor_2[index]))/100);
		descuento= descuento+result_2;
	});
	//CALCULA IMPUESTO
	jQuery(valor_4).each(function(index,value){
		result_3=parseFloat(value);
		impuesto= parseFloat(impuesto)+parseFloat(result_3);
	});
	//CALCULA SUBTOTAL
	total=(subtotal-descuento)+impuesto;
	jQuery('#subtotal').val(subtotal);
	jQuery('#value_subtotal').html('<strong>'+ moneda+' '+ numeral(subtotal).format('0,0.00') +'</strong>');
	jQuery('#descuento_total').val(descuento);
	jQuery('#value_descuento').html('<strong> - '+ moneda+' '+numeral(descuento).format('0,0.00')+'</strong>');
	jQuery('#impuesto_total').val(impuesto);
	jQuery('#value_impuesto').html('<strong>'+ moneda+' '+ numeral(impuesto).format('0,0.00')+'</strong>');
	jQuery('#total_data').val(total);
	jQuery('#value_total').html('<strong>'+ moneda+' '+ numeral(total).format('0,0.00')+'</strong>');
	jQuery('input[name="aceptar[]"]').each(function() {
		if(jQuery(this).is(':checked')){
		}else{cont++;}
	});
	if(cont>0){
		jQuery('#comentario').addClass('requerido');
	}else{
		jQuery('#comentario').removeClass('requerido');
	}
	if(jQuery('#listado_'+id).is(':checked')){
		var objData = formData('#formulario');
	  	objData['incomplete'] = values_requeridos();
		jQuery.ajax({
			type:"POST",
			url: path()+"almacen/entradas_recepcion/modal_lote_caducidad",
			dataType: "json",
			data : {id:id,
					cantidad:cantidad,
					caducidad_val : caducidad_val,
					lote_val : lote_val,
					cantidad_lote : cantidad_lote,
					proveedor  : proveedor,
					articulo : articulo,
					presentacion : presentacion},
			beforeSend : function(){
				jQuery("#registro_loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
			},
			success : function(data){
				functions.push('calendar("caducidad")');
				jQuery('#modal').html(data+include_script(functions));
				jQuery('#lote').modal();
			}
		});
	}
	jQuery('#listado_'+id).prop("checked", "");
}
function validar_cantidad(id){
	var cantidad_lote 	  = jQuery('#cantidad_lote_modal').val();
	var cantidad_resta    = jQuery('#cantidad_resta_'+id).val();
	var cantidad_total;
	cantidad_total=cantidad_resta-cantidad_lote;
	jQuery('#cantidad_resta_'+id).val(cantidad_total);
}
function aceptar_lote(id){	
	validar_cantidad(id);
	var lote 	  = jQuery('#lotemodal').val();
	var caducidad = jQuery('#caducidad').val();
	var cantidad_lote 	= jQuery('#cantidad_lote_modal').val();
	var cantidad_resta  = jQuery('#cantidad_resta_'+id).val();
	var cantidad_val    = jQuery('#cantidad_'+id).val();
	var cantidad_total;
	var tds=7;
	var nuevaFila;
	var cantidad;
	var hiddens;;
	var lote_hidden;
	var caducidad_hidden;
	var cantidad_hidden;
	var cont=1;
	var contador=jQuery('#candidad_modal_1').val();
	var td_valor=new Array(
					'',
					jQuery('#proveedor_'+id).val(),
					jQuery('#articulo_'+id).val(),
					jQuery('#presentacion_'+id).val(),
					lote,
					cantidad_lote,
					caducidad,
					''
					);
	if(cantidad_resta<=0){
		if(cantidad_resta>=0){
			jQuery('#listado_'+id).attr("disabled", true);
			jQuery('#listado_'+id).prop("checked", "checked");
		}else{
			jQuery('#cantidad_resta_'+id).val(cantidad_val);
			alert('No puede ser mayor a la cantidad');
			return false;
		}
	}else{
		jQuery('#listado_'+id).prop("checked", "");
	}
	//Comienzan operaciones
	for(var i=0;i<tds;i++){
		if(i==4){
			if(typeof(contador) == 'undefined'){
				lote_hidden='<input type="hidden" id="lote_modal_'+cont+'" name="lote_modal[]"  data-campo="lote_modal['+id+'-'+cont+']" value="'+td_valor[i]+'">';
			}else{
				jQuery('input[name="candidad_modal[]"]').each(function() {
					cont++;
					lote_hidden='<input type="hidden" id="lote_modal_'+cont+'" name="lote_modal[]"  data-campo="lote_modal['+id+'-'+cont+']" value="'+td_valor[i]+'">';
				});
			}
		}else{
			lote_hidden='';
		}
		 if(i==5){
			if(typeof(contador) == 'undefined'){
				cantidad_hidden='<input type="hidden" id="candidad_modal_'+cont+'" name="candidad_modal[]"  data-campo="candidad_modal['+id+'-'+cont+']" value="'+td_valor[i]+'">';
			}else{
				jQuery('input[name="candidad_modal[]"]').each(function() {
					cont++;
					cantidad_hidden='<input type="hidden" id="candidad_modal_'+cont+'" name="candidad_modal[]"  data-campo="candidad_modal['+id+'-'+cont+']" value="'+td_valor[i]+'">';
				});
			}
		}else{
			cantidad_hidden='';
		}
		 if(i==6){
			if(typeof(contador) == 'undefined'){
				caducidad_hidden='<input type="hidden" id="caducidad_modal_'+cont+'" name="caducidad_modal[]"  data-campo="caducidad_modal['+id+'-'+cont+']" value="'+td_valor[i]+'">';
			}else{
				jQuery('input[name="candidad_modal[]"]').each(function() {
					cont++;
					caducidad_hidden='<input type="hidden" id="caducidad_modal_'+cont+'" name="caducidad_modal[]"  data-campo="caducidad_modal['+id+'-'+cont+']" value="'+td_valor[i]+'">';
				});
			}
		}else{
			caducidad_hidden='';
		}
		hiddens=lote_hidden+caducidad_hidden+cantidad_hidden;
		nuevaFila+='<td>'+hiddens+td_valor[i]+'</td>';
    }
	jQuery('#'+id).after('<tr id="muestra_lote_'+id+'" style="background-color: #BDBDBD">'+nuevaFila+'</tr>');

	jQuery('#lote_val_'+id).val(lote);
	jQuery('#caducidad_val_'+id).val(caducidad);
	jQuery('#cantidad_lote_'+id).val(cantidad_lote);
	jQuery('#lote').modal('toggle');
	//jQuery('#listado_'+id).prop("checked", "checked");
}
function calcula_totla_pagar(){
	var total;
	var subtotal 	= jQuery('#subtotal_final').val();
	var descuento 	= jQuery('#descuento_final').val();
	var impuesto 	= jQuery('#impuesto_final').val();
	total=parseFloat((subtotal-descuento))+parseFloat(impuesto);
	jQuery('#value_total').html(total);
}
function recibir_orden(){
	jQuery('#mensajes').hide();	
	// Obtiene campos en formulario
  	var objData = formData('#formulario');
  	objData['incomplete'] = values_requeridos();
	jQuery.ajax({
		type:"POST",
		url: path()+"almacen/entradas_recepcion/insert",
		dataType: "json",
		data : objData,
		beforeSend : function(){
			jQuery("#registro_loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
		},
		success : function(data){
			if(data.id==1){
				clean_formulario();
			}
			jQuery("#registro_loader").html('');
		    jQuery("#mensajes").html(data.contenido).show('slow');
		    jQuery('input[name="aceptar[]"]:checked').attr('checked',false);
			jQuery('#recibir').hide();	
			jQuery('#volver').hide();	
		}
	});
}
function volver_lote(id){
	jQuery('#lote_val_'+id).val('');
	jQuery('#caducidad_val_'+id).val('');
	jQuery('#cantidad_lote_'+id).val('');
	jQuery('#lote').modal('toggle');
	jQuery('#listado_'+id).prop("checked", "");
}
