 jQuery(document).ready(function() {
    jQuery(".tabbedwidget").tabs();  
    window.onload = live_clock;
    tool_tips();

    jQuery( ".load_controller" ).click(function() {
        jQuery("#loader_content").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
        //jQuery(".maincontent").hide('slow');
    });
    

    // GoTopIcon
    GoTop();
});
function datepicker_regional(){
    jQuery.datepicker.regional['es'] = {
        monthNames: months_timepicker,
        monthNamesShort: months_timepicker,
        dayNames: days_timepicker,
        dayNamesShort: days_timepicker,
        dayNamesMin: days_timepicker,
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 6,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };
    jQuery.datepicker.setDefaults(jQuery.datepicker.regional['es']);
}
function GoTop(){    
    //Check to see if the window is top if not then display button
    jQuery(window).scroll(function(){
        if (jQuery(this).scrollTop() > 100) {
            jQuery('.scrollToTop').fadeIn();
        } else {
            jQuery('.scrollToTop').fadeOut();
        }
    });    
    //Click event to scroll to top
    jQuery('.scrollToTop').click(function(){
        jQuery('html, body').animate({scrollTop : 0},800);
        return false;
    });    
}

function tool_tips(){
    jQuery('a[data-rel]').each(function() {
            jQuery(this).attr('rel', jQuery(this).data('rel'));
        });
    if(jQuery('.tooltips').length > 0)
        jQuery('.tooltips').tooltip({selector: "a[rel=tooltip]",offset: [0, 15]});
}
function input_keypress(identificador, name_funcion){
    var script = "<script>jQuery('#"+identificador+"').keypress(function(event){var keycode = (event.keyCode ? event.keyCode : event.which);if(keycode == '13'){   "+name_funcion+"(); } });</script>";
        return script;
}
function input_keyup(identificador, name_funcion){
    var script = "<script>jQuery('#"+identificador+"').keyup(function(e){if(e.which == '13' || e.which == 8){   "+name_funcion+"(); } });</script>";
        return script;
}
function load_treeview(id){
    jQuery("#"+id).treeview({
        animated: "fast",
        control:"#sidetreecontrol",
        collapsed: true,
        unique: false
      });
}

function include_script(script){
    var functions = "";
    if(jQuery.isArray(script)){
        jQuery.each(script, function(key, value){
            functions += value; 
        });
        var script = "<script>"+functions+"</script>";
        return script;
    }
    var script = "<script>"+script+"</script>";
    return script;
}
function send_form_ajax(uri, content, form){
    var uri     = (uri==undefined) ? '404_override' : uri;
    var form    = (form==undefined) ? 'formulario' : form;
    var objData = formData('#'+form); 
    var functions = [];
    var scripts = "";
    jQuery.ajax({
        type: "POST",
        url: path()+uri,
        dataType: 'json',
        data: objData,
        beforeSend : function(){
            imgLoader();
        },
        success: function(data){
            if(data.functions){
                jQuery.each(data.functions, function(key, value){
                    value = (jQuery.isArray(value)) ? value.join(',') : value;
                    if(jQuery.isArray(value)){
                        var params = value.join(',');
                    }else{
                        var params = value;
                    }
                    functions.push( key+'('+params+');');
                });
                scripts = include_script(functions);
            }
            jQuery('#'+content).html(data.result).show('slow');
            jQuery( "body" ).append(scripts);
            imgLoader_clean();
        }
    });
}

function remove_tr(id){
     $("#"+id).click(function() {
 
        // get handle to the current image (trashcan)
        var img = $(this);
        // gradually hide the parent row
        img.parents("tr").fadeOut(function()  {
            img.data("tooltip").hide();
        });
 
    });
}
function dump_var(arr,level) {
    // Explota un array y regres su estructura
    // Uso: alert(dump_var(array));
    var dumped_text = "";
    if(!level) level = 0;   
    //The padding given at the beginning of the line.
    var level_padding = "";
    for(var j=0;j<level+1;j++) level_padding += "    "; 
    if(typeof(arr) == 'object') { //Array/Hashes/Objects 
        for(var item in arr) {
            var value = arr[item];          
            if(typeof(value) == 'object') { //If it is an array,
                dumped_text += level_padding + "'" + item + "' ...\n";
                dumped_text += dump_var(value,level+1);
            } else {
                dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
            }
        }
    } else { //Stings/Chars/Numbers etc.
        dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
    }
    return dumped_text;
}
function live_clock(){
    if (jQuery('#liveclock').length){

        if (!document.layers&&!document.all&&!document.getElementById)
        return

        var Digital = new Date()
        var hours   = Digital.getHours()
        var minutes = Digital.getMinutes()
        var seconds = Digital.getSeconds()
        var dn      = "PM"
        if (hours<12)
        dn="AM"
        if (hours>12)
        hours=hours-12
        if (hours==0)
        hours=12

         if (minutes<=9)
         minutes="0"+minutes
         if (seconds<=9)
         seconds="0"+seconds

        myclock=hours+":"+minutes+":"+seconds+" "+dn
        if (document.layers){
            document.layers.liveclock.document.write(myclock)
            document.layers.liveclock.document.close()
        }
        else if (document.all)
            liveclock.innerHTML=myclock
        else if (document.getElementById)
            document.getElementById("liveclock").innerHTML=myclock
            setTimeout("live_clock()",1000)
    }
} 

function obj2json(_data){
    str = '{ ';
    first = true;
    jQuery.each(_data, function(i, v) { 
        if(first != true)
            str += ",";
        else first = false;
        if (jQuery.type(v)== 'object' )
            str += "'" + i + "':" + obj2json(v) ;
        else if (jQuery.type(v)== 'array')
            str += "'" + i + "':" + obj2json(v) ;
        else{
            str +=  "'" + i + "':'" + v + "'";
        }
    });
    return str+= '}';
}

function redirect(uri){
    jQuery('.ui-tabs-panel').html('<img src="'+path()+'assets/images/loaders/loader27.gif"/>');
    location.href = uri;
}

function load_content_tab(uri, id_content){
    jQuery.ajax({
        type: "POST",
        url: uri,
        dataType: 'json',
        data: {tabs:1},
        success: function(data){
           jQuery('#a-'+id_content).html(data);
        }
    });
}

function clean_formulario(){
    jQuery(":text,textarea").each(function(){ 
        jQuery(jQuery(this)).val('');
    });
    jQuery("select").each(function(){ 
        jQuery(".requerido  :nth(1)").prop("selected","selected").trigger('change');;
    });
    jQuery('.chzn-select').val('').trigger('liszt:updated');
}

function values_requeridos(){
    var ids = "";
    var items_vacios = 0;
    jQuery(".requerido").each(function(){ 
        if(jQuery(this).prop('tagName')=='SELECT'){
            var select = jQuery("select[name='"+jQuery(this).attr('name')+"'] option:selected");
            
            if(select.val()==0){
                items_vacios++
            }
        }else{
            if(jQuery(this).val() == ''){
                ids = jQuery(this).attr("id")+'|'+ids;
                items_vacios++
            } 
        }
    });
   // alert(ids+'-'+items_vacios);
    return items_vacios;
}

function alertas_tpl(type , mensaje ,close){
    var alert = "";
    var button_close = "";
    if(type == ""){
        type = "alert";
    }else{
        type = "alert-"+type;
    }
    if(close){
        button_close = "<button data-dismiss='alert' class='close' type='button'>×</button>";
    }
    alert = "<div class='alert "+type+"'>"+button_close+mensaje+"</div>";
    return alert
}

function formData(selector, template){
    /**
    * Descripcion:  Crea un objeto recuperando los valores ingresados en los campos INPUT
    * Comentario:   Los elementos html deben estar dentro de un mismo <div> y tiene que 
    *               tener el atributo:data-campo="[nombre_campo]"
    * Ejemplo:      <div id="formulario"><input id="id_orden" type="hidden" data-campo="id_orden" value="{id_orden}" /></div>
    *               <script> var objData = formData('#formulario'); </script>
    * @author:      Oscar Maldonado - O3M
    */
    var data = template ? template : {}; // Valores predeterminados - Opcional
    var c, f, r, v, m, $e, $elements = jQuery(selector).find("input, select, textarea");
    for (var i = 0; i < $elements.length; i++){
        $e = jQuery($elements[i]);
        // alert($elements[i]['id']);  
        f = $e.data("campo");
        r = $e.attr("required") ? true: false;  
        // validación de que exista un elemento
        if (!f) continue;  
        // Recolección datos por tipo de elemento
        v = undefined;
        switch ($e[0].nodeName.toUpperCase()){
            case "LABEL":
                v = $e.text();
                break;
            case "INPUT":
                var type = $e.attr("type").toUpperCase();
                if (type == "TEXT" || type == "HIDDEN"){
                    v = jQuery.trim($e.val());
                }
                else if (type == "CHECKBOX"){
                    v = $e.prop("checked");
                }
                else if (type == "RADIO"){
                    if ($e.prop("checked"))
                        v = $e.val();
                }
                else if ($e.datepicker){
                    v = $e.datepicker("getDate");
                }
                else{
                    v = jQuery.trim($e.val());
                }
                break;
            case "TEXTAREA":
            default:
                v = jQuery.trim($e.val());
        }  
        // Guarda el valor en el objeto
        if (r && (v == undefined || v == "")){
            m = $e.data("mensaje");
            if (m)
                alert(m);
            else
                alert("Es necesario especificar un valor para el campo \"" + f + "\".");
            $e.focus();
            return null;
        }
        else if (v != undefined)            
            data[i] = v;  
            data[f] = v; 
    }// next  
    return data;
}

function objLength(objeto) {
    // Devuelve la longitud de un Objeto => array.length
    var size = 0, key; 
    for (key in objeto) {
        if (objeto.hasOwnProperty(key)) size++;
    }
    return size;
}

function imgLoader(idDiv, imageFile){
    // Muestra image de loader en <div> 
    var imageFile   = ((imageFile=='') || (imageFile==undefined)) ? 'loader.gif' : imageFile;
    var idDiv       = ((idDiv=='') || (idDiv==undefined)) ? '#loader' : idDiv;
    
    var imgPath     = path()+'assets/images/loaders/';
    var imageName   = 'loader.gif';    

    imageFile       = imgPath + imageName ;

    var htmlLoader  = '<img src="'+imageFile+'"/>';
    jQuery(idDiv).html(htmlLoader);
    return true;
}

function imgLoader_clean(idDiv){
    var idDiv = ((idDiv=='') || (idDiv==undefined)) ? '#loader' : idDiv;
    jQuery(idDiv).html('');
}