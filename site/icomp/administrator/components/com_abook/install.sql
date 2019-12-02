CREATE TABLE IF NOT EXISTS `#__abauthor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lastname` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) DEFAULT '',
  `alias` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext,
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `#__abbook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `alias` varchar(255) NOT NULL,
  `ideditor` int(11) DEFAULT NULL,
  `price` varchar(255) DEFAULT '0',
  `pag` int(11) DEFAULT '0',
  `pag_index` int(4) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_by_alias` varchar(255) NOT NULL,
  `description` mediumtext,
  `image` varchar(255) DEFAULT NULL,
  `docsfolder` text,
  `file` varchar(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `idlocation` int(10) DEFAULT NULL,
  `idlibrary` int(10) DEFAULT NULL,
  `vote` int(11) DEFAULT '0',
  `numvote` int(11) DEFAULT '0',
  `hits` int(11) DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `catid` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `isbn` varchar(255) DEFAULT NULL,
  `approved` tinyint(1) DEFAULT '0',
  `userid` int(11) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `url_label` varchar(255) DEFAULT NULL,
  `dateinsert` datetime DEFAULT NULL,
  `catalogo` varchar(255) DEFAULT NULL,
  `checked_out` varchar(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` tinyint(3) NOT NULL,
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `metadata` text NOT NULL,
  `language` char(7) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `note` varchar(255) NOT NULL,
  `editedby` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `#__abbookauth` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `idbook` int(11) NOT NULL DEFAULT '0',
  `idauth` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_idbook` (`idbook`),
  KEY `idx_idauth` (`idauth`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `#__abbooktag` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `idbook` int(11) NOT NULL DEFAULT '0',
  `idtag` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_idbook` (`idbook`),
  KEY `idx_idauth` (`idtag`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `#__abcategories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  `path` varchar(255) NOT NULL DEFAULT '',
  `extension` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `note` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(5120) NOT NULL DEFAULT '',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `params` varchar(2048) NOT NULL DEFAULT '',
  `metadesc` varchar(1024) NOT NULL COMMENT 'The meta description for the page.',
  `metakey` varchar(1024) NOT NULL COMMENT 'The meta keywords for the page.',
  `metadata` varchar(2048) NOT NULL COMMENT 'JSON encoded metadata properties.',
  `created_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cat_idx` (`extension`,`published`,`access`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_path` (`path`),
  KEY `idx_left_right` (`lft`,`rgt`),
  KEY `idx_alias` (`alias`),
  KEY `idx_language` (`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

INSERT INTO `#__abcategories` (`id`, `asset_id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `extension`, `title`, `alias`, `note`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `metadesc`, `metakey`, `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`) VALUES
(1, 0, 0, 0, 1, 0, '', 'system', 'ROOT', 'root', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{}', '', '', '', 0, '2009-10-18 16:07:09', 0, '0000-00-00 00:00:00', 0, '*');

CREATE TABLE IF NOT EXISTS `#__abeditor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) NOT NULL,
  `description` mediumtext,
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `#__ablibrary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) NOT NULL,
  `description` mediumtext,
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `#__ablocations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `#__abrating` (
  `book_id` int(11) NOT NULL,
  `rating_sum` int(11) unsigned NOT NULL DEFAULT '0',
  `rating_count` int(11) unsigned NOT NULL DEFAULT '0',
  `lastip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`book_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__abtag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext,
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL,
  `id_taggroup` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `#__abtag_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

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
  PRIMARY KEY (`id`),
  KEY `book_id` (`book_id`,`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
