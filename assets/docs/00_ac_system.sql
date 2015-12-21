/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50626
Source Host           : localhost:3306
Source Database       : 00_ac_system

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2015-12-21 17:07:33
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `sys_claves`
-- ----------------------------
DROP TABLE IF EXISTS `sys_claves`;
CREATE TABLE `sys_claves` (
  `id_clave` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(20) DEFAULT NULL,
  `pwd` varchar(50) DEFAULT NULL,
  `token` text,
  `timestamp` datetime NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `edit_timestamp` date DEFAULT NULL,
  `edit_id_usuario` int(11) DEFAULT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_clave`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sys_claves
-- ----------------------------
INSERT INTO `sys_claves` VALUES ('1', 'root', '63a9f0ea7bb98050796b649e85481845', null, '2015-12-18 12:15:40', '1', '2015-12-18', '1', '1');
INSERT INTO `sys_claves` VALUES ('2', 'dianareyes', '167db7af5709816a8d3dae9816aa439c', null, '2015-12-21 13:01:02', '1', '2015-12-21', '1', '1');

-- ----------------------------
-- Table structure for `sys_empresas`
-- ----------------------------
DROP TABLE IF EXISTS `sys_empresas`;
CREATE TABLE `sys_empresas` (
  `id_empresa` int(11) NOT NULL AUTO_INCREMENT,
  `empresa` varchar(50) DEFAULT NULL,
  `razon_social` varchar(50) DEFAULT NULL,
  `rfc` varchar(50) DEFAULT NULL,
  `direccion` text,
  `telefono` varchar(50) DEFAULT NULL,
  `image` varchar(50) DEFAULT NULL,
  `id_usuario` tinyint(1) DEFAULT NULL,
  `edit_id_usuario` tinyint(1) DEFAULT NULL,
  `edit_timestamp` datetime DEFAULT NULL,
  `timestamp` datetime NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_empresa`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sys_empresas
-- ----------------------------
INSERT INTO `sys_empresas` VALUES ('1', 'iSolution', 'Intelligent Solution S.A. de C.V.', 'XXX000000X99', 'Insurgentes Sur 1898, Piso 3-4', '59804817', null, '1', '1', '2015-08-25 16:03:24', '2015-08-25 16:03:24', '1');

-- ----------------------------
-- Table structure for `sys_menu_n1`
-- ----------------------------
DROP TABLE IF EXISTS `sys_menu_n1`;
CREATE TABLE `sys_menu_n1` (
  `id_menu_n1` int(11) NOT NULL AUTO_INCREMENT,
  `menu_n1` varchar(70) DEFAULT NULL,
  `routes` varchar(150) NOT NULL DEFAULT '404_override',
  `icon` varchar(50) NOT NULL DEFAULT 'iconfa-exclamation-sign',
  `order` int(11) DEFAULT NULL,
  `registro` datetime NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_menu_n1`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sys_menu_n1
-- ----------------------------
INSERT INTO `sys_menu_n1` VALUES ('1', 'inicio', 'inicio', 'iconfa-home', '1', '2015-12-18 12:22:30', '1');
INSERT INTO `sys_menu_n1` VALUES ('2', 'administracion', '404_override', 'fa fa-cogs', '2', '2015-12-18 12:23:09', '1');
INSERT INTO `sys_menu_n1` VALUES ('3', 'sucursales', '404_override', 'iconfa-sitemap', '3', '2015-12-18 12:23:30', '1');
INSERT INTO `sys_menu_n1` VALUES ('4', 'compras', '404_override', 'iconfa-exclamation-sign', '4', '2015-12-21 16:11:56', '1');
INSERT INTO `sys_menu_n1` VALUES ('5', 'menus', '404_override', 'iconfa-exclamation-sign', '5', '2015-12-21 16:19:04', '1');

-- ----------------------------
-- Table structure for `sys_menu_n2`
-- ----------------------------
DROP TABLE IF EXISTS `sys_menu_n2`;
CREATE TABLE `sys_menu_n2` (
  `id_menu_n2` int(11) NOT NULL AUTO_INCREMENT,
  `menu_n2` varchar(100) NOT NULL,
  `routes` varchar(150) NOT NULL DEFAULT '404_override',
  `icon` varchar(50) NOT NULL DEFAULT 'iconfa-exclamation-sign',
  `order` int(11) DEFAULT NULL,
  `id_menu_n1` int(11) NOT NULL,
  `registro` datetime NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_menu_n2`),
  KEY `id_modulo` (`id_menu_n1`),
  KEY `id_modulo_2` (`id_menu_n1`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sys_menu_n2
-- ----------------------------
INSERT INTO `sys_menu_n2` VALUES ('1', 'usuarios', 'administracion/control_de_usuarios/udsuarios', 'iconfa-exclamation-sign', '1', '2', '2015-12-18 12:27:38', '0');
INSERT INTO `sys_menu_n2` VALUES ('2', 'puestos', 'administracion/control_de_usuarios/puestos', 'iconfa-exclamation-sign', '2', '2', '2015-12-18 12:28:21', '0');
INSERT INTO `sys_menu_n2` VALUES ('3', 'areas', 'administracion/control_de_usuarios/areas', 'iconfa-exclamation-sign', '3', '2', '2015-12-18 12:28:57', '0');
INSERT INTO `sys_menu_n2` VALUES ('4', 'control_de_usuarios', '404_override', 'fa fa-users', '4', '2', '2015-12-18 12:30:20', '1');
INSERT INTO `sys_menu_n2` VALUES ('5', 'catalogos_generales', 'administracion/catalogos_generales/formas_de_pago', 'iconfa-book', '5', '2', '2015-12-18 12:31:00', '1');
INSERT INTO `sys_menu_n2` VALUES ('6', 'empresa', 'administracion/empresa', 'fa fa-building', '6', '2', '2015-12-18 12:31:32', '1');
INSERT INTO `sys_menu_n2` VALUES ('7', 'listado_de_sucursales', 'sucursales/listado_sucursales', 'iconfa-sitemap', '1', '3', '2015-12-18 12:34:02', '1');
INSERT INTO `sys_menu_n2` VALUES ('8', 'horarios_de_servicio', 'sucursales/horarios_servicio', 'fa fa-clock-o', '2', '3', '2015-12-18 12:34:44', '1');
INSERT INTO `sys_menu_n2` VALUES ('9', 'eventos', 'sucursales/eventos', 'fa fa-th-list', '3', '3', '2015-12-18 12:35:12', '1');
INSERT INTO `sys_menu_n2` VALUES ('10', 'catalogos', 'sucursales/catalogos', 'fa fa-pie-chart', '4', '3', '2015-12-18 12:35:43', '1');
INSERT INTO `sys_menu_n2` VALUES ('11', 'punto_venta', 'sucursales/punto_venta', 'fa fa-pie-chart', '5', '3', '2015-12-18 12:36:16', '1');
INSERT INTO `sys_menu_n2` VALUES ('12', 'proveedores', 'compras/proveedores', 'iconfa-exclamation-sign', '1', '4', '2015-12-21 16:12:38', '1');
INSERT INTO `sys_menu_n2` VALUES ('13', 'presentacion_articulo', 'compras/presentacion_articulo', 'iconfa-exclamation-sign', '2', '4', '2015-12-21 16:13:17', '1');
INSERT INTO `sys_menu_n2` VALUES ('14', 'precios_proveedor', 'compras/precios_proveedor', 'iconfa-exclamation-sign', '3', '4', '2015-12-21 16:13:47', '1');
INSERT INTO `sys_menu_n2` VALUES ('15', 'catalogos', 'compras/catalogos', 'iconfa-exclamation-sign', '4', '4', '2015-12-21 16:14:06', '1');
INSERT INTO `sys_menu_n2` VALUES ('16', 'recetario', 'menus/recetario', 'iconfa-exclamation-sign', '1', '5', '2015-12-21 16:19:54', '1');
INSERT INTO `sys_menu_n2` VALUES ('17', 'catalogos', '404_override', 'iconfa-exclamation-sign', '2', '5', '2015-12-21 16:21:11', '1');

-- ----------------------------
-- Table structure for `sys_menu_n3`
-- ----------------------------
DROP TABLE IF EXISTS `sys_menu_n3`;
CREATE TABLE `sys_menu_n3` (
  `id_menu_n3` int(11) NOT NULL AUTO_INCREMENT,
  `menu_n3` varchar(100) DEFAULT NULL,
  `routes` varchar(100) NOT NULL DEFAULT '404_override',
  `icon` varchar(50) NOT NULL DEFAULT 'iconfa-exclamation-sign',
  `order` int(11) DEFAULT NULL,
  `id_menu_n2` int(11) NOT NULL,
  `registro` datetime NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_menu_n3`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sys_menu_n3
-- ----------------------------
INSERT INTO `sys_menu_n3` VALUES ('1', 'usuarios', 'administracion/control_de_usuarios/usuarios', 'fa fa-user', '1', '4', '2015-12-18 12:54:16', '1');
INSERT INTO `sys_menu_n3` VALUES ('2', 'puestos', 'administracion/control_de_usuarios/puestos', 'fa fa-sitemap', '2', '4', '2015-12-18 12:54:45', '1');
INSERT INTO `sys_menu_n3` VALUES ('3', 'areas', 'administracion/control_de_usuarios/areas', 'iconfa-fire', '3', '4', '2015-12-18 12:55:13', '1');
INSERT INTO `sys_menu_n3` VALUES ('4', 'perfiles', 'administracion/control_de_usuarios/perfiles', 'iconfa-key', '4', '4', '2015-12-18 12:55:40', '1');
INSERT INTO `sys_menu_n3` VALUES ('5', 'impuestos', 'administracion/catalogos_generales/impuestos', 'fa fa-tasks', '1', '5', '2015-12-18 12:58:40', '0');
INSERT INTO `sys_menu_n3` VALUES ('6', 'descuentos', 'administracion/catalogos_generales/descuentos', 'fa fa-tag', '2', '5', '2015-12-18 12:59:07', '1');
INSERT INTO `sys_menu_n3` VALUES ('7', 'formas_de_pago', 'administracion/catalogos_generales/formas_de_pago', 'fa fa-money', '3', '5', '2015-12-18 12:59:39', '1');
INSERT INTO `sys_menu_n3` VALUES ('8', 'creditos', 'administracion/catalogos_generales/creditos', 'fa fa-credit-card', '4', '5', '2015-12-18 13:00:03', '1');
INSERT INTO `sys_menu_n3` VALUES ('9', 'entidades', 'administracion/catalogos_generales/entidades', 'fa fa-bookmark', '5', '5', '2015-12-18 13:00:30', '1');
INSERT INTO `sys_menu_n3` VALUES ('10', 'regiones', 'administracion/catalogos_generales/regiones', 'fa fa-map-marker', '6', '5', '2015-12-18 13:01:01', '1');
INSERT INTO `sys_menu_n3` VALUES ('11', 'subrogacion', 'administracion/catalogos_generales/subrogacion', 'iconfa-retweet', '7', '5', '2015-12-18 13:01:28', '1');
INSERT INTO `sys_menu_n3` VALUES ('12', 'articulos', 'compras/catalogos/articulos', 'iconfa-exclamation-sign', '1', '15', '2015-12-21 16:15:08', '1');
INSERT INTO `sys_menu_n3` VALUES ('13', 'lineas', 'compras/catalogos/lineas', 'iconfa-exclamation-sign', '2', '15', '2015-12-21 16:16:00', '1');
INSERT INTO `sys_menu_n3` VALUES ('14', 'presentaciones', 'compras/catalogos/presentaciones', 'iconfa-exclamation-sign', '3', '15', '2015-12-21 16:16:53', '1');
INSERT INTO `sys_menu_n3` VALUES ('15', 'marcas', 'compras/catalogos/marcas', 'iconfa-exclamation-sign', '4', '15', '2015-12-21 16:17:15', '1');
INSERT INTO `sys_menu_n3` VALUES ('16', 'um', 'compras/catalogos/um', 'iconfa-exclamation-sign', '5', '15', '2015-12-21 16:17:55', '1');
INSERT INTO `sys_menu_n3` VALUES ('17', 'familias', 'menus/catalogos/familias', 'iconfa-exclamation-sign', '1', '17', '2015-12-21 16:22:40', '1');
INSERT INTO `sys_menu_n3` VALUES ('18', 'contratos', 'sucursales/eventos/contratos', 'iconfa-exclamation-sign', '1', '9', '2015-12-21 16:44:10', '1');
INSERT INTO `sys_menu_n3` VALUES ('19', 'tipo_contrato', 'sucursales/eventos/tipo_contrato', 'iconfa-exclamation-sign', '2', '9', '2015-12-21 16:45:10', '1');
INSERT INTO `sys_menu_n3` VALUES ('20', 'clientes', 'sucursales/catalogos/clientes', 'iconfa-exclamation-sign', '1', '10', '2015-12-21 16:46:47', '1');
INSERT INTO `sys_menu_n3` VALUES ('21', 'paquetes', 'sucursales/catalogos/paquetes', 'iconfa-exclamation-sign', '2', '10', '2015-12-21 16:47:14', '1');
INSERT INTO `sys_menu_n3` VALUES ('22', 'rango_evento', 'sucursales/catalogos/rango_evento', 'iconfa-exclamation-sign', '3', '10', '2015-12-21 16:47:41', '1');

-- ----------------------------
-- Table structure for `sys_paises`
-- ----------------------------
DROP TABLE IF EXISTS `sys_paises`;
CREATE TABLE `sys_paises` (
  `id_pais` int(11) NOT NULL AUTO_INCREMENT,
  `pais` varchar(100) DEFAULT NULL,
  `dominio` varchar(11) DEFAULT NULL,
  `moneda` varchar(5) DEFAULT NULL,
  `moneda_desc` varchar(30) DEFAULT NULL,
  `avatar` varchar(200) DEFAULT NULL,
  `registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_pais`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sys_paises
-- ----------------------------
INSERT INTO `sys_paises` VALUES ('1', 'Mexico', 'mx', '$', 'peso mexicano', 'country/mx.png', '2015-06-10 17:32:19', '1');
INSERT INTO `sys_paises` VALUES ('2', 'Costa Rica', 'cr', '₡', 'colón', 'country/cr.png', '2015-06-10 17:33:11', '1');

-- ----------------------------
-- Table structure for `sys_perfiles`
-- ----------------------------
DROP TABLE IF EXISTS `sys_perfiles`;
CREATE TABLE `sys_perfiles` (
  `id_perfil` int(11) NOT NULL AUTO_INCREMENT,
  `perfil` varchar(50) DEFAULT NULL,
  `clave_corta` varchar(200) DEFAULT NULL,
  `descripcion` text,
  `id_menu_n1` varchar(200) NOT NULL DEFAULT '1',
  `id_menu_n2` varchar(200) NOT NULL DEFAULT '1',
  `id_menu_n3` varchar(200) NOT NULL DEFAULT '1',
  `edit_id_usuario` int(11) DEFAULT NULL,
  `edit_timestamp` datetime DEFAULT NULL,
  `id_usuario` int(1) NOT NULL,
  `timestamp` datetime NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_perfil`),
  KEY `id_modulo` (`id_menu_n1`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sys_perfiles
-- ----------------------------
INSERT INTO `sys_perfiles` VALUES ('1', 'ROOT', 'ROOT', 'ROOT', '', '', '', null, null, '1', '2015-12-18 12:19:01', '1');
INSERT INTO `sys_perfiles` VALUES ('2', 'ADMINISTRADOR', 'ADMIN', 'ADMINISTRADOR', '1,2,3', '4,5,6,7,8,9,10,11', '1,2,3,4,6,7,8,9,10,11', '1', '2015-12-18 13:36:39', '1', '2015-12-18 12:19:51', '1');
INSERT INTO `sys_perfiles` VALUES ('3', 'PERFIL TEST', 'TESTCV', 'ESTE ES UN PERFIL TES', '1,2', '4,5,6', '1,2,3,4,6,7,8,9,10,11', null, null, '1', '2015-12-21 11:18:53', '1');
INSERT INTO `sys_perfiles` VALUES ('4', 'TEST', 'TEST', 'TEST', '', '', '', null, null, '1', '2015-12-21 12:39:01', '1');
INSERT INTO `sys_perfiles` VALUES ('5', '', '', '', '', '', '', null, null, '1', '2015-12-21 12:39:05', '1');
INSERT INTO `sys_perfiles` VALUES ('6', '1', '2', '', '', '', '', null, null, '1', '2015-12-21 12:53:59', '1');
INSERT INTO `sys_perfiles` VALUES ('7', '2', '3', '', '', '', '', null, null, '1', '2015-12-21 12:54:26', '1');
INSERT INTO `sys_perfiles` VALUES ('8', 's', 'd', '', '1', '', '', '1', '2015-12-21 12:57:31', '1', '2015-12-21 12:55:16', '1');

-- ----------------------------
-- Table structure for `sys_personales`
-- ----------------------------
DROP TABLE IF EXISTS `sys_personales`;
CREATE TABLE `sys_personales` (
  `id_personal` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `paterno` varchar(50) DEFAULT NULL,
  `materno` varchar(50) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `mail` varchar(50) DEFAULT NULL,
  `avatar` varchar(50) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `edit_id_usuario` int(11) DEFAULT NULL,
  `edit_timestamp` datetime DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `activo` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id_personal`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sys_personales
-- ----------------------------
INSERT INTO `sys_personales` VALUES ('1', 'Root', 'Del', 'Sistema', '123456', null, 'root.png', '1', '1', '2015-12-18 12:13:38', '2015-12-18 12:13:41', '1');
INSERT INTO `sys_personales` VALUES ('2', 'DIANA', 'REYES', 'MILLAN', '5588774466', 'minorusal@hotmail.com', null, '1', '3', '2015-12-21 13:07:32', '2015-12-21 13:01:01', '1');

-- ----------------------------
-- Table structure for `sys_sessions`
-- ----------------------------
DROP TABLE IF EXISTS `sys_sessions`;
CREATE TABLE `sys_sessions` (
  `session_id` varchar(40) COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `user_agent` varchar(120) COLLATE utf8_spanish_ci NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ----------------------------
-- Records of sys_sessions
-- ----------------------------
INSERT INTO `sys_sessions` VALUES ('f88605ae2234f1adc109d3249450a455', '::1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.124 Safari/537.36', '1435086563', '');

-- ----------------------------
-- Table structure for `sys_sucursales`
-- ----------------------------
DROP TABLE IF EXISTS `sys_sucursales`;
CREATE TABLE `sys_sucursales` (
  `id_sucursal` int(11) NOT NULL AUTO_INCREMENT,
  `sucursal` varchar(60) DEFAULT NULL,
  `clave_corta` varchar(20) DEFAULT NULL,
  `inicio` time DEFAULT NULL,
  `final` time DEFAULT NULL,
  `factura` tinyint(1) DEFAULT NULL,
  `razon_social` varchar(60) DEFAULT NULL,
  `rfc` varchar(30) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `encargado` varchar(50) DEFAULT NULL,
  `direccion` text,
  `telefono` varchar(50) DEFAULT NULL,
  `id_region` int(11) DEFAULT NULL,
  `id_entidad` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `edit_timestamp` datetime DEFAULT NULL,
  `edit_id_usuario` int(11) NOT NULL,
  `image` varchar(50) NOT NULL,
  `timestamp` datetime NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_sucursal`),
  KEY `i_clave_corta` (`clave_corta`),
  KEY `i_id_entidad` (`id_entidad`),
  KEY `i_activo` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sys_sucursales
-- ----------------------------
INSERT INTO `sys_sucursales` VALUES ('1', 'CATALOGO GENERAL', 'CATALOGO GENERAL', '11:30:00', '18:00:00', '0', 'CATALOGO GENERAL', 'DISH7210337', 'sa@ss.cOM', 'JOSE LUIS PEREZ', 'insurgentes sur', '5556846501', '6', '1', '1', '2015-11-09 11:28:57', '1', '', '2015-02-03 17:13:20', '1');
INSERT INTO `sys_sucursales` VALUES ('2', 'DICONSA, SOCIEDAD ANÓNIMA DE C.V.', 'DICONSA', '07:32:00', '12:27:25', null, 'DICONSA, SOCIEDAD ANÓNIMA DE C.V.', 'LALA837261C342', 'sja@ss.c', '', 'BLVD ADOLFO LOPEZ MATEOS 625', '5553214821', '4', '6', '1', '2015-07-28 11:00:00', '5', '', '2015-05-13 13:30:22', '1');
INSERT INTO `sys_sucursales` VALUES ('3', 'QUALTIA ALIMENTOS OPERACIONES S DE R.L DE C.V.', 'QUALTIA ALIMENTOS MT', '00:00:01', '12:27:27', null, 'QUALTIA ALIMENTOS OPERACIONES S DE R.L DE C.V.', 'QUA28349X999', 'sja@ss.c', 'NALLELY HERRERA', 'IZTAPALAPA', '5526325154', '5', '8', '1', '2015-06-04 15:53:44', '7', '', '2015-05-18 12:30:24', '1');
INSERT INTO `sys_sucursales` VALUES ('4', 'CONCILIA ASESORES Y SERVICIOS S.A. DE C.V.', 'QUAKER STATE', '00:00:01', '12:27:29', null, 'CONCILIA ASESORES Y SERVICIOS S.A. DE C.V.', 'dhsh123456', 'sja@ss.c', null, 'direccion form', '123456789', '7', '5', '1', null, '1', '', '2015-05-18 19:05:35', '1');
INSERT INTO `sys_sucursales` VALUES ('5', 'GONCALVES DE MÉXICO, S. DE R.L DE C.V', 'GRAFICAS GONCALVES', '00:00:01', '12:27:31', null, 'GONCALVES DE MÉXICO, S. DE R.L DE C.V', 'fsd', 'sja@ss.c', null, 'almoloya', 'fs', '6', '1', '1', null, '1', '', '2015-05-18 22:05:26', '1');
INSERT INTO `sys_sucursales` VALUES ('6', 'PRUEBAS', 'PRUEBAS', '00:00:01', null, null, 'PRUEBAS', 'jhsy273727', 'sja@ss.c', null, 'londres', '734635', '4', '6', '1', null, '1', '', '2015-05-19 17:05:51', '1');
INSERT INTO `sys_sucursales` VALUES ('7', 'RENOVA', 'RENOVA', '00:00:01', '15:47:00', '0', 'RENOVA', 'khjkh', 'sja@ss.co', '', 'kljlkj', 'hjkhk', '4', '13', '1', '2015-12-08 15:47:11', '1', '', '2015-05-19 18:05:29', '1');
INSERT INTO `sys_sucursales` VALUES ('8', 'EVENTOS VALKIRIA', 'EVENTOS VALKIRIA', '00:00:01', null, null, 'EVENTOS VALKIRIA', 'hdjs626261', 'sja@ss.c', null, 'direccion', '123456', '6', '1', '1', null, '1', '', '2015-05-19 20:05:20', '1');
INSERT INTO `sys_sucursales` VALUES ('9', 'DIRECCION DE SUMINISTROS SSA', 'DIRECCION DE SUMINIS', '00:00:01', null, null, 'DIRECCION DE SUMINISTROS SSA', '123456', 'sja@ss.c', null, '123456', '123456', '4', '14', '1', null, '1', '', '2015-05-19 20:05:52', '1');
INSERT INTO `sys_sucursales` VALUES ('10', 'LALA', 'LALA', '00:00:01', null, null, 'LALA', 'hsjs392873', 'sja@ss.c', null, 'direccion', '5424876574', '8', '0', '1', '2015-05-25 10:15:42', '1', '', '2015-05-25 10:15:34', '1');
INSERT INTO `sys_sucursales` VALUES ('11', 'ZUKO', 'ZUKO', '00:00:01', null, null, 'ZUKO', 'GEPAE S.A. DE C.V.', 'sja@ss.c', 'EDGAR AVILA', 'INSURGENTES SUR 1898 P-3', '56464', '6', '0', '1', '2015-06-04 15:59:18', '7', '', '2015-06-02 17:10:40', '1');
INSERT INTO `sys_sucursales` VALUES ('12', 'DISH', 'DISH', '00:00:01', '23:59:00', '0', 'DISH', '453465464', 'sja@ss.com', '', 'INSURGENTES', '56464', '4', '3', '1', '2015-09-10 12:50:25', '1', '', '2015-06-02 17:14:25', '1');
INSERT INTO `sys_sucursales` VALUES ('13', 'CARTA DISH', 'CARTA DISH', '00:00:01', null, null, 'CARTA DISH', 'FHJD374837', 'SUCURSAL@HOTMAIL.COM', 'SERGIO GODINEZ', 'DESC', '123456789', '5', '1', '1', '2015-07-09 10:21:40', '1', '', '2015-06-03 10:23:25', '1');
INSERT INTO `sys_sucursales` VALUES ('14', 'AMERICAN TEXTIL', 'AMERICAN TEXTIL', '00:00:01', null, null, 'AMERICAN TEXTIL', 'sjdh738492nf5', 'prueba@d.c', 'jose', 'calle amapola col napoles', '1234567890', '3', '6', '5', null, '0', '', '2015-07-09 15:46:01', '1');
INSERT INTO `sys_sucursales` VALUES ('15', 'CONSAME', 'CONSAME', '00:00:01', null, null, 'CONSAME', 'sjdh738492nf5', 'prueba@d.c', 'jose', 'calle amapola col napoles', '1234567890', '4', '2', '5', null, '0', '', '2015-07-09 15:46:37', '1');
INSERT INTO `sys_sucursales` VALUES ('16', 'CARTA QUALTIA', 'CARTA QUALTIA', '00:00:01', null, null, 'CARTA QUALTIA', 'shdk293848fhg', 'sja@ss.c', 'jose', 'calle relox', '123457890', '7', '1', '5', null, '0', '', '2015-07-09 16:05:58', '1');
INSERT INTO `sys_sucursales` VALUES ('17', 'ZUKO MENU NOCTURNO', 'ZUKO NOCTURNO', '00:00:01', null, null, 'ZUKO MENU NOCTURNO', null, null, null, null, null, null, null, '0', null, '0', '', '0000-00-00 00:00:00', '1');
INSERT INTO `sys_sucursales` VALUES ('18', 'NEZAHUALCOYOTL', 'NEZA-SUC', '00:00:01', null, null, 'NEZA HU AL CO YO TL', 'NEZA637452MD7', 'NEZ@D.COM', 'JOAQUIN', 'NEZA', '4521254786', '3', '15', '5', null, '0', '', '2015-07-14 11:22:21', '1');
INSERT INTO `sys_sucursales` VALUES ('19', 'ULTIMA SUCURSAL', 'LASTSUC', null, null, null, 'ULTIMA SUCRSAL SA DE CV', 'HFJD873727DS7', 'LAST@LAST.COM', 'GENARO', 'DESCRIPCION', '54121325457', '3', '1', '5', null, '0', '', '2015-07-23 15:57:35', '1');
INSERT INTO `sys_sucursales` VALUES ('20', 'ULTIMA SUCURSAL', 'LASTSUCW', '00:00:00', null, null, 'ULTIMA SUCRSAL SA DE CV', 'HFJD873727DS7', 'LAST@LAST.COM', 'GENARO', 'DESCRIPCION', '54121325457', '3', '1', '5', null, '0', '', '2015-07-23 15:58:26', '1');
INSERT INTO `sys_sucursales` VALUES ('21', 'fdf', 'fdf', '00:00:01', null, null, 'fd', 'fd', 'fd@64.d', 'jkj', 'sjkhd', '4521354785', '3', '1', '5', '2015-12-03 09:18:01', '1', '', '2015-07-23 16:04:20', '0');
INSERT INTO `sys_sucursales` VALUES ('22', 'tiempos', 'times', '13:05:00', '14:05:00', null, 'dfsfas', 'fsdf', 'fsd@dd.c', 'fds', 'fasd', 'fsd', '3', '1', '5', '2015-12-03 09:18:06', '1', '', '2015-07-24 13:05:27', '0');
INSERT INTO `sys_sucursales` VALUES ('23', '12:36', 'NORT', '12:36:00', '15:36:00', null, 'NORTE', 'NORT374635SHG', null, 'NORTEÑO', 'DIRECCIÓN DEL NORTE', '5421457852', '3', '1', '5', null, '0', '', '2015-07-27 12:37:29', '1');
INSERT INTO `sys_sucursales` VALUES ('24', '12:36', 'NORTS', '12:36:00', '15:36:00', null, 'NORTE', 'NORT374635SHG', null, 'NORTEÑO', 'DIRECCIÓN DEL NORTE', '5421457852', '3', '1', '5', null, '0', '', '2015-07-27 12:37:59', '1');
INSERT INTO `sys_sucursales` VALUES ('25', 'SUR', 'S.U.R', '12:41:00', '19:41:00', null, 'SUR', 'SUEJDH653G', null, 'SUREÑO', 'DIRECCIÓN SUR', '4521236547', '3', '2', '5', null, '0', '', '2015-07-27 12:42:42', '1');
INSERT INTO `sys_sucursales` VALUES ('26', 'SUR', 'S.U.R.', '12:41:00', '19:41:00', null, 'SUR', 'SUEJDH653G', 'email@email.com', 'SUREÑO', 'DIRECCIÓN SUR', '4521236547', '3', '2', '5', '2015-07-27 12:52:45', '5', '', '2015-07-27 12:43:34', '1');
INSERT INTO `sys_sucursales` VALUES ('27', 'OESTE', 'OEST', '03:59:00', '12:59:00', null, 'OESTE SA DE CV', 'OESTEWE323234DH6', 'OESTE@HOTMAIL.COM', 'SEÑOR OESTE', 'DIRECCIÓN DE OESTE', '1245785478', '5', '1', '5', '2015-07-27 13:01:46', '5', '', '2015-07-27 13:00:15', '1');
INSERT INTO `sys_sucursales` VALUES ('28', 'TEPIC', 'TEP', '04:00:00', '13:13:00', null, 'TEPIC SA DE CV', 'TEPI463726YH6', 'TEPIC@TEPUC.COM', 'JUAN', 'DIRECCION TEPIC', '5421547853', '4', '18', '5', null, '0', '', '2015-07-27 13:14:47', '1');
INSERT INTO `sys_sucursales` VALUES ('29', 'TABASCO', 'TAS', '04:00:00', '13:16:00', null, 'TABASCO SA DE CV', 'TABS364728HDH9', 'TABASCO@TS.C', 'PEGE', 'TABASCO', '5538888737', '3', '1', '5', null, '0', '', '2015-07-27 13:17:37', '1');
INSERT INTO `sys_sucursales` VALUES ('30', 'TUPPER', 'TUPP', '04:00:00', '16:28:00', '1', 'TUPPERWARE SA DE CV', 'TUPPDD364736HD6', 'TUP@T.C', 'TUPPER', 'AV TUPPERWARE', '78542546685', '3', '2', '5', '2015-07-27 16:29:21', '5', '', '2015-07-27 16:29:01', '1');
INSERT INTO `sys_sucursales` VALUES ('31', 'PAE', 'PAE', '04:00:00', '10:52:00', '1', 'PAE SA DE CV', 'PAE63736H', 'PAE@PAE.CC', 'PAE', 'INSURGENTES SUR', '4587452145', '3', '1', '5', '2015-07-28 10:55:20', '5', '', '2015-07-28 10:53:03', '1');
INSERT INTO `sys_sucursales` VALUES ('32', 'PAE', 'PAE2', '04:00:00', '10:52:00', '1', 'PAE SA DE CV', 'PAE63736H', 'PAE@PAE.CC', 'PAE', 'INSURGENTES SUR', '4587452145', '3', '1', '5', '2015-07-28 10:55:31', '5', '', '2015-07-28 10:54:56', '1');
INSERT INTO `sys_sucursales` VALUES ('33', 'flamingos', 'flama', '08:30:00', '18:00:00', '1', 'play juegos', 'sjdhd364533gg', 'play@hotmail.com', 'jesus', 'direccion', '5487985641', '3', '1', '1', null, '0', '', '2015-09-09 11:32:26', '1');
INSERT INTO `sys_sucursales` VALUES ('34', 'flamingos 2', 'flam', '08:30:00', '18:00:00', '1', 'play juegos', 'sjdhd364533gg', 'play@hotmail.com', 'jesus', 'direccion', '5487985641', '3', '1', '1', '2015-09-09 11:50:58', '1', '', '2015-09-09 11:34:16', '1');
INSERT INTO `sys_sucursales` VALUES ('35', 'test', 'test suc', '15:29:00', '15:31:00', '0', '12345643', '234567865', 'test@tyest.com', '456', '', '45', '10', '1', '1', null, '0', '', '2015-11-06 15:30:59', '1');
INSERT INTO `sys_sucursales` VALUES ('36', 'hh', 'jkh', '07:00:00', '17:00:00', '1', 'fsdafsd', 'fsdafsd', 'suc@suc.com', 'fsda', '', '56464564', '3', '2', '1', '2015-12-02 17:29:34', '1', '', '2015-12-02 16:44:03', '0');
INSERT INTO `sys_sucursales` VALUES ('37', 'SECTOR 1', 'SEC 1', '09:00:00', '18:00:00', '1', 'SECTOR UNO SA DE CV', 'SECTORUNO473847', 'SECTORUNO@SECTOR.COM.MX', 'CLAUDIA ROSALES', 'CALLE PABLO LOPEZ 165', '8785468529', '3', '1', '1', null, '0', '', '2015-12-14 10:08:09', '1');
INSERT INTO `sys_sucursales` VALUES ('38', 'SECTOR 2', 'SEC2', '09:00:00', '18:00:00', '1', 'SECTOR 2 SA DE CV', 'SECDOS548755', 'SECTORDOS@SECTOR.COM.MX', 'LOURDES PEREZ', 'ASEQUIA 87', '8587585224', '3', '1', '1', null, '0', '', '2015-12-14 10:21:24', '1');
INSERT INTO `sys_sucursales` VALUES ('39', 'SECTOR 3', 'SEC3', '09:00:00', '18:00:00', '1', 'SECTOR3 SA DE CV', 'SECTRES548755', 'SECTORDOS@SECTOR.COM.MX', 'LOURDES PEREZ', 'ASEQUIA 87', '8587585224', '3', '1', '1', null, '0', '', '2015-12-14 10:22:38', '1');
INSERT INTO `sys_sucursales` VALUES ('40', 'SECTOR 4', 'SEC4', '09:00:00', '18:00:00', '1', 'SUCCUATRO SA DE CV', 'SUC637463', 'SUCURSALCUATRO@SUCURSAL.COM.MX', 'JIMENA PEREZ', 'ACASIAS 34', '8754587458', '3', '1', '1', null, '0', '', '2015-12-14 10:25:09', '1');

-- ----------------------------
-- Table structure for `sys_usuarios`
-- ----------------------------
DROP TABLE IF EXISTS `sys_usuarios`;
CREATE TABLE `sys_usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `id_personal` int(11) NOT NULL,
  `id_clave` int(11) NOT NULL,
  `id_perfil` int(11) DEFAULT NULL,
  `id_pais` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_puesto` int(11) DEFAULT NULL,
  `id_area` int(11) DEFAULT NULL,
  `id_sucursales` varchar(250) DEFAULT NULL,
  `id_menu_n1` varchar(250) DEFAULT NULL,
  `id_menu_n2` varchar(250) DEFAULT NULL,
  `id_menu_n3` varchar(250) DEFAULT NULL,
  `edit_timestamp` datetime DEFAULT NULL,
  `edit_id_usuario` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `id_usuario_reg` int(11) DEFAULT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_usuario`),
  KEY `id_personal` (`id_personal`),
  KEY `id_clave` (`id_clave`),
  KEY `id_nivel` (`id_perfil`),
  KEY `id_empresa` (`id_empresa`),
  KEY `id_sucursal` (`id_sucursal`),
  KEY `id_perfil` (`id_perfil`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sys_usuarios
-- ----------------------------
INSERT INTO `sys_usuarios` VALUES ('1', '1', '1', '1', '1', '1', '1', '1', '1', null, null, null, null, null, null, '2015-12-18 10:52:44', '1', '1');
INSERT INTO `sys_usuarios` VALUES ('2', '1', '1', '2', '1', '1', '1', '1', '1', null, null, null, null, '2015-12-18 10:53:31', '1', '2015-12-18 10:53:51', '1', '1');
INSERT INTO `sys_usuarios` VALUES ('3', '2', '2', '2', '1', '1', '1', '1', '1', null, '', '', '', '2015-12-21 13:07:32', '3', '2015-12-21 13:01:02', '1', '0');
INSERT INTO `sys_usuarios` VALUES ('4', '2', '2', '4', '1', '1', '1', '1', '1', null, '', '', '', '2015-12-21 13:07:32', '3', '2015-12-21 13:07:26', '3', '0');
INSERT INTO `sys_usuarios` VALUES ('5', '2', '2', '8', '1', '1', '1', '1', '1', null, '', '', '', '2015-12-21 13:07:32', '3', '2015-12-21 13:07:32', '3', '1');

-- ----------------------------
-- View structure for `vw_personal`
-- ----------------------------
DROP VIEW IF EXISTS `vw_personal`;
CREATE ALGORITHM=UNDEFINED DEFINER=`omaldonado`@`%` SQL SECURITY DEFINER VIEW `vw_personal` AS select `a`.`id_usuario` AS `id_usuario`,`a`.`id_personal` AS `id_personal`,`a`.`id_clave` AS `id_clave`,`c`.`user` AS `user`,`c`.`pwd` AS `pwd`,`a`.`id_pais` AS `id_pais`,`d`.`pais` AS `pais`,`d`.`dominio` AS `dominio`,`d`.`avatar` AS `avatar_pais`,`a`.`id_empresa` AS `id_empresa`,`e`.`empresa` AS `empresa`,`e`.`razon_social` AS `razon_social`,`e`.`rfc` AS `rfc`,`e`.`direccion` AS `direccion`,`a`.`id_sucursal` AS `id_sucursal`,`f`.`sucursal` AS `sucursal`,`b`.`nombre` AS `nombre`,`b`.`paterno` AS `paterno`,`b`.`materno` AS `materno`,concat(ifnull(`b`.`nombre`,''),' ',ifnull(`b`.`paterno`,''),' ',ifnull(`b`.`materno`,'')) AS `usuario_nombre`,`b`.`telefono` AS `telefono`,`b`.`mail` AS `mail`,`b`.`avatar` AS `avatar_personales`,`a`.`id_perfil` AS `id_perfil`,`g`.`perfil` AS `perfil`,`g`.`id_menu_n1` AS `id_menu_n1`,`g`.`id_menu_n2` AS `id_menu_n2`,`g`.`id_menu_n3` AS `id_menu_n3`,if((`a`.`activo` and `c`.`activo` and `e`.`activo` and `f`.`activo` and `g`.`activo`),1,0) AS `activo` from ((((((`sys_usuarios` `a` left join `sys_personales` `b` on((`a`.`id_personal` = `b`.`id_personal`))) left join `sys_claves` `c` on((`a`.`id_clave` = `c`.`id_clave`))) left join `sys_paises` `d` on((`a`.`id_pais` = `d`.`id_pais`))) left join `sys_empresas` `e` on((`a`.`id_empresa` = `e`.`id_empresa`))) left join `sys_sucursales` `f` on((`a`.`id_sucursal` = `f`.`id_sucursal`))) left join `sys_perfiles` `g` on((`a`.`id_perfil` = `g`.`id_perfil`))) group by `a`.`id_usuario` ;
