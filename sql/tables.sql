--
-- Base tables used by Chassis framework PHP libraries
--

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Table structure for table `core_logins`
--
CREATE TABLE IF NOT EXISTS `core_logins` (
  `uid` bigint(20) NOT NULL,
  `ns` char(64) COLLATE utf8_unicode_ci NOT NULL,
  `stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `uid` (`uid`),
  KEY `uid_2` (`uid`,`ns`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `core_sessions`
--
CREATE TABLE IF NOT EXISTS `core_sessions` (
  `sid` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `clid` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `uid` bigint(20) NOT NULL,
  `valid` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `sid` (`sid`),
  KEY `clid` (`clid`,`uid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `core_settings`
--
CREATE TABLE IF NOT EXISTS `core_settings` (
  `scope` enum('G','U','S') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'U',
  `id` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `ns` char(64) COLLATE utf8_unicode_ci NOT NULL,
  `key` char(64) COLLATE utf8_unicode_ci NOT NULL,
  `value` tinytext COLLATE utf8_unicode_ci NOT NULL,
  KEY `uid` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `core_users`
--
CREATE TABLE IF NOT EXISTS `core_users` (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `passwd` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `check` (`login`,`passwd`,`enabled`),
  KEY `passwd` (`passwd`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=28 ;

--
-- Table structure for table `core_autologin`
--

CREATE TABLE IF NOT EXISTS `core_autologin` (
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `clid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `uid` bigint(20) NOT NULL,
  `valid` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Constraints for dumped tables
--


--
-- Constraints for table `core_logins`
--
ALTER TABLE `core_logins`
  ADD CONSTRAINT `core_logins_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `core_users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `core_sessions`
--
ALTER TABLE `core_sessions`
  ADD CONSTRAINT `core_sessions_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `core_users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `core_autologin`
--
ALTER TABLE `core_autologin`
  ADD CONSTRAINT `core_autologin_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `core_users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;