jQuery(document).ready(function(){

})
function buscar_usuario(){
  var filtro = jQuery('#search-query').val();
  jQuery.ajax({
    type:"POST",
    url: path()+"administracion/usuarios/listado_usuarios",
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
              		var funcion = 'buscar_usuario';
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
function detalle_usuario(id_usuario){
  jQuery('#ui-id-2').click();
  jQuery.ajax({
        type: "POST",
        url: path()+"administracion/usuarios/load_tree_view_perfil",
        dataType: 'json',
        data: {id_usuario : id_usuario},
        success: function(data){
          var chosen = 'jQuery(".chzn-select").chosen();';
          jQuery('#a-0').html('');
          jQuery('#a-2').html(data+include_script(chosen));
          jQuery('#ui-id-2').show('slow');
        }
    });
}
