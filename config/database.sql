-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************

-- 
-- Table `tl_news`
-- 

CREATE TABLE `tl_news` (
  `news_slideshow` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`),
  KEY `alias` (`alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `tl_module` (
  `showinfobox` char(1) NOT NULL default '1',
  `showmenupicture` char(1) NOT NULL default '0',
  `duration` int(10) unsigned NOT NULL default '800',
  `autostart` char(1) NOT NULL default '1',
  `intervall` int(10) unsigned NOT NULL default '4000',
  `transition` varchar(32) NOT NULL default 'fade',
  `showmenupicturesize` varchar(64) NOT NULL default '',
  `showmenupicturesimagemargin` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;