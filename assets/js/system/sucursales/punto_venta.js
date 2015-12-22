jQuery(document).ready(function(){
  jQuery('#ui-id-2').hide('slow');
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
          jQuery('#form_pventa_cliente').remove();
          jQuery('#form_pventa_edit').remove();
          jQuery('#form_pventa_save').remove();
           if(id_content==1){
           		var funcion = 'buscar';
           		jQuery('#a-1').html(data+input_keypress('search-query', funcion));
           		jQuery('#search-query').val(filtro).focus();
           		tool_tips();
           }else{
           		//jQuery('#a-'+id_content).html(data);
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
    url: path()+"sucursales/punto_venta/listado",
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

function detalle(id_punto_venta){
  jQuery('#ui-id-2').click();
  jQuery.ajax({
        type: "POST",
        url: path()+"sucursales/punto_venta/detalle",
        dataType: 'json',
        data: {id_punto_venta : id_punto_venta},
        success: function(data){
          var chosen  = 'jQuery(".chzn-select").chosen();';
          jQuery('#a-2').html(data+include_script(chosen));
          jQuery('#ui-id-2').show('slow');
        }
    });
}

function load_almacenes(id_sucursal){
  jQuery.ajax({
        type: "POST",
        url: path()+"sucursales/punto_venta/load_almacenes",
        dataType: 'json',
        data: {id_sucursal:id_sucursal},
        beforeSend : function(){
          
        },
        success: function(data){
          var chosen  = 'jQuery(".chzn-select").chosen();';
          jQuery('#list_almacenes').html(data+include_script(chosen));
          jQuery('#list_almacenes').show('slow');
        }
    });
}

function load_gavetas(id_almacen){
  jQuery.ajax({
        type: "POST",
        url: path()+"sucursales/punto_venta/load_gavetas",
        dataType: 'json',
        data: {id_almacen:id_almacen},
        beforeSend : function(){
          
        },
        success: function(data){
          var chosen  = 'jQuery(".chzn-select").chosen();';
          jQuery('#list_gavetas').html(data+include_script(chosen));
          jQuery('#list_gavetas').show('slow');
        }
    });
}

function actualizar(){
  var progress = progress_initialized('update_loader');
  jQuery('#mensajes_update').hide();
  var btn          = jQuery("button[name='actualizar']");
  btn.attr('disabled','disabled');
  var btn_text     = btn.html();  

  var objData = formData('#form_pventa_edit');
  objData['incomplete'] = values_requeridos('form_pventa_edit');

  jQuery.ajax({
    type:"POST",
    url: path()+"sucursales/punto_venta/actualizar",
    dataType: "json",
    data: {objData:objData},
    beforeSend : function(){
      btn.attr('disabled',true);
    },

    success : function(data){
      if(data.success == 'true'){
        jgrowl(data.mensaje);
      }else{
        jQuery("#mensajes").html(data.mensaje).show('slow'); 
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

function insert(){
  var progress = progress_initialized('registro_loader');
  var btn = jQuery("button[name='save_punto_venta']");
  btn.attr('disabled','disabled');
  jQuery('#mensajes').hide();

  var objData = formData('#form_pventa_save');
    objData['incomplete'] = values_requeridos('form_pventa_save');
    
  jQuery.ajax({
    type:"POST",
    url: path()+"sucursales/punto_venta/insert",
    dataType: "json",
    data: {objData:objData},
    beforeSend : function(){
      btn.attr('disabled',true);
    },
    success : function(data){
        if(data.success == 'true' ){
        clean_formulario();
        jgrowl(data.mensaje);
      }else{
        jQuery("#mensajes").html(data.mensaje).show('slow');  
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

function add_cliente(id_punto_venta,id_sucursal){
  jQuery('#ui-id-2').click();
  jQuery.ajax({
        type: "POST",
        url: path()+"sucursales/punto_venta/add_cliente",
        dataType: 'json',
        data: {id_punto_venta : id_punto_venta, id_sucursal : id_sucursal},
        success: function(data){
          var chosen  = 'jQuery(".chzn-select").chosen();';
          jQuery('#a-2').html(data+include_script(chosen));
          jQuery('#ui-id-2').show('slow');
        }
    });
}

function duplicar(){
    var progress = progress_initialized('update_loader');
    var btn = jQuery("button[name='add_punto_venta']");
   // btn.attr('disabled','disabled');
    jQuery('#mensajes').hide();

    var objData = formData('#form_pventa_cliente');
    objData['incomplete'] = values_requeridos('form_pventa_cliente');
    
    jQuery.ajax({
      type:"POST",
      url: path()+"sucursales/punto_venta/duplicar",
      dataType: "json",
      data: {objData:objData},
      beforeSend : function(){
        //btn.attr('disabled',true);
      },
      success : function(data){
        //alert(dump_var(data));
          if(data.success == 'true' ){
          //clean_formulario();
          jgrowl(data.mensaje);
        }else{
          jQuery("#mensajes").html(data.mensaje).show('slow');  
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

function eliminar(id_pventa){
  var progress = progress_initialized('loader_global');
    jQuery.ajax({
        type: "POST",
        url: path()+"sucursales/punto_venta/eliminar_registro",
        dataType: 'json',
        data: {id_pventa : id_pventa},
        beforeSend: function(){
        },
        success: function(data){
          if(data.success == 'true'){
            jQuery('#ico-eliminar_'+data.id_pventa).parent().parent().parent().remove();
            jgrowl(data.mensaje);
          }else{
            jQuery("#mensajes_grid").html(data.mensaje).show('slow');
          }
        }
    }).error(function(){
      progress.progressTimer('error', {
            errorText:'ERROR!',
            onFinish:function(){
            }
        });
    }).done(function(){
          progress.progressTimer('complete');
      });
}

function confirm_delete(id_pventa){
  promp_delete(eliminar,id_pventa);
}
