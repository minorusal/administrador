jQuery(document).ready(function(){
	jQuery('#search-query').focus();
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
			data: {incomplete :incomplete, articulo:articulo, clave_corta:clave_corta, descripcion:descripcion,presentacion:presentacion,linea:linea,um:um,marca:marca },
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
	});
	jQuery("button[name='reset']").click(function(){
		clean_formulario();
	});

	jQuery('#search-query').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){  
			buscar_articulo();
		} 
	});
})

function buscar_articulo(){
	var filtro = jQuery('#search-query').val();
	jQuery.ajax({
		type:"POST",
		url: path()+"compras/articulos/listado_articulos",
		dataType: "json",
		data: {filtro : filtro},
		beforeSend : function(){
			jQuery("#loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
		},
		success : function(data){
			var funcion = 'buscar_articulo';
        	jQuery("#loader").html('');
        	jQuery('#a-1').html(data+input_keypress('search-query', funcion));
			jQuery('#search-query').val(filtro).focus();
			tool_tips();
			
		}
	})
}

function load_content(uri, id_content){
	var filtro = jQuery('#search-query').val();
    jQuery.ajax({
        type: "POST",
        url: uri,
        dataType: 'json',
        data: {filtro : filtro, tabs:1},
        success: function(data){
           if(id_content==1){
           		var funcion = 'buscar_articulo';
           		jQuery('#a-1').html(data+input_keypress('search-query', funcion));
           		jQuery('#search-query').val(filtro).focus();
           		tool_tips();
           }else{
           		jQuery('#a-'+id_content).html(data);
           }
        }
    });
}
