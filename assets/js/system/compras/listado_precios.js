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
  var impuesto_aplica;
  var id_embalaje;
  if( jQuery('#impuesto_aplica').is(':checked') ){
    impuesto_aplica = 1;
  }else{
    impuesto_aplica = 0;    
  }

  if(!jQuery('#embalaje_aplica').is(':checked') ){
    id_embalaje = 1;
  }
  else{
    id_embalaje = jQuery("select[name='lts_embalaje'] option:selected").val();
  }
  var id_articulo               = jQuery("select[name='lts_articulos'] option:selected").val();
  var id_proveedor              = jQuery("select[name='lts_proveedores'] option:selected").val();
  var id_marca                  = jQuery("select[name='lts_marcas'] option:selected").val();
  var id_presentacion           = jQuery("select[name='lts_presentaciones'] option:selected").val();
  var impuesto_porcentaje       = jQuery("select[name='lts_impuesto'] option:selected").val();
  var presentacion_x_embalaje   = jQuery('#presentacion_x_embalaje').val();
  var um_x_embalaje             = jQuery('#um_x_embalaje').val();
  var um_x_presentacion         = jQuery('#um_x_presentacion').val();
  var costo_sin_impuesto        = jQuery('#costo_sin_impuesto').val();
  var peso_unitario             = jQuery('#peso_unitario').val();
  var costo_unitario            = jQuery('#costo_unitario').val();
  var costo_x_um                = jQuery('#costo_x_um').val();

  jQuery.ajax({
    type:"POST",
    url: path()+"compras/listado_precios/insert",
    dataType: "json",
    data: {
        incomplete :incomplete,
        presentacion_x_embalaje:presentacion_x_embalaje,
        um_x_embalaje: um_x_embalaje,
        um_x_presentacion : um_x_presentacion,
        costo_sin_impuesto : costo_sin_impuesto,
        impuesto_aplica : impuesto_aplica,
        impuesto_porcentaje : impuesto_porcentaje,
        id_articulo : id_articulo,
        id_proveedor : id_proveedor,
        id_marca : id_marca,
        id_presentacion : id_presentacion,
        id_embalaje : id_embalaje,
        peso_unitario  : peso_unitario,
        costo_unitario : costo_unitario,
        costo_x_um   :costo_x_um
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
  });
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
  var impuesto_aplica;
  if( jQuery('#impuesto_aplica').is(':checked') ){
    impuesto_aplica = 1;
  }else{
    impuesto_aplica = 0;    
  }
  var id_compras_articulo_precios  = jQuery('#id_compras_articulo_precios').val();
  var cant_presentacion_embalaje  = jQuery('#cantidad_presentacion_embalaje').val();
  var cant_um_presentacion        = jQuery('#cantidad_um_presentacion').val();
  var precio_proveedor            = jQuery('#precio_proveedor').val();
  var id_articulo                 = jQuery("select[name='lts_articulos'] option:selected").val();
  var id_proveedor                = jQuery("select[name='lts_proveedores'] option:selected").val();
  var id_marca                    = jQuery("select[name='lts_marcas'] option:selected").val();
  var id_presentacion             = jQuery("select[name='lts_presentaciones'] option:selected").val();
  var id_embalaje                 = jQuery("select[name='lts_embalaje'] option:selected").val();
  var impuesto_porcentaje         = jQuery("select[name='lts_impuesto'] option:selected").val();
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
function oculta_impuesto(){
  if(jQuery('#impuesto_aplica').is(':checked') ){
    jQuery('#impuesto').show('slow');
    jQuery('[name=lts_impuesto]').addClass('requerido');
    calcular_precio_final();
  }else{
    var costo_final = jQuery('#costo_sin_impuesto').val();
    jQuery('#costo_final').val(costo_final);
    jQuery('#impuesto').hide('slow');
    jQuery('[name=lts_impuesto]').removeClass('requerido');
  }
}
function oculta_embalaje(){
  if(jQuery('#embalaje_aplica').is(':checked') ){
    jQuery('#presentacion_x_embalaje').attr('readonly', false);
    jQuery('#presentacion_x_embalaje').val("")
    jQuery('#embajale').show('slow');
    jQuery('[name=lts_embalaje]').addClass('requerido');
    if(jQuery("select[name='lts_embalaje'] option:selected").val()>0){
      jQuery('#embalaje_cl').show('slow');
      jQuery('#pre_um2').show('slow');
      jQuery('#signo2').show('slow');
    }
  }else{
    jQuery('#presentacion_x_embalaje').attr('readonly',true);
    jQuery('#presentacion_x_embalaje').val(1)
    jQuery('#embajale').hide('slow');
    jQuery('[name=lts_embalaje]').removeClass('requerido');
    jQuery('#embalaje_cl').hide('slow');
    jQuery('#pre_um2').hide('slow');
    jQuery('#signo2').hide('slow');
  }
}
function validar_um(id_opcion){
  if(id_opcion==1){    
    jQuery('#um_x_embalaje').attr('readonly', false);
    jQuery('#um_x_embalaje').addClass('requerido');
    jQuery('#um_x_presentacion').attr('readonly', true);
    jQuery('#um_x_presentacion').removeClass('requerido');
  }else{
    jQuery('#um_x_presentacion').attr('readonly', false);
    jQuery('#um_x_presentacion').addClass('requerido');

    jQuery('#um_x_embalaje').attr('readonly', true);
    jQuery('#um_x_embalaje').removeClass('requerido');
  }
}
function load_pre_emb(id_presentacion){
  jQuery.ajax({
        type: "POST",
        url: path()+"compras/listado_precios/load_presentacion_em",
        dataType: 'json',
        data: {id_presentacion : id_presentacion},
        success: function(data){
          jQuery('#pre_em').show('slow');
          jQuery('#pre_em').html(data);
          jQuery('#pre_em2').show('slow');
          jQuery('#pre_em2').html(data);
          jQuery('#signo').show('slow');
        }
    });
}
function load_pre_um(id_articulo){
  jQuery.ajax({
        type: "POST",
        url: path()+"compras/listado_precios/load_presentacion_um",
        dataType: 'json',
        data: {id_articulo : id_articulo},
        success: function(data){
          jQuery('#pre_um').show('slow');
          jQuery('#pre_um').html(data);
          jQuery('#pre_um2').show('slow');
          jQuery('#pre_um2').html(data);
        }
    });
}
function load_emb(id_embalaje){
  jQuery.ajax({
        type: "POST",
        url: path()+"compras/listado_precios/load_embalaje_cl",
        dataType: 'json',
        data: {id_embalaje : id_embalaje},
        success: function(data){
          jQuery('#embalaje_cl').show('slow');
          jQuery('#embalaje_cl').html(data);
          jQuery('#signo2').show('slow');
          jQuery('#pre_um2').show('slow');
        }
    });
}
function clean_campos(){
  jQuery('#um_x_presentacion').val('');
  jQuery('#um_x_embalaje').val('');
}
function calcula_um_prsentacion(){
  if(jQuery('#peso_unitario').val()!=""){
    calula_costos();
  }
  var presentacion_x_embalaje = jQuery('#presentacion_x_embalaje').val();
  var um_x_embalaje = jQuery('#um_x_embalaje').val();
  var um_x_presentacion= um_x_embalaje/presentacion_x_embalaje;
  jQuery('#um_x_presentacion').val(um_x_presentacion.toFixed(2));
}
function calcula_um_embalaje(){
  if(jQuery('#peso_unitario').val()!=""){
    calula_costos();
  }
  var presentacion_x_embalaje = jQuery('#presentacion_x_embalaje').val();
  var um_x_presentacion = jQuery('#um_x_presentacion').val();
  var um_x_embalaje= um_x_presentacion*presentacion_x_embalaje;
  jQuery('#um_x_embalaje').val(um_x_embalaje);
}
function calula_costos(){
  var presentacion_x_embalaje = jQuery('#presentacion_x_embalaje').val();
  var um_x_embalaje = jQuery('#um_x_embalaje').val();
  var costo_sin_impuesto = jQuery('#costo_sin_impuesto').val();
  var peso_unitario;
  var costo_unitario; 
  var costo_x_um;
  var costo_final = jQuery('#costo_sin_impuesto').val();
  //CALCULOS
  peso_unitario  = um_x_embalaje/presentacion_x_embalaje;
  costo_unitario = costo_sin_impuesto/presentacion_x_embalaje
  costo_x_um     = costo_sin_impuesto/um_x_embalaje
  jQuery('#peso_unitario').val(peso_unitario.toFixed(3));
  jQuery('#costo_unitario').val(costo_unitario.toFixed(3));
  jQuery('#costo_x_um').val(costo_x_um.toFixed(3));
  jQuery('#costo_final').val(costo_final);
  if(jQuery("select[name='lts_impuesto'] option:selected").val()>0){
    calcular_precio_final();
  }
}
function calcular_precio_final(){
  var impuesto  = jQuery("select[name='lts_impuesto'] option:selected").text();
  var costo_sin_impuesto = jQuery('#costo_sin_impuesto').val();
  var valor=impuesto.split("-");
  var desglose_impuesto = (costo_sin_impuesto*valor[1])/100;
  var resultado = parseFloat(costo_sin_impuesto)+parseFloat(desglose_impuesto);
  if(costo_sin_impuesto==""){
      costo_final="";
  }
  else{
    costo_final= resultado.toFixed(3)
  }
  jQuery('#costo_final').val(costo_final);
}