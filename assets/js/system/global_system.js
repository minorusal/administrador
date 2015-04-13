 jQuery(document).ready(function() {
    jQuery(".tabbedwidget").tabs();       
});
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