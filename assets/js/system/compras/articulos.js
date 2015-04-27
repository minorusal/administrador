jQuery(document).ready(function(){
	jQuery("button[name='save_articulo']").click(function(){
		jQuery('#mensajes').hide();
		var btn      = jQuery("button[name='save_articulo']");
		btn.attr('disabled','disabled');
		var btn_text = btn.html();
		
		var incomplete   = values_requeridos();
        var articulo     = jQuery('#articulo').val();
        var clave_corta  = jQuery('#clave_corta').val();
        var descripcion  = jQuery('#descripcion').val();
       
        var presentacion = jQuery("select[name='lts_presentaciones'] option:selected").val();
        var linea        = jQuery("select[name='lts_lineas'] option:selected").val();
        var um           = jQuery("select[name='lts_um'] option:selected").val();
        var marca        = jQuery("select[name='lts_marcas'] option:selected").val();


		jQuery.ajax({
			type:"POST",
			url: path()+"compras/articulos/agregar_articulo",
			dataType: "json",
			data: {incomplet :incomplete, articulo:articulo, clave_corta:clave_corta, descripcion:descripcion,presentacion:presentacion,linea:linea,um:um,marca:marca },
			beforeSend : function(){

				jQuery("#registro_loader").html('<br><img src="'+path()+'assets/images/loaders/loader.gif"/>');
			},
			success : function(msg){
				//btn.html(btn_text);
				btn.removeAttr('disabled');

			    jQuery("#mensajes").html(msg).show('slow');
				jQuery("#registro_loader").html('');
			}
		})
	});
})