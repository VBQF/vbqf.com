DROP TABLE IF EXISTS `#__gcalendar`;

CREATE TABLE IF NOT EXISTS `#__gcalendar` (
  `id` int(11) NOT NULL auto_increment,
  `calendar_id` text NOT NULL,
  `name` text NOT NULL,
  `magic_cookie` text NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` text DEFAULT NULL,
  `color` text NOT NULL,
  `access` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `access_content` tinyint UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY  (`id`)
) ;
