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

/*Catalogo Presentaciones*/
$route['compras/catalogos/presentaciones']                               = 'compras/presentaciones/index';
$route['compras/catalogos/presentaciones/listado_presentaciones']        = 'compras/presentaciones/listado_presentaciones';
$route['compras/catalogos/presentaciones/listado_presentaciones/(:num)'] = 'compras/presentaciones/listado_presentaciones/$1';

/*Catalogo Lineas*/
$route['compras/catalogos/lineas']                       = 'compras/lineas/index';
$route['compras/catalogos/lineas/listado_lineas']        = 'compras/lineas/listado_lineas';
$route['compras/catalogos/lineas/listado_lineas/(:num)'] = 'compras/lineas/listado_lineas/$1';

/*Catalogo de Marcas*/
$route['compras/catalogos/marcas']                       = 'compras/marcas/index';
$route['compras/catalogos/marcas/listado_marcas']        = 'compras/marcas/listado_marcas';
$route['compras/catalogos/marcas/listado_marcas/(:num)'] = 'compras/marcas/listado_marcas/$1';

/*Catalogo de U.M.*/
$route['compras/catalogos/um']                   = 'compras/um/index';
$route['compras/catalogos/um/listado_um']        = 'compras/um/listado_um';
$route['compras/catalogos/um/listado_um/(:num)'] = 'compras/um/listado_um/$1';

/*Catalogo de Ordenes*/
$route['compras/ordenes']                   	 = 'compras/ordenes/index';
$route['compras/ordenes/listado_ordenes']        = 'compras/ordenes/listado_ordenes';
$route['compras/ordenes/listado_ordenes/(:num)'] = 'compras/ordenes/listado_ordenes/$1';

/*ALMACEN*/

/*Catalogo de almacenes*/
/*$route['almacen/catalogos/almacenes']			= 'almacen/almacenes/index';
$route['almacen/catalogos/almacenes/listado_almacenes']        = 'almacen/almacenes/listado_almacenes';
$route['almacen/catalogos/almacenes/listado_almacenes/(:num)'] = 'almacen/almacenes/listado_almacenes/$1';*/

$route['almacen/catalogos/almacenes']			     = 'almacen/almacenes/index';
$route['almacen/catalogos/almacenes/listado']        = 'almacen/almacenes/listado';
$route['almacen/catalogos/almacenes/listado/(:num)'] = 'almacen/almacenes/listado/$1';



/*VENTAS*/

/*Catalogo de clientes*/
$route['ventas/catalogos/clientes']				= 'ventas/clientes/index';
/*Catalogo de vendedores*/
$route['ventas/catalogos/vendedores']			= 'ventas/vendedores/index';

/* End of file routes.php */
/* Location: ./application/config/routes.php */