 jQuery(document).ready(function() {
    jQuery(".tabbedwidget").tabs();  
    window.onload = live_clock;
    tool_tips();    
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
function path(){
    
    var pathname = window.location.pathname;
    var hostname = pathname.split('/');
    if(document.domain=="localhost"){
        hostname = '/'+hostname[1]+'/';
    }else{
        hostname = '/';
    }
    return hostname;
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
}

function values_requeridos(){
    var ids = "";
    jQuery(".value_important").each(function(){ 
        var check = jQuery(this).val();
        if(check == ''){
            ids = jQuery(this).attr("id")+'|'+ids;
        }
    });
    return ids;
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