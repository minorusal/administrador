function load_dropdowns(id_sucursal){

	var id_sucursal     = id_sucursal; 
	var multi_recetas   = jQuery('#recetas_selected');
	var multi_articulos = jQuery('#articulos_selected');
	
	multi_articulos.find('option').empty();
	multi_recetas.find('option').empty();
	
	jQuery.ajax({
		type: 'POST',
		url : path()+'nutricion/menus/load_dropdowns',
		dataType : 'json',
		data : {id_sucursal :id_sucursal},
		beforeSend : function(){

		},
		success: function(data){
			if(data.recetas!=''){
				jQuery.each(data.recetas, function(){
					multi_recetas.append(jQuery('<option></option>').attr('value', this.key).text(this.item));
				});
				multi_recetas.trigger('liszt:updated');
			}
				
			if(data.articulos!=''){
				jQuery.each(data.articulos, function(){
					multi_articulos.append(jQuery('<option></option>').attr('value', this.key).text(this.item));
				});
				multi_articulos.trigger('liszt:updated');
			}
		}

	});
}

function conformar_menu(){
var progress = progress_initialized('loader_formatos');
	var btn = jQuery("button[name='save_formato']");
	btn.attr('disabled','disabled');
	jQuery('#mensajes_formatos').hide();

	var objData = formData('#formatos');
  	objData['incomplete'] = values_requeridos();
  	
	jQuery.ajax({
		type:"POST",
		url: path()+"nutricion/menus/conformar_menu",
		dataType: "json",
		data: {objData:objData},
		beforeSend : function(){
			btn.attr('disabled',true);
		},
		success : function(data){
		    if(data.success == 'true' ){
				clean_formulario();
				multi_articulos.find('option').empty();
				multi_recetas.find('option').empty();
				jgrowl(data.mensaje);
			}else{
				jQuery("#mensajes_formatos").html(data.mensaje).show('slow');	
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