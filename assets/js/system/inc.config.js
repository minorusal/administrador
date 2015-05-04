function path(Folder){
// Obtiene Carpeta raiz
    if(!Folder){Folder='adminventasadmin';}
    Folrder = Folder + '/';
    var dominio = document.domain;
    var raiz = window.location.pathname.split(Folder);
    var ruta = raiz[0] + Folder + '/';
    return ruta;
}
