jQuery(document).ready(function(){
  selec_manual_auto();
	jQuery('#search-query').focus();
	jQuery('#search-query').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){  
			buscar();
		} 
	});
});

function selec_manual_auto(){
  var letra = allow_only_numeric_integer();
  jQuery("#txt_ciclo").attr("disabled", "disabled");
  jQuery(".manual").hide();
  jQuery("#txt_ciclo").removeClass("requerido");
  jQuery("#txt_clave_corta").removeClass("requerido");
  jQuery("#txt_ciclo").val("");
  jQuery("#txt_ciclo").attr("disabled", "disabled");
  jQuery("#txt_clave_corta").attr("disabled", "disabled");
  jQuery('input[name=tipo]').click(function(){
    var valor = jQuery(this).val();
    if(valor == 'auto'){
      jQuery("#txt_ciclo").removeClass("requerido");
      jQuery("#txt_clave_corta").removeClass("requerido");
      jQuery("#txt_ciclo").attr('value','');
      jQuery("#txt_cantidad_ciclo").removeAttr("disabled");
      jQuery("#txt_ciclo").attr("disabled", "disabled");
      jQuery(".manual").hide('slow');
      jQuery(".auto").show('slow');
      jQuery("#txt_clave_corta").attr("disabled", "disabled");
    }else{
       jQuery("#txt_cantidad_ciclo").removeClass("requerido");
       jQuery("#txt_cantidad_ciclo").attr("disabled", "disabled");
       jQuery("#txt_ciclo").removeAttr("disabled");
       jQuery("#txt_clave_corta").removeAttr("disabled");
       jQuery("#txt_cantidad_ciclo").attr('value','');
       jQuery(".manual").show('slow');
       jQuery(".auto").hide('slow');
       jQuery("#txt_ciclo").removeAttr("disabled");
    }
  });
}

function load_content(uri, id_content){
	jQuery('#ui-id-2').hide('slow');
	var filtro = jQuery('#search-query').val();
    jQuery.ajax({
        type: "POST",
        url: uri,
        dataType: 'json',
        data: {filtro : filtro, tabs:0},
        success: function(data){
           if(id_content==1){
           }else{
           		var chosen  = 'jQuery(".chzn-select").chosen();';
              var tipo_insert  = 'selec_manual_auto();';
           		jQuery('#a-'+id_content).html(data+include_script(chosen+tipo_insert));
           }
        }
    });
}

function load_ciclos(id_sucursal){
	jQuery.ajax({
        type: "POST",
        url: path()+"nutricion/ciclos/cargar_ciclos",
        dataType: 'json',
        data: {id_sucursal:id_sucursal},
        beforeSend : function(){
        	jQuery("#loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
        },
        success: function(data){
          var chosen  = 'jQuery(".chzn-select").chosen();';
          jQuery('#ciclo').html(data+include_script(chosen));
          jQuery('#ciclo').show('slow'); 
        }
    });
}

function load_contenido_ciclo(id_ciclo){
  var nombre_ciclo = jQuery('select[name=lts_ciclos] option:selected').text();
  var treeview  = 'load_treeview("treeview_ciclos");';
  if(id_ciclo!=0){
    jQuery.ajax({
          type: "POST",
          url: path()+"nutricion/ciclos/ciclo_detalle",
          dataType: 'json',
          data: {id_ciclo : id_ciclo, nombre_ciclo: nombre_ciclo},
          beforeSend : function(){
           jQuery("#loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
          },
          success: function(data){
            jQuery('#ciclo_detalle').html(data+include_script(treeview));
          }
      });
  }else{
    jQuery('#ciclo_detalle').html('');
  }
  jQuery('#loader').html('');
}

function load_treeview(id){  
    jQuery("#"+id).treeview({
        animated: "fast",
        control:"#sidetreecontrol",
        collapsed: false,
        unique: false
      });
}

function load_recetas(id_familia){
  var id_sucursal  = jQuery('select[name=lts_sucursales]').val();
  if(id_familia!=0){
    jQuery.ajax({
          type: "POST",
          url: path()+"nutricion/ciclos/ciclo_receta",
          dataType: 'json',
          data: {id_familia : id_familia,id_sucursal:id_sucursal},
          beforeSend : function(){
            jQuery("#loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
          },
          success: function(data){
            jQuery('#lts_recetas').html(data);
          }
      });
  }else{
    jQuery('select[name=lts_recetas]').empty().append('whatever');
  }
  jQuery('#loader').html('');
}


function buscar(){
	var filtro = jQuery('#search-query').val();
	jQuery.ajax({
		type:"POST",
		url: path()+"nutricion/ciclos/listado",
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

function agregar(){
  var btn          = jQuery("button[name='save_ciclo']");
  btn.attr('disabled','disabled');
  jQuery('#mensajes').hide();
  
  var objData = formData('#formulario');
  //alert(dump_var(objData));
  objData['incomplete'] = values_requeridos();

  jQuery.ajax({
    type:"POST",
    url: path()+"nutricion/ciclos/insert_ciclo",
    dataType: "json",
    data: objData,
    beforeSend : function(){
      jQuery("#registro_loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
    },
    success : function(data){
      //alert(dump_var(data));
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


//SELECT * FROM `notas_facturas` WHERE fecha_registro between '2015-06-26 18:38:00' and '2015-06-26 23:59:59' and envio = 1
//UPDATE `notas_facturas` set envio = 0 WHERE fecha_registro between '2015-06-26 18:38:00' and '2015-06-26 23:59:59' and envio = 1

//SELECT * FROM `notas_facturas` WHERE fecha_registro between '2015-06-28 00:00:00' and '2015-06-28 23:59:59'
//SELECT * FROM `notas_facturas` WHERE fecha_registro between '2015-06-28 00:00:00' and '2015-06-28 23:59:59'
//SELECT * FROM `notas_facturas` WHERE fecha_registro between '2015-06-28 00:00:00' and '2015-06-28 23:59:59
//UPDATE `notas_facturas` set envio = 0 WHERE fecha_registro between '2015-06-28 00:00:00' and '2015-06-28 23:59:59'