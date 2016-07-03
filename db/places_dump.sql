
# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `role` varchar(50) NOT NULL default 'member',
  `first_name` varchar(50) default NULL,
  `last_name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `users` (`id`, `date_created`, `date_updated`, `username`, `password`, `role`, `first_name`, `last_name`) VALUES(1, '2007-02-14 00:00:00', '2007-02-14 00:00:00', 'rob', '7e09c9d3e96378bf549fc283fd6e1e5b7014cc33', 'member', 'Rob', 'Allen');
INSERT INTO `users` (`id`, `date_created`, `date_updated`, `username`, `password`, `role`, `first_name`, `last_name`) VALUES(2, '2007-02-14 00:00:00', '2007-02-14 00:00:00', 'nick', '75ef9faee755c70589550b513ad881e5a603182c', 'member', 'Nick', 'Lo');
INSERT INTO `users` (`id`, `date_created`, `date_updated`, `username`, `password`, `role`, `first_name`, `last_name`) VALUES(3, '2007-02-14 00:00:00', '2007-02-14 00:00:00', 'charlie', 'd8cd10b920dcbdb5163ca0185e402357bc27c265', 'member', 'Charlie', 'Brown');
INSERT INTO `users` (`id`, `date_created`, `date_updated`, `username`, `password`, `role`, `first_name`, `last_name`) VALUES(4, '2007-02-14 00:00:00', '2007-02-14 00:00:00', 'lucy', '474e97d07b83ea9b34d1ec399840354182f3b6c1', 'member', 'Lucy', 'van Pelt');


# Dump of table articles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `articles`;

CREATE TABLE `articles` (
  `id` int(11) NOT NULL auto_increment,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `body` mediumtext NOT NULL,
  `rating` int(11) default NULL,
  `creator` int(11) default NULL,
  `keywords` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO `articles` (`id`,`date_created`,`date_updated`,`title`,`body`,`rating`,`creator`,`keywords`) VALUES ('1','2007-11-07 00:00:00','2007-11-07 00:00:00','A day out at London zoo','<p>We had a great day out at the zoo, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p>\n\r<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum.</p>',NULL,NULL,'edinburgh,zoo');


# Dump of table reviews
# ------------------------------------------------------------

DROP TABLE IF EXISTS `reviews`;

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL auto_increment,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `created_by` int(11) default '-1',
  `place_id` int(11) NOT NULL,
  `body` mediumtext NOT NULL,
  `rating` int(11) default NULL,
  `helpful_yes` int(11) NOT NULL default '0',
  `helpful_total` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

INSERT INTO `reviews` (`id`,`date_created`,`date_updated`,`created_by`,`place_id`,`body`,`rating`,`helpful_yes`,`helpful_total`) VALUES ('1','2007-02-14 00:00:00','2007-02-14 00:00:00','1','1','The facilities here are really good. All the family enjoyed it','4','3','6');
INSERT INTO `reviews` (`id`,`date_created`,`date_updated`,`created_by`,`place_id`,`body`,`rating`,`helpful_yes`,`helpful_total`) VALUES ('2','2007-02-14 00:00:00','2007-02-14 00:00:00','2','1','Good day out, but not so many big animals now.','2','4','4');
INSERT INTO `reviews` (`id`,`date_created`,`date_updated`,`created_by`,`place_id`,`body`,`rating`,`helpful_yes`,`helpful_total`) VALUES ('3','2007-02-14 00:00:00','2007-02-14 00:00:00','3','1','Excellent food in the cafeteria. Even my 2 year old ate her lunch!','4','2','5');
INSERT INTO `reviews` (`id`,`date_created`,`date_updated`,`created_by`,`place_id`,`body`,`rating`,`helpful_yes`,`helpful_total`) VALUES ('4','2007-02-14 00:00:00','2007-02-14 00:00:00','2','2','Good for teenagers!','2','5','6');
INSERT INTO `reviews` (`id`,`date_created`,`date_updated`,`created_by`,`place_id`,`body`,`rating`,`helpful_yes`,`helpful_total`) VALUES ('5','2007-02-14 00:00:00','2007-02-14 00:00:00','3','2','A great family day out, but lots of queues!','2','7','10');
INSERT INTO `reviews` (`id`,`date_created`,`date_updated`,`created_by`,`place_id`,`body`,`rating`,`helpful_yes`,`helpful_total`) VALUES ('6','2007-02-14 00:00:00','2007-02-14 00:00:00','1','2','A fun day was had by our family!','2','8','12');
INSERT INTO `reviews` (`id`,`date_created`,`date_updated`,`created_by`,`place_id`,`body`,`rating`,`helpful_yes`,`helpful_total`) VALUES ('7','2007-02-14 00:00:00','2007-02-14 00:00:00','1','3','Our children enjoyed learning some of the history!','3','23','24');


# Dump of table category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table article_to_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `article_to_category`;

CREATE TABLE `article_to_category` (
  `id` int(11) NOT NULL auto_increment,
  `article_id` int(11) NOT NULL default '0',
  `category_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table places
# ------------------------------------------------------------

DROP TABLE IF EXISTS `places`;

CREATE TABLE `places` (
  `id` int(11) NOT NULL auto_increment,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `created_by` int(11) default '-1',
  `name` varchar(100) NOT NULL,
  `address1` varchar(100) default NULL,
  `address2` varchar(100) default NULL,
  `town` varchar(75) default NULL,
  `county` varchar(75) default NULL,
  `postcode` varchar(30) default NULL,
  `country` varchar(75) default NULL,
  `information` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO `places` (`id`,`date_created`,`date_updated`,`created_by`,`name`,`address1`,`address2`,`town`,`county`,`postcode`,`country`,`information`) VALUES ('1','2007-02-14 00:00:00','2007-02-14 00:00:00','1','London Zoo','Regent\'s Park',NULL,'London','','NW1 4RY',NULL,NULL);
INSERT INTO `places` (`id`,`date_created`,`date_updated`,`created_by`,`name`,`address1`,`address2`,`town`,`county`,`postcode`,`country`,`information`) VALUES ('2','2007-02-14 00:00:00','2007-02-14 00:00:00','1','Alton Towers','Regent\'s Park',NULL,'Alton','Staffordshire','ST10 4DB',NULL,NULL);
INSERT INTO `places` (`id`,`date_created`,`date_updated`,`created_by`,`name`,`address1`,`address2`,`town`,`county`,`postcode`,`country`,`information`) VALUES ('3','2007-02-14 00:00:00','2007-02-14 00:00:00','2','Coughton Court','',NULL,'Alcester','Warwickshire','B49 5JA',NULL,NULL);


