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

/*COMPRAS*/

/*Catalogo Articulos*/
$route['compras/catalogos/presentaciones']            = 'compras/catalogo_presentaciones/presentaciones';
$route['compras/catalogos/presentaciones/(:num)']     = 'compras/catalogo_presentaciones/presentaciones/$1';
$route['compras/catalogos/agregar_presentaciones']    = 'compras/catalogo_presentaciones/agregar_presentaciones';
$route['compras/catalogos/detalle_presentaciones']    = 'compras/catalogo_presentaciones/detalle_presentaciones';
$route['compras/catalogos/actualizar_presentaciones'] = 'compras/catalogo_presentaciones/actualizar_presentaciones';

/*Catalogo Lineas*/
$route['compras/catalogos/lineas']           = 'compras/catalogo_lineas/lineas';
$route['compras/catalogos/lineas/(:num)']    = 'compras/catalogo_lineas/lineas/$1';
$route['compras/catalogos/agregar_linea']    = 'compras/catalogo_lineas/agregar_linea';
$route['compras/catalogos/detalle_linea']    = 'compras/catalogo_lineas/detalle_linea';
$route['compras/catalogos/actualizar_linea'] = 'compras/catalogo_lineas/actualizar_linea';

/*Catalogo de U.M.*/
$route['compras/catalogos/um']            = 'compras/catalogo_um/um';
$route['compras/catalogos/um/(:num)']     = 'compras/catalogo_um/um/$1';
$route['compras/catalogos/agregar_um']    = 'compras/catalogo_um/agregar_um';
$route['compras/catalogos/detalle_um']    = 'compras/catalogo_um/detalle_um';
$route['compras/catalogos/actualizar_um'] = 'compras/catalogo_um/actualizar_um';

/*Catalogo de Marcas*/
$route['compras/catalogos/marcas']            = 'compras/catalogo_marcas/marcas';
$route['compras/catalogos/marcas/(:num)']     = 'compras/catalogo_marcas/marcas/$1';
$route['compras/catalogos/agregar_marcas']    = 'compras/catalogo_marcas/agregar_marcas';
$route['compras/catalogos/detalle_marcas']    = 'compras/catalogo_marcas/detalle_marcas';
$route['compras/catalogos/actualizar_marcas'] = 'compras/catalogo_marcas/actualizar_marcas';

/*Catalogo de Articulos*/
$route['compras/articulos/(:num)']     = 'compras/articulos/index/$1';

/* End of file routes.php */
/* Location: ./application/config/routes.php */