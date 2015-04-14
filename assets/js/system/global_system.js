 jQuery(document).ready(function() {
    jQuery(".tabbedwidget").tabs();  
    window.onload = live_clock;
});

function live_clock(){
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