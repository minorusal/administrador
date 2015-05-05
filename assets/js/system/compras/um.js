jQuery(document).ready(function(){
	jQuery('#search-query').focus();
	jQuery('#search-query').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){  
			buscar_marca();
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
           		var funcion = 'buscar_um';
           		jQuery('#a-1').html(data+input_keypress('search-query', funcion));
           		jQuery('#search-query').val(filtro).focus();
           		tool_tips();
           }else{
           		jQuery('#a-'+id_content).html(data);
           }
        }
    });
}
function buscar_um(){
  var filtro = jQuery('#search-query').val();
  jQuery.ajax({
    type:"POST",
    url: path()+"compras/um/listado_um",
    dataType: "json",
    data: {filtro : filtro},
    beforeSend : function(){
      jQuery("#loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
    },
    success : function(data){
      var funcion = 'buscar_um';
          jQuery("#loader").html('');
          jQuery('#a-1').html(data+input_keypress('search-query', funcion));
      jQuery('#search-query').val(filtro).focus();
      tool_tips();
    }
  })
}
function detalle_um(id_um){
  jQuery('#ui-id-2').click();
  jQuery.ajax({
        type: "POST",
        url: path()+"compras/um/detalle_um",
        dataType: 'json',
        data: {id_um : id_um},
        success: function(data){
          jQuery('#a-0').html('');
          jQuery('#a-2').html(data);
          jQuery('#ui-id-2').show('slow');
        }
    });
}
function update_um(){
  jQuery('#mensajes_update').hide();
  var btn          = jQuery("button[name='update_um']");
  btn.attr('disabled','disabled');
  var btn_text     = btn.html();  
  var incomplete       = values_requeridos();
  var id_um            = jQuery('#id_um').val();
  var um               = jQuery('#um').val();
  var clave_corta      = jQuery('#clave_corta').val();
  var descripcion      = jQuery('#descripcion').val();
  
  jQuery.ajax({
    type:"POST",
    url: path()+"compras/um/update_um",
    dataType: "json",
    data: {incomplete :incomplete,id_um:id_um, um:um, clave_corta:clave_corta, descripcion:descripcion },
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
  })
}
function insert_um(){
  var btn          = jQuery("button[name='save_um']");
  btn.attr('disabled','disabled');
  jQuery('#mensajes').hide();
  var incomplete     = values_requeridos();
    var um           = jQuery('#um').val();
    var clave_corta  = jQuery('#clave_corta').val();
    var descripcion  = jQuery('#descripcion').val();
  
  jQuery.ajax({
    type:"POST",
    url: path()+"compras/um/insert_um",
    dataType: "json",
    data: {incomplete :incomplete, um:um, clave_corta:clave_corta, descripcion:descripcion },
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
}