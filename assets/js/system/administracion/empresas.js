function agregar(){
	var btn          = jQuery("button[name='save_area']");
	btn.attr('disabled','disabled');
	var objData = formData('#formulario');
	
	objData['incomplete']   = values_requeridos();
	objData['razon_social'] = jQuery('#txt_razon_social').val();
	objData['rfc']          = jQuery('#txt_rfc').val();
	objData['telefono']     = jQuery('#txt_telefono').val();
	objData['empresa']      = jQuery('#txt_empresa').val();
	objData['direccion']    = jQuery('#txt_direccion').val();
	
	jQuery.ajax({
		type:"POST",
		url: path()+"administracion/empresa/insert",
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