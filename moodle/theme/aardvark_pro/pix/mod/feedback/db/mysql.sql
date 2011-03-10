
#
# Tabellenstruktur für Tabelle `prefix_feedback`
#

CREATE TABLE `prefix_feedback` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `summary` text NOT NULL,
  `anonymous` int(1) NOT NULL default '1',
  `email_notification` int(1) NOT NULL default '1',
  `multiple_submit` int(1) NOT NULL default '0',
  `timemodified` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `course` (`course`)
) TYPE=MyISAM COMMENT='feedback modul';

#
# Tabellenstruktur für Tabelle `prefix_feedback_template`
#

CREATE TABLE `prefix_feedback_template` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) NOT NULL default '0',
  `public` int(1) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `course` (`course`)
) TYPE=MyISAM COMMENT='templates of feedbackstructures';

#
# Tabellenstruktur für Tabelle `prefix_feedback_item`
#

CREATE TABLE `prefix_feedback_item` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `feedback` int(10) NOT NULL default '0',
  `template` int(10) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `presentation` text NOT NULL,
  `typ` varchar(255) NOT NULL default '0',
  `hasvalue` int(1) NOT NULL default '0',
  `position` int(3) NOT NULL default '0',
  `required` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `feedback` (`feedback`),
  KEY `template` (`template`)
) TYPE=MyISAM COMMENT='feedback items';

#
# Tabellenstruktur für Tabelle `prefix_feedback_completed`
#

CREATE TABLE `prefix_feedback_completed` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `feedback` int(10) NOT NULL default '0',
  `userid` int(10) NOT NULL default '0',
  `timemodified` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `feedback` (`feedback`),
  KEY `userid` (`userid`)
) TYPE=MyISAM COMMENT='filled out feedbacks';

#
# Tabellenstruktur für Tabelle `prefix_feedback_value`
#

CREATE TABLE `prefix_feedback_value` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `item` int(10) NOT NULL default '0',
  `completed` int(10) NOT NULL default '0',
  `tmp_completed` int(10) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `item` (`item`),
  KEY `completed` (`completed`)
) TYPE=MyISAM COMMENT='feedback values';

#
# Tabellenstruktur für Tabelle `prefix_feedback_tracking`
#

CREATE TABLE `prefix_feedback_tracking` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) NOT NULL default '0',
  `feedback` int(10) NOT NULL default '0',
  `completed` int(10) NOT NULL default '0',
  `tmp_completed` int(10) NOT NULL default '0',
  `count` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`),
  KEY `feedback` (`feedback`),
  KEY `completed` (`completed`)
) TYPE=MyISAM COMMENT='feedback trackingdata';
