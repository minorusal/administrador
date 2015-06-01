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
           		var db = jQuery('#dualselect').find('.ds_arrow button');	//get arrows of dual select
				var sel1 = jQuery('#dualselect select:first-child');		//get first select element
				var sel2 = jQuery('#dualselect select:last-child');			//get second select element
				sel2.empty(); //empty it first from dom.
				db.click(function(){
					var t = (jQuery(this).hasClass('ds_prev'))? 0 : 1;	// 0 if arrow prev otherwise arrow next
					if(t){
						sel1.find('option').each(function(){
						if(jQuery(this).is(':selected')){
							jQuery(this).attr('selected',false);
							var op = sel2.find('option:first-child');
							sel2.append(jQuery(this));
						}
						});	
					}else{
						sel2.find('option').each(function(){
							if(jQuery(this).is(':selected')){
								jQuery(this).attr('selected',false);
								sel1.append(jQuery(this));
							}
						});
					}
					return false;
				});
           }
        }
    });
}

function agregar(){
	var btn     = jQuery("button[name='save_region']");
	btn.attr('disabled','disabled');
	jQuery('#mensajes').hide();
	var entidades = [];
	
	var objData = formData('#formulario');

	jQuery("[name='list'] option").each(function(){
	  entidades.push(jQuery(this).val());
	});
	
	objData['incomplete']  = values_requeridos();
	objData['region']      = jQuery('#txt_region').val();
	objData['clave_corta'] = jQuery('#txt_clave_corta').val();
	objData['descripcion'] = jQuery('#txt_descripcion').val();
	objData['entidades']   = entidades;
	
	jQuery.ajax({
		type:"POST",
		url: path()+"administracion/regiones/insert_region",
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