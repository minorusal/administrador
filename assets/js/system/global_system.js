
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