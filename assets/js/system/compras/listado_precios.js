jQuery(document).ready(function(){
	jQuery('#search-query').focus();
	jQuery('#search-query').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){  
			buscar();
		} 
	});
})
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
           		jQuery('#a-'+id_content).html(data);
           		var chosen  = 'jQuery(".chzn-select").chosen();';
           		jQuery('#a-'+id_content).html(data+include_script(chosen));
           }
        }
    });
}
function buscar(){
  var filtro = jQuery('#search-query').val();
  jQuery.ajax({
    type:"POST",
    url: path()+"compras/listado_precios/listado",
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
function agregar(){
  var btn          = jQuery("button[name='listado_precios_save']");
  btn.attr('disabled','disabled');
  jQuery('#mensajes').hide();
  var incomplete   = values_requeridos();
  var cant_presentacion_embalaje  = jQuery('#cantidad_presentacion_embalaje').val();
  var cant_um_presentacion        = jQuery('#cantidad_um_presentacion').val();
  var precio_proveedor            = jQuery('#precio_proveedor').val();
  var impuesto_aplica             = jQuery('#impuesto_aplica').val();
  var impuesto_porcentaje         = jQuery('#impuesto_porcentaje').val();
  var id_articulo                 = jQuery("select[name='lts_articulos'] option:selected").val();
  var id_proveedor                = jQuery("select[name='lts_proveedores'] option:selected").val();
  var id_marca                    = jQuery("select[name='lts_marcas'] option:selected").val();
  var id_presentacion             = jQuery("select[name='lts_presentaciones'] option:selected").val();
  var id_embalaje                 = jQuery("select[name='lts_embalaje'] option:selected").val();
  alert(incomplete);
 /* jQuery.ajax({
    type:"POST",
    url: path()+"compras/listado_precios/insert",
    dataType: "json",
    data: {
        incomplete :incomplete,
        cant_presentacion_embalaje:cant_presentacion_embalaje,
        cant_um_presentacion : cant_um_presentacion,
        precio_proveedor : precio_proveedor,
        impuesto_aplica : impuesto_aplica,
        impuesto_porcentaje : impuesto_porcentaje,
        id_articulo : id_articulo,
        id_proveedor : id_proveedor,
        id_marca : id_marca,
        id_presentacion : id_presentacion,
        id_embalaje : id_embalaje
    },
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
  });*/
}
function detalle(id_compras_articulo_precio){
  jQuery('#ui-id-2').click();
  jQuery.ajax({
        type: "POST",
        url: path()+"compras/listado_precios/detalle",
        dataType: 'json',
        data: {id_compras_articulo_precio : id_compras_articulo_precio},
        success: function(data){
          var chosen = 'jQuery(".chzn-select").chosen();';
          jQuery('#a-0').html('');
          jQuery('#a-2').html(data);
          jQuery('#a-2').html(data+include_script(chosen));
          jQuery('#ui-id-2').show('slow');
        }
    });
}
function update(){
  jQuery('#mensajes_update').hide();
  var btn          = jQuery("button[name='update']");
  btn.attr('disabled','disabled');
  var btn_text     = btn.html();  
  var incomplete   = values_requeridos();
  alert(incomplete);
  var id_compras_articulo_precios  = jQuery('#id_compras_articulo_precios').val();
  var cant_presentacion_embalaje  = jQuery('#cantidad_presentacion_embalaje').val();
  var cant_um_presentacion        = jQuery('#cantidad_um_presentacion').val();
  var precio_proveedor            = jQuery('#precio_proveedor').val();
  var impuesto_aplica             = jQuery('#impuesto_aplica').val();
  var impuesto_porcentaje         = jQuery('#impuesto_porcentaje').val();
  var id_articulo                 = jQuery("select[name='lts_articulos'] option:selected").val();
  var id_proveedor                = jQuery("select[name='lts_proveedores'] option:selected").val();
  var id_marca                    = jQuery("select[name='lts_marcas'] option:selected").val();
  var id_presentacion             = jQuery("select[name='lts_presentaciones'] option:selected").val();
  var id_embalaje                 = jQuery("select[name='lts_embalaje'] option:selected").val();
  jQuery.ajax({
    type:"POST",
    url: path()+"compras/listado_precios/update",
    dataType: "json",
    data: {
        incomplete                 : incomplete,
        id_compras_articulo_precios : id_compras_articulo_precios,
        cant_presentacion_embalaje : cant_presentacion_embalaje,
        cant_um_presentacion       : cant_um_presentacion,
        precio_proveedor           : precio_proveedor,
        impuesto_aplica            : impuesto_aplica,
        impuesto_porcentaje        : impuesto_porcentaje,
        id_articulo                : id_articulo,
        id_proveedor               : id_proveedor,
        id_marca                   : id_marca,
        id_presentacion            : id_presentacion,
        id_embalaje                : id_embalaje},
    beforeSend : function(){
      jQuery("#update_loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
    },
    success : function(data){
      btn.removeAttr('disabled');   
      var data = data.split('|');
        if(data[0]==1){
        }
      jQuery("#update_loader").html('');
          jQuery("#mensajes_update").html(data[1]).show('slow');
    }
  });
}