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
           		jQuery('#a-'+id_content).html(data);
           }
        }
    });
}

function buscar(){
	var filtro = jQuery('#search-query').val();
	jQuery.ajax({
		type:"POST",
		url: path()+"nutricion/servicios/listado",
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

function detalle(id_servicio){
	jQuery('#ui-id-2').click();
	jQuery.ajax({
        type: "POST",
        url: path()+"nutricion/servicios/detalle",
        dataType: 'json',
        data: {id_servicio : id_servicio},
        success: function(data){
        	jQuery('#a-0').html('');
        	jQuery('#a-2').html(data);
        	jQuery('#ui-id-2').show('slow');
        }
    });
}

function actualizar(){
  jQuery('#mensajes_update').hide();
  var btn             = jQuery("button[name='aactualizr']");
  btn.attr('disabled','disabled');
  var btn_text        = btn.html(); 
  var objData = formData('#formulario');
  
  objData['incomplete']   = values_requeridos();
  objData['id_servicio']   = jQuery('#id_servicio').val();
  objData['servicio']      = jQuery('#txt_servicio').val();
  objData['clave_corta']  = jQuery('#txt_clave_corta').val();
  objData['descripcion']  = jQuery('#txt_descripcion').val();

  jQuery.ajax({
    type:"POST",
    url: path()+"nutricion/servicios/actualizar",
    dataType: "json",
    data: objData,
    beforeSend : function(){
      jQuery("#update_loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
    },
    success : function(data){
      btn.removeAttr('disabled');   
      jQuery("#mensajes_update").html(data.contenido).show('slow');
      jQuery("#update_loader").html('');
    }
  })
}

function agregar(){
  var btn          = jQuery("button[name='save_servicio']");
  btn.attr('disabled','disabled');
  jQuery('#mensajes').hide();

  var objData = formData('#formulario');
  objData['incomplete']   = values_requeridos();
  objData['id_servicio']   = jQuery('#id_servicio').val();
  objData['servicio']      = jQuery('#txt_servicio').val();
  objData['clave_corta']  = jQuery('#txt_clave_corta').val();
  objData['descripcion']  = jQuery('#txt_descripcion').val();
  jQuery.ajax({
    type:"POST",
    url: path()+"nutricion/servicios/insert_servicio",
    dataType: "json",
    data: objData,
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