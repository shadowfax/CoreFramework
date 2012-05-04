-- Base de datos: `iformentera`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acl_roles`
--

CREATE TABLE IF NOT EXISTS `acl_roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `core` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_UNIQUE` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `acl_roles`
--

INSERT INTO `acl_roles` (`id`, `name`, `core`, `created`, `modified`) VALUES
(1, 'anonymous', 1, '2012-05-03 00:00:00', '2012-05-03 10:10:51'),
(2, 'administrator', 1, '2012-05-03 00:00:00', '2012-05-03 10:11:48'),
(3, 'traveler', 0, '2012-05-04 00:00:00', '2012-05-03 22:14:34'),
(4, 'profesional', 0, '2012-05-04 00:00:00', '2012-05-03 22:14:34');

--
-- Disparadores `acl_roles`
--
DROP TRIGGER IF EXISTS `AclRoles_OnInsert`;
DELIMITER //
CREATE TRIGGER `AclRoles_OnInsert` BEFORE INSERT ON `acl_roles`
 FOR EACH ROW SET NEW.created = IFNULL(NEW.created, NOW())
//
DELIMITER ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acl_role_inheritance`
--

CREATE TABLE IF NOT EXISTS `acl_role_inheritance` (
  `role_id` int(11) unsigned NOT NULL,
  `parent_id` int(11) unsigned NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`,`parent_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `acl_role_inheritance`
--

INSERT INTO `acl_role_inheritance` (`role_id`, `parent_id`, `created`, `modified`) VALUES
(2, 3, '2012-05-04 00:00:00', '2012-05-03 23:41:33'),
(2, 4, '2012-05-04 00:00:00', '2012-05-03 22:16:23'),
(3, 1, '2012-05-04 00:00:00', '2012-05-03 22:14:58'),
(4, 3, '2012-05-04 00:00:00', '2012-05-03 22:16:34');

--
-- Disparadores `acl_role_inheritance`
--
DROP TRIGGER IF EXISTS `AclRoleInheritance_OnInsert`;
DELIMITER //
CREATE TRIGGER `AclRoleInheritance_OnInsert` BEFORE INSERT ON `acl_role_inheritance`
 FOR EACH ROW SET NEW.created = IFNULL(NEW.created, NOW())
//
DELIMITER ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `acl_role_inheritance`
--
ALTER TABLE `acl_role_inheritance`
  ADD CONSTRAINT `acl_role_inheritance_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `acl_roles` (`id`),
  ADD CONSTRAINT `acl_role_inheritance_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `acl_roles` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acl_resources`
--

CREATE TABLE IF NOT EXISTS `acl_resources` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL,
  `controller` varchar(50) NOT NULL,
  `action` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_UNIQUE` (`module`,`controller`,`action`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Volcado de datos para la tabla `acl_resources`
--

INSERT INTO `acl_resources` (`id`, `module`, `controller`, `action`, `created`, `modified`) VALUES
(1, 'default', 'error', NULL, '2012-05-04 00:00:00', '2012-05-04 01:59:01'),
(2, 'default', 'index', NULL, '2012-05-04 00:00:00', '2012-05-04 02:01:53'),
(3, 'default', 'auth', NULL, '2012-05-04 00:00:00', '2012-05-04 02:01:56'),
(4, 'default', 'formentera', NULL, '2012-05-04 00:00:00', '2012-05-04 02:02:00'),
(5, 'default', 'guide', NULL, '2012-05-04 00:00:00', '2012-05-04 02:02:04'),
(6, 'default', 'beach', NULL, '2012-05-04 00:00:00', '2012-05-04 02:02:34'),
(7, 'default', 'auth', 'logout', '2012-05-04 00:00:00', '2012-05-04 02:06:14'),
(8, 'api', 'synchronize', NULL, '2012-05-04 00:00:00', '2012-05-04 03:27:05'),
(9, 'default', 'guide', 'edit', '2012-05-04 00:00:00', '2012-05-04 03:37:38'),
(10, 'default', 'guide', 'delete', '2012-05-04 00:00:00', '2012-05-04 03:52:58');

--
-- Disparadores `acl_resources`
--
DROP TRIGGER IF EXISTS `AclResources_OnInsert`;
DELIMITER //
CREATE TRIGGER `AclResources_OnInsert` BEFORE INSERT ON `acl_resources`
 FOR EACH ROW SET NEW.created = IFNULL(NEW.created, NOW())
//
DELIMITER ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acl_rights`
--

CREATE TABLE IF NOT EXISTS `acl_rights` (
  `role_id` int(11) unsigned NOT NULL,
  `resource_id` int(11) unsigned NOT NULL,
  `grant` tinyint(1) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`,`resource_id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `acl_rights`
--

INSERT INTO `acl_rights` (`role_id`, `resource_id`, `grant`, `created`, `modified`) VALUES
(1, 1, 1, '2012-05-04 00:00:00', '2012-05-04 02:03:14'),
(1, 2, 1, '2012-05-04 00:00:00', '2012-05-04 02:04:56'),
(1, 3, 1, '2012-05-04 00:00:00', '2012-05-04 02:03:32'),
(1, 4, 1, '2012-05-04 00:00:00', '2012-05-04 02:04:00'),
(1, 5, 1, '2012-05-04 00:00:00', '2012-05-04 02:04:09'),
(1, 6, 1, '2012-05-04 00:00:00', '2012-05-04 02:04:21'),
(1, 7, 0, '2012-05-04 00:00:00', '2012-05-04 02:07:27'),
(1, 8, 1, '2012-05-04 00:00:00', '2012-05-04 03:27:20'),
(1, 9, 0, '2012-05-04 00:00:00', '2012-05-04 03:39:04'),
(1, 10, 0, '2012-05-04 00:00:00', '2012-05-04 03:53:51'),
(2, 9, 1, '2012-05-04 00:00:00', '2012-05-04 03:45:49'),
(2, 10, 1, '2012-05-04 00:00:00', '2012-05-04 03:53:51'),
(3, 7, 1, '2012-05-04 00:00:00', '2012-05-04 02:08:27'),
(4, 9, 1, '2012-05-04 00:00:00', '2012-05-04 03:46:00');

--
-- Disparadores `acl_rights`
--
DROP TRIGGER IF EXISTS `AclRights_OnInsert`;
DELIMITER //
CREATE TRIGGER `AclRights_OnInsert` BEFORE INSERT ON `acl_rights`
 FOR EACH ROW SET NEW.created = IFNULL(NEW.created, NOW())
//
DELIMITER ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `acl_rights`
--
ALTER TABLE `acl_rights`
  ADD CONSTRAINT `acl_rights_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `acl_resources` (`id`),
  ADD CONSTRAINT `acl_rights_ibfk_3` FOREIGN KEY (`role_id`) REFERENCES `acl_roles` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

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
  `created` datetime DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `IDX_ACL_ROLE` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `salt`, `email`, `role_id`, `active`, `last_access`, `created`, `modified`) VALUES
(1, 'admin', 'fafb849b77f22008f5c02acc5517c6c9', '21232f297a57a5a743894a0e4a801fc3', 'unaputacuenta@hotmail.com', 2, 1, '2012-04-14 14:19:15', '2012-04-13 00:00:00', '2012-05-04 03:14:14');

--
-- Disparadores `users`
--
DROP TRIGGER IF EXISTS `Users_OnInsert`;
DELIMITER //
CREATE TRIGGER `Users_OnInsert` BEFORE INSERT ON `users`
 FOR EACH ROW SET NEW.created = IFNULL(NEW.created, NOW())
//
DELIMITER ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `acl_roles` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
