jQuery(document).ready(function(){
  jQuery('#search-query').focus();
  jQuery('#search-query').keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){  
      buscar();
    } 
  });
})
function buscar(){
  var filtro = jQuery('#search-query').val();
  jQuery.ajax({
    type:"POST",
    url: path()+"ventas/clientes/listado",
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
           		jQuery('#a-'+id_content).html(data+include_script(chosen));
           }
        }
    });
}
function insert(){
  var progress = progress_initialized('registro_loader');
  var btn          = jQuery("button[name='save_cliente']");
  btn.attr('disabled','disabled');
  jQuery('#mensajes').hide();
  
  var incomplete    = values_requeridos();
  var  nombre       = jQuery('#nombre').val();
  var  paterno      = jQuery('#paterno').val();
  var  materno      = jQuery('#materno').val();
  var  razon_social = jQuery('#razon_social').val();
  var  clave_corta  = jQuery('#clave_corta').val();
  var  rfc          = jQuery('#rfc').val();
  var  calle        = jQuery('#calle').val();
  var  num_int      = jQuery('#num_int').val();
  var  num_ext      = jQuery('#num_ext').val();
  var  colonia      = jQuery('#colonia').val();
  var  municipio    = jQuery('#municipio').val(); 
  var  cp           = jQuery('#cp').val();
  var  telefonos    = jQuery('#telefonos').val();
  var  email        = jQuery('#email').val();
  var id_entidad       = jQuery("select[name='lts_entidades'] option:selected").val();
  var id_sucursal    = jQuery("select[name='lts_sucursales'] option:selected").val();

  jQuery.ajax({
    type:"POST",
    url: path()+"ventas/clientes/insert",
    dataType: "json",
    data: {
            incomplete :incomplete, nombre:nombre,paterno:paterno,materno:materno,razon_social:razon_social, clave_corta:clave_corta, rfc:rfc,calle:calle,num_int:num_int,num_ext:num_ext,colonia:colonia,municipio:municipio,id_entidad:id_entidad,cp:cp,telefonos:telefonos,email:email,id_sucursal:id_sucursal},
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
function detalle(id_cliente){
  jQuery('#ui-id-2').click();
  jQuery.ajax({
        type: "POST",
        url: path()+"ventas/clientes/detalle",
        dataType: 'json',
        data: {id_cliente : id_cliente},
        success: function(data){
          var chosen = 'jQuery(".chzn-select").chosen();';
          jQuery('#a-0').html('');
          jQuery('#a-2').html(data+include_script(chosen));
          jQuery('#ui-id-2').show('slow');
        }
    });
}

function update(){
  var progress = progress_initialized('update_loader');
  var btn          = jQuery("button[name='update_cliente']");
  btn.attr('disabled','disabled');
  jQuery('#mensajes').hide();
  
  var incomplete    = values_requeridos();    
  var  id_cliente   = jQuery('#id_cliente').val();
  var  nombre       = jQuery('#nombre').val();
  var  paterno      = jQuery('#paterno').val();
  var  materno      = jQuery('#materno').val();
  var  razon_social = jQuery('#razon_social').val();
  var  clave_corta  = jQuery('#clave_corta').val();
  var  rfc          = jQuery('#rfc').val();
  var  calle        = jQuery('#calle').val();
  var  num_int      = jQuery('#num_int').val();
  var  num_ext      = jQuery('#num_ext').val();
  var  colonia      = jQuery('#colonia').val();
  var  municipio    = jQuery('#municipio').val();  
  var  cp           = jQuery('#cp').val();
  var  telefonos    = jQuery('#telefonos').val();
  var  email        = jQuery('#email').val();
  var id_entidad       = jQuery("select[name='lts_entidades'] option:selected").val();
  var id_sucursal      = jQuery("select[name='lts_sucursales'] option:selected").val();

  jQuery.ajax({
    type:"POST",
    url: path()+"ventas/clientes/update",
    dataType: "json",
    data: {
            incomplete :incomplete, id_cliente:id_cliente,nombre:nombre,paterno:paterno,materno:materno,razon_social:razon_social,clave_corta:clave_corta,rfc:rfc,calle:calle,num_int:num_int,num_ext:num_ext,colonia:colonia,municipio:municipio,id_entidad:id_entidad,id_sucursal:id_sucursal,cp:cp,telefonos:telefonos,email:email},
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