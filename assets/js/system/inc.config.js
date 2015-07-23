function path(Folder){
// Obtiene Carpeta raiz
    if(!Folder){Folder='admincontrol';}
    Folder = Folder + '/';
    var dominio = document.domain;
    var raiz = window.location.pathname.split(Folder);
    var ruta = raiz[0] + Folder;
    return ruta;
}

// function path(Folder){
// // Lee un JSON
//     var json_url = "assets/js/system/config.json";
//     var valor = '';
//     jQuery.getJSON(json_url, function(data) {       
//         valor = data['system_path'];
//     });
//     setTimeout(function(){
//         var system_path = valor;
//         Folder = system_path;
//     }, 5);
//     Folrder = Folder + '/';
//     var dominio = document.domain;
//     var raiz = window.location.pathname.split(Folder);
//     var ruta = raiz[0] + Folder + '/';
//     return ruta;
// }
