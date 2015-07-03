jQuery(document).ready(function(){
	calendar_dual("fecha_inicio", "fecha_termino");
});

function load_programacion(id_sucursal){

	var calendar     = 'calendar_dual("fecha_inicio", "fecha_termino");';
	var dual_select  = 'dual_select();';
	var festivos     = 'calendar("fechas_festivas");';
	jQuery.ajax({
        type: "POST",
        url: path()+"nutricion/programacion/form_config_programacion",
        dataType: 'json',
        data: { id_sucursal : id_sucursal },
        beforeSend : function(){
        	imgLoader('#loader_programacion');
        },
        success: function(data){
        	imgLoader_clean("#loader_programacion");
        	jQuery('#configuracion_programacion').html(data+include_script(calendar+dual_select+festivos));
        }
    });
}
function quitar_ciclo(){
	var id = '';
	jQuery('select[name=multiselect_ciclos_agregados] option:selected').each(function(){
		id = jQuery(this).val();
		jQuery(jQuery('#'+id)).remove();
	});
}
function agregar_ciclo(){
	jQuery('select[name=multiselect_ciclos] option:selected').each(function(){
		jQuery('#ciclos_programados').append('<li id ="'+jQuery(this).val()+'" class="onclick_on "><h3><span class=" icon-chevron-right"></span>&nbsp;'+jQuery(this).text()+'</h3></li>');
	});
}
function load_contenido_ciclo(id_ciclo){
	var nombre_ciclo = jQuery('select[name=dropdown_ciclos] option:selected').text();
	var treeview    = 'load_treeview_collapse("treeview_ciclos_recetas");';
	if(id_ciclo!=0){
		jQuery.ajax({
	        type: "POST",
	        url: path()+"nutricion/programacion/ciclo_cantidad_recetas",
	        dataType: 'json',
	        data: { id_ciclo : id_ciclo, nombre_ciclo: nombre_ciclo},
	        beforeSend : function(){
	        	imgLoader('#cantidades_ciclo_receta')
	        },
	        success: function(data){
	        	jQuery('#cantidades_ciclo_receta').html(data+include_script(treeview));
	        }
	    });
	}
}
function load_treeview_collapse(id){
    jQuery("#"+id).treeview({
        animated: "fast",
        control:"#sidetreecontrol",
        collapsed: true,
        unique: false
      });
}