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
  jQuery('#mensajes_update').hide();
  var btn             = jQuery("button[name='actualizar']");
  btn.attr('disabled','disabled');
  var btn_text        = btn.html(); 
  var objData = formData('#formulario');
  objData['incomplete']    = values_requeridos();
  objData['numerico']      = values_numericos();
  
  objData['id_articulo']   = jQuery('#id_articulo').val();
  
  objData['cant_sugerida'] = jQuery('#cant_sugerida').val();
  objData['p_bruto']       = jQuery('#p_bruto').val();
  objData['p_neto']        = jQuery('#p_neto').val();
  objData['ener']          = jQuery('#ener').val();
  objData['prot']          = jQuery('#prot').val();
  objData['lipids']        = jQuery('#lipids').val();
  objData['h_carbono']     = jQuery('#h_carbono').val();
  objData['fib']           = jQuery('#fib').val();
  objData['vit_a']         = jQuery('#vit_a').val();
  objData['a_ascorbico']   = jQuery('#a_ascorbico').val();
  objData['a_folico']      = jQuery('#a_folico').val();
  objData['h_nohem']       = jQuery('#h_nohem').val();
  objData['pot']           = jQuery('#pot').val();
  objData['azu']           = jQuery('#azu').val();
  objData['i_glicemico']   = jQuery('#i_glicemico').val();
  objData['c_glicemica']   = jQuery('#c_glicemica').val();
  objData['calc']          = jQuery('#calc').val();
  objData['sod']           = jQuery('#sod').val();
  objData['sel']           = jQuery('#sel').val();
  objData['fos']           = jQuery('#fos').val();
  objData['coles']         = jQuery('#coles').val();
  objData['saturados']     = jQuery('#saturados').val();
  objData['mono']          = jQuery('#mono').val();
  objData['poli']          = jQuery('#poli').val();
  jQuery.ajax({
    type:"POST",
    url: path()+"nutricion/valores_nutricionales/actualizar",
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
