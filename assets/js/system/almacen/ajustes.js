jQuery(document).ready(function(){
	var Buscar = jQuery('#search-query');
	Buscar.focus();
	Buscar.keyup(function(e) { if(e.which == 13) buscar(); });
});
function buscar(){	
	var filtro = jQuery('#search-query').val();
	jQuery.ajax({
		type:"POST",
		url: path()+"almacen/ajustes/listado",
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
function load_content(uri, id_content){
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
function detalle(id_articulo){
	var params = [];
	params.push('allow_only_numeric();');
	jQuery.ajax({
        type: "POST",
        url: path()+"almacen/ajustes/detalle",
        dataType: 'json',
        data: {id_articulo : id_articulo},
        success: function(data){
        	params.push('jQuery(".chzn-select").chosen();');
        	jQuery('#a-1').html(data+include_script(params));
        	jQuery('#ui-id-1').show('slow');
        	jQuery('#ui-id-1').click();
        }
    });
}
function load_stock(id_articulo){
	id_almacen = jQuery('select[name=lts_almacen] option:selected').val();
	id_pasillo = jQuery('select[name=lts_pasillos] option:selected').val();
	id_gavetas = jQuery('select[name=lts_gavetas] option:selected').val();
	  jQuery.ajax({
	        type: "POST",
	        url: path()+"almacen/ajustes/load_stock",
	        dataType: 'json',
	        data: {id_articulo : id_articulo,id_almacen:id_almacen,id_pasillo:id_pasillo,id_gavetas:id_gavetas},
	        success: function(data){
	         var chosen = 'jQuery(".chzn-select").chosen();';
	          jQuery('#value_stock').html(data['stock']+include_script(chosen));
	          jQuery('#value_stock_um').html(data['stock_um']+' '+data['u_m_cv']);
	          jQuery('#stock_num').val(data['stock']);
	        }
	    });
}
function load_gaveta_pas(id_almacen){
	  jQuery.ajax({
	        type: "POST",
	        url: path()+"almacen/ajustes/load_gaveta_pas",
	        dataType: 'json',
	        data: {id_almacen : id_almacen},
	        success: function(data){
	         var chosen = 'jQuery(".chzn-select").chosen();';
	          jQuery('#lts_pasillo').html(data['pasillos']+include_script(chosen));
	          jQuery('#lts_gavetas').html(data['gavetas']+include_script(chosen));
	          jQuery('#lts_ajustes').html(data['lts_ajustes']+include_script(chosen));
	        }
	    });
}
function load_gaveta(id_pasillo){
	id_almacen = jQuery('select[name=lts_almacen] option:selected').val();
	  jQuery.ajax({
	        type: "POST",
	        url: path()+"almacen/ajustes/load_gaveta",
	        dataType: 'json',
	        data: {id_pasillo : id_pasillo,id_almacen:id_almacen},
	        success: function(data){
	         var chosen = 'jQuery(".chzn-select").chosen();';
	          jQuery('#lts_gavetas').html(data['lts_gavetas']+include_script(chosen));
	          jQuery('#lts_ajustes').html(data['lts_ajustes']+include_script(chosen));
	        }
	    });
}
function load_articulos(id_gaveta){
	id_almacen = jQuery('select[name=lts_almacen] option:selected').val();
	id_pasillo = jQuery('select[name=lts_pasillos] option:selected').val();
	  jQuery.ajax({
	        type: "POST",
	        url: path()+"almacen/ajustes/load_articulos",
	        dataType: 'json',
	        data: {id_almacen:id_almacen,id_pasillo:id_pasillo,id_gaveta:id_gaveta},
	        success: function(data){
	         var chosen = 'jQuery(".chzn-select").chosen();';
	          jQuery('#lts_ajustes').html(data+include_script(chosen));
	        }
	    });
}
function realiza_calculos(){
	var stock 	  =	jQuery('#stock_destino').val();
	var stock_num = jQuery('#stock_num').val();
	var cantidad;
	
	if(stock_num>stock){
		cantidad=stock_num-stock;	
	}else{
		alert('mayor');
	}
	//alert(cantidad);
}