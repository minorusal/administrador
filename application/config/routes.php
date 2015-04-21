<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

/*DEFAULT*/
$route['default_controller']       = 'login/index';
$route['404_override']             = 'error404';
$route['inicio']                   = 'inicio/index';
/********/

/*LOGIN*/
$route['login']                    = 'login/index';
$route['login/authentication']     = 'login/authentication';
$route['login/valindando']         = 'login/redireccion';
$route['logout']                   = 'login/logout';
/*******/

/*INVENTARIO*/

/*Catalogo Articulos*/
$route['inventario/catalogos/articulos']           = 'inventario/catalogo_articulos/articulos';
$route['inventario/catalogos/articulos/(:num)']    = 'inventario/catalogo_articulos/articulos/$1';
$route['inventario/catalogos/agregar_articulo']    = 'inventario/catalogo_articulos/agregar_articulo';
$route['inventario/catalogos/detalle_articulo']    = 'inventario/catalogo_articulos/detalle_articulo';
$route['inventario/catalogos/actualizar_articulo'] = 'inventario/catalogo_articulos/actualizar_articulo';

/*Catalogo Lineas*/
$route['inventario/catalogos/lineas']           = 'inventario/catalogo_lineas/lineas';
$route['inventario/catalogos/lineas/(:num)']    = 'inventario/catalogo_lineas/lineas/$1';
$route['inventario/catalogos/agregar_linea']    = 'inventario/catalogo_lineas/agregar_linea';
$route['inventario/catalogos/detalle_linea']    = 'inventario/catalogo_lineas/detalle_linea';
$route['inventario/catalogos/actualizar_linea'] = 'inventario/catalogo_lineas/actualizar_linea';

/*Catalogo de U.M.*/
$route['inventario/catalogos/um']            = 'inventario/catalogo_um/um';
$route['inventario/catalogos/um/(:num)']     = 'inventario/catalogo_um/um/$1';
$route['inventario/catalogos/agregar_um']    = 'inventario/catalogo_um/agregar_um';
$route['inventario/catalogos/detalle_um']    = 'inventario/catalogo_um/detalle_um';
$route['inventario/catalogos/actualizar_um'] = 'inventario/catalogo_um/actualizar_um';

/*Catalogo de Marcas*/
$route['inventario/catalogos/marcas']            = 'inventario/catalogo_marcas/marcas';
$route['inventario/catalogos/marcas/(:num)']     = 'inventario/catalogo_marcas/marcas/$1';
$route['inventario/catalogos/agregar_marcas']    = 'inventario/catalogo_marcas/agregar_marcas';
$route['inventario/catalogos/detalle_marcas']    = 'inventario/catalogo_marcas/detalle_marcas';
$route['inventario/catalogos/actualizar_marcas'] = 'inventario/catalogo_marcas/actualizar_marcas';

/* End of file routes.php */
/* Location: ./application/config/routes.php */