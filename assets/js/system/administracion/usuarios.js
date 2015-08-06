jQuery(document).ready(function(){
  jQuery('#search-query').focus();
  jQuery('#search-query').keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){  
      buscar_usuario();
    } 
  });
});
function buscar_usuario(){
  var filtro = jQuery('#search-query').val();
  jQuery.ajax({
    type:"POST",
    url: path()+"administracion/usuarios/listado",
    dataType: "json",
    data: {filtro : filtro},
    beforeSend : function(){
      jQuery("#loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
    },
    success : function(data){
      var funcion = 'buscar_usuario';
          jQuery("#loader").html('');
          jQuery('#a-1').html(data+input_keypress('search-query', funcion));
      jQuery('#search-query').val(filtro).focus();
      tool_tips();
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
            var treeview           = 'load_treeview("treeview-modules");';
            var treeview_childrens = 'treeview_childrens();'; 
               if(id_content==1){
              		var funcion = 'buscar_usuario';
              		jQuery('#a-1').html(data+input_keypress('search-query', funcion));
              		jQuery('#search-query').val(filtro).focus();
              		tool_tips();
               }else{ 
                    var escribir = 'jQuery("#txt_nombre_usuario").on("keyup", function(){ find_string("administracion/usuarios/find_string",jQuery("#txt_nombre_usuario").val(),jQuery("#txt_nombre_usuario").attr("name"));});';
             	 	var chosen   = 'jQuery(".chzn-select").chosen();';
              		jQuery('#a-'+id_content).html(data+include_script(chosen+treeview+treeview_childrens+escribir));
                }
        }
    });
}

function asignar_perfil(id_personal,id_usuario, id_perfil){   
    jQuery('#ui-id-2').click();
    jQuery.ajax({
        type: "POST",
        url: path()+"administracion/usuarios/asignar_perfil",
        dataType: 'json',
        data: {id_personal : id_personal, id_usuario : id_usuario, id_perfil : id_perfil},
        success: function(data){
            var treeview           = 'load_treeview("treeview-modules");';
            var treeview_childrens = 'treeview_childrens();'; 
            var chosen = 'jQuery(".chzn-select").chosen();';
            jQuery('#a-2').html(data+include_script(chosen+treeview+treeview_childrens));
            jQuery('#ui-id-2').show('slow');
        }
    });
}

function find_string(uri,id,nom){
    jQuery.ajax({
        type: "POST",
        url: path()+uri,
        dataType: 'json',
        data: {item: id, nom : nom},
        success: function(data){
            if(data.existe == 0){
                jQuery('#'+data.campo).css("border", " 1px solid red");
                jQuery('#no_disponible').css({ color: "red", display: "block" });
            }else{
                jQuery('#'+data.campo).css("border", " 1px solid #339933");
                jQuery('#no_disponible').css('display','none');
            }
        }
    });
}
function load_tree_view(id_perfil){
    var treeview = [];
    jQuery.ajax({
        type: "POST",
        url : path()+'administracion/usuarios/load_tree_view_perfil',
        dataType : 'json',
        data : {id_perfil: id_perfil},
        success: function(data){
            treeview.push('load_treeview("treeview-modules");');
            treeview.push('treeview_childrens();');
            jQuery('#treeview_perfiles').html(data+include_script(treeview)).show('show');
        }
    });
}

function load_tree_view_perfil_usuario(id_personal,id_perfil){
    var treeview = [];
    jQuery.ajax({
        type: "POST",
        url : path()+'administracion/usuarios/load_tree_view_perfil_usuario',
        dataType : 'json',
        data : {id_personal:id_personal,id_perfil: id_perfil},
        success: function(data){
            treeview.push('load_treeview("treeview-modules");');
            treeview.push('treeview_childrens();');
            jQuery('#treeview_perfiles').html(data+include_script(treeview)).show('show');
        }
    });
}
function detalle(id_usuario){
  jQuery('#ui-id-2').click();
  jQuery.ajax({
        type: "POST",
        url: path()+"administracion/usuarios/detalle",
        dataType: 'json',
        data: {id_usuario : id_usuario},
        success: function(data){
            var treeview = 'load_treeview("treeview-modules");';
            var treeview_childrens = 'treeview_childrens();'; 
            var chosen = 'jQuery(".chzn-select").chosen();';
            jQuery('#a-0').html('');
            jQuery('#a-2').html(data+include_script(chosen+treeview+treeview_childrens));
            jQuery('#ui-id-2').show('slow');
        }
    });
}


function agregar_perfil(){
    var progress = progress_initialized('registro_loader');
    jQuery("#mensajes_update").html('').hide('slow');
    jQuery('#mensajes').hide();
    var btn             = jQuery("button[name='agregar_perfil']");
    btn.attr('disabled','disabled');
    var btn_text        = btn.html();

    var nivel_1 = [];
    var nivel_2 = [];
    var nivel_3 = [];
    var objData = formData('#formulario');

    jQuery("input[name='nivel_1']:enabled:checked").each(function(){
        nivel_1.push(jQuery(this).val());
    });
    
    jQuery("input[name='nivel_2']:enabled:checked").each(function(){
      nivel_2.push(jQuery(this).val());
    });
    
    jQuery("input[name='nivel_3']:enabled:checked").each(function(){
      nivel_3.push(jQuery(this).val());
    });

    objData['incomplete']  = values_requeridos();
    objData['nivel_1']     = (nivel_1.length>0) ? nivel_1.join(',') : nivel_1;
    objData['nivel_2']     = (nivel_2.length>0) ? nivel_2.join(',') : nivel_2;
    objData['nivel_3']     = (nivel_3.length>0) ? nivel_3.join(',') : nivel_3;
    
    jQuery.ajax({
        type:"POST",
        url: path()+"administracion/usuarios/insert_perfiles",
        dataType: "json",
        data: {objData:objData},
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

function insert(){
    var progress = progress_initialized('registro_loader');
    jQuery("#mensajes_update").html('').hide('slow');
    jQuery('#mensajes').hide();
    var btn             = jQuery("button[name='actualizar']");
    btn.attr('disabled','disabled');
    var btn_text        = btn.html();

    var nivel_1 = [];
    var nivel_2 = [];
    var nivel_3 = [];
    var objData = formData('#formulario');
    
    jQuery("input[name='nivel_1']:enabled:checked").each(function(){
        nivel_1.push(jQuery(this).val());
    });
    
    jQuery("input[name='nivel_2']:enabled:checked").each(function(){
      nivel_2.push(jQuery(this).val());
    });
    
    jQuery("input[name='nivel_3']:enabled:checked").each(function(){
      nivel_3.push(jQuery(this).val());
    });
    
    objData['incomplete']  = values_requeridos();
    objData['nivel_1']     = (nivel_1.length>0) ? nivel_1.join(',') : nivel_1;
    objData['nivel_2']     = (nivel_2.length>0) ? nivel_2.join(',') : nivel_2;
    objData['nivel_3']     = (nivel_3.length>0) ? nivel_3.join(',') : nivel_3;

    jQuery.ajax({
        type:"POST",
        url: path()+"administracion/usuarios/insert",
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
