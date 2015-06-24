function agregar(){
	alert();
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
          	functions.push('calendar_dual_detalle("orden_fecha","entrega_fecha")');
        	jQuery('#a-3').html(data+include_script(functions));
        	jQuery('#ui-id-3').show('slow');
        	jQuery('#ui-id-3').click();
        }
    });
}
function calcula_totla_pagar(){
	var total;
	var subtotal 	= jQuery('#subtotal_final').val();
	var descuento 	= jQuery('#descuento_final').val();
	var impuesto 	= jQuery('#impuesto_final').val();
	total=parseFloat((subtotal-descuento))+parseFloat(impuesto);
	jQuery('#value_total').html(total);
}