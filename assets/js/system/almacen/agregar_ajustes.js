jQuery(document).ready(function(){
	var Buscar = jQuery('#search-query');
	Buscar.focus();
	Buscar.keyup(function(e) { if(e.which == 13) buscar(); });
});
function buscar(){	
	var filtro = jQuery('#search-query').val();
	jQuery.ajax({
		type:"POST",
		url: path()+"almacen/agregar_ajustes/listado",
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
	jQuery('#ui-id-2').hide('slow');
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
/*function detalle(id_articulo){
	var params = [];
	params.push('allow_only_numeric();');
	jQuery.ajax({
        type: "POST",
        url: path()+"almacen/agregar_ajustes/detalle",
        dataType: 'json',
        data: {id_articulo : id_articulo},
        success: function(data){
        	params.push('jQuery(".chzn-select").chosen();');
        	jQuery('#a-1').html(data+include_script(params));
        	jQuery('#ui-id-1').show('slow');
        	jQuery('#ui-id-1').click();
        }
    });
}*/
function load_stock(id_articulo){
	id_almacen = jQuery('select[name=lts_almacen] option:selected').val();
	id_pasillo = jQuery('select[name=lts_pasillos] option:selected').val();
	id_gavetas = jQuery('select[name=lts_gavetas] option:selected').val();
	jQuery('#stock_destino').val('');
	jQuery('#stock_um_destino').val('');
	jQuery('#stock_num').val('');
	jQuery('#value_stock_um').empty();
	jQuery('#value_stock').empty();
	jQuery('#stock_num_um').val('');
	  jQuery.ajax({
	        type: "POST",
	        url: path()+"almacen/agregar_ajustes/load_stock",
	        dataType: 'json',
	        data: {id_articulo : id_articulo,id_almacen:id_almacen,id_pasillo:id_pasillo,id_gavetas:id_gavetas},
	        success: function(data){
	         var chosen = 'jQuery(".chzn-select").chosen();';
	          jQuery('#value_stock').html(data['stock']+include_script(chosen));
	          jQuery('#value_stock_um').html(data['stock_um']+' '+data['u_m_cv']);
	          jQuery('#stock_num_um').val(data['stock_um']);
	          jQuery('#stock_num').val(data['stock']);
	        }
	    });
}
function load_gaveta_pas(id_almacen){
	jQuery('#stock_destino').val('');
	jQuery('#stock_num').val('');
	jQuery('#value_stock_um').empty();
	jQuery('#value_stock').empty();
	jQuery('#stock_num_um').val('');
	jQuery('#stock_um_destino').val('');
  jQuery.ajax({
        type: "POST",
        url: path()+"almacen/agregar_ajustes/load_gaveta_pas",
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
	jQuery('#stock_destino').val('');
	jQuery('#stock_um_destino').val('');
	jQuery('#stock_num').val('');
	jQuery('#stock_num_um').val('');
	jQuery('#value_stock').empty();
	jQuery('#value_stock_um').empty();
	id_almacen = jQuery('select[name=lts_almacen] option:selected').val();
	  jQuery.ajax({
	        type: "POST",
	        url: path()+"almacen/agregar_ajustes/load_gaveta",
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
	jQuery('#stock_destino').val('');
	jQuery('#stock_um_destino').val('');
	jQuery('#stock_num').val('');
	jQuery('#value_stock').empty();
	jQuery('#value_stock_um').empty();
	jQuery('#stock_num_um').val('');
	  jQuery.ajax({
	        type: "POST",
	        url: path()+"almacen/agregar_ajustes/load_articulos",
	        dataType: 'json',
	        data: {id_almacen:id_almacen,id_pasillo:id_pasillo,id_gaveta:id_gaveta},
	        success: function(data){
	         var chosen = 'jQuery(".chzn-select").chosen();';
	          jQuery('#lts_ajustes').html(data+include_script(chosen));
	        }
	    });
}
function realiza_calculos(){
	var stock 	     = jQuery('#stock_destino').val();
	var stock_num    = jQuery('#stock_num').val();
	var stock_num_um = jQuery('#stock_num_um').val();
	var cantidad;

	cantidad=stock_num-stock;
	if(cantidad>=0){
		var um = regla_tres(stock_num,stock_num_um,stock)
		jQuery('#stock').val(stock);
		jQuery('#stock_um_destino').val(um);
	}else{
		alert('No puede ser mayor a la cantidad registrada');
		jQuery('#stock').val('');
	}
}
function agregar(){
	var progress = progress_initialized('update_loader');
	jQuery("#mensajes_update").html('').hide('slow');
	jQuery('#mensajes').hide();
	var btn = jQuery("button[name='ajuste_save']");
	//btn.attr('disabled','disabled');
	var stock = jQuery('#stock').val();
	var stock_um_destino = jQuery('#stock_um_destino').val();
	
	var id_articulo = jQuery('select[name=lts_ajustes] option:selected').val();
	//ORIGEN
	var id_almacen  = jQuery('select[name=lts_almacen] option:selected').val();
	var id_pasillo  = jQuery('select[name=lts_pasillos] option:selected').val();
	var id_gavetas  = jQuery('select[name=lts_gavetas] option:selected').val();
	//DESTINO

	var incomplete = values_requeridos();
	jQuery.ajax({
		type:"POST",
		url: path()+"almacen/agregar_ajustes/update",
		dataType: "json",			
		data : {
				incomplete			 :  incomplete,
				stock	 			 :  stock,
				stock_um_destino 	 :  stock_um_destino,
				id_articulo	 		 :  id_articulo,	
				id_almacen	 		 :	id_almacen,
				id_pasillo	 		 :	id_pasillo,
				id_gavetas	 		 :	id_gavetas
		},
		beforeSend : function(){
			btn.attr('disabled',true);
		},
		success : function(data){
			if(data.success == 'true' ){
				jgrowl(data.mensaje);
			}else{
				jQuery("#mensajes_update").html(data.mensaje).show('slow');	
			}
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
function detalle(id_almacen_ajuste){
	jQuery('#ui-id-2').click();
	jQuery.ajax({
        type: "POST",
        url: path()+"almacen/agregar_ajustes/detalle",
        dataType: 'json',
        data: {id_almacen_ajuste : id_almacen_ajuste},
        success: function(data){
         	jQuery('#a-0').html('');
        	jQuery('#a-2').html(data);
        	jQuery('#ui-id-2').show('slow');
        }
    });
}