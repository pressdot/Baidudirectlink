DROP TABLE IF EXISTS `baidupan_user`;
CREATE TABLE `baidupan_user` (
`id` int(11) NOT NULL auto_increment,
`user` varchar(32) DEFAULT NULL,
`pwd` varchar(32) DEFAULT NULL,
`bduss` varchar(255) DEFAULT NULL,
`active` int(1) NOT NULL DEFAULT '1',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000;

DROP TABLE IF EXISTS `baidupan_share`;
CREATE TABLE `baidupan_share` (
`id` int(11) NOT NULL auto_increment,
`hash` varchar(32) NOT NULL,
`uid` int(11) NOT NULL,
`path` text NOT NULL,
`folder` int(1) NOT NULL DEFAULT '0',
`type` varchar(10) NOT NULL,
`time` datetime DEFAULT NULL,
`status` int(1) NOT NULL DEFAULT '0',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;