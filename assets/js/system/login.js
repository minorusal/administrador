jQuery(document).ready(function(){
	jQuery( "#button_login" ).click(function() {
		authentication();
	});
	jQuery('#user').keypress(function(event){
	    var keycode = (event.keyCode ? event.keyCode : event.which);
	    if(keycode == '13'){
	        authentication();
	    }
	});
	jQuery('#pwd').keypress(function(event){
	    var keycode = (event.keyCode ? event.keyCode : event.which);
	    if(keycode == '13'){
	       authentication();
	    }
	});
});

function authentication(){
	var user     = jQuery('#user').val();
	var pwd      = jQuery('#pwd').val();
	var id_user  = jQuery('#id_user').val();
	jQuery.ajax({
		type: "POST",
		url: "login/authentication",
		dataType: 'json',
		data: {id_user: id_user,user: user, pwd: pwd},
		success: function(data){
			switch (data){
				case 0:
						jQuery(location).attr('href','login');
					break
				case 1:
						jQuery(location).attr('href','inicio');
					break
				default:
					var promp_content = {
									content_01:{
										html:data,
										buttons: { Cancelar: false, Ingresar: true },
										focus: 1,
										submit:function(e,v,m,f){
											if(v){
												e.preventDefault();
												var id_usuario = jQuery('input:radio[name=perfil_ingreso]:checked').val();
												jQuery('#id_user').val(id_usuario);
												authentication();
												return false;
											}
											jQuery.prompt.close(promp_content);
										}
									}
								};
			
					jQuery.prompt(promp_content);
					break
			}
		}
	});
	
}


