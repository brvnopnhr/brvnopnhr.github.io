CREATE TABLE IF NOT EXISTS `#__ablend` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(5) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `custom_user_name` varchar(255) DEFAULT NULL,
  `custom_user_email` varchar(255) DEFAULT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `admin_id` int(11) DEFAULT NULL,
  `dateinsert` datetime DEFAULT '0000-00-00 00:00:00',
  `checked_out` varchar(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `note` varchar(255) NOT NULL,
  `lend_out` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lend_in` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

UPDATE `#__abbook` SET qty =1 WHERE (qty =0 OR qty IS NULL );
