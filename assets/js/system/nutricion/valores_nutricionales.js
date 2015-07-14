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

	jQuery('#ui-id-1').hide('slow');
	var filtro = jQuery('#search-query').val();
    jQuery.ajax({
        type: "POST",
        url: uri,
        dataType: 'json',
        data: {filtro : filtro, tabs:0},
        success: function(data){
           if(id_content==0){
           		var funcion = 'buscar';
           		jQuery('#a-0').html(data+input_keypress('search-query', funcion));
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
    url: path()+"nutricion/valores_nutricionales/listado",
    dataType: "json",
    data: {filtro : filtro},
    beforeSend : function(){
      jQuery("#loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
    },
    success : function(data){
      var funcion = 'buscar';
          jQuery("#loader").html('');
          jQuery('#a-0').html(data+input_keypress('search-query', funcion));
      jQuery('#search-query').val(filtro).focus();
      tool_tips();
    }
  });
}

function detalle(id_articulo){  
  jQuery('#ui-id-1').click();
  jQuery.ajax({
        type: "POST",
        url: path()+"nutricion/valores_nutricionales/detalle",
        dataType: 'json',
        data: {id_articulo : id_articulo},
        success: function(data){
          jQuery('#a-1').html(data);
          jQuery('#ui-id-1').show('slow');
          var letra = allow_only_numeric();
          if(letra){
            jQuery('#formulario .numerico').prop( "disabled", true );
          }else{
            jQuery('#formulario .numerico').prop( "disabled", false );
          }
        }
    });
}

function actualizar(){
  var progress = progress_initialized('update_loader');
  jQuery('#mensajes_update').hide();
  var btn             = jQuery("button[name='actualizar']");
  btn.attr('disabled','disabled');
  var btn_text        = btn.html(); 

  var objData = formData('#formulario');
  objData['incomplete']    = values_requeridos();
  objData['numerico']      = values_numericos();
  
  jQuery.ajax({
    type:"POST",
    url: path()+"nutricion/valores_nutricionales/actualizar",
    dataType: "json",
    data: {objData},
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
