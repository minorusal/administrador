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
$route['compras/catalogos/unidad_de_medida']                   = 'compras/um/index';
$route['compras/catalogos/unidad_de_medida/listado_um']        = 'compras/um/listado_um';
$route['compras/catalogos/unidad_de_medida/listado_um/(:num)'] = 'compras/um/listado_um/$1';

/*Catalogo de Ordenes*/
$route['compras/ordenes']                   	 = 'compras/ordenes/index';

/*ALMACEN*/

/*Catalogo de almacenes*/
$route['almacen/catalogos/almacenes']			     = 'almacen/almacenes/index';
/*Catalogo de pasillos*/
$route['almacen/catalogos/pasillos']			     = 'almacen/pasillos/index';
/*Catalogo de gavetas*/
$route['almacen/catalogos/gavetas']			         = 'almacen/gavetas/index';


/*ADMINISTRACION*/

/*
 * CONTROL DE USUARIOS
 */
/*Catalogo de Usuarios*/
$route['administracion/control_de_usuarios/usuarios']        = 'administracion/usuarios/index';
/*Catalogo de Usuarios*/
$route['administracion/control_de_usuarios/perfiles']        = 'administracion/perfiles/index';
/*Catalogo de Puestos*/
$route['administracion/control_de_usuarios/puestos']        = 'administracion/puestos/index';
/*Catalogo de Areas*/
$route['administracion/control_de_usuarios/areas']        = 'administracion/areas/index';
/*
 * CATALOGOS GENERALES
 */
/*Catalogo de Sucursales*/
$route['administracion/catalogos_generales/sucursales']      = 'administracion/sucursales/index';
/*Catalogo de Impuestos*/
$route['administracion/catalogos_generales/impuestos']       = 'administracion/impuestos/index';
/*Catalogo de Descuentos*/
$route['administracion/catalogos_generales/descuentos']      = 'administracion/descuentos/index';
/*Catalogo de Formas de pago*/
$route['administracion/catalogos_generales/formas_de_pago']  = 'administracion/formas_de_pago/index';
/*Catalogo de Creditos*/
$route['administracion/catalogos_generales/creditos']        = 'administracion/creditos/index';
/*Catálogo de Entidades*/
$route['administracion/catalogos_generales/entidades']       = 'administracion/entidades/index'; 

/*VENTAS*/

/*Catalogo de clientes*/
$route['ventas/catalogos/clientes']				= 'ventas/clientes/index';
/*Catalogo de vendedores*/
$route['ventas/catalogos/vendedores']			= 'ventas/vendedores/index';

/* End of file routes.php */
/* Location: ./application/config/routes.php */