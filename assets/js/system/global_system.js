 jQuery(document).ready(function() {
    jQuery(".tabbedwidget").tabs();  
    window.onload = live_clock;
    tool_tips();

    jQuery( ".load_controller" ).click(function() {
        jQuery("#loader_content").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
        //jQuery(".maincontent").hide('slow');
    });
});
function tool_tips(){
    jQuery('a[data-rel]').each(function() {
            jQuery(this).attr('rel', jQuery(this).data('rel'));
        });
    if(jQuery('.tooltipsample').length > 0)
        jQuery('.tooltipsample').tooltip({selector: "a[rel=tooltip]"});
        
    jQuery('.popoversample').popover({selector: 'a[rel=popover]', trigger: 'hover'});
}
function input_keypress(identificador, name_funcion){
    var script = "<script>jQuery('#"+identificador+"').keypress(function(event){var keycode = (event.keyCode ? event.keyCode : event.which);if(keycode == '13'){   "+name_funcion+"(); } });</script>";
        return script;
}
function include_script(script){
     var script = "<script>"+script+"</script>";
        return script;
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