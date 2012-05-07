-- phpMyAdmin SQL Dump
-- version 3.5.0
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 07-05-2012 a las 02:15:11
-- Versión del servidor: 5.5.23
-- Versión de PHP: 5.4.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `CoreFramework`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acl_permissions`
--

CREATE TABLE IF NOT EXISTS `acl_permissions` (
  `role_id` int(11) unsigned NOT NULL,
  `resource_id` int(11) unsigned NOT NULL,
  `type` enum('Allow','Deny') NOT NULL,
  `action` varchar(32) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `IDX_RESOURCE` (`resource_id`),
  KEY `IDX_ROLE` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acl_resources`
--

CREATE TABLE IF NOT EXISTS `acl_resources` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL,
  `controller` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_UNIQUE` (`module`,`controller`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acl_roles`
--

CREATE TABLE IF NOT EXISTS `acl_roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `core` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_UNIQUE` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `acl_roles`
--

INSERT INTO `acl_roles` (`id`, `name`, `core`, `created`, `modified`) VALUES
(1, 'anonymous', 1, '2012-05-03 00:00:00', '2012-05-03 10:10:51'),
(2, 'administrator', 1, '2012-05-03 00:00:00', '2012-05-03 10:11:48')

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acl_roles_inheritance`
--

CREATE TABLE IF NOT EXISTS `acl_roles_inheritance` (
  `role_id` int(10) unsigned NOT NULL,
  `member_id` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`,`member_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` char(32) NOT NULL,
  `salt` char(32) NOT NULL,
  `email` tinytext NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `last_access` datetime DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `IDX_ACL_ROLE` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `salt`, `email`, `role_id`, `active`, `last_access`, `created`, `modified`) VALUES
(1, 'admin', 'fafb849b77f22008f5c02acc5517c6c9', '21232f297a57a5a743894a0e4a801fc3', 'youremail@server.com', 2, 1, '2012-04-14 14:19:15', '2012-04-13 00:00:00', '2012-05-04 03:14:14');

-- --------------------------------------------------------
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `acl_permissions`
--
ALTER TABLE `acl_permissions`
  ADD CONSTRAINT `acl_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `acl_roles` (`id`),
  ADD CONSTRAINT `acl_permissions_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `acl_resources` (`id`);

--
-- Filtros para la tabla `acl_roles_inheritance`
--
ALTER TABLE `acl_roles_inheritance`
  ADD CONSTRAINT `acl_roles_inheritance_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `acl_resources` (`id`),
  ADD CONSTRAINT `acl_roles_inheritance_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `acl_roles` (`id`);

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `acl_roles` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
