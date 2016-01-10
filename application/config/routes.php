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
$route['sign_up']                  = 'sign_up/index';
$route['forgot_pwd']               = 'sign_up/forgot_pwd';
$route['sync/SyncCatalogs']         = 'sync/ws_sync/SyncCatalogs';
$route['sync/SyncSales']            = 'sync/ws_sync/SyncSales';


$route['404_override']             = 'error404';
$route['inicio']                   = 'inicio/index';
/********/

/*LOGIN*/
$route['login']                    = 'login/index';
$route['login/authentication']     = 'login/authentication';
$route['login/valindando']         = 'login/redireccion';
$route['logout']                   = 'login/logout';
/*******/

/*ADMINISTRACION*/

/*
 * CONTROL DE USUARIOS
 */
/*Catalogo de Usuarios*/
$route['administracion/control_de_usuarios/usuarios']        = 'administracion/usuarios/index';
/*Catalogo de Usuarios*/
$route['administracion/control_de_usuarios/perfiles']        = 'administracion/perfiles/index';
/*Catalogo de Puestos*/
$route['administracion/control_de_usuarios/puestos']         = 'administracion/puestos/index';
/*Catalogo de Areas*/
$route['administracion/control_de_usuarios/areas']           = 'administracion/areas/index';
/*Catalogo de Perfiles*/
$route['administracion/control_de_usuarios/perfiles']        = 'administracion/perfiles/index';
/*Catalogo de Perfiles*/
$route['administracion/control_de_usuarios/empresa']         = 'administracion/empresa/index';
/*
 * CATALOGOS GENERALES
 */
/*Catalogo de Sucursales*/
$route['administracion/sucursales/sucursales']               = 'administracion/sucursales/index';
/*Catalogo de Descuentos*/
$route['administracion/catalogos_generales/descuentos']      = 'administracion/descuentos/index';
/*Catalogo de Formas de pago*/
$route['administracion/catalogos_generales/formas_de_pago']  = 'administracion/formas_de_pago/index';
/*Catalogo de Creditos*/
$route['administracion/catalogos_generales/creditos']        = 'administracion/creditos/index';
/*Catálogo de Entidades*/
$route['administracion/catalogos_generales/entidades']       = 'administracion/entidades/index';
/*Catálogo de Regiones*/ 
$route['administracion/catalogos_generales/regiones']        = 'administracion/regiones/index'; 
/*Catálogo de subrogacion*/ 
$route['administracion/catalogos_generales/subrogacion']     = 'administracion/subrogacion/index'; 
/*Catálogo de Servicios*/ 
$route['administracion/sucursales/servicios']                = 'administracion/servicios/index';


/*SUCURSALES*/
$route['sucursales/listado_sucursales']				    =	'sucursales/listado_sucursales/index';
$route['sucursales/horarios_servicio']				    =	'sucursales/horarios_servicio/index';
$route['sucursales/punto_venta']				        =	'sucursales/punto_venta/index';
$route['sucursales/catalogos/clientes']					=	'sucursales/clientes/index';
$route['sucursales/operadores']							=	'sucursales/operadores/index';
$route['sucursales/horarios_atencion']				    =	'sucursales/horarios_atencion/index';

/* End of file routes.php */
/* Location: ./application/config/routes.php */