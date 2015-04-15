function agregar_articulo(){
	var articulo = jQuery("#articulo").val();
	var clave_corta = jQuery("#clave_corta").val();
	var descripcion = jQuery("#descripcion").text();
	if(values_requeridos()==""){
		jQuery.ajax({
			type:"POST",
			url: path()+"inventario/catalogos/agregar_articulo",
			dataType: "json",
			data: {articulo : articulo, clave_corta:clave_corta, descripcion:descripcion},
			beforeSend : function(){
				jQuery.prompt('<center><strong>Aplicando registro</strong><br><img src="'+path()+'assets/images/loaders/loader27.gif"/></center>');
				jQuery(".jqiclose ").html('');
			},
			success : function(result){
				if(result==1){
					var msg = alertas_tpl('success' , '<strong>Done!</strong><br>El registro de dio de alta correctamente' ,true);
				}else{
					var msg = result;
				}
				jQuery.ajax({
			        type: "POST",
			        url: path()+"inventario/catalogos/agregar_articulo",
			        dataType: 'json',
			        data: {tabs:1},
			        success: function(view){
			           jQuery('#a-0').html(view);
			           jQuery("#mensajes").html(msg).show('slow');
			        }
			    });
				jQuery.prompt.close();
				
			}
		});
	}else{
		jQuery("#mensajes").html(alertas_tpl('error' , ' <strong>Atencion!</strong><br>Los campos marcado con (*) son obligatorios, gracias' ,true)).show('slow');
		
	}
}