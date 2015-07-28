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