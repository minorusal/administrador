jQuery(document).ready(function(){
  selec_manual_auto();
});

function selec_manual_auto(){
  /*Se llama a la funcion que permite ingresar solo números enteros
  *en un campo de texto*/

  var letra = allow_only_numeric_integer();
  
  //Se inicializan los estados de los elementos del formulario
  /*
   * txt_ciclo se deshabilita, se le remueve la clase requerido
   */
  jQuery("#txt_ciclo").attr("disabled", "disabled");
  jQuery("#txt_ciclo").removeClass("requerido");

  jQuery(".manual").hide();
  jQuery("#txt_clave_corta").removeClass("requerido");
  jQuery("#txt_clave_corta").attr("disabled", "disabled");
  jQuery('input[name=tipo]').click(function(){
    var valor = jQuery(this).val();
    if(valor == 'auto'){
      jQuery("#txt_ciclo").removeClass("requerido");
      jQuery("#txt_clave_corta").removeClass("requerido");
      jQuery("#txt_ciclo").attr('value','');
      jQuery("#txt_clave_corta").attr('value','');
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
        data: {filtro : filtro, tabs:1},
        success: function(data){
          var chosen  = 'jQuery(".chzn-select").chosen();';
           if(id_content==1){
            jQuery('#a-0').html('');
            jQuery('#a-'+id_content).html(data+include_script(chosen+tipo_insert));
           }else{
              var treeview    = 'load_treeview("treeview_ciclos");';
              var tipo_insert  = 'selec_manual_auto();';
              jQuery('#a-1').html('');
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
          //alert(dump_var(data['ciclos']));
          var chosen  = 'jQuery(".chzn-select").chosen();';
          jQuery('#ciclo').html(data['ciclos']+include_script(chosen));
          jQuery('#ciclo').show('slow');

          jQuery('#servicio').html(data['servicios']+include_script(chosen));
          jQuery('#servicio').show('slow');  
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
            var chosen  = 'jQuery(".chzn-select").chosen();';
            jQuery('#lts_recetas').html(data+include_script(chosen));
          }
      });
  }else{
    jQuery('select[name=lts_recetas]').empty().append('whatever');
  }
  jQuery('#loader').html('');
}

function agregar(){
  var btn          = jQuery("button[name='save_ciclo']");
  btn.attr('disabled','disabled');
  jQuery('#mensajes').hide();
  
  var objData = formData('#formulario');
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

function insert_config(){
  var treeview    = 'load_treeview("treeview_ciclos")';
  var btn = jQuery("button[name='save_config']");
  btn.attr('disabled','disabled');
  jQuery('#mensajes').hide();

  var objData = formData('#formularios');
  objData['incomplete'] = values_requeridos();

  jQuery.ajax({
    type:"POST",
    url: path()+"nutricion/ciclos/insert_config",
    dataType: "json",
    data: {objData: objData},
    beforeSend : function(){
      //jQuery("#registro_loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
    },
    success : function(data){
      //jQuery('.chzn-choices').clear();
      //jQuery('.chzn-choices').append(toAppend).multiselect("destroy").multiselect();
      jQuery('#ciclo_detalle').html(data+include_script(treeview));
    }
  });
}

function eliminar_receta(id_receta,id_ciclo){
  var eliminar = confirm("¿Desea eliminar la receta de este ciclo?");
  if(eliminar){
    var id = (id_receta)?1:0;
    var treeview    = 'load_treeview("treeview_ciclos");';
    var nombre_ciclo = jQuery('select[name=lts_ciclos] option:selected').text();
    jQuery.ajax({
          type: "POST",
          url: path()+"nutricion/ciclos/eliminar_receta",
          dataType: 'json',
          data: {id_receta : id_receta, id_ciclo:id_ciclo, nombre_ciclo:nombre_ciclo},
          beforeSend : function(){
            jQuery("#registro_loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
          },
          success: function(data){
            jQuery('#ciclo_detalle').html(data+include_script(treeview));

          }
    });
    jQuery('#loader').html('');
  }else{
    alert('No se elimino');
  }
} 

//SELECT * FROM `notas_facturas` WHERE fecha_registro between '2015-07-02 08:12:13' and '2015-07-02 23:59:59' and envio = 1
//UPDATE `notas_facturas` set envio = 0 WHERE fecha_registro between '2015-06-26 18:38:00' and '2015-06-26 23:59:59' and envio = 1

//SELECT * FROM `notas_facturas` WHERE fecha_registro between '2015-06-28 00:00:00' and '2015-06-28 23:59:59'
//SELECT * FROM `notas_facturas` WHERE fecha_registro between '2015-06-28 00:00:00' and '2015-06-28 23:59:59'
//SELECT * FROM `notas_facturas` WHERE fecha_registro between '2015-06-28 00:00:00' and '2015-06-28 23:59:59
//UPDATE `notas_facturas` set envio = 1 WHERE fecha_registro between '2015-07-02 08:12:13' and '2015-07-02 23:59:59'


//UPDATE `notas_facturas` set envio = 0 WHERE fecha_registro between '2015-07-02 08:12:13' and '2015-07-02 23:59:59'
//UPDATE `notas_facturas` set envio = 1 WHERE fecha_registro between '2015-07-03 01:00:00' and '2015-07-03 23:59:59'