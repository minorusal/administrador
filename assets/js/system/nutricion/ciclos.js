jQuery(document).ready(function(){
	jQuery('#search-query').focus();
	jQuery('#search-query').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){  
			buscar();
		} 
	});
});

function load_content(uri, id_content){
	jQuery('#ui-id-2').hide('slow');
	var filtro = jQuery('#search-query').val();
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
           		var chosen  = 'jQuery(".chzn-select").chosen();';
           		//jQuery('#a-'+id_content).html(data);
           		jQuery('#a-'+id_content).html(data+include_script(chosen));
           		jQuery('#contenido_ciclos').hide();
           }
        }
    });
}

function load_ciclos(id_sucursal){
  alert(id_aucursal);
	jQuery.ajax({
        type: "POST",
        url: path()+"nutricion/ciclos/cargar_ciclos",
        dataType: 'json',
        data: {id_sucursal:id_sucursal},
        beforeSend : function(){
        	jQuery("#loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
        },
        success: function(data){
        	/*var chosen  = 'jQuery(".chzn-select").chosen();';
          jQuery('#a-'+id_content).html(data+include_script(chosen));*/
        	jQuery('#ciclo').html(data);
        	jQuery('#ciclo').show('slow');
        }
    });
}

function buscar(){
	var filtro = jQuery('#search-query').val();
	jQuery.ajax({
		type:"POST",
		url: path()+"nutricion/ciclos/listado",
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
	});
}