#
# @package      hubzero-cms-joomla
# @file         installation/sql/mysql/hubzero.sql
# @author       Nicholas J. Kisseberth <nkissebe@purdue.edu>
# @copyright    Copyright (c) 2010-2011 Purdue University. All rights reserved.
# @license      http://www.gnu.org/licenses/gpl2.html GPLv2
#
# Copyright (c) 2010-2011 Purdue University
# All rights reserved.
#
# This file is free software: you can redistribute it and/or modify it
# under the terms of the GNU General Public License as published by the
# Free Software Foundation, either version 2 of the License, or (at your
# option) any later version.
#
# This file is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
# HUBzero is a registered trademark of Purdue University.
#
# This file incorporates work covered by the following copyright and  
# permission notice:  
#
#    $Id: joomla.sql 12384 2009-06-28 03:02:34Z ian $
#    @copyright      Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
#    @license                GNU/GPL, see LICENSE.php
#    Joomla! is free software. This version may have been modified pursuant
#    to the GNU General Public License, and as distributed it includes or
#    is derivative of works licensed under the GNU General Public License or
#    other free or open source software licenses.
#    See COPYRIGHT.php for copyright notices and details.
#

# --------------------------------------------------------

#
# Table structure for table `#__banner`
#

CREATE TABLE `#__banner` (
  `bid` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL default '0',
  `type` varchar(30) NOT NULL default 'banner',
  `name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `imptotal` int(11) NOT NULL default '0',
  `impmade` int(11) NOT NULL default '0',
  `clicks` int(11) NOT NULL default '0',
  `imageurl` varchar(100) NOT NULL default '',
  `clickurl` varchar(200) NOT NULL default '',
  `date` datetime default NULL,
  `showBanner` tinyint(1) NOT NULL default '0',
  `checked_out` tinyint(1) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `editor` varchar(50) default NULL,
  `custombannercode` text,
  `catid` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `description` TEXT NOT NULL DEFAULT '',
  `sticky` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `ordering` INTEGER NOT NULL DEFAULT 0,
  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
  `tags` TEXT NOT NULL DEFAULT '',
  `params` TEXT NOT NULL DEFAULT '',
  PRIMARY KEY  (`bid`),
  KEY `viewbanner` (`showBanner`),
  INDEX `idx_banner_catid`(`catid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__bannerclient`
#

CREATE TABLE `#__bannerclient` (
  `cid` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `contact` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `extrainfo` text NOT NULL,
  `checked_out` tinyint(1) NOT NULL default '0',
  `checked_out_time` time default NULL,
  `editor` varchar(50) default NULL,
  PRIMARY KEY  (`cid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__bannertrack`
#

CREATE TABLE  `#__bannertrack` (
  `track_date` date NOT NULL,
  `track_type` int(10) unsigned NOT NULL,
  `banner_id` int(10) unsigned NOT NULL
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__categories`
#

CREATE TABLE `#__categories` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default 0,
  `title` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `section` varchar(50) NOT NULL default '',
  `image_position` varchar(30) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `editor` varchar(50) default NULL,
  `ordering` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cat_idx` (`section`,`published`,`access`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__components`
#

CREATE TABLE `#__components` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `menuid` int(11) unsigned NOT NULL default '0',
  `parent` int(11) unsigned NOT NULL default '0',
  `admin_menu_link` varchar(255) NOT NULL default '',
  `admin_menu_alt` varchar(255) NOT NULL default '',
  `option` varchar(50) NOT NULL default '',
  `ordering` int(11) NOT NULL default '0',
  `admin_menu_img` varchar(255) NOT NULL default '',
  `iscore` tinyint(4) NOT NULL default '0',
  `params` text NOT NULL,
  `enabled` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `parent_option` (`parent`, `option`(32))
) TYPE=MyISAM CHARACTER SET `utf8`;

#
# Dumping data for table `#__components`
#

INSERT INTO `#__components` VALUES (1, 'Banners', '', 0, 0, '', 'Banner Management', 'com_banners', 0, 'js/ThemeOffice/component.png', 0, 'track_impressions=0\ntrack_clicks=0\ntag_prefix=\n\n', 1);
INSERT INTO `#__components` VALUES (2, 'Banners', '', 0, 1, 'option=com_banners', 'Active Banners', 'com_banners', 1, 'js/ThemeOffice/edit.png', 0, '', 1);
INSERT INTO `#__components` VALUES (3, 'Clients', '', 0, 1, 'option=com_banners&c=client', 'Manage Clients', 'com_banners', 2, 'js/ThemeOffice/categories.png', 0, '', 1);
INSERT INTO `#__components` VALUES (4, 'Web Links', 'option=com_weblinks', 0, 0, '', 'Manage Weblinks', 'com_weblinks', 0, 'js/ThemeOffice/component.png', 0, 'show_comp_description=1\ncomp_description=\nshow_link_hits=1\nshow_link_description=1\nshow_other_cats=1\nshow_headings=1\nshow_page_title=1\nlink_target=0\nlink_icons=\n\n', 1);
INSERT INTO `#__components` VALUES (5, 'Links', '', 0, 4, 'option=com_weblinks', 'View existing weblinks', 'com_weblinks', 1, 'js/ThemeOffice/edit.png', 0, '', 1);
INSERT INTO `#__components` VALUES (6, 'Categories', '', 0, 4, 'option=com_categories&section=com_weblinks', 'Manage weblink categories', '', 2, 'js/ThemeOffice/categories.png', 0, '', 1);
INSERT INTO `#__components` VALUES (7, 'Contacts', 'option=com_contact', 0, 0, '', 'Edit contact details', 'com_contact', 0, 'js/ThemeOffice/component.png', 1, 'contact_icons=0\nicon_address=\nicon_email=\nicon_telephone=\nicon_fax=\nicon_misc=\nshow_headings=1\nshow_position=1\nshow_email=0\nshow_telephone=1\nshow_mobile=1\nshow_fax=1\nbannedEmail=\nbannedSubject=\nbannedText=\nsession=1\ncustomReply=0\n\n', 1);
INSERT INTO `#__components` VALUES (8, 'Contacts', '', 0, 7, 'option=com_contact', 'Edit contact details', 'com_contact', 0, 'js/ThemeOffice/edit.png', 1, '', 1);
INSERT INTO `#__components` VALUES (9, 'Categories', '', 0, 7, 'option=com_categories&section=com_contact_details', 'Manage contact categories', '', 2, 'js/ThemeOffice/categories.png', 1, 'contact_icons=0\nicon_address=\nicon_email=\nicon_telephone=\nicon_fax=\nicon_misc=\nshow_headings=1\nshow_position=1\nshow_email=0\nshow_telephone=1\nshow_mobile=1\nshow_fax=1\nbannedEmail=\nbannedSubject=\nbannedText=\nsession=1\ncustomReply=0\n\n', 1);
INSERT INTO `#__components` VALUES (10, 'Polls', 'option=com_poll', 0, 0, 'option=com_poll', 'Manage Polls', 'com_poll', 0, 'js/ThemeOffice/component.png', 0, '', 1);
INSERT INTO `#__components` VALUES (11, 'News Feeds', 'option=com_newsfeeds', 0, 0, '', 'News Feeds Management', 'com_newsfeeds', 0, 'js/ThemeOffice/component.png', 0, '', 1);
INSERT INTO `#__components` VALUES (12, 'Feeds', '', 0, 11, 'option=com_newsfeeds', 'Manage News Feeds', 'com_newsfeeds', 1, 'js/ThemeOffice/edit.png', 0, 'show_headings=1\nshow_name=1\nshow_articles=1\nshow_link=1\nshow_cat_description=1\nshow_cat_items=1\nshow_feed_image=1\nshow_feed_description=1\nshow_item_description=1\nfeed_word_count=0\n\n', 1);
INSERT INTO `#__components` VALUES (13, 'Categories', '', 0, 11, 'option=com_categories&section=com_newsfeeds', 'Manage Categories', '', 2, 'js/ThemeOffice/categories.png', 0, '', 1);
INSERT INTO `#__components` VALUES (14, 'User', 'option=com_user', 0, 0, '', '', 'com_user', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (15, 'Search', 'option=com_search', 0, 0, 'option=com_search', 'Search Statistics', 'com_search', 0, 'js/ThemeOffice/component.png', 1, 'enabled=0\n\n', 1);
INSERT INTO `#__components` VALUES (16, 'Categories', '', 0, 1, 'option=com_categories&section=com_banner', 'Categories', '', 3, '', 1, '', 1);
INSERT INTO `#__components` VALUES (17, 'Wrapper', 'option=com_wrapper', 0, 0, '', 'Wrapper', 'com_wrapper', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (18, 'Mail To', '', 0, 0, '', '', 'com_mailto', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (19, 'Media Manager', '', 0, 0, 'option=com_media', 'Media Manager', 'com_media', 0, '', 1, 'upload_extensions=bmp,csv,doc,epg,gif,ico,jpg,odg,odp,ods,odt,pdf,png,ppt,swf,txt,xcf,xls,BMP,CSV,DOC,EPG,GIF,ICO,JPG,ODG,ODP,ODS,ODT,PDF,PNG,PPT,SWF,TXT,XCF,XLS\nupload_maxsize=10000000\nfile_path=images\nimage_path=images/stories\nrestrict_uploads=1\ncheck_mime=1\nimage_extensions=bmp,gif,jpg,png\nignore_extensions=\nupload_mime=image/jpeg,image/gif,image/png,image/bmp,application/x-shockwave-flash,application/msword,application/excel,application/pdf,application/powerpoint,text/plain,application/x-zip\nupload_mime_illegal=text/html', 1);
INSERT INTO `#__components` VALUES (20, 'Articles', 'option=com_content', 0, 0, '', '', 'com_content', 0, '', 1, 'show_noauth=0\nshow_title=1\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=1\nshow_create_date=1\nshow_modify_date=1\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=1\nshow_pdf_icon=1\nshow_print_icon=1\nshow_email_icon=1\nshow_hits=1\nfeed_summary=0\n\n', 1);
INSERT INTO `#__components` VALUES (21, 'Configuration Manager', '', 0, 0, '', 'Configuration', 'com_config', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (22, 'Installation Manager', '', 0, 0, '', 'Installer', 'com_installer', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (23, 'Language Manager', '', 0, 0, '', 'Languages', 'com_languages', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (24, 'Mass mail', '', 0, 0, '', 'Mass Mail', 'com_massmail', 0, '', 1, 'mailSubjectPrefix=\nmailBodySuffix=\n\n', 1);
INSERT INTO `#__components` VALUES (25, 'Menu Editor', '', 0, 0, '', 'Menu Editor', 'com_menus', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (27, 'Messaging', '', 0, 0, '', 'Messages', 'com_messages', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (28, 'Modules Manager', '', 0, 0, '', 'Modules', 'com_modules', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (29, 'Plugin Manager', '', 0, 0, '', 'Plugins', 'com_plugins', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (30, 'Template Manager', '', 0, 0, '', 'Templates', 'com_templates', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (31, 'User Manager', '', 0, 0, '', 'Users', 'com_users', 0, '', 1, 'allowUserRegistration=1\nnew_usertype=Registered\nuseractivation=1\nfrontend_userparams=1\n\n', 1);
INSERT INTO `#__components` VALUES (32, 'Cache Manager', '', 0, 0, '', 'Cache', 'com_cache', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (33, 'Control Panel', '', 0, 0, '', 'Control Panel', 'com_cpanel', 0, '', 1, '', 1);

INSERT INTO `#__components` VALUES (34,'Manage Points','option=com_userpoints',0,0,'option=com_userpoints','Manage Points','com_userpoints',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (35,'Summary','',0,34,'option=com_userpoints&task=summary','Summary','com_userpoints',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (36,'Answers','option=com_answers',0,0,'option=com_answers','Answers','com_answers',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (37,'Contribute','option=com_contribute',0,0,'','','com_contribute',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (38,'Events','option=com_events',0,0,'option=com_events','Events','com_events',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (39,'Manage Events','',0,38,'option=com_events','Manage Events','com_events',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (40,'Manage Events Categories','',0,38,'option=com_events&task=cats','Manage Events Categories','com_events',1,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (41,'Edit Config','',0,38,'option=com_events&task=configure','Edit Config','com_events',2,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (42,'Groups','option=com_groups',0,0,'option=com_groups','Groups','com_groups',0,'js/ThemeOffice/component.png',0,'ldapGroupMirror=1\nldapGroupLegacy=1\nuploadpath=/site/groups\niconpath=/components/com_groups/assets/img/icons\njoin_policy=0\nprivacy=0\nauto_approve=1\n\n',1);
INSERT INTO `#__components` VALUES (43,'Topics','option=com_topics',0,0,'','','com_topics',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (44,'MyHUB','option=com_myhub',0,0,'option=com_myhub','My Hub','com_myhub',0,'js/ThemeOffice/component.png',0,'allow_customization=0\nposition=myhub\nstatic=\n\n',1);
INSERT INTO `#__components` VALUES (45,'Usage','option=com_usage',0,0,'option=com_usage','Usage','com_usage',0,'js/ThemeOffice/component.png',0,'statsDBDriver=mysql\nstatsDBHost=localhost\nstatsDBPort=\nstatsDBUsername=\nstatsDBPassword=\nstatsDBDatabase=\nstatsDBPrefix=\nmapsApiKey=\nstats_path=/site/stats\nmaps_path=/site/stats/maps\nplots_path=/site/stats/plots\ncharts_path=/site/stats/plots\n\n',1);
INSERT INTO `#__components` VALUES (46,'Citations','option=com_citations',0,0,'','','com_citations',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (47,'Citations Manager','',0,46,'option=com_citations','Citations Manager','com_citations',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (48,'Feedback','option=com_feedback',0,0,'option=com_feedback','Feedback','com_feedback',0,'js/ThemeOffice/component.png',0,'defaultpic=/components/com_feedback/images/contributor.gif\nuploadpath=/site/quotes\nmaxAllowed=40000000\nfile_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif\nblacklist=\nbadwords=viagra, pharmacy, xanax, phentermine, dating, ringtones, tramadol, hydrocodone, levitra, ambien, vicodin, fioricet, diazepam, cash advance, free online, online gambling, online prescriptions, debt consolidation, baccarat, loan, slots, credit, mortgage, casino, slot, texas holdem, teen nude, orgasm, gay, fuck, crap, shit, asshole, cunt, fucker, fuckers, motherfucker, fucking, milf, cocksucker, porno, videosex, sperm, hentai, internet gambling, kasino, kasinos, poker, lottery, texas hold em, texas holdem, fisting\n\n',1);
INSERT INTO `#__components` VALUES (49,'Manage Success Stories','',0,48,'option=com_feedback','Manage Success Stories','com_feedback',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (50,'Hub','option=com_hub',0,0,'option=com_hub','Hub','com_hub',0,'js/ThemeOffice/component.png',0,'registrationUsername=RRUU\nregistrationPassword=RRUU\nregistrationConfirmPassword=RRUU\nregistrationFullname=RRUU\nregistrationEmail=RRUU\nregistrationConfirmEmail=RRUU\nregistrationURL=HOHO\nregistrationPhone=HOHO\nregistrationEmployment=HOHO\nregistrationOrganization=HOHO\nregistrationCitizenship=HHHH\nregistrationResidency=HHHH\nregistrationSex=HHHH\nregistrationDisability=HHHH\nregistrationHispanic=HHHH\nregistrationRace=HHHH\nregistrationInterests=HOHO\nregistrationReason=HOHO\nregistrationOptIn=HOHO\nregistrationTOU=RHHH',1);
INSERT INTO `#__components` VALUES (51,'Site','',0,50,'option=com_hub&task=site','Site','com_hub',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (52,'Registration','',0,50,'option=com_hub&task=registration','Registration','com_hub',1,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (53,'Databases','',0,50,'option=com_hub&task=databases','Databases','com_hub',2,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (54,'Misc. Settings','',0,50,'option=com_hub&task=misc','Misc. Settings','com_hub',3,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (55,'Components','',0,50,'option=com_hub&task=components','Components','com_hub',4,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (57,'Support','option=com_support',0,0,'option=com_support','Support','com_support',0,'js/ThemeOffice/component.png',0,'feed_summary=0\nseverities=critical,major,normal,minor,trivial\nwebpath=/site/tickets\nmaxAllowed=40000000\nfile_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,html,js,wav,mp3,eps,ppt,pps,swf,tar,tex,gz\ngroup=\n\n',1);
INSERT INTO `#__components` VALUES (59,'Messages','',0,57,'option=com_support&task=messages','Messages','com_support',1,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (60,'Resolutions','',0,57,'option=com_support&task=resolutions','Resolutions','com_support',2,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (62,'Tickets','',0,57,'option=com_support&task=tickets','Tickets','com_support',4,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (63,'WhatsNew','option=com_whatsnew',0,0,'','','com_whatsnew',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (64,'XPoll','option=com_xpoll',0,0,'option=com_xpoll','XPoll','com_xpoll',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (65,'Contribtool','option=com_contribtool',0,0,'option=com_contribtool','Contribtool','com_contribtool',0,'js/ThemeOffice/component.png',0,'contribtool_on=1\nadmingroup=apps\ndefault_mw=narwhal\ndefault_vnc=780x600\ndeveloper_url=https://\ndeveloper_site=Forge\ndeveloper_email=\nproject_path=/tools/\ninvokescript_dir=/apps/\nadminscript_dir=\ndev_suffix=_dev\ngroup_prefix=app-\nsourcecodePath=\nlearn_url=http://rappture.org/wiki/FAQ_UpDownloadSrc\nrappture_url=http://rappture.org\ndemo_url=\ndoi_service=\ndoi_prefix=\nldap_save=0\nldap_read=0\nusedoi=0\nexec_pu=1\nscreenshot_edit=1\n\n',1);
INSERT INTO `#__components` VALUES (66,'Knowledgebase','option=com_kb',0,0,'option=com_kb','Knowledgebase','com_kb',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (67,'Resources','option=com_resources',0,0,'option=com_resources','Resources','com_resources',0,'js/ThemeOffice/component.png',0,'autoapprove=0\nautoapproved_users=\ncc_license=1\nemail_when_approved=0\ndefaultpic=/components/com_resources/images/resource_thumb.gif\ntagstool=screenshots,poweredby,bio,credits,citations,sponsoredby,references,publications\ntagsothr=bio,credits,citations,sponsoredby,references,publications\naccesses=Public,Registered,Special,Protected,Private\nwebpath=/site/resources\ntoolpath=/site/resources/tools\nuploadpath=/site/resources\nmaxAllowed=40000000\nfile_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,html,js,wav,mp3,eps,ppt,pps,swf,tar,tex,gz\ndoi=\naboutdoi=\nsupportedtag=\nsupportedlink=\nbrowsetags=on\ngoogle_id=\nshow_authors=1\nshow_assocs=1\nshow_ranking=0\nshow_rating=1\nshow_date=3\nshow_metadata=1\nshow_citation=1\nshow_audience=0\naudiencelink=\n\n',1);
INSERT INTO `#__components` VALUES (68,'Types','',0,67,'option=com_resources&task=viewtypes','Types','com_resources',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (69,'Orphans','',0,67,'option=com_resources&task=orphans','Orphans','com_resources',1,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (70,'Resources','',0,67,'option=com_resources&task=browse','Resources','com_resources',2,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (71,'Tags','option=com_tags',0,0,'option=com_tags','Tags','com_tags',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (72,'New Tag','',0,71,'option=com_tags&task=new','New Tag','com_tags',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (73,'Whois','option=com_whois',0,0,'','','com_whois',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (74,'XSearch','option=com_xsearch',0,0,'','','com_xsearch',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (75,'Tools','option=com_tools',0,0,'option=com_tools','','com_tools',0,'js/ThemeOffice/component.png',0,'mw_on=1\nmwDBDriver=mysql\nmwDBHost=localhost\nmwDBPort=\nmwDBUsername=\nmwDBPassword=\nmwDBDatabase=\nmwDBPrefix=\nstoragehost=\nshow_storage=0\n\n',1);
INSERT INTO `#__components` VALUES (76,'Members','option=com_members',0,0,'option=com_members','Members','com_members',0,'js/ThemeOffice/component.png',0,'ldapProfileMirror=1\ndefaultpic=/components/com_members/images/profile.gif\nwebpath=/site/members\nmaxAllowed=40000000\nfile_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif\nuser_messaging=1\nprivacy=1\naccess_org=0\naccess_orgtype=0\naccess_email=2\naccess_url=0\naccess_phone=2\naccess_tags=0\naccess_bio=0\naccess_countryorigin=0\naccess_countryresident=0\naccess_gender=0\naccess_race=2\naccess_hispanic=2\naccess_disability=2\naccess_optin=2\nemployeraccess=0\n\n',1);
INSERT INTO `#__components` VALUES (77,'XFlash','option=com_xflash',0,0,'option=com_xflash','XFlash','com_xflash',0,'js/ThemeOffice/component.png',0,'num_featured=3\nuploadpath=/site/xflash/\nmaxAllowed=40000000\nfile_ext=jpg,png,gif\niconpath=templates/azure/images/icons/16x16\n\n',1);
INSERT INTO `#__components` VALUES (78,'Store','option=com_store',0,0,'option=com_store','Store','com_store',0,'js/ThemeOffice/component.png',0,'store_enabled=1\nwebpath=/site/store\nhubaddress_ln1=\nhubaddress_ln2=\nhubaddress_ln3=\nhubaddress_ln4=\nhubaddress_ln5=\nhubemail=\nhubphone=\nheadertext_ln1=\nheadertext_ln2=\nfootertext=\nreceipt_title=Your Order at HUB Store\nreceipt_note=Thank You for contributing to our HUB!\n\n',1);
INSERT INTO `#__components` VALUES (79,'404 SEF','option=com_sef',0,0,'option=com_sef','404 SEF','com_sef',0,'js/ThemeOffice/component.png',0,'enabled=1\n\n',1);
INSERT INTO `#__components` VALUES (80,'Wishlists','option=com_wishlist',0,0,'option=com_wishlist','Wishlists','com_wishlist',0,'js/ThemeOffice/component.png',0,'categories=general, resource, group, user\ngroup=hubdev\nbanking=0\nallow_advisory=0\nvotesplit=0\nwebpath=/site/wishlist\nshow_percentage_granted=0\n\n',1);
INSERT INTO `#__components` VALUES (81,'Features','option=com_features',0,0,'','','com_features',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (82,'Stats','',0,57,'option=com_support&task=stats','Stats','com_support',6,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (83,'Tables','option=com_tools&task=browse',0,75,'option=com_tools&task=browse','Tables','com_tools',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (84,'Organizations','',0,50,'option=com_hub&task=orgs','Organizations','com_hub',6,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (85,'Blog','option=com_blog',0,0,'option=com_blog','Blog','com_blog',0,'js/ThemeOffice/component.png',0,'title=\nuploadpath=/site/blog\nshow_authors=1\nallow_comments=1\nfeeds_enabled=1\nfeed_entries=partial\nshow_date=3',1);
INSERT INTO `#__components` VALUES (86,'Tag/Group','',0,57,'option=com_support&task=taggroup','Tag/Group','com_support',5,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (87,'Jobs','option=com_jobs',0,0,'option=com_jobs','Jobs','com_jobs',0,'js/ThemeOffice/component.png',0,'component_enabled=1\nindustry=\nadmingroup=\nspecialgroup=jobsadmin\nautoapprove=1\ndefaultsort=category\njobslimit=25\nmaxads=3\nallowsubscriptions=1\nusonly=0\nusegoogle=0\nbanking=0\npromoline=For a limited time: FREE Employer Services Basic subscription\ninfolink=kb/jobs\npremium_infolink=',1);
INSERT INTO `#__components` VALUES (88,'Categories','',0,87,'option=com_jobs&task=categories','Categores','com_jobs',2,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (89,'Types','',0,87,'option=com_jobs&task=types','Types','com_jobs',3,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (90,'Jobs','',0,87,'option=com_jobs','Jobs','com_jobs',1,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (91,'Services','option=com_services',0,0,'option=com_services','Services & Subscriptions','com_services',0,'js/ThemeOffice/component.png',0,'autoapprove=1',1);
INSERT INTO `#__components` VALUES (92,'Services','option=com_services',0,91,'option=com_services&task=services','Services','com_services',2,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (93,'Subscriptions','option=com_services',0,91,'option=com_services&task=subscriptions','Subscriptions','com_services',1,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (94,'System','',0,42,'option=com_groups&task=system','System','com_groups',1,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (95,'Manage','',0,42,'option=com_groups&task=browse','Manage','com_groups',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (96,'YSearch','',0,0,'option=com_ysearch&task=configure','YSearch Management','com_ysearch',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (97,'ACL','',0,57,'option=com_support&task=acl','ACL','com_support',7,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (98,'Orphaned Articles','option=com_kb&task=orphans',0,66,'option=com_kb&task=orphans','Orphaned Articles','com_kb',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (99,'Wiki','option=com_wiki',0,0,'option=com_wiki','Wiki','com_wiki',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (100,'Push Module to Users','option=com_myhub&task=select',0,44,'option=com_myhub&task=select','Push Module to Users','com_myhub',0,'js/ThemeOffice/component.png',0,'allow_customization=0\nposition=myhub\ndefaults=55\nstatic=',1);
INSERT INTO `#__components` VALUES (101,'Abuse Reports','',0,57,'option=com_support&task=abusereports','Abuse Reports','com_support',4,'js/ThemeOffice/component.png',0,'',1);

# --------------------------------------------------------

#
# Table structure for table `#__contact_details`
#

CREATE TABLE `#__contact_details` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `con_position` varchar(255) default NULL,
  `address` text,
  `suburb` varchar(100) default NULL,
  `state` varchar(100) default NULL,
  `country` varchar(100) default NULL,
  `postcode` varchar(100) default NULL,
  `telephone` varchar(255) default NULL,
  `fax` varchar(255) default NULL,
  `misc` mediumtext,
  `image` varchar(255) default NULL,
  `imagepos` varchar(20) default NULL,
  `email_to` varchar(255) default NULL,
  `default_con` tinyint(1) unsigned NOT NULL default '0',
  `published` tinyint(1) unsigned NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  `user_id` int(11) NOT NULL default '0',
  `catid` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `mobile` varchar(255) NOT NULL default '',
  `webpage` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `catid` (`catid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__content`
#

CREATE TABLE `#__content` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `title_alias` varchar(255) NOT NULL default '',
  `introtext` mediumtext NOT NULL,
  `fulltext` mediumtext NOT NULL,
  `state` tinyint(3) NOT NULL default '0',
  `sectionid` int(11) unsigned NOT NULL default '0',
  `mask` int(11) unsigned NOT NULL default '0',
  `catid` int(11) unsigned NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL default '0',
  `created_by_alias` varchar(255) NOT NULL default '',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` int(11) unsigned NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
  `images` text NOT NULL,
  `urls` text NOT NULL,
  `attribs` text NOT NULL,
  `version` int(11) unsigned NOT NULL default '1',
  `parentid` int(11) unsigned NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `access` int(11) unsigned NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '0',
  `metadata` TEXT NOT NULL DEFAULT '',
  PRIMARY KEY  (`id`),
  KEY `idx_section` (`sectionid`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_catid` (`catid`),
  KEY `idx_createdby` (`created_by`),
  KEY `#__content_state_idx` (`state`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `introtext` (`introtext`,`fulltext`),
  FULLTEXT KEY `#__content_title_introtext_fulltext_ftidx` (`title`,`introtext`,`fulltext`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__content_frontpage`
#

CREATE TABLE `#__content_frontpage` (
  `content_id` int(11) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`content_id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__content_rating`
#

CREATE TABLE `#__content_rating` (
  `content_id` int(11) NOT NULL default '0',
  `rating_sum` int(11) unsigned NOT NULL default '0',
  `rating_count` int(11) unsigned NOT NULL default '0',
  `lastip` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`content_id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

# Table structure for table `#__core_log_items`

CREATE TABLE `#__core_log_items` (
  `time_stamp` date NOT NULL default '0000-00-00',
  `item_table` varchar(50) NOT NULL default '',
  `item_id` int(11) unsigned NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '0'
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

# Table structure for table `#__core_log_searches`

CREATE TABLE `#__core_log_searches` (
  `search_term` varchar(128) NOT NULL default '',
  `hits` int(11) unsigned NOT NULL default '0'
) TYPE=MyISAM CHARACTER SET `utf8`;

#
# Table structure for table `#__groups`
#

# --------------------------------------------------------

CREATE TABLE `#__groups` (
  `id` tinyint(3) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

#
# Dumping data for table `#__groups`
#

INSERT INTO `#__groups` VALUES (0, 'Public');
INSERT INTO `#__groups` VALUES (1, 'Registered');
INSERT INTO `#__groups` VALUES (2, 'Special');

# --------------------------------------------------------

#
# Table structure for table `#__plugins`
#

CREATE TABLE `#__plugins` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `element` varchar(100) NOT NULL default '',
  `folder` varchar(100) NOT NULL default '',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `published` tinyint(3) NOT NULL default '0',
  `iscore` tinyint(3) NOT NULL default '0',
  `client_id` tinyint(3) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_folder` (`published`,`client_id`,`access`,`folder`)
) TYPE=MyISAM CHARACTER SET `utf8`;

INSERT INTO `#__plugins` VALUES (1, 'Authentication - Joomla', 'joomla', 'authentication', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (2, 'Authentication - LDAP', 'ldap', 'authentication', 0, 2, 0, 1, 0, 0, '0000-00-00 00:00:00', 'host=\nport=389\nuse_ldapV3=0\nnegotiate_tls=0\nno_referrals=0\nauth_method=bind\nbase_dn=\nsearch_string=\nusers_dn=\nusername=\npassword=\nldap_fullname=fullName\nldap_email=mail\nldap_uid=uid\n\n');
INSERT INTO `#__plugins` VALUES (3, 'Authentication - GMail', 'gmail', 'authentication', 0, 4, 0, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (4, 'Authentication - OpenID', 'openid', 'authentication', 0, 3, 0, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (5, 'User - Joomla!', 'joomla', 'user', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', 'autoregister=1\n\n');
INSERT INTO `#__plugins` VALUES (6, 'Search - Content','content','search',0,1,1,1,0,0,'0000-00-00 00:00:00','search_limit=50\nsearch_content=1\nsearch_uncategorised=1\nsearch_archived=1\n\n');
INSERT INTO `#__plugins` VALUES (7, 'Search - Contacts','contacts','search',0,3,1,1,0,0,'0000-00-00 00:00:00','search_limit=50\n\n');
INSERT INTO `#__plugins` VALUES (8, 'Search - Categories', 'categories', 'search', 0, 4, 1, 0, 0, 0, '0000-00-00 00:00:00', 'search_limit=50\n\n');
INSERT INTO `#__plugins` VALUES (9, 'Search - Sections', 'sections', 'search', 0, 5, 1, 0, 0, 0, '0000-00-00 00:00:00', 'search_limit=50\n\n');
INSERT INTO `#__plugins` VALUES (10, 'Search - Newsfeeds', 'newsfeeds', 'search', 0, 6, 1, 0, 0, 0, '0000-00-00 00:00:00', 'search_limit=50\n\n');
INSERT INTO `#__plugins` VALUES (11, 'Search - Weblinks','weblinks','search',0,2,1,1,0,0,'0000-00-00 00:00:00','search_limit=50\n\n');
INSERT INTO `#__plugins` VALUES (12, 'Content - Pagebreak','pagebreak','content',0,10000,1,1,0,0,'0000-00-00 00:00:00','enabled=1\ntitle=1\nmultipage_toc=1\nshowall=1\n\n');
INSERT INTO `#__plugins` VALUES (13, 'Content - Rating','vote','content',0,4,1,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (14, 'Content - Email Cloaking', 'emailcloak', 'content', 0, 5, 1, 0, 0, 0, '0000-00-00 00:00:00', 'mode=1\n\n');
INSERT INTO `#__plugins` VALUES (15, 'Content - Code Hightlighter (GeSHi)', 'geshi', 'content', 0, 5, 0, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (16, 'Content - Load Module', 'loadmodule', 'content', 0, 6, 1, 0, 0, 0, '0000-00-00 00:00:00', 'enabled=1\nstyle=0\n\n');
INSERT INTO `#__plugins` VALUES (17, 'Content - Page Navigation','pagenavigation','content',0,2,1,1,0,0,'0000-00-00 00:00:00','position=1\n\n');
INSERT INTO `#__plugins` VALUES (18, 'Editor - No Editor','none','editors',0,0,1,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (19, 'Editor - TinyMCE', 'tinymce', 'editors', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 'mode=advanced\nskin=0\ncompressed=0\ncleanup_startup=0\ncleanup_save=2\nentity_encoding=raw\nlang_mode=0\nlang_code=en\ntext_direction=ltr\ncontent_css=1\ncontent_css_custom=\nrelative_urls=1\nnewlines=0\ninvalid_elements=applet\nextended_elements=\ntoolbar=top\ntoolbar_align=left\nhtml_height=550\nhtml_width=750\nelement_path=1\nfonts=1\npaste=1\nsearchreplace=1\ninsertdate=1\nformat_date=%Y-%m-%d\ninserttime=1\nformat_time=%H:%M:%S\ncolors=1\ntable=1\nsmilies=1\nmedia=1\nhr=1\ndirectionality=1\nfullscreen=1\nstyle=1\nlayer=1\nxhtmlxtras=1\nvisualchars=1\nnonbreaking=1\ntemplate=0\nadvimage=1\nadvlink=1\nautosave=1\ncontextmenu=1\ninlinepopups=1\nsafari=1\ncustom_plugin=\ncustom_button=\n\n');
INSERT INTO `#__plugins` VALUES (20, 'Editor - XStandard Lite 2.0', 'xstandard', 'editors', 0, 0, 0, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (21, 'Editor Button - Image','image','editors-xtd',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (22, 'Editor Button - Pagebreak','pagebreak','editors-xtd',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (23, 'Editor Button - Readmore','readmore','editors-xtd',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (24, 'XML-RPC - Joomla', 'joomla', 'xmlrpc', 0, 7, 0, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (25, 'XML-RPC - Blogger API', 'blogger', 'xmlrpc', 0, 7, 0, 1, 0, 0, '0000-00-00 00:00:00', 'catid=1\nsectionid=0\n\n');
INSERT INTO `#__plugins` VALUES (26, 'XML-RPC - MetaWeblog API', 'metaweblog', 'xmlrpc', 0, 7, 0, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (27, 'System - SEF','sef','system',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (28, 'System - Debug', 'debug', 'system', 0, 2, 1, 0, 0, 0, '0000-00-00 00:00:00', 'queries=1\nmemory=1\nlangauge=1\n\n');
INSERT INTO `#__plugins` VALUES (29, 'System - Legacy', 'legacy', 'system', 0, 3, 0, 1, 0, 0, '0000-00-00 00:00:00', 'route=0\n\n');
INSERT INTO `#__plugins` VALUES (30, 'System - Cache', 'cache', 'system', 0, 4, 0, 1, 0, 0, '0000-00-00 00:00:00', 'browsercache=0\ncachetime=15\n\n');
INSERT INTO `#__plugins` VALUES (31, 'System - Log', 'log', 'system', 0, 5, 0, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (32, 'System - Remember Me', 'remember', 'system', 0, 6, 1, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (33, 'System - Backlink', 'backlink', 'system', 0, 7, 0, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (34, 'System - Mootools Upgrade', 'mtupgrade', 'system', 0, 8, 0, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (35,'Content - xHubTags','xhubtags','content',0,7,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (36,'Groups - Forum','forum','groups',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (37,'Groups - Resources','resources','groups',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (38,'Groups - Members','members','groups',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (39,'Groups - Wiki','wiki','groups',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (40,'Members - Messages','messages','members',0,7,1,0,0,0,'0000-00-00 00:00:00','default_method=email\n\n');
INSERT INTO `#__plugins` VALUES (41,'Members - Usage','usage','members',0,5,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (42,'Members - Contributions - Topics','topics','members',0,4,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (43,'Members - Contributions - Resources','resources','members',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (44,'Members - Groups','groups','members',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (45,'Members - Favorites','favorites','members',0,6,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (46,'Members - Points','points','members',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (47,'Members - Contributions','contributions','members',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (48,'Middleware - About','resource','mw',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (49,'Middleware - Questions','questions','mw',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (50,'Tags - Groups','groups','tags',0,6,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (51,'Tags - Support','support','tags',0,4,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (52,'Tags - Topics','topics','tags',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (53,'Tags - Answers','answers','tags',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (54,'Tags - Events','events','tags',0,5,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (55,'Tags - Members','members','tags',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (56,'Tags - Resources','resources','tags',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (57,'Resources - Share','share','resources',0,8,1,0,0,0,'0000-00-00 00:00:00','icons_limit=3\nshare_facebook=1\nshare_twitter=1\nshare_google=1\nshare_digg=1\nshare_technorati=1\nshare_delicious=1\nshare_reddit=0\nshare_email=0\nshare_print=0\n\n');
INSERT INTO `#__plugins` VALUES (58,'Resources - Favorite','favorite','resources',0,7,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (59,'Resources - Versions','versions','resources',0,6,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (60,'Resources - Reviews','reviews','resources',0,4,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (61,'Resources - Questions','questions','resources',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (62,'Resources - Wishlist','wishlist','resources',0,9,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (63,'Resources - Usage','usage','resources',0,5,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (64,'Resources - Related','related','resources',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (65,'Resources - Recommendations','recommendations','resources',0,2,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (66,'Resources - Citations','citations','resources',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (67,'Support - Comments','comments','support',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (68,'Support - Transfer','transfer','support',0,4,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (69,'Support - Wishlist','wishlist','support',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (70,'Support - Resources','resources','support',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (71,'Support - Answers','answers','support',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (72,'System - xHUB','xhub','system',0,8,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (73,'System - xFeed','xfeed','system',0,9,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (74,'Tag Editor - Auto complete','autocompleter','tageditor',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (75,'Usage - Region','region','usage',0,7,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (76,'Usage - Overview','overview','usage',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (77,'Usage - Chart','chart','usage',0,4,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (78,'Usage - Partners','partners','usage',0,2,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (79,'Usage - Domain Class','domainclass','usage',0,0,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (80,'Usage - Domains','domains','usage',0,5,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (81,'Usage - Tools','tools','usage',0,3,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (82,'Usage - Maps','maps','usage',0,6,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (83,'User - xHUB','xusers','user',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (84,'Whatsnew - Topics','topics','whatsnew',0,4,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (85,'Whatsnew - Resources','resources','whatsnew',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (86,'Whatsnew - Content','content','whatsnew',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (87,'Whatsnew - Events','events','whatsnew',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (88,'Whatsnew - Knowledge Base','kb','whatsnew',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (89,'xHUB Authentication - Site','hzldap','xauthentication',0,0,1,0,0,0,'0000-00-00 00:00:00','domain=Hub Account');
INSERT INTO `#__plugins` VALUES (90,'xHUB - Libraries','xlibrary','xhub',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (91,'XMessage - RSS','rss','xmessage',0,4,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (92,'XMessage - Internal','internal','xmessage',0,5,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (93,'XMessage - SMS TXT','smstxt','xmessage',0,3,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (94,'XMessage - Instant Message','im','xmessage',0,2,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (95,'XMessage - Handler','handler','xmessage',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (96,'XMessage - Email','email','xmessage',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (97,'XSearch - Groups','groups','xsearch',0,7,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (98,'XSearch - Tags','tags','xsearch',0,4,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (99,'XSearch - Resources','resources','xsearch',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (100,'XSearch - Knowledge Base','kb','xsearch',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (101,'XSearch - Events','events','xsearch',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (102,'XSearch - Members','members','xsearch',0,6,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (103,'XSearch - Topics','topics','xsearch',0,5,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (104,'XSearch - Content','content','xsearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (105,'Groups - Wishlist','wishlist','groups',0,8,1,0,0,0,'0000-00-00 00:00:00','limit=50');
INSERT INTO `#__plugins` VALUES (106,'Resource - Supporting Documents','supportingdocs','resources',0,11,1,0,0,0,'0000-00-00 00:00:00','display_limit=50');
INSERT INTO `#__plugins` VALUES (107,'Members - Resume','resume','members',0,14,1,0,0,0,'0000-00-00 00:00:00','limit=50');
INSERT INTO `#__plugins` VALUES (108,'Members - Usage Extended','usages','members',0,15,0,0,0,0,'0000-00-00 00:00:00','groups=usage_admin');
INSERT INTO `#__plugins` VALUES (109,'Members - Blog','blog','members',0,16,1,0,0,0,'0000-00-00 00:00:00','uploadpath=/site/members/{{uid}}/blog\nfeeds_enabled=0\nfeed_entries=partial');
INSERT INTO `#__plugins` VALUES (110,'Tags - Blogs','blogs','tags',0,9,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (111,'XSearch - Blogs','blogs','xsearch',0,15,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (112,'Support - Blog','blog','support',0,6,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (113,'YSearch - Content','content','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (114,'YSearch - Increase weight of items with terms matching in their titles','weighttitle','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (115,'YSearch - Events','events','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (116,'YSearch - Knowledge Base','kb','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (117,'YSearch - Groups','groups','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (118,'YSearch - Members','members','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (119,'YSearch - Resources','resources','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (120,'YSearch - Topics','topics','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (121,'YSearch - Increase weight of items with contributors matching terms','weightcontributor','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (122,'YSearch - Wishlists','wishlists','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (123,'YSearch - Questions and Answers','questions','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (124,'YSearch - Increase relevance for tool results','weighttools','ysearch',0,0,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (125,'YSearch - Site Map','sitemap','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (126,'YSearch - Terms - Suffix Expansion','suffixes','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (127,'YSearch - Sort courses by date','sortcourses','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (128,'Groups - Blog','blog','groups',0,7,1,0,0,0,'0000-00-00 00:00:00','uploadpath=/site/groups/{{gid}}/blog\nposting=0\nfeeds_enabled=0\nfeed_entries=partial');
INSERT INTO `#__plugins` VALUES (130,'Groups - Usage','usage','groups',0,9,0,0,0,0,'0000-00-00 00:00:00','uploadpath=/site/groups/{{gid}}/blog\nposting=0\nfeeds_enabled=0\nfeed_entries=partial');
INSERT INTO `#__plugins` VALUES (131,'Groups - Messages','messages','groups','0','2','1','0','0','0','0000-00-00 00:00:00','limit=50');
INSERT INTO `#__plugins` VALUES (132,'Authentication - xHUB','xauth','authentication',0,5,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (133,'HUBzero - Wiki Parser','wikiparser','hubzero',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (134,'YSearch - Sort events by date','sortevents','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (135,'HUBzero - Autocompleter','autocompleter','hubzero',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (136,'HUBzero - Wiki Editor Toolbar','wikieditortoolbar','hubzero',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (137,'Members - HTML Snippet','snippet','members',0,17,1,0,0,0,'0000-00-00 00:00:00','uploadpath=/site/members/{{uid}}/blog\nfeeds_enabled=0\nfeed_entries=partial');
INSERT INTO `#__plugins` VALUES (138,'Groups - Calendar','calendar','groups',0,10,1,0,0,0,'0000-00-00 00:00:00','');


# --------------------------------------------------------

#
# Table structure for table `#__menu`
#

CREATE TABLE `#__menu` (
  `id` int(11) NOT NULL auto_increment,
  `menutype` varchar(75) default NULL,
  `name` varchar(255) default NULL,
  `alias` varchar(255) NOT NULL default '',
  `link` text,
  `type` varchar(50) NOT NULL default '',
  `published` tinyint(1) NOT NULL default 0,
  `parent` int(11) unsigned NOT NULL default 0,
  `componentid` int(11) unsigned NOT NULL default 0,
  `sublevel` int(11) default 0,
  `ordering` int(11) default 0,
  `checked_out` int(11) unsigned NOT NULL default 0,
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `pollid` int(11) NOT NULL default 0,
  `browserNav` tinyint(4) default 0,
  `access` tinyint(3) unsigned NOT NULL default 0,
  `utaccess` tinyint(3) unsigned NOT NULL default 0,
  `params` text NOT NULL,
  `lft` int(11) unsigned NOT NULL default 0,
  `rgt` int(11) unsigned NOT NULL default 0,
  `home` INTEGER(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY  (`id`),
  KEY `componentid` (`componentid`,`menutype`,`published`,`access`),
  KEY `menutype` (`menutype`)
) TYPE=MyISAM CHARACTER SET `utf8`;

INSERT INTO `#__menu` VALUES (1, 'mainmenu', 'Home', 'home', 'index.php?option=com_content&view=frontpage', 'component', 1, 0, 20, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'num_leading_articles=1\nnum_intro_articles=4\nnum_columns=2\nnum_links=4\norderby_pri=\norderby_sec=front\nshow_pagination=2\nshow_pagination_results=1\nshow_feed_link=1\nshow_noauth=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_item_navigation=\nshow_readmore=\nshow_vote=\nshow_icons=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nshow_hits=\nfeed_summary=\npage_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 1);

# --------------------------------------------------------

#
# Table structure for table `#__menu_types`
#

CREATE TABLE `#__menu_types` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `menutype` VARCHAR(75) NOT NULL DEFAULT '',
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY(`id`),
  UNIQUE `menutype`(`menutype`)
) TYPE=MyISAM CHARACTER SET `utf8`;

INSERT INTO `#__menu_types` VALUES (1, 'mainmenu', 'Main Menu', 'The main menu for the site');

# --------------------------------------------------------

#
# Table structure for table `#__messages`
#

CREATE TABLE `#__messages` (
  `message_id` int(10) unsigned NOT NULL auto_increment,
  `user_id_from` int(10) unsigned NOT NULL default '0',
  `user_id_to` int(10) unsigned NOT NULL default '0',
  `folder_id` int(10) unsigned NOT NULL default '0',
  `date_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `state` int(11) NOT NULL default '0',
  `priority` int(1) unsigned NOT NULL default '0',
  `subject` text NOT NULL default '',
  `message` text NOT NULL,
  PRIMARY KEY  (`message_id`),
  KEY `useridto_state` (`user_id_to`, `state`)
) TYPE=MyISAM CHARACTER SET `utf8`;
# --------------------------------------------------------

#
# Table structure for table `#__messages_cfg`
#

CREATE TABLE `#__messages_cfg` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `cfg_name` varchar(100) NOT NULL default '',
  `cfg_value` varchar(255) NOT NULL default '',
  UNIQUE `idx_user_var_name` (`user_id`,`cfg_name`)
) TYPE=MyISAM CHARACTER SET `utf8`;
# --------------------------------------------------------

#
# Table structure for table `#__modules`
#

CREATE TABLE `#__modules` (
  `id` int(11) NOT NULL auto_increment,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  `position` varchar(50) default NULL,
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL default '0',
  `module` varchar(50) default NULL,
  `numnews` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `showtitle` tinyint(3) unsigned NOT NULL default '1',
  `params` text NOT NULL,
  `iscore` tinyint(4) NOT NULL default '0',
  `client_id` tinyint(4) NOT NULL default '0',
  `control` TEXT NOT NULL DEFAULT '',
  PRIMARY KEY  (`id`),
  KEY `published` (`published`,`access`),
  KEY `newsfeeds` (`module`,`published`)
) TYPE=MyISAM CHARACTER SET `utf8`;

INSERT INTO `#__modules` VALUES (1,'Main Menu','',0,'user3',62,NOW(),1,'mod_mainmenu',0,0,0,'menutype=mainmenu\nmenu_style=list\nstartLevel=0\nendLevel=0\nshowAllChildren=1\nwindow_open=\nshow_whitespace=0\ncache=1\ntag_id=\nclass_sfx=\nmoduleclass_sfx=_menu\nmaxdepth=10\nmenu_images=0\nmenu_images_align=0\nmenu_images_link=0\nexpand_menu=0\nactivate_parent=0\nfull_active_id=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nspacer=\nend_spacer=\n\n',1,0,'');
INSERT INTO `#__modules` VALUES (2, 'Login', '', 1, 'login', 0, '0000-00-00 00:00:00', 1, 'mod_login', 0, 0, 1, '', 1, 1, '');
INSERT INTO `#__modules` VALUES (3, 'Popular','',3,'cpanel',0,'0000-00-00 00:00:00',1,'mod_popular',0,2,1,'',0, 1, '');
INSERT INTO `#__modules` VALUES (4, 'Recent added Articles','',4,'cpanel',0,'0000-00-00 00:00:00',1,'mod_latest',0,2,1,'ordering=c_dsc\nuser_id=0\ncache=0\n\n',0, 1, '');
INSERT INTO `#__modules` VALUES (5, 'Menu Stats','',5,'cpanel',0,'0000-00-00 00:00:00',1,'mod_stats',0,2,1,'',0, 1, '');
INSERT INTO `#__modules` VALUES (6, 'Unread Messages','',1,'header',0,'0000-00-00 00:00:00',1,'mod_unread',0,2,1,'',1, 1, '');
INSERT INTO `#__modules` VALUES (7, 'Online Users','',2,'header',0,'0000-00-00 00:00:00',1,'mod_online',0,2,1,'',1, 1, '');
INSERT INTO `#__modules` VALUES (8, 'Toolbar','',1,'toolbar',0,'0000-00-00 00:00:00',1,'mod_toolbar',0,2,1,'',1, 1, '');
INSERT INTO `#__modules` VALUES (9, 'Quick Icons','',1,'icon',0,'0000-00-00 00:00:00',1,'mod_quickicon',0,2,1,'',1,1, '');
INSERT INTO `#__modules` VALUES (10, 'Logged in Users','',2,'cpanel',0,'0000-00-00 00:00:00',1,'mod_logged',0,2,1,'',0,1, '');
INSERT INTO `#__modules` VALUES (11, 'Footer', '', 0, 'footer', 0, '0000-00-00 00:00:00', 1, 'mod_footer', 0, 0, 1, '', 1, 1, '');
INSERT INTO `#__modules` VALUES (12, 'Admin Menu','', 1,'menu', 0,'0000-00-00 00:00:00', 1,'mod_menu', 0, 2, 1, '', 0, 1, '');
INSERT INTO `#__modules` VALUES (13, 'Admin SubMenu','', 1,'submenu', 0,'0000-00-00 00:00:00', 1,'mod_submenu', 0, 2, 1, '', 0, 1, '');
INSERT INTO `#__modules` VALUES (14, 'User Status','', 1,'status', 0,'0000-00-00 00:00:00', 1,'mod_status', 0, 2, 1, '', 0, 1, '');
INSERT INTO `#__modules` VALUES (15, 'Title','', 1,'title', 0,'0000-00-00 00:00:00', 1,'mod_title', 0, 2, 1, '', 0, 1, '');

# --------------------------------------------------------

#
# Table structure for table `#__modules_menu`
#

CREATE TABLE `#__modules_menu` (
  `moduleid` int(11) NOT NULL default '0',
  `menuid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`moduleid`,`menuid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

#
# Dumping data for table `#__modules_menu`
#

INSERT INTO `#__modules_menu` VALUES (1,0);

# --------------------------------------------------------

#
# Table structure for table `#__newsfeeds`
#

CREATE TABLE `#__newsfeeds` (
  `catid` int(11) NOT NULL default '0',
  `id` int(11) NOT NULL auto_increment,
  `name` text NOT NULL,
  `alias` varchar(255) NOT NULL default '',
  `link` text NOT NULL,
  `filename` varchar(200) default NULL,
  `published` tinyint(1) NOT NULL default '0',
  `numarticles` int(11) unsigned NOT NULL default '1',
  `cache_time` int(11) unsigned NOT NULL default '3600',
  `checked_out` tinyint(3) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `rtl` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `published` (`published`),
  KEY `catid` (`catid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__poll_data`
#

CREATE TABLE `#__poll_data` (
  `id` int(11) NOT NULL auto_increment,
  `pollid` int(11) NOT NULL default '0',
  `text` text NOT NULL default '',
  `hits` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pollid` (`pollid`,`text`(1))
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__poll_date`
#

CREATE TABLE `#__poll_date` (
  `id` bigint(20) NOT NULL auto_increment,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `vote_id` int(11) NOT NULL default '0',
  `poll_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `poll_id` (`poll_id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__polls`
#

CREATE TABLE `#__polls` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `voters` int(9) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL default '0',
  `access` int(11) NOT NULL default '0',
  `lag` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__poll_menu`
# !!!DEPRECATED!!!
#

CREATE TABLE `#__poll_menu` (
  `pollid` int(11) NOT NULL default '0',
  `menuid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pollid`,`menuid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__sections`
#

CREATE TABLE `#__sections` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `image` TEXT NOT NULL default '',
  `scope` varchar(50) NOT NULL default '',
  `image_position` varchar(30) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_scope` (`scope`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__session`
#

CREATE TABLE `#__session` (
  `username` varchar(150) default '',
  `time` varchar(14) default '',
  `session_id` varchar(200) NOT NULL default '0',
  `guest` tinyint(4) default '1',
  `userid` int(11) default '0',
  `usertype` varchar(50) default '',
  `gid` tinyint(3) unsigned NOT NULL default '0',
  `client_id` tinyint(3) unsigned NOT NULL default '0',
  `data` longtext,
  `ip` varchar(15) default NULL,
  PRIMARY KEY  (`session_id`(64)),
  KEY `whosonline` (`guest`,`usertype`),
  KEY `userid` (`userid`),
  KEY `time` (`time`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__stats_agents`
#

CREATE TABLE `#__stats_agents` (
  `agent` varchar(255) NOT NULL default '',
  `type` tinyint(1) unsigned NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '1'
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__templates_menu`
#

CREATE TABLE `#__templates_menu` (
  `template` varchar(255) NOT NULL default '',
  `menuid` int(11) NOT NULL default '0',
  `client_id` tinyint(4) NOT NULL default '0',
  PRIMARY KEY (`menuid`, `client_id`, `template`(255))
) TYPE=MyISAM CHARACTER SET `utf8`;

# Dumping data for table `#__templates_menu`
INSERT INTO `#__templates_menu` VALUES ('rhuk_milkyway', '0', '0');
INSERT INTO `#__templates_menu` VALUES ('khepri', '0', '1');

# --------------------------------------------------------

#
# Table structure for table `#__users`
#

CREATE TABLE `#__users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `username` varchar(150) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `password` varchar(100) NOT NULL default '',
  `usertype` varchar(25) NOT NULL default '',
  `block` tinyint(4) NOT NULL default '0',
  `sendEmail` tinyint(4) default '0',
  `gid` tinyint(3) unsigned NOT NULL default '1',
  `registerDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastvisitDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `activation` varchar(100) NOT NULL default '',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `usertype` (`usertype`),
  KEY `idx_name` (`name`),
  KEY `gid_block` (`gid`, `block`),
  KEY `username` (`username`),
  KEY `email` (`email`)
) TYPE=MyISAM AUTO_INCREMENT=1000 CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__weblinks`
#

CREATE TABLE `#__weblinks` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `catid` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `title` varchar(250) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `url` varchar(250) NOT NULL default '',
  `description` text NOT NULL default '',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `hits` int(11) NOT NULL default '0',
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `archived` tinyint(1) NOT NULL default '0',
  `approved` tinyint(1) NOT NULL default '1',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `catid` (`catid`,`published`,`archived`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__core_acl_aro`
#

CREATE TABLE `#__core_acl_aro` (
  `id` int(11) NOT NULL auto_increment,
  `section_value` varchar(240) NOT NULL default '0',
  `value` varchar(240) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `#__section_value_value_aro` (`section_value`(100),`value`(100)),
  KEY `#__gacl_hidden_aro` (`hidden`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__core_acl_aro_map`
#

CREATE TABLE  `#__core_acl_aro_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(100) NOT NULL,
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__core_acl_aro_groups`
#
CREATE TABLE `#__core_acl_aro_groups` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `lft` int(11) NOT NULL default '0',
  `rgt` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `#__gacl_parent_id_aro_groups` (`parent_id`),
  KEY `#__gacl_lft_rgt_aro_groups` (`lft`,`rgt`)
) TYPE=MyISAM CHARACTER SET `utf8`;

#
# Dumping data for table `#__core_acl_aro_groups`
#

INSERT INTO `#__core_acl_aro_groups` VALUES (17,0,'ROOT',1,22,'ROOT');
INSERT INTO `#__core_acl_aro_groups` VALUES (28,17,'USERS',2,21,'USERS');
INSERT INTO `#__core_acl_aro_groups` VALUES (29,28,'Public Frontend',3,12,'Public Frontend');
INSERT INTO `#__core_acl_aro_groups` VALUES (18,29,'Registered',4,11,'Registered');
INSERT INTO `#__core_acl_aro_groups` VALUES (19,18,'Author',5,10,'Author');
INSERT INTO `#__core_acl_aro_groups` VALUES (20,19,'Editor',6,9,'Editor');
INSERT INTO `#__core_acl_aro_groups` VALUES (21,20,'Publisher',7,8,'Publisher');
INSERT INTO `#__core_acl_aro_groups` VALUES (30,28,'Public Backend',13,20,'Public Backend');
INSERT INTO `#__core_acl_aro_groups` VALUES (23,30,'Manager',14,19,'Manager');
INSERT INTO `#__core_acl_aro_groups` VALUES (24,23,'Administrator',15,18,'Administrator');
INSERT INTO `#__core_acl_aro_groups` VALUES (25,24,'Super Administrator',16,17,'Super Administrator');

# --------------------------------------------------------

#
# Table structure for table `#__core_acl_groups_aro_map`
#
CREATE TABLE `#__core_acl_groups_aro_map` (
  `group_id` int(11) NOT NULL default '0',
  `section_value` varchar(240) NOT NULL default '',
  `aro_id` int(11) NOT NULL default '0',
  UNIQUE KEY `group_id_aro_id_groups_aro_map` (`group_id`,`section_value`,`aro_id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__core_acl_aro_sections`
#
CREATE TABLE `#__core_acl_aro_sections` (
  `id` int(11) NOT NULL auto_increment,
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `#__gacl_value_aro_sections` (`value`),
  KEY `#__gacl_hidden_aro_sections` (`hidden`)
) TYPE=MyISAM CHARACTER SET `utf8`;

INSERT INTO `#__core_acl_aro_sections` VALUES (10,'users',1,'Users',0);

# --------------------------------------------------------

#
# Table structure for table `#__migration_backlinks`
#
CREATE TABLE `#__migration_backlinks` (
	`itemid` INT(11) NOT NULL,
	`name` VARCHAR(100) NOT NULL,
	`url` TEXT NOT NULL,
	`sefurl` TEXT NOT NULL,
	`newurl` TEXT NOT NULL,
	PRIMARY KEY(`itemid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `app`
#

CREATE TABLE `app` (
  `appname` varchar(80) NOT NULL default '',
  `geometry` varchar(9) NOT NULL default '',
  `depth` smallint(5) unsigned NOT NULL default '16',
  `hostreq` bigint(20) unsigned NOT NULL default '0',
  `userreq` bigint(20) unsigned NOT NULL default '0',
  `timeout` int(10) unsigned NOT NULL default '0',
  `command` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `display`
#

CREATE TABLE `display` (
  `hostname` varchar(40) NOT NULL default '',
  `dispnum` int(10) unsigned default '0',
  `geometry` varchar(9) NOT NULL default '',
  `depth` smallint(5) unsigned NOT NULL default '16',
  `sessnum` bigint(20) unsigned default '0',
  `vncpass` varchar(16) NOT NULL default '',
  `status` varchar(20) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `domainclass`
#

CREATE TABLE `domainclass` (
  `class` tinyint(4) NOT NULL default '0',
  `country` varchar(4) NOT NULL,
  `domain` varchar(64) NOT NULL,
  `name` tinytext NOT NULL,
  `state` varchar(4) NOT NULL,
  PRIMARY KEY  (`domain`),
  KEY `class` USING BTREE (`class`),
  KEY `domain` USING BTREE (`domain`,`class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `domainclasses`
#

CREATE TABLE `domainclasses` (
  `class` tinyint(4) NOT NULL default '0',
  `name` varchar(64) NOT NULL,
  PRIMARY KEY  (`class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `fileperm`
#

CREATE TABLE `fileperm` (
  `sessnum` bigint(20) unsigned NOT NULL default '0',
  `fileuser` varchar(32) NOT NULL default '',
  `fwhost` varchar(40) NOT NULL default '',
  `fwport` smallint(5) unsigned NOT NULL default '0',
  `cookie` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`sessnum`,`fileuser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `host`
#

CREATE TABLE `host` (
  `hostname` varchar(40) NOT NULL default '',
  `provisions` bigint(20) unsigned NOT NULL default '0',
  `status` varchar(20) NOT NULL default '',
  `uses` smallint(5) unsigned NOT NULL default '0',
  `portbase` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `hosttype`
#

CREATE TABLE `hosttype` (
  `name` varchar(40) NOT NULL default '',
  `value` bigint(20) unsigned NOT NULL default '0',
  `description` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Dumping data for table `hosttype`
#

INSERT INTO `hosttype` (`name`, `value`, `description`) VALUES ('workspace',1,'Workspace host');
INSERT INTO `hosttype` (`name`, `value`, `description`) VALUES ('fileserver',2,'Fileserver host');
INSERT INTO `hosttype` (`name`, `value`, `description`) VALUES ('pubnet',4,'Public host');
INSERT INTO `hosttype` (`name`, `value`, `description`) VALUES ('sessions',8,'Normal jobs');
INSERT INTO `hosttype` (`name`, `value`, `description`) VALUES ('openvz',16,'OpenVZ');

#
# Table structure for table `ipusers`
#

CREATE TABLE `ipusers` (
  `id` int(11) NOT NULL auto_increment,
  `ip` varchar(15) NOT NULL,
  `user` tinytext NOT NULL,
  `ntimes` smallint(6) NOT NULL,
  `from` datetime default NULL,
  `to` datetime default NULL,
  `orgtype` varchar(4) NOT NULL,
  `countryresident` char(2) NOT NULL,
  `countrycitizen` char(2) NOT NULL,
  `countryip` char(2) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `job`
#

CREATE TABLE `job` (
  `sessnum` bigint(20) unsigned NOT NULL default '0',
  `jobid` bigint(20) unsigned NOT NULL auto_increment,
  `superjob` bigint(20) unsigned NOT NULL default '0',
  `username` varchar(32) NOT NULL default '',
  `event` varchar(40) NOT NULL default '',
  `ncpus` smallint(5) unsigned NOT NULL default '0',
  `venue` varchar(80) NOT NULL default '',
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  `heartbeat` datetime NOT NULL default '0000-00-00 00:00:00',
  `active` smallint(2) NOT NULL default '1',
  UNIQUE KEY `jobid` (`jobid`),
  KEY `start` (`start`),
  KEY `heartbeat` (`heartbeat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `joblog`
#

CREATE TABLE `joblog` (
  `sessnum` bigint(20) unsigned NOT NULL default '0',
  `job` int(10) unsigned NOT NULL default '0',
  `superjob` bigint(20) unsigned NOT NULL default '0',
  `event` varchar(40) NOT NULL default '',
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  `walltime` float unsigned default '0',
  `cputime` float unsigned default '0',
  `ncpus` smallint(5) unsigned NOT NULL default '0',
  `status` smallint(5) unsigned default '0',
  `venue` varchar(80) NOT NULL default '',
  PRIMARY KEY  (`sessnum`,`job`,`event`),
  KEY `sessnum` (`sessnum`),
  KEY `event` (`event`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__abuse_reports`
#

CREATE TABLE IF NOT EXISTS `#__abuse_reports` (
  `id` int(11) NOT NULL auto_increment,
  `category` varchar(50) default NULL,
  `referenceid` int(11) default '0',
  `report` text NOT NULL,
  `created_by` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `state` int(3) default '0',
  `subject` varchar(150) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__answers_log`
#

CREATE TABLE IF NOT EXISTS `#__answers_log` (
  `id` int(11) NOT NULL auto_increment,
  `rid` int(11) NOT NULL default '0',
  `ip` varchar(15) default NULL,
  `helpful` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__answers_questions`
#

CREATE TABLE IF NOT EXISTS `#__answers_questions` (
  `id` int(11) NOT NULL auto_increment,
  `subject` varchar(250) default NULL,
  `question` text,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` varchar(50) default NULL,
  `state` tinyint(3) NOT NULL default '0',
  `anonymous` tinyint(2) NOT NULL default '0',
  `email` tinyint(2) default '0',
  `helpful` int(11) default '0',
  `reward` tinyint(2) default '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `question` (`question`),
  FULLTEXT KEY `subject` (`subject`),
  FULLTEXT KEY `#__answers_questions_question_subject_ftidx` (`question`,`subject`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__answers_questions_log`
#

CREATE TABLE IF NOT EXISTS `#__answers_questions_log` (
  `id` int(11) NOT NULL auto_increment,
  `qid` int(11) NOT NULL default '0',
  `expires` datetime NOT NULL default '0000-00-00 00:00:00',
  `voter` int(11) default NULL,
  `ip` varchar(15) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__answers_responses`
#

CREATE TABLE IF NOT EXISTS `#__answers_responses` (
  `id` int(11) NOT NULL auto_increment,
  `qid` int(11) NOT NULL default '0',
  `answer` text,
  `created_by` varchar(50) default NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `helpful` int(11) NOT NULL default '0',
  `nothelpful` int(11) NOT NULL default '0',
  `state` tinyint(3) NOT NULL default '0',
  `anonymous` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `answer` (`answer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__answers_tags`
#

CREATE TABLE IF NOT EXISTS `#__answers_tags` (
  `id` int(11) NOT NULL auto_increment,
  `questionid` int(11) NOT NULL default '0',
  `tagid` int(11) NOT NULL default '0',
  `taggerid` varchar(200) default NULL,
  `taggedon` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__auth_domain`
#

CREATE TABLE IF NOT EXISTS `#__auth_domain` (
  `authenticator` varchar(255) default NULL,
  `domain` varchar(255) default NULL,
  `id` int(11) NOT NULL auto_increment,
  `params` varchar(255) default NULL,
  `type` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__auth_link`
#

CREATE TABLE IF NOT EXISTS `#__auth_link` (
  `auth_domain_id` int(11) default NULL,
  `email` varchar(255) default NULL,
  `id` int(11) NOT NULL auto_increment,
  `params` varchar(255) default NULL,
  `password` varchar(255) default NULL,
  `user_id` int(11) default NULL,
  `username` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__author_assoc`
#

CREATE TABLE IF NOT EXISTS `#__author_assoc` (
  `subtable` varchar(50) NOT NULL default '',
  `subid` int(11) NOT NULL default '0',
  `authorid` int(11) NOT NULL default '0',
  `ordering` int(11) default NULL,
  `role` varchar(50) default NULL,
  `name` varchar(255) default NULL,
  `organization` varchar(255) default NULL,
  PRIMARY KEY  (`subtable`,`subid`,`authorid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__author_stats`
#

CREATE TABLE IF NOT EXISTS `#__author_stats` (
  `id` bigint(20) NOT NULL auto_increment,
  `authorid` int(11) NOT NULL,
  `tool_users` bigint(20) default NULL,
  `andmore_users` bigint(20) default NULL,
  `total_users` bigint(20) default NULL,
  `datetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL default '-1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__blog_comments`
#

CREATE TABLE IF NOT EXISTS `#__blog_comments` (
  `id` int(11) NOT NULL auto_increment,
  `entry_id` int(11) default '0',
  `content` text,
  `created` datetime default '0000-00-00 00:00:00',
  `created_by` int(11) default '0',
  `anonymous` tinyint(2) default '0',
  `parent` int(11) default '0',
  PRIMARY KEY  (`id`)

) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__blog_entries`
#

CREATE TABLE IF NOT EXISTS `#__blog_entries` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `alias` varchar(255) default NULL,
  `content` text,
  `created` datetime default '0000-00-00 00:00:00',
  `created_by` int(11) default '0',
  `state` tinyint(2) default '0',
  `publish_up` datetime default '0000-00-00 00:00:00',
  `publish_down` datetime default '0000-00-00 00:00:00',
  `params` tinytext,
  `group_id` int(11) default '0',
  `hits` int(11) default '0',
  `allow_comments` tinyint(2) default '0',
  `scope` varchar(100) default NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__cart`
#

CREATE TABLE IF NOT EXISTS `#__cart` (
  `id` int(10) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `itemid` int(11) NOT NULL default '0',
  `type` varchar(20) default NULL,
  `quantity` int(11) NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `selections` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__citations`
#

CREATE TABLE IF NOT EXISTS `#__citations` (
  `id` int(11) NOT NULL auto_increment,
  `uid` varchar(200) default NULL,
  `affiliated` int(3) default NULL,
  `fundedby` int(3) default NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `address` varchar(250) default NULL,
  `author` text,
  `booktitle` varchar(250) default NULL,
  `chapter` varchar(250) default NULL,
  `cite` varchar(250) default NULL,
  `edition` varchar(250) default NULL,
  `editor` varchar(250) default NULL,
  `eprint` varchar(250) default NULL,
  `howpublished` varchar(250) default NULL,
  `institution` varchar(250) default NULL,
  `isbn` varchar(50) default NULL,
  `journal` varchar(250) default NULL,
  `key` varchar(250) default NULL,
  `location` varchar(250) default NULL,
  `month` varchar(50) default NULL,
  `note` text,
  `number` varchar(50) default NULL,
  `organization` varchar(250) default NULL,
  `pages` varchar(250) default NULL,
  `publisher` varchar(250) default NULL,
  `series` varchar(250) default NULL,
  `school` varchar(250) default NULL,
  `title` varchar(250) default NULL,
  `type` varchar(30) default NULL,
  `url` varchar(250) default NULL,
  `volume` int(11) default NULL,
  `year` int(4) default NULL,
  `doi` varchar(250) default NULL,
  `ref_type` varchar(50) default NULL,
  `date_submit` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_accept` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_publish` datetime NOT NULL default '0000-00-00 00:00:00',
  `software_use` int(3) default NULL,
  `res_edu` int(3) default NULL,
  `exp_list_exp_data` int(3) default NULL,
  `exp_data` int(3) default NULL,
  `notes` text,
  `published` int(3) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__citations_assoc`
#

CREATE TABLE IF NOT EXISTS `#__citations_assoc` (
  `id` int(11) NOT NULL auto_increment,
  `cid` int(11) default '0',
  `oid` int(11) default '0',
  `type` varchar(50) default NULL,
  `table` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__citations_authors`
#

CREATE TABLE IF NOT EXISTS `#__citations_authors` (
   `id` int(11) NOT NULL auto_increment,
  `cid` int(11) default '0',
  `author` varchar(64) default NULL,
  `author_uid` bigint(20) default NULL,
  `ordering` int(11) NOT NULL default '0',
  `givenName` varchar(255) NOT NULL default '',
  `middleName` varchar(255) NOT NULL default '',
  `surname` varchar(255) NOT NULL default '',
  `organization` varchar(255) NOT NULL default '',
  `org_dept` varchar(255) NOT NULL default '',
  `orgtype` varchar(255) NOT NULL default '',
  `countryresident` char(2) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `ip` varchar(40) NOT NULL default '',
  `host` varchar(64) NOT NULL default '',
  `countrySHORT` char(2) NOT NULL default '',
  `countryLONG` varchar(64) NOT NULL default '',
  `ipREGION` varchar(128) NOT NULL default '',
  `ipCITY` varchar(128) NOT NULL default '',
  `ipLATITUDE` double default NULL,
  `ipLONGITUDE` double default NULL,
  `in_network` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `cid_auth_uid` (`cid`,`author`,`author_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__citations_secondary`
#

CREATE TABLE IF NOT EXISTS `#__citations_secondary` (
  `id` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL,
  `sec_cits_cnt` int(11) default NULL,
  `search_string` tinytext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__comments`
#

CREATE TABLE IF NOT EXISTS `#__comments` (
  `id` int(11) NOT NULL auto_increment,
  `referenceid` varchar(11) default NULL,
  `category` varchar(50) default NULL,
  `comment` text,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `added_by` int(11) default NULL,
  `state` tinyint(3) NOT NULL default '0',
  `anonymous` tinyint(2) NOT NULL default '0',
  `email` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `question` (`comment`),
  FULLTEXT KEY `subject` (`referenceid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__doi_mapping`
#

CREATE TABLE IF NOT EXISTS `#__doi_mapping` (
  `local_revision` int(11) NOT NULL,
  `doi_label` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  `alias` varchar(30) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__event_registration`
#

CREATE TABLE IF NOT EXISTS `#__event_registration` (
  `id` int(11) NOT NULL auto_increment,
  `event` varchar(100) default NULL,
  `username` varchar(100) default NULL,
  `name` varchar(100) default NULL,
  `email` varchar(100) default NULL,
  `phone` varchar(100) default NULL,
  `institution` varchar(100) default NULL,
  `address` varchar(100) default NULL,
  `city` varchar(100) default NULL,
  `state` varchar(10) default NULL,
  `zip` varchar(10) default NULL,
  `submitted` datetime default NULL,
  `active` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__events`
#

CREATE TABLE IF NOT EXISTS `#__events` (
  `id` int(12) NOT NULL auto_increment,
  `sid` int(11) NOT NULL default '0',
  `catid` int(11) NOT NULL default '1',
  `title` varchar(255) NOT NULL default '',
  `content` longtext NOT NULL,
  `adresse_info` varchar(120) NOT NULL default '',
  `contact_info` varchar(120) NOT NULL default '',
  `extra_info` varchar(240) NOT NULL default '',
  `color_bar` varchar(8) NOT NULL default '',
  `useCatColor` tinyint(1) NOT NULL default '0',
  `state` tinyint(3) NOT NULL default '0',
  `mask` int(11) unsigned NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL default '0',
  `created_by_alias` varchar(100) NOT NULL default '',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` int(11) unsigned NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
  `images` text NOT NULL,
  `reccurtype` tinyint(1) NOT NULL default '0',
  `reccurday` varchar(4) NOT NULL default '',
  `reccurweekdays` varchar(20) NOT NULL default '',
  `reccurweeks` varchar(10) NOT NULL default '',
  `approved` tinyint(1) NOT NULL default '1',
  `announcement` tinyint(1) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `archived` tinyint(1) NOT NULL default '0',
  `access` int(11) unsigned NOT NULL default '0',
  `hits` int(11) NOT NULL default '0',
  `registerby` datetime NOT NULL default '0000-00-00 00:00:00',
  `params` text,
  `restricted` varchar(100) default NULL,
  `email` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `content` (`content`),
  FULLTEXT KEY `#__events_title_content_ftidx` (`title`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__events_categories`
#


CREATE TABLE IF NOT EXISTS `#__events_categories` (
  `id` int(12) NOT NULL default '0',
  `color` varchar(8) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__events_config`
#

CREATE TABLE IF NOT EXISTS `#__events_config` (
  `param` varchar(100) default NULL,
  `value` tinytext
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__events_pages`
#

CREATE TABLE IF NOT EXISTS `#__events_pages` (
  `id` int(11) NOT NULL auto_increment,
  `event_id` int(11) default '0',
  `alias` varchar(100) NOT NULL,
  `title` varchar(250) NOT NULL,
  `pagetext` text,
  `created` datetime default '0000-00-00 00:00:00',
  `created_by` int(11) default '0',
  `modified` datetime default '0000-00-00 00:00:00',
  `modified_by` int(11) default '0',
  `ordering` int(2) default '0',
  `params` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__events_respondent_race_rel`
#

CREATE TABLE IF NOT EXISTS `#__events_respondent_race_rel` (
  `respondent_id` int(11) default NULL,
  `race` varchar(255) default NULL,
  `tribal_affiliation` varchar(255) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__events_respondents`
#

CREATE TABLE IF NOT EXISTS `#__events_respondents` (
  `id` int(11) NOT NULL auto_increment,
  `event_id` int(11) NOT NULL default '0',
  `registered` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `affiliation` varchar(50) default NULL,
  `title` varchar(50) default NULL,
  `city` varchar(50) default NULL,
  `state` varchar(20) default NULL,
  `zip` varchar(10) default NULL,
  `country` varchar(20) default NULL,
  `telephone` varchar(20) default NULL,
  `fax` varchar(20) default NULL,
  `email` varchar(255) default NULL,
  `website` varchar(255) default NULL,
  `position_description` varchar(50) default NULL,
  `highest_degree` varchar(10) default NULL,
  `gender` char(1) default NULL,
  `disability_needs` tinyint(4) default NULL,
  `dietary_needs` varchar(500) default NULL,
  `attending_dinner` tinyint(4) default NULL,
  `abstract` text,
  `comment` text,
  `arrival` varchar(50) default NULL,
  `departure` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__faq`
#

CREATE TABLE IF NOT EXISTS `#__faq` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(250) default NULL,
  `alias` varchar(200) default NULL,
  `params` text,
  `fulltext` text,
  `created` datetime default '0000-00-00 00:00:00',
  `created_by` int(11) default '0',
  `modified` datetime default '0000-00-00 00:00:00',
  `modified_by` int(11) default '0',
  `checked_out` int(11) default '0',
  `checked_out_time` datetime default '0000-00-00 00:00:00',
  `state` int(3) default '0',
  `access` tinyint(3) default '0',
  `hits` int(11) default '0',
  `version` int(11) default '0',
  `section` int(11) NOT NULL default '0',
  `category` int(11) default '0',
  `helpful` int(11) NOT NULL default '0',
  `nothelpful` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `fulltext` (`fulltext`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `#__faq_title_introtext_fulltext_ftidx` (`title`,`params`,`fulltext`),
  FULLTEXT KEY `introtext` (`params`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__faq_categories`
#

CREATE TABLE IF NOT EXISTS `#__faq_categories` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(200) default NULL,
  `alias` varchar(200) default NULL,
  `description` text,
  `section` int(11) NOT NULL default '0',
  `state` tinyint(3) NOT NULL default '0',
  `access` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__faq_comments`
#

CREATE TABLE IF NOT EXISTS `#__faq_comments` (
  `id` int(11) NOT NULL auto_increment,
  `entry_id` int(11) default '0',
  `content` text character set latin1,
  `created` datetime default '0000-00-00 00:00:00',
  `created_by` int(11) default '0',
  `anonymous` tinyint(2) default '0',
  `parent` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__faq_helpful_log`
#

CREATE TABLE IF NOT EXISTS `#__faq_helpful_log` (
  `id` int(11) NOT NULL auto_increment,
  `object_id` int(11) default '0',
  `ip` varchar(15) default NULL,
  `vote` varchar(10) default NULL,
  `user_id` int(11) default '0',
  `type` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__feature_history`
#

CREATE TABLE IF NOT EXISTS `#__feature_history` (
  `id` int(11) NOT NULL auto_increment,
  `objectid` int(11) default NULL,
  `featured` datetime default '0000-00-00 00:00:00',
  `tbl` varchar(255) default NULL,
  `note` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__feedback`
#

CREATE TABLE IF NOT EXISTS `#__feedback` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) default NULL,
  `fullname` varchar(100) default '',
  `org` varchar(100) default '',
  `quote` text,
  `picture` varchar(250) default '',
  `date` datetime default '0000-00-00 00:00:00',
  `publish_ok` tinyint(1) default '0',
  `contact_ok` tinyint(1) default '0',
  `notes` text,
  `short_quote` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__groups`
#

CREATE TABLE IF NOT EXISTS `#__groups` (
  `id` tinyint(3) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__jobs_admins`
#

CREATE TABLE IF NOT EXISTS `#__jobs_admins` (
  `id` int(11) NOT NULL auto_increment,
  `jid` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__jobs_applications`
#

CREATE TABLE IF NOT EXISTS `#__jobs_applications` (
  `id` int(11) NOT NULL auto_increment,
  `jid` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `applied` datetime NOT NULL default '0000-00-00 00:00:00',
  `withdrawn` datetime default '0000-00-00 00:00:00',
  `cover` text,
  `resumeid` int(11) default '0',
  `status` int(11) default '1',
  `reason` varchar(255) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__jobs_categories`
#

CREATE TABLE IF NOT EXISTS `#__jobs_categories` (
  `id` int(11) NOT NULL auto_increment,
  `category` varchar(150) NOT NULL default '',
  `ordernum` int(11) NOT NULL default '0',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__jobs_categories` VALUES (1,'Scientific/Technical Staff',3,'');
INSERT INTO `#__jobs_categories` VALUES (2,'Post-Doctoral Researcher',2,'');
INSERT INTO `#__jobs_categories` VALUES (3,'Faculty Tenure & Tenure-Track',1,'');

#
# Table structure for table `#__jobs_employers`
#

CREATE TABLE IF NOT EXISTS `#__jobs_employers` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `subscriptionid` int(11) NOT NULL default '0',
  `companyName` varchar(250) default '',
  `companyLocation` varchar(250) default '',
  `companyWebsite` varchar(250) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__jobs_openings`
#

CREATE TABLE IF NOT EXISTS `#__jobs_openings` (
  `id` int(11) NOT NULL auto_increment,
  `cid` int(11) default '0',
  `employerid` int(11) NOT NULL default '0',
  `code` int(11) NOT NULL default '0',
  `title` varchar(200) NOT NULL default '',
  `companyName` varchar(200) NOT NULL default '',
  `companyLocation` varchar(200) default '',
  `companyLocationCountry` varchar(100) default '',
  `companyWebsite` varchar(200) default '',
  `description` text,
  `addedBy` int(11) NOT NULL default '0',
  `editedBy` int(11) default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `edited` datetime default '0000-00-00 00:00:00',
  `status` int(3) NOT NULL default '0',
  `type` int(3) NOT NULL default '0',
  `closedate` datetime default '0000-00-00 00:00:00',
  `opendate` datetime default '0000-00-00 00:00:00',
  `startdate` datetime default '0000-00-00 00:00:00',
  `applyExternalUrl` varchar(250) default '',
  `applyInternal` int(3) default '0',
  `contactName` varchar(100) default '',
  `contactEmail` varchar(100) default '',
  `contactPhone` varchar(100) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__jobs_prefs`
#

CREATE TABLE IF NOT EXISTS `#__jobs_prefs` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(10) NOT NULL default '0',
  `category` varchar(20) NOT NULL default 'resume',
  `filters` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__jobs_resumes`
#

CREATE TABLE IF NOT EXISTS `#__jobs_resumes` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(100) default NULL,
  `filename` varchar(100) default NULL,
  `main` tinyint(2) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__jobs_seekers`
#

CREATE TABLE IF NOT EXISTS `#__jobs_seekers` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `active` int(11) NOT NULL default '0',
  `lookingfor` varchar(255) default '',
  `tagline` varchar(255) default '',
  `linkedin` varchar(255) default '',
  `url` varchar(255) default '',
  `updated` datetime default '0000-00-00 00:00:00',
  `sought_cid` int(11) default '0',
  `sought_type` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__jobs_shortlist`
#

CREATE TABLE IF NOT EXISTS `#__jobs_shortlist` (
  `id` int(11) NOT NULL auto_increment,
  `emp` int(11) NOT NULL default '0',
  `seeker` int(11) NOT NULL default '0',
  `category` varchar(11) NOT NULL default 'resume',
  `jobid` int(11) default '0',
  `added` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__jobs_stats`
#

CREATE TABLE IF NOT EXISTS `#__jobs_stats` (
  `id` int(11) NOT NULL auto_increment,
  `itemid` int(11) NOT NULL,
  `category` varchar(11) NOT NULL default '',
  `total_viewed` int(11) default '0',
  `total_shared` int(11) default '0',
  `viewed_today` int(11) default '0',
  `lastviewed` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__jobs_types`
#

CREATE TABLE IF NOT EXISTS `#__jobs_types` (
  `id` int(11) NOT NULL auto_increment,
  `category` varchar(150) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__jobs_types` (`id`,`category`) VALUES ('1','Full-time');
INSERT INTO `#__jobs_types` (`id`,`category`) VALUES ('2','Part-time');
INSERT INTO `#__jobs_types` (`id`,`category`) VALUES ('3','Contract');
INSERT INTO `#__jobs_types` (`id`,`category`) VALUES ('4','Internship');
INSERT INTO `#__jobs_types` (`id`,`category`) VALUES ('5','Temporary');

#
# Table structure for table `#__licenses`
#

CREATE TABLE IF NOT EXISTS `#__licenses` (
  `id` int(11) NOT NULL auto_increment,
  `alias` varchar(255) default NULL,
  `description` text,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__licenses_tools`
#

CREATE TABLE IF NOT EXISTS `#__licenses_tools` (
  `license_id` int(11) default '0',
  `tool_id` int(11) default '0',
  `created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__licenses_users`
#

CREATE TABLE IF NOT EXISTS `#__licenses_users` (
  `license_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `created` datetime NOT NULL,
  PRIMARY KEY  (`license_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__market_history`
#

CREATE TABLE IF NOT EXISTS `#__market_history` (
  `id` int(11) NOT NULL auto_increment,
  `itemid` int(11) NOT NULL default '0',
  `category` varchar(50) default NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `action` varchar(50) default NULL,
  `log` text,
  `market_value` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__modifications`
#

CREATE TABLE IF NOT EXISTS `#__modifications` (
  `id` int(11) NOT NULL auto_increment,
  `component` varchar(31) default NULL,
  `item_label` varchar(100) default NULL,
  `action` varchar(31) default NULL,
  `when` datetime default NULL,
  `who` int(11) default NULL,
  `comments` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__myhub`
#

CREATE TABLE IF NOT EXISTS `#__myhub` (
  `uid` int(11) NOT NULL,
  `prefs` varchar(200) default NULL,
  `modified` datetime default '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__myhub_params`
#

CREATE TABLE IF NOT EXISTS `#__myhub_params` (
  `uid` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `params` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__order_items`
#

CREATE TABLE IF NOT EXISTS `#__order_items` (
  `id` int(10) NOT NULL auto_increment,
  `oid` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `itemid` int(11) NOT NULL default '0',
  `price` int(11) NOT NULL default '0',
  `quantity` int(11) NOT NULL default '0',
  `selections` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__orders`
#

CREATE TABLE IF NOT EXISTS `#__orders` (
  `id` int(10) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `type` varchar(20) default NULL,
  `total` int(11) default '0',
  `status` int(11) NOT NULL default '0',
  `details` text,
  `email` varchar(150) default NULL,
  `ordered` datetime NOT NULL default '0000-00-00 00:00:00',
  `status_changed` datetime default '0000-00-00 00:00:00',
  `notes` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__password_blacklist`
#

CREATE TABLE IF NOT EXISTS `#__password_blacklist` (
  `id` int(11) NOT NULL auto_increment,
  `word` char(32) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__password_character_class`
#

CREATE TABLE IF NOT EXISTS `#__password_character_class` (
  `flag` int(11) NOT NULL,
  `id` int(11) NOT NULL auto_increment,
  `name` char(32) NOT NULL,
  `regex` char(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__password_rule`
#

CREATE TABLE IF NOT EXISTS `#__password_rule` (
  `class` char(255) default NULL,
  `description` char(255) default NULL,
  `enabled` tinyint(1) NOT NULL default '0',
  `failuremsg` char(255) default NULL,
  `group` char(32) NOT NULL,
  `id` int(11) NOT NULL auto_increment,
  `ordering` int(11) NOT NULL default '0',
  `rule` char(255) default NULL,
  `value` char(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__plugin_params`
#

CREATE TABLE IF NOT EXISTS `#__plugin_params` (
  `id` int(11) NOT NULL auto_increment,
  `object_id` int(11) default '0',
  `folder` varchar(100) default NULL,
  `element` varchar(100) default NULL,
  `params` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__recent_tools`
#

CREATE TABLE IF NOT EXISTS `#__recent_tools` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `tool` varchar(200) default NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__redirection`
#

CREATE TABLE IF NOT EXISTS `#__redirection` (
  `id` int(11) NOT NULL auto_increment,
  `cpt` int(11) NOT NULL default '0',
  `oldurl` varchar(100) NOT NULL default '',
  `newurl` varchar(150) NOT NULL default '',
  `dateadd` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`),
  KEY `newurl` (`newurl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__resource_assoc`
#

CREATE TABLE IF NOT EXISTS `#__resource_assoc` (
  `parent_id` int(11) NOT NULL default '0',
  `child_id` int(11) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `grouping` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__resource_ratings`
#

CREATE TABLE IF NOT EXISTS `#__resource_ratings` (
  `id` int(11) NOT NULL auto_increment,
  `resource_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `rating` decimal(2,1) NOT NULL default '0.0',
  `comment` text NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `anonymous` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__resource_stats`
#

CREATE TABLE IF NOT EXISTS `#__resource_stats` (
  `id` bigint(20) NOT NULL auto_increment,
  `resid` bigint(20) NOT NULL,
  `restype` int(11) default NULL,
  `users` bigint(20) default NULL,
  `jobs` bigint(20) default NULL,
  `avg_wall` int(20) default NULL,
  `tot_wall` int(20) default NULL,
  `avg_cpu` int(20) default NULL,
  `tot_cpu` int(20) default NULL,
  `datetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL default '-1',
  `processed_on` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `res_stats` (`resid`,`restype`,`datetime`,`period`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__resource_stats_tools`
#

CREATE TABLE IF NOT EXISTS `#__resource_stats_tools` (
  `id` bigint(20) NOT NULL auto_increment,
  `resid` bigint(20) NOT NULL,
  `restype` int(11) NOT NULL,
  `users` bigint(20) default NULL,
  `sessions` bigint(20) default NULL,
  `simulations` bigint(20) default NULL,
  `jobs` bigint(20) default NULL,
  `avg_wall` double unsigned default '0',
  `tot_wall` double unsigned default '0',
  `avg_cpu` double unsigned default '0',
  `tot_cpu` double unsigned default '0',
  `avg_view` double unsigned default '0',
  `tot_view` double unsigned default '0',
  `avg_wait` double unsigned default '0',
  `tot_wait` double unsigned default '0',
  `avg_cpus` int(20) default NULL,
  `tot_cpus` int(20) default NULL,
  `datetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL default '-1',
  `processed_on` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__resource_stats_tools_tops`
#

CREATE TABLE IF NOT EXISTS `#__resource_stats_tools_tops` (
  `top` tinyint(4) NOT NULL default '0',
  `name` varchar(128) NOT NULL default '',
  `valfmt` tinyint(4) NOT NULL default '0',
  `size` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`top`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__resource_stats_tools_topvals`
#

CREATE TABLE IF NOT EXISTS `#__resource_stats_tools_topvals` (
  `id` bigint(20) NOT NULL,
  `top` tinyint(4) NOT NULL default '0',
  `rank` tinyint(4) NOT NULL default '0',
  `name` varchar(255) default NULL,
  `value` bigint(20) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__resource_stats_tools_users`
#

CREATE TABLE IF NOT EXISTS `#__resource_stats_tools_users` (
  `id` bigint(20) NOT NULL auto_increment,
  `resid` bigint(20) NOT NULL,
  `restype` int(11) NOT NULL,
  `user` varchar(32) NOT NULL default '',
  `sessions` bigint(20) default NULL,
  `simulations` bigint(20) default NULL,
  `jobs` bigint(20) default NULL,
  `tot_wall` double unsigned default '0',
  `tot_cpu` double unsigned default '0',
  `tot_view` double unsigned default '0',
  `datetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL default '-1',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__resource_tags`
#
CREATE TABLE IF NOT EXISTS `#__resource_tags` (
  `id` int(11) NOT NULL auto_increment,
  `resourceid` int(11) default NULL,
  `tagid` int(11) default NULL,
  `strength` tinyint(3) default '0',
  `taggerid` int(11) default '0',
  `taggedon` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__resource_taxonomy_audience`
#

CREATE TABLE IF NOT EXISTS `#__resource_taxonomy_audience` (
  `id` int(11) NOT NULL auto_increment,
  `rid` int(11) NOT NULL default '0',
  `versionid` int(11) default '0',
  `level0` tinyint(2) NOT NULL default '0',
  `level1` tinyint(2) NOT NULL default '0',
  `level2` tinyint(2) NOT NULL default '0',
  `level3` tinyint(2) NOT NULL default '0',
  `level4` tinyint(2) NOT NULL default '0',
  `level5` tinyint(2) NOT NULL default '0',
  `comments` varchar(255) default '',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `addedBy` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__resource_taxonomy_audience_levels`
#

CREATE TABLE IF NOT EXISTS `#__resource_taxonomy_audience_levels` (
  `id` int(11) NOT NULL auto_increment,
  `label` varchar(11) NOT NULL default '0',
  `title` varchar(100) default '',
  `description` varchar(255) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__resource_types`
#

CREATE TABLE IF NOT EXISTS `#__resource_types` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(200) NOT NULL default '',
  `category` int(11) NOT NULL default '0',
  `description` tinytext,
  `contributable` int(2) default '1',
  `customFields` text,
  `params` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__resource_types` VALUES (1,'Seminars',27,'A lecture of some sort, usually recorded with voice or video.  It may be a graduate or undergraduate level seminar, a lecture for a class, or a tutorial presentation.',1,'bio=Bio=textarea=0\ncredits=Credits=textarea=0\nsponsoredby=Sponsored by=textarea=0',NULL);
INSERT INTO `#__resource_types` VALUES (2,'Workshops',27,'A collection of lectures, seminars, and materials that were presented at a workshop.',0,'credits=Credits=textarea=0\nsponsoredby=Sponsored by=textarea=0','plg_citations=0\nplg_questions=0\nplg_recommendations=1\nplg_related=1\nplg_reviews=1\nplg_usage=0\nplg_versions=0\nplg_favorite=1\nplg_share=1\nplg_wishlist=0\nplg_supportingdocs=1');
INSERT INTO `#__resource_types` VALUES (3,'Publications',27,'Articles, technical reports, theses, and other documents, usually in PDF or DOC format.',1,'acknowledgments=Acknowledgments=textarea=0\nreferences=References=textarea=0','plg_citations=0\nplg_questions=0\nplg_recommendations=1\nplg_related=1\nplg_reviews=1\nplg_usage=0\nplg_versions=0\nplg_favorite=1\nplg_share=1\nplg_wishlist=0\nplg_supportingdocs=1');
INSERT INTO `#__resource_types` VALUES (6,'Courses',27,'University courses and short courses with lectures and associated teaching materials.',0,'credits=Credits=textarea=0\nreferences=References=textarea=0\nsponsoredby=Sponsored by=textarea=0','plg_citations=0\nplg_questions=0\nplg_recommendations=1\nplg_related=1\nplg_reviews=1\nplg_usage=0\nplg_versions=0\nplg_favorite=1\nplg_share=1\nplg_wishlist=0\nplg_supportingdocs=1');
INSERT INTO `#__resource_types` VALUES (7,'Tools',27,'Simulation and modeling tools that can be accessed via a web browser.',1,'poweredby=Powered by=textarea=0\ncredits=Credits=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0','plg_citations=1\nplg_questions=1\nplg_recommendations=1\nplg_related=1\nplg_reviews=1\nplg_usage=1\nplg_versions=1\nplg_favorite=1\nplg_share=1\nplg_wishlist=1\nplg_supportingdocs=1');
INSERT INTO `#__resource_types` VALUES (8,'Simulation Tool Sets',-1,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (9,'Downloads',27,'Spreadsheets, executables, and other items that are available for download but don\\\'t fit into other categories.',1,'credits=Credits=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=text=0','plg_citations=0\nplg_questions=0\nplg_recommendations=1\nplg_related=1\nplg_reviews=1\nplg_usage=0\nplg_versions=0\nplg_favorite=1\nplg_share=1\nplg_wishlist=0\nplg_supportingdocs=1');
INSERT INTO `#__resource_types` VALUES (11,'External Link',30,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (12,'Internal Link',30,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (13,'File',30,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (14,'Presentation Slides',28,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (15,'Quicktime',30,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (16,'Examples',29,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (17,'Exercises',29,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (18,'References',29,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (19,'Presentation (without audio)',28,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (20,'Presentation (with audio)',28,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (21,'Sub Type',0,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (22,'Research Seminars',21,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (23,'Troubleshooting',29,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (24,'How to ...',29,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (25,'Advanced Exercises',29,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (26,'Flash Paper',30,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (27,'Main Types',0,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (28,'Logical Type',0,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (29,'Group',0,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (30,'Type',0,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (31,'Series',27,'Series are collections of lectures, publications, and other resources presented as a list.  Each series is available as a podcast feed.',0,'credits=Credits=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0','plg_citations=0\nplg_questions=0\nplg_recommendations=1\nplg_related=1\nplg_reviews=1\nplg_usage=0\nplg_versions=0\nplg_favorite=1\nplg_share=1\nplg_wishlist=0\nplg_supportingdocs=1');
INSERT INTO `#__resource_types` VALUES (32,'Breeze',30,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (33,'PDF',30,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (34,'Quiz',28,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (35,'PowerPoint',30,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (36,'Poster',28,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (37,'Media Player',30,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (38,'Package',30,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (39,'Teaching Materials',27,'Supplementary materials (study notes, guides, etc.) that don\\\'t quite fit into any of the other categories.',1,'references=References=textarea=0','plg_citations=0\nplg_questions=0\nplg_recommendations=1\nplg_related=1\nplg_reviews=1\nplg_usage=0\nplg_versions=0\nplg_favorite=1\nplg_share=1\nplg_wishlist=0\nplg_supportingdocs=1');
INSERT INTO `#__resource_types` VALUES (40,'Video Stream',28,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (41,'Video',28,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (44,'Nanotechnology',29,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (45,'Chemistry',29,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (46,'Semiconductors and Circuits',29,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (47,'Other Tools',29,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (48,'Tutorials',21,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (49,'Podcast (audio)',28,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (50,'Podcast (video)',28,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (51,'Homework Assignment',28,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (52,'MOS Capacitor Examples',29,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (53,'Dual Gate Examples',29,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (54,'Course Lectures',21,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (55,'Ph.D. Thesis',28,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (56,'Publication Preprint',28,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (57,'Handout',28,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (58,'Undergraduate Presentation',21,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (59,'Manual',28,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (60,'Software Download',28,NULL,1,NULL,NULL);
INSERT INTO `#__resource_types` VALUES (61,'Exercise Solutions',29,NULL,1,NULL,NULL);

#
# Table structure for table `#__resources`
#

CREATE TABLE IF NOT EXISTS `#__resources` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(250) NOT NULL default '',
  `type` int(11) NOT NULL default '0',
  `logical_type` int(11) NOT NULL default '0',
  `introtext` text NOT NULL,
  `fulltext` text NOT NULL,
  `footertext` text NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL default '0',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL default '0',
  `published` int(1) NOT NULL default '0',
  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
  `access` int(11) NOT NULL default '0',
  `hits` int(11) NOT NULL default '0',
  `path` varchar(200) NOT NULL default '',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `standalone` tinyint(1) NOT NULL default '0',
  `group_owner` varchar(250) NOT NULL default '',
  `group_access` text,
  `rating` decimal(2,1) NOT NULL default '0.0',
  `times_rated` int(11) NOT NULL default '0',
  `params` text,
  `attribs` text,
  `alias` varchar(100) NOT NULL default '',
  `ranking` float NOT NULL default '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `introtext` (`introtext`,`fulltext`),
  FULLTEXT KEY `#__resources_title_introtext_fulltext_ftidx` (`title`,`introtext`,`fulltext`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__screenshots`
#

CREATE TABLE IF NOT EXISTS `#__screenshots` (
  `id` int(10) NOT NULL auto_increment,
  `versionid` int(11) default '0',
  `title` varchar(127) default '',
  `ordering` int(11) default '0',
  `filename` varchar(100) NOT NULL,
  `resourceid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__selected_quotes`
#

CREATE TABLE IF NOT EXISTS `#__selected_quotes` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) default '0',
  `fullname` varchar(100) default '',
  `org` varchar(200) default '',
  `miniquote` varchar(200) default '',
  `short_quote` text,
  `quote` text,
  `picture` varchar(250) default '',
  `date` datetime default '0000-00-00 00:00:00',
  `flash_rotation` tinyint(1) default '0',
  `notable_quotes` tinyint(1) default '1',
  `notes` text,
  PRIMARY KEY  (`id`)

) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__sites`
#

CREATE TABLE IF NOT EXISTS `#__sites` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(100) default NULL,
  `category` varchar(100) default NULL,
  `url` varchar(255) default NULL,
  `image` varchar(255) default NULL,
  `teaser` varchar(255) default NULL,
  `description` text,
  `notes` text,
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL default '0',
  `published_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `state` varchar(30) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__stats_agents`
#

CREATE TABLE IF NOT EXISTS `#__stats_agents` (
  `agent` varchar(255) NOT NULL default '',
  `type` tinyint(1) unsigned NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__stats_tops`
#

CREATE TABLE IF NOT EXISTS `#__stats_tops` (
  `id` tinyint(4) NOT NULL default '0',
  `name` varchar(128) NOT NULL default '',
  `valfmt` tinyint(4) NOT NULL default '0',
  `size` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__stats_topvals`
#

CREATE TABLE IF NOT EXISTS `#__stats_topvals` (
  `top` tinyint(4) NOT NULL default '0',
  `datetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL default '1',
  `rank` tinyint(4) NOT NULL default '0',
  `name` varchar(255) default NULL,
  `value` bigint(20) NOT NULL default '0',
  KEY `top` (`top`),
  KEY `top_2` (`top`,`rank`),
  KEY `top_3` (`top`,`datetime`),
  KEY `top_4` (`top`,`datetime`,`rank`),
  KEY `top_5` (`top`,`datetime`,`period`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__store`
#

CREATE TABLE IF NOT EXISTS `#__store` (
  `id` int(10) NOT NULL auto_increment,
  `title` varchar(127) NOT NULL default '',
  `price` int(11) NOT NULL default '0',
  `description` text,
  `published` tinyint(1) NOT NULL default '0',
  `featured` tinyint(1) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `available` int(1) NOT NULL default '0',
  `params` text,
  `special` int(11) default '0',
  `type` int(11) default '1',
  `category` varchar(127) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__support_acl_acos`
#

CREATE TABLE IF NOT EXISTS `#__support_acl_acos` (
  `id` int(11) NOT NULL auto_increment,
  `model` varchar(100) default NULL,
  `foreign_key` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__support_acl_aros`
#

CREATE TABLE IF NOT EXISTS `#__support_acl_aros` (
  `id` int(11) NOT NULL auto_increment,
  `model` varchar(100) default NULL,
  `foreign_key` int(11) default '0',
  `alias` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__support_acl_aros_acos`
#

CREATE TABLE IF NOT EXISTS `#__support_acl_aros_acos` (
  `id` int(11) NOT NULL auto_increment,
  `aro_id` int(11) default '0',
  `aco_id` int(11) default '0',
  `action_create` int(3) default '0',
  `action_read` int(3) default '0',
  `action_update` int(3) default '0',
  `action_delete` int(3) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__support_attachments`
#

CREATE TABLE IF NOT EXISTS `#__support_attachments` (
  `id` int(11) NOT NULL auto_increment,
  `ticket` int(11) NOT NULL default '0',
  `filename` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__support_categories`
#

CREATE TABLE IF NOT EXISTS `#__support_categories` (
  `id` int(11) NOT NULL auto_increment,
  `section` int(11) default '0',
  `category` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__support_comments`
#

CREATE TABLE IF NOT EXISTS `#__support_comments` (
  `id` int(11) NOT NULL auto_increment,
  `ticket` int(11) NOT NULL default '0',
  `comment` text,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` varchar(50) default NULL,
  `changelog` text,
  `access` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__support_messages`
#

CREATE TABLE IF NOT EXISTS `#__support_messages` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(250) default NULL,
  `message` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__support_messages` VALUES (1,'First Contact (no further information needed)','Thank you for using {sitename}, and for reporting this problem.  Your request has been forwarded to a member of our team, and it is being tracked as ticket {ticket#} in our system.  We will keep you informed as we make progress in resolving this issue.\r\n\r\nThanks again for your support!\r\n--the {sitename} team');
INSERT INTO `#__support_messages` VALUES (2,'First Contact (more information is needed)','Thank you for using {sitename}, and for reporting this problem.  Your request has been forwarded to a member of our team, and it is being tracked as ticket {ticket#} in our system.\r\n\r\nIn order to resolve this issue, we need some more information:\r\n\r\nXXXXXXX  Input questions here XXXXXX\r\n\r\nPlease reply back to {siteemail} with the requested information. If we haven\\\'t heard back from you in 48 hours, we\\\'ll assume that you are no longer experiencing the problem, or that you\\\'ve worked around it, and we\\\'ll consider the matter closed.  You can reopen the matter at any time by sending email or by submitting another problem report on our web site.\r\n\r\nThanks again for your support!\r\n--the {sitename} team');
INSERT INTO `#__support_messages` VALUES (3,'Final Contact (closing ticket)','We haven not heard back from you so we will assume that you are no\r\nlonger experiencing the problem, or that you\\\'ve worked around it, and we\\\'ll consider the matter closed.  You can reopen the matter at any time by sending email or by submitting another problem report on our web site.\r\n\r\nThanks again for your support!\r\n--the {sitename} team');
INSERT INTO `#__support_messages` VALUES (4,'Ticket Resolved','Thank you for using {sitename}, and for reporting this problem.  We believe that your issue (ticket {ticket#} in our system) has been resolved. If you continue to have problems please let us know.\r\n\r\nThank you for helping us to improve {sitename}!\r\n--the {sitename} team');
INSERT INTO `#__support_messages` VALUES (5,'Reply to tickets that have been in the queue for a while','Thank you for using {sitename}.  We apologize for not responding to your request sooner.  We have received more support requests than we can handle, and we are working hard to improve our help-desk support.\r\n\r\nYour request was concerning XXXXXXXXXXXXX. \r\n\r\nMany problems have been fixed since your message.  Is this still a problem for you now?  Is there any more information you could give us about this? Please reply back to {siteemail} with any additional information. If we haven\\\'t heard back from you in 48 hours, we\\\'ll assume that you are no longer experiencing the problem, or that it is no longer an issue for you, and we\\\'ll consider the matter closed.  You can reopen the matter at any time by sending email or by submitting another problem report on our web site.\r\n\r\nThanks again for your support!\r\n--the {sitename} team');

#
# Table structure for table `#__support_resolutions`
#

CREATE TABLE IF NOT EXISTS `#__support_resolutions` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(100) default NULL,
  `alias` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__support_resolutions` VALUES (1,'Fixed','fixed');
INSERT INTO `#__support_resolutions` VALUES (2,'Invalid','invalid');
INSERT INTO `#__support_resolutions` VALUES (3,'Won\'t fix','wontfix');
INSERT INTO `#__support_resolutions` VALUES (4,'Duplicate','duplicate');
INSERT INTO `#__support_resolutions` VALUES (5,'Works for me','worksforme');
INSERT INTO `#__support_resolutions` VALUES (6,'Transferred','transferred');
INSERT INTO `#__support_resolutions` VALUES (7,'Answered','answered');

#
# Table structure for table `#__support_sections`
#

CREATE TABLE IF NOT EXISTS `#__support_sections` (
  `id` int(11) NOT NULL auto_increment,
  `section` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__support_tags`
#

CREATE TABLE IF NOT EXISTS `#__support_tags` (
  `id` int(11) NOT NULL auto_increment,
  `ticketid` int(11) default NULL,
  `tagid` int(11) default NULL,
  `strength` tinyint(3) default '0',
  `taggerid` int(11) default '0',
  `taggedon` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__support_tickets`
#

CREATE TABLE IF NOT EXISTS `#__support_tickets` (
  `id` int(11) NOT NULL auto_increment,
  `status` tinyint(3) default '0',
  `created` datetime default '0000-00-00 00:00:00',
  `login` varchar(200) default NULL,
  `severity` varchar(30) default NULL,
  `owner` varchar(50) default NULL,
  `category` varchar(50) default NULL,
  `summary` varchar(250) default NULL,
  `report` text,
  `resolved` varchar(50) default NULL,
  `email` varchar(200) default NULL,
  `name` varchar(200) default NULL,
  `os` varchar(50) default NULL,
  `browser` varchar(50) default NULL,
  `ip` varchar(200) default NULL,
  `hostname` varchar(200) default NULL,
  `uas` varchar(250) default NULL,
  `referrer` varchar(250) default NULL,
  `cookies` tinyint(3) NOT NULL default '0',
  `instances` int(11) NOT NULL default '1',
  `section` int(11) NOT NULL default '1',
  `type` tinyint(3) NOT NULL default '0',
  `group` varchar(250) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__tags`
#

CREATE TABLE IF NOT EXISTS `#__tags` (
  `id` int(11) NOT NULL auto_increment,
  `tag` varchar(100) default NULL,
  `raw_tag` varchar(100) default NULL,
  `alias` varchar(100) default NULL,
  `description` text,
  `admin` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `description` (`description`),
  FULLTEXT KEY `#__tags_raw_tag_alias_description_ftidx` (`raw_tag`,`alias`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__tags_group`
#

CREATE TABLE IF NOT EXISTS `#__tags_group` (
  `id` int(11) NOT NULL auto_increment,
  `groupid` int(11) default '0',
  `tagid` int(11) default '0',
  `priority` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__tags_object`
#

CREATE TABLE IF NOT EXISTS `#__tags_object` (
  `id` int(11) NOT NULL auto_increment,
  `objectid` int(11) default NULL,
  `tagid` int(11) default NULL,
  `strength` tinyint(3) default '0',
  `taggerid` int(11) default '0',
  `taggedon` datetime default '0000-00-00 00:00:00',
  `tbl` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__tool`
#

CREATE TABLE IF NOT EXISTS `#__tool` (
  `id` int(10) NOT NULL auto_increment,
  `toolname` varchar(64) NOT NULL default '',
  `title` varchar(127) NOT NULL default '',
  `version` varchar(15) default NULL,
  `description` text,
  `fulltext` text,
  `license` text,
  `toolaccess` varchar(15) default NULL,
  `codeaccess` varchar(15) default NULL,
  `wikiaccess` varchar(15) default NULL,
  `published` tinyint(1) default '0',
  `state` int(15) default NULL,
  `priority` int(15) default '3',
  `team` text,
  `registered` datetime default NULL,
  `registered_by` varchar(31) default NULL,
  `mw` varchar(31) default NULL,
  `vnc_geometry` varchar(31) default NULL,
  `ticketid` int(15) default NULL,
  `state_changed` datetime default '0000-00-00 00:00:00',
  `revision` int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `toolname` (`toolname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__tool_authors`
#

CREATE TABLE IF NOT EXISTS `#__tool_authors` (
  `toolname` varchar(50) NOT NULL default '',
  `revision` int(15) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `ordering` int(11) default '0',
  `version_id` int(11) NOT NULL default '0',
  `name` varchar(255) default NULL,
  `organization` varchar(255) default NULL,
  PRIMARY KEY  (`toolname`,`revision`,`uid`,`version_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__tool_groups`
#

CREATE TABLE IF NOT EXISTS `#__tool_groups` (
  `cn` varchar(255) NOT NULL default '',
  `toolid` int(11) NOT NULL default '0',
  `role` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`cn`,`toolid`,`role`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__tool_licenses`
#

CREATE TABLE IF NOT EXISTS `#__tool_licenses` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  `text` text,
  `title` varchar(100) default NULL,
  `ordering` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__tool_statusviews`
#

CREATE TABLE IF NOT EXISTS `#__tool_statusviews` (
  `id` int(10) NOT NULL auto_increment,
  `ticketid` varchar(15) NOT NULL default '',
  `uid` varchar(31) NOT NULL default '',
  `viewed` datetime default '0000-00-00 00:00:00',
  `elapsed` int(11) default '500000',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__tool_version`
#

CREATE TABLE IF NOT EXISTS `#__tool_version` (
  `id` int(10) NOT NULL auto_increment,
  `toolname` varchar(64) NOT NULL default '',
  `instance` varchar(31) NOT NULL default '',
  `title` varchar(127) NOT NULL default '',
  `description` text,
  `fulltext` text,
  `version` varchar(15) default NULL,
  `revision` int(11) default NULL,
  `toolaccess` varchar(15) default NULL,
  `codeaccess` varchar(15) default NULL,
  `wikiaccess` varchar(15) default NULL,
  `state` int(15) default NULL,
  `released_by` varchar(31) default NULL,
  `released` datetime default NULL,
  `unpublished` datetime default NULL,
  `exportControl` varchar(16) default NULL,
  `license` text,
  `vnc_geometry` varchar(31) default NULL,
  `vnc_depth` int(11) default NULL,
  `vnc_timeout` int(11) default NULL,
  `vnc_command` varchar(100) default NULL,
  `mw` varchar(31) default NULL,
  `toolid` int(11) default NULL,
  `priority` int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `toolname` (`toolname`,`instance`),
  KEY `instance` (`instance`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__tool_version_alias`
#

CREATE TABLE IF NOT EXISTS `#__tool_version_alias` (
  `tool_version_id` int(11) NOT NULL,
  `alias` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__tool_version_hostreq`
#

CREATE TABLE IF NOT EXISTS `#__tool_version_hostreq` (
  `tool_version_id` int(11) NOT NULL,
  `hostreq` varchar(255) NOT NULL,
  UNIQUE KEY `toolid` (`tool_version_id`,`hostreq`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__tool_version_middleware`
#

CREATE TABLE IF NOT EXISTS `#__tool_version_middleware` (
  `tool_version_id` int(11) NOT NULL,
  `middleware` varchar(255) NOT NULL,
  UNIQUE KEY `toolid` (`tool_version_id`,`middleware`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__tool_version_tracperm`
#

CREATE TABLE IF NOT EXISTS `#__tool_version_tracperm` (
  `tool_version_id` int(11) NOT NULL,
  `tracperm` varchar(64) NOT NULL,
  UNIQUE KEY `toolid` (`tool_version_id`,`tracperm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__trac_group_permission`
#

CREATE TABLE IF NOT EXISTS `#__trac_group_permission` (
  `id` int(11) NOT NULL auto_increment,
  `group_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `trac_project_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `trac_action` USING BTREE (`group_id`,`action`,`trac_project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__trac_project`
#

CREATE TABLE IF NOT EXISTS `#__trac_project` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__trac_projects`
#

CREATE TABLE IF NOT EXISTS `#__trac_projects` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__trac_user_permission`
#

CREATE TABLE IF NOT EXISTS `#__trac_user_permission` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `action` varchar(255) default NULL,
  `trac_project_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `trac_action` USING BTREE (`user_id`,`action`,`trac_project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__users_password`
#

CREATE TABLE IF NOT EXISTS `#__users_password` (
  `passhash` char(127) NOT NULL,
  `shadowExpire` int(11) default NULL,
  `shadowFlag` int(11) default NULL,
  `shadowInactive` int(11) default NULL,
  `shadowLastChange` int(11) default NULL,
  `shadowMax` int(11) default NULL,
  `shadowMin` int(11) default NULL,
  `shadowWarning` int(11) default NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__users_password_history`
#

CREATE TABLE IF NOT EXISTS `#__users_password_history` (
  `action` int(11) default NULL,
  `created` datetime default NULL,
  `created_by` int(11) default NULL,
  `invalidated` datetime default NULL,
  `invalidated_by` int(11) default NULL,
  `passhash` char(32) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__users_points`
#

CREATE TABLE IF NOT EXISTS `#__users_points` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `balance` int(11) NOT NULL default '0',
  `earnings` int(11) NOT NULL default '0',
  `credit` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__users_points_config`
#

CREATE TABLE IF NOT EXISTS `#__users_points_config` (
  `id` int(11) NOT NULL auto_increment,
  `points` int(11) default '0',
  `description` varchar(255) default NULL,
  `alias` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__users_points_services`
#

CREATE TABLE IF NOT EXISTS `#__users_points_services` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(250) NOT NULL default '',
  `category` varchar(50) NOT NULL default '',
  `alias` varchar(50) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `unitprice` float(6,2) default '0.00',
  `pointsprice` int(11) default '0',
  `currency` varchar(50) default 'points',
  `maxunits` int(11) default '0',
  `minunits` int(11) default '0',
  `unitsize` int(11) default '0',
  `status` int(11) default '0',
  `restricted` int(11) default '0',
  `ordering` int(11) default '0',
  `params` text,
  `unitmeasure` varchar(200) NOT NULL default '',
  `changed` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `alias` (`alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__users_points_subscriptions`
#

CREATE TABLE IF NOT EXISTS `#__users_points_subscriptions` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `serviceid` int(11) NOT NULL default '0',
  `units` int(11) NOT NULL default '1',
  `status` int(11) NOT NULL default '0',
  `pendingunits` int(11) default '0',
  `pendingpayment` float(6,2) default '0.00',
  `totalpaid` float(6,2) default '0.00',
  `installment` int(11) default '0',
  `contact` varchar(20) default '',
  `code` varchar(10) default '',
  `usepoints` tinyint(2) default '0',
  `notes` text,
  `added` datetime NOT NULL,
  `updated` datetime default NULL,
  `expires` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__users_tracperms`
#

CREATE TABLE IF NOT EXISTS `#__users_tracperms` (
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__users_transactions`
#

CREATE TABLE IF NOT EXISTS `#__users_transactions` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `type` varchar(20) default NULL,
  `description` varchar(250) default NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `category` varchar(50) default NULL,
  `referenceid` int(11) default '0',
  `amount` int(11) default '0',
  `balance` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__vote_log`
#

CREATE TABLE IF NOT EXISTS `#__vote_log` (
  `id` int(11) NOT NULL auto_increment,
  `referenceid` int(11) NOT NULL default '0',
  `voted` datetime NOT NULL default '0000-00-00 00:00:00',
  `voter` int(11) default NULL,
  `helpful` varchar(11) default NULL,
  `ip` varchar(15) default NULL,
  `category` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__wiki_attachments`
#

CREATE TABLE IF NOT EXISTS `#__wiki_attachments` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) default '0',
  `filename` varchar(255) default NULL,
  `description` tinytext,
  `created` datetime default '0000-00-00 00:00:00',
  `created_by` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__wiki_comments`
#

CREATE TABLE IF NOT EXISTS `#__wiki_comments` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL default '0',
  `version` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL default '0',
  `ctext` text,
  `chtml` text,
  `rating` tinyint(1) NOT NULL default '0',
  `anonymous` tinyint(1) NOT NULL default '0',
  `parent` int(11) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__wiki_log`
#

CREATE TABLE IF NOT EXISTS `#__wiki_log` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `timestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `uid` int(11) default '0',
  `action` varchar(50) default NULL,
  `comments` text,
  `actorid` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__wiki_math`
#

CREATE TABLE IF NOT EXISTS `#__wiki_math` (
  `inputhash` varbinary(16) NOT NULL,
  `outputhash` varbinary(16) NOT NULL,
  `conservativeness` tinyint(4) NOT NULL,
  `html` text,
  `mathml` text,
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `inputhash` (`inputhash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__wiki_page`
#

CREATE TABLE IF NOT EXISTS `#__wiki_page` (
  `id` int(11) NOT NULL auto_increment,
  `pagename` varchar(100) default NULL,
  `hits` int(11) NOT NULL default '0',
  `created_by` int(11) NOT NULL default '0',
  `rating` decimal(2,1) NOT NULL default '0.0',
  `times_rated` int(11) NOT NULL default '0',
  `title` varchar(255) default NULL,
  `scope` varchar(255) NOT NULL,
  `params` tinytext,
  `ranking` float default '0',
  `authors` varchar(255) default NULL,
  `access` tinyint(2) default '0',
  `group` varchar(255) default NULL,
  `state` tinyint(2) default '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__wiki_version`
#

CREATE TABLE IF NOT EXISTS `#__wiki_version` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL default '0',
  `version` int(11) NOT NULL default '0',
  `created` datetime default NULL,
  `created_by` int(11) NOT NULL default '0',
  `minor_edit` int(1) NOT NULL default '0',
  `pagetext` text,
  `pagehtml` text,
  `approved` int(1) NOT NULL default '0',
  `summary` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `pagetext` (`pagetext`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__wish_attachments`
#

CREATE TABLE IF NOT EXISTS `#__wish_attachments` (
  `id` int(11) NOT NULL auto_increment,
  `wish` int(11) NOT NULL default '0',
  `filename` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__wishlist`
#

CREATE TABLE IF NOT EXISTS `#__wishlist` (
  `id` int(11) NOT NULL auto_increment,
  `category` varchar(50) NOT NULL,
  `referenceid` int(11) NOT NULL default '0',
  `title` varchar(150) NOT NULL,
  `created_by` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `state` int(3) NOT NULL default '0',
  `public` int(3) NOT NULL default '1',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__wishlist_implementation`
#

CREATE TABLE IF NOT EXISTS `#__wishlist_implementation` (
  `id` int(11) NOT NULL auto_increment,
  `wishid` int(11) NOT NULL default '0',
  `version` int(11) NOT NULL default '0',
  `created` datetime default NULL,
  `created_by` int(11) NOT NULL default '0',
  `minor_edit` int(1) NOT NULL default '0',
  `pagetext` text,
  `pagehtml` text,
  `approved` int(1) NOT NULL default '0',
  `summary` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `pagetext` (`pagetext`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__wishlist_item`
#

CREATE TABLE IF NOT EXISTS `#__wishlist_item` (
  `id` int(11) NOT NULL auto_increment,
  `wishlist` int(11) default '0',
  `subject` varchar(200) NOT NULL,
  `about` text,
  `proposed_by` int(11) default '0',
  `granted_by` int(11) default '0',
  `assigned` int(11) default '0',
  `granted_vid` int(11) default '0',
  `proposed` datetime NOT NULL default '0000-00-00 00:00:00',
  `granted` datetime default '0000-00-00 00:00:00',
  `status` int(3) NOT NULL default '0',
  `due` datetime default '0000-00-00 00:00:00',
  `anonymous` int(3) default '0',
  `ranking` int(11) default '0',
  `points` int(11) default '0',
  `private` int(3) default '0',
  `accepted` int(3) default '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `#__wishlist_item_subject_about_ftidx` (`subject`,`about`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__wishlist_ownergroups`
#

CREATE TABLE IF NOT EXISTS `#__wishlist_ownergroups` (
  `id` int(11) NOT NULL auto_increment,
  `wishlist` int(11) default '0',
  `groupid` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__wishlist_owners`
#

CREATE TABLE IF NOT EXISTS `#__wishlist_owners` (
  `id` int(11) NOT NULL auto_increment,
  `wishlist` int(11) default '0',
  `userid` int(11) NOT NULL default '0',
  `type` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__wishlist_vote`
#

CREATE TABLE IF NOT EXISTS `#__wishlist_vote` (
  `id` int(11) NOT NULL auto_increment,
  `wishid` int(11) default '0',
  `userid` int(11) NOT NULL default '0',
  `voted` datetime NOT NULL default '0000-00-00 00:00:00',
  `importance` int(3) default '0',
  `effort` int(3) default '0',
  `due` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xdomain_users`
#

CREATE TABLE IF NOT EXISTS `#__xdomain_users` (
  `domain_id` int(11) NOT NULL,
  `domain_username` varchar(150) NOT NULL default '',
  `uidNumber` int(11) default NULL,
  PRIMARY KEY  (`domain_id`,`domain_username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xdomains`
#

CREATE TABLE IF NOT EXISTS `#__xdomains` (
  `domain_id` int(11) NOT NULL auto_increment,
  `domain` varchar(150) NOT NULL default '',
  PRIMARY KEY  (`domain_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xfavorites`
#

CREATE TABLE IF NOT EXISTS `#__xfavorites` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) default '0',
  `oid` int(11) default '0',
  `tbl` varchar(250) default NULL,
  `faved` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xforum`
#

CREATE TABLE IF NOT EXISTS `#__xforum` (
  `id` int(11) NOT NULL auto_increment,
  `topic` varchar(255) default NULL,
  `comment` text,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` int(11) default '0',
  `state` tinyint(3) NOT NULL default '0',
  `sticky` tinyint(2) NOT NULL default '0',
  `parent` int(11) NOT NULL default '0',
  `hits` int(11) default '0',
  `group` int(11) default '0',
  `access` tinyint(2) default '4',
  `anonymous` tinyint(2) default '0',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` int(11) default '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `question` (`comment`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xgroups`
#

CREATE TABLE IF NOT EXISTS `#__xgroups` (
  `gidNumber` int(11) NOT NULL auto_increment,
  `cn` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  `published` tinyint(3) default '0',
  `type` tinyint(3) default '0',
  `access` tinyint(3) default '0',
  `public_desc` text,
  `private_desc` text,
  `restrict_msg` text,
  `join_policy` tinyint(3) default '0',
  `privacy` tinyint(3) default '0',
  `logo` varchar(255) default NULL,
  `overview_type` int(11) default NULL,
  `overview_content` text,
  `plugins` text,
  PRIMARY KEY  (`gidNumber`),
  FULLTEXT KEY `#__xgroups_cn_description_public_desc_ftidx` (`cn`,`description`,`public_desc`)
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xgroups_applicants`
#

CREATE TABLE IF NOT EXISTS `#__xgroups_applicants` (
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  PRIMARY KEY  (`gidNumber`,`uidNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xgroups_events`
#

CREATE TABLE IF NOT EXISTS `#__xgroups_events` (
  `id` int(11) NOT NULL auto_increment,
  `gidNumber` int(11) NOT NULL,
  `actorid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `active` bit(1) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xgroups_inviteemails`
#

CREATE TABLE IF NOT EXISTS `#__xgroups_inviteemails` (
  `id` int(11) NOT NULL auto_increment,
  `email` varchar(150) NOT NULL,
  `gidNumber` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xgroups_invitees`
#

CREATE TABLE IF NOT EXISTS `#__xgroups_invitees` (
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  PRIMARY KEY  (`gidNumber`,`uidNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xgroups_log`
#

CREATE TABLE IF NOT EXISTS `#__xgroups_log` (
  `id` int(11) NOT NULL auto_increment,
  `gid` int(11) NOT NULL default '0',
  `timestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `uid` int(11) default '0',
  `action` varchar(50) default NULL,
  `comments` text,
  `actorid` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xgroups_managers`
#

CREATE TABLE IF NOT EXISTS `#__xgroups_managers` (
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  PRIMARY KEY  (`gidNumber`,`uidNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xgroups_member_roles`
#

CREATE TABLE IF NOT EXISTS `#__xgroups_member_roles` (
  `role` int(11) default NULL,
  `uidNumber` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xgroups_members`
#

CREATE TABLE IF NOT EXISTS `#__xgroups_members` (
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  PRIMARY KEY  (`gidNumber`,`uidNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xgroups_modules`
#

CREATE TABLE IF NOT EXISTS `#__xgroups_modules` (
  `id` int(11) NOT NULL auto_increment,
  `gid` int(11) default NULL,
  `type` varchar(100) default NULL,
  `content` text,
  `morder` int(11) default NULL,
  `active` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xgroups_pages`
#

CREATE TABLE IF NOT EXISTS `#__xgroups_pages` (
  `id` int(11) NOT NULL auto_increment,
  `gid` varchar(100) default NULL,
  `url` varchar(100) default NULL,
  `title` varchar(100) default NULL,
  `content` text,
  `porder` int(11) default NULL,
  `active` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xgroups_reasons`
#

CREATE TABLE IF NOT EXISTS `#__xgroups_reasons` (
  `id` int(11) NOT NULL auto_increment,
  `uidNumber` int(11) NOT NULL,
  `gidNumber` int(11) NOT NULL,
  `reason` text,
  `date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xgroups_roles`
#

CREATE TABLE IF NOT EXISTS `#__xgroups_roles` (
  `id` int(11) NOT NULL auto_increment,
  `gidNumber` int(11) default NULL,
  `role` varchar(150) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xgroups_tracperm`
#

CREATE TABLE IF NOT EXISTS `#__xgroups_tracperm` (
  `group_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY  (`group_id`,`action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xmessage`
#

CREATE TABLE IF NOT EXISTS `#__xmessage` (
  `id` int(11) NOT NULL auto_increment,
  `created` datetime default '0000-00-00 00:00:00',
  `created_by` int(11) default '0',
  `message` mediumtext,
  `subject` varchar(250) default NULL,
  `component` varchar(100) default NULL,
  `type` varchar(100) default NULL,
  `group_id` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xmessage_action`
#

CREATE TABLE IF NOT EXISTS `#__xmessage_action` (
  `id` int(11) NOT NULL auto_increment,
  `class` varchar(20) NOT NULL default '',
  `element` int(11) unsigned NOT NULL default '0',
  `description` mediumtext,
  KEY `id` (`id`),
  KEY `class` (`class`),
  KEY `element` (`element`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xmessage_component`
#

CREATE TABLE IF NOT EXISTS `#__xmessage_component` (
  `id` int(11) NOT NULL auto_increment,
  `component` varchar(50) NOT NULL default '',
  `action` varchar(100) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Dumping data for table `#__xmessage_component`
#

INSERT INTO `#__xmessage_component` VALUES (1,'com_support','support_reply_submitted','Someone replies to a support ticket I submitted.');
INSERT INTO `#__xmessage_component` VALUES (2,'com_support','support_reply_assigned','Someone replies to a support ticket I am assigned to.');
INSERT INTO `#__xmessage_component` VALUES (3,'com_support','support_close_submitted','Someone closes a support ticket I submitted.');
INSERT INTO `#__xmessage_component` VALUES (4,'com_answers','answers_reply_submitted','Someone answers a question I submitted.');
INSERT INTO `#__xmessage_component` VALUES (5,'com_answers','answers_reply_comment','Someone replies to a comment I posted.');
INSERT INTO `#__xmessage_component` VALUES (6,'com_answers','answers_question_deleted','Someone deletes a question I replied to.');
INSERT INTO `#__xmessage_component` VALUES (7,'com_groups','groups_requests_membership','Someone requests membership to a group I manage.');
INSERT INTO `#__xmessage_component` VALUES (8,'com_groups','groups_requests_status','Someone is approved/denied membership to a group I manage.');
INSERT INTO `#__xmessage_component` VALUES (9,'com_groups','groups_cancels_membership','Someone cancels membership to a group I manage.');
INSERT INTO `#__xmessage_component` VALUES (10,'com_groups','groups_promoted_demoted','Someone promotes/demotes a member of a group I manage.');
INSERT INTO `#__xmessage_component` VALUES (11,'com_groups','groups_approved_denied','My membership request to a group is approved or denied.');
INSERT INTO `#__xmessage_component` VALUES (12,'com_groups','groups_status_changed','My membership status changes');
INSERT INTO `#__xmessage_component` VALUES (13,'com_groups','groups_cancelled_me','My membership to a group is cancelled.');
INSERT INTO `#__xmessage_component` VALUES (14,'com_groups','groups_changed','Someone changes the settings of a group I am a member of.');
INSERT INTO `#__xmessage_component` VALUES (15,'com_groups','groups_deleted','Someone deletes a group I am a member of.');
INSERT INTO `#__xmessage_component` VALUES (16,'com_resources','resources_submission_approved','A contribution I submitted is approved.');
INSERT INTO `#__xmessage_component` VALUES (17,'com_resources','resources_new_comment','Someone adds a review/comment to one of my contributions.');
INSERT INTO `#__xmessage_component` VALUES (21,'com_store','store_notifications','Shipping and other notifications about my purchases.');
INSERT INTO `#__xmessage_component` VALUES (22,'com_wishlist','wishlist_new_wish','Someone posted a wish on the Wish List I control.');
INSERT INTO `#__xmessage_component` VALUES (23,'com_wishlist','wishlist_status_changed','A wish I submitted got accepted/rejected/granted.');
INSERT INTO `#__xmessage_component` VALUES (24,'com_support','support_item_transferred','A support ticket/wish/question I submitted got transferred.');
INSERT INTO `#__xmessage_component` VALUES (25,'com_wishlist','wishlist_comment_posted','Someone commented on a wish I posted or am assigned to');
INSERT INTO `#__xmessage_component` VALUES (26,'com_groups','groups_invite','When you are invited to join a group.');
INSERT INTO `#__xmessage_component` VALUES (27,'com_contribtool','contribtool_status_changed','Tool development status has changed');
INSERT INTO `#__xmessage_component` VALUES (28,'com_contribtool','contribtool_new_message','New contribtool message is received');
INSERT INTO `#__xmessage_component` VALUES (29,'com_contribtool','contribtool_info_changed','Information about a tool I develop has changed');
INSERT INTO `#__xmessage_component` VALUES (30,'com_wishlist','wishlist_comment_thread','Someone replied to my comment or followed me in a discussion');
INSERT INTO `#__xmessage_component` VALUES (31,'com_wishlist','wishlist_new_owner','You were added as an administrator of a Wish List');
INSERT INTO `#__xmessage_component` VALUES (32,'com_wishlist','wishlist_wish_assigned','A wish has been assigned to me');
INSERT INTO `#__xmessage_component` VALUES (33,'com_groups','group_message','Messages from fellow group members');
INSERT INTO `#__xmessage_component` VALUES (34,'com_members','member_message','Messages from fellow site members');

#
# Table structure for table `#__xmessage_notify`
#

CREATE TABLE IF NOT EXISTS `#__xmessage_notify` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) default '0',
  `method` varchar(250) default NULL,
  `type` varchar(250) default NULL,
  `priority` int(2) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xmessage_recipient`
#

CREATE TABLE IF NOT EXISTS `#__xmessage_recipient` (
  `id` int(11) NOT NULL auto_increment,
  `mid` int(11) default '0',
  `uid` int(11) default '0',
  `created` datetime default '0000-00-00 00:00:00',
  `expires` datetime default '0000-00-00 00:00:00',
  `actionid` int(11) default '0',
  `state` tinyint(2) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xmessage_seen`
#

CREATE TABLE IF NOT EXISTS `#__xmessage_seen` (
  `mid` int(11) unsigned NOT NULL default '0',
  `uid` int(11) unsigned NOT NULL default '0',
  `whenseen` datetime default '0000-00-00 00:00:00',
  KEY `mid` (`mid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xorganizations`
#

CREATE TABLE IF NOT EXISTS `#__xorganizations` (
  `id` int(11) NOT NULL auto_increment,
  `organization` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xpoll_data`
#

CREATE TABLE IF NOT EXISTS `#__xpoll_data` (
  `id` int(11) NOT NULL auto_increment,
  `pollid` int(4) NOT NULL default '0',
  `text` text NOT NULL,
  `hits` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pollid` (`pollid`,`text`(1))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xpoll_date`
#

CREATE TABLE IF NOT EXISTS `#__xpoll_date` (
  `id` bigint(20) NOT NULL auto_increment,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `vote_id` int(11) NOT NULL default '0',
  `poll_id` int(11) NOT NULL default '0',
  `voter_ip` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  KEY `poll_id` (`poll_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xpoll_menu`
#

CREATE TABLE IF NOT EXISTS `#__xpoll_menu` (
  `pollid` int(11) NOT NULL default '0',
  `menuid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pollid`,`menuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xpolls`
#

CREATE TABLE IF NOT EXISTS `#__xpolls` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(150) NOT NULL,
  `voters` int(9) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL default '0',
  `access` int(11) NOT NULL default '0',
  `lag` int(11) NOT NULL default '0',
  `open` tinyint(1) NOT NULL default '0',
  `opened` date default NULL,
  `closed` date default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


#
# Table structure for table `#__xprofiles`
#

CREATE TABLE IF NOT EXISTS `#__xprofiles` (
  `uidNumber` int(11) NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `username` varchar(150) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `registerDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `gidNumber` varchar(11) NOT NULL default '',
  `homeDirectory` varchar(255) NOT NULL default '',
  `loginShell` varchar(255) NOT NULL default '',
  `ftpShell` varchar(255) NOT NULL default '',
  `userPassword` varchar(255) NOT NULL default '',
  `gid` varchar(255) NOT NULL default '',
  `orgtype` varchar(255) NOT NULL default '',
  `organization` varchar(255) NOT NULL default '',
  `countryresident` char(2) NOT NULL default '',
  `countryorigin` char(2) NOT NULL default '',
  `gender` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `reason` text NOT NULL,
  `mailPreferenceOption` int(11) NOT NULL default '0',
  `usageAgreement` int(11) NOT NULL default '0',
  `jobsAllowed` int(11) NOT NULL default '0',
  `modifiedDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `emailConfirmed` int(11) NOT NULL default '0',
  `regIP` varchar(255) NOT NULL default '',
  `regHost` varchar(255) NOT NULL default '',
  `nativeTribe` varchar(255) NOT NULL default '',
  `phone` varchar(255) NOT NULL default '',
  `proxyPassword` varchar(255) NOT NULL default '',
  `proxyUidNumber` varchar(255) NOT NULL default '',
  `givenName` varchar(255) NOT NULL default '',
  `middleName` varchar(255) NOT NULL default '',
  `surname` varchar(255) NOT NULL default '',
  `picture` varchar(255) NOT NULL default '',
  `vip` int(11) NOT NULL default '0',
  `public` tinyint(2) NOT NULL default '0',
  `params` text NOT NULL,
  `note` text NOT NULL,
  `shadowExpire` int(11) default NULL,
  PRIMARY KEY  (`uidNumber`),
  KEY `username` (`username`),
  FULLTEXT KEY `author` (`givenName`,`surname`),
  FULLTEXT KEY `#__xprofiles_name_ftidx` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xprofiles_admin`
#

CREATE TABLE IF NOT EXISTS `#__xprofiles_admin` (
  `uidNumber` int(11) NOT NULL,
  `admin` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`uidNumber`,`admin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xprofiles_bio`
#

CREATE TABLE IF NOT EXISTS `#__xprofiles_bio` (
  `uidNumber` int(11) NOT NULL,
  `bio` text,
  PRIMARY KEY  (`uidNumber`),
  FULLTEXT KEY `#__xprofiles_bio_bio_ftidx` (`bio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xprofiles_disability`
#

CREATE TABLE IF NOT EXISTS `#__xprofiles_disability` (
  `uidNumber` int(11) NOT NULL,
  `disability` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`uidNumber`,`disability`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xprofiles_edulevel`
#

CREATE TABLE IF NOT EXISTS `#__xprofiles_edulevel` (
  `uidNumber` int(11) NOT NULL,
  `edulevel` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`uidNumber`,`edulevel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xprofiles_hispanic`
#

CREATE TABLE IF NOT EXISTS `#__xprofiles_hispanic` (
  `uidNumber` int(11) NOT NULL,
  `hispanic` varchar(255) NOT NULL,
  PRIMARY KEY  (`uidNumber`,`hispanic`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xprofiles_host`
#

CREATE TABLE IF NOT EXISTS `#__xprofiles_host` (
  `uidNumber` int(11) NOT NULL,
  `host` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`uidNumber`,`host`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xprofiles_manager`
#

CREATE TABLE IF NOT EXISTS `#__xprofiles_manager` (
  `uidNumber` int(11) NOT NULL,
  `manager` varchar(255) NOT NULL,
  PRIMARY KEY  (`uidNumber`,`manager`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xprofiles_race`
#

CREATE TABLE IF NOT EXISTS `#__xprofiles_race` (
  `uidNumber` int(11) NOT NULL,
  `race` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`uidNumber`,`race`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xprofiles_role`
#

CREATE TABLE IF NOT EXISTS `#__xprofiles_role` (
  `uidNumber` int(11) NOT NULL,
  `role` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`uidNumber`,`role`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xprofiles_tags`
#

CREATE TABLE IF NOT EXISTS `#__xprofiles_tags` (
  `id` int(11) NOT NULL auto_increment,
  `uidNumber` int(11) default NULL,
  `tagid` int(11) default NULL,
  `taggerid` int(11) default '0',
  `taggedon` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__xsession`
#

CREATE TABLE IF NOT EXISTS `#__xsession` (
  `session_id` varchar(200) NOT NULL default '0',
  `ip` varchar(15) default NULL,
  `host` varchar(128) default NULL,
  `domain` varchar(128) default NULL,
  `signed` tinyint(3) default '0',
  `countrySHORT` char(2) default NULL,
  `countryLONG` varchar(64) default NULL,
  `ipREGION` varchar(128) default NULL,
  `ipCITY` varchar(128) default NULL,
  `ipLATITUDE` double default NULL,
  `ipLONGITUDE` double default NULL,
  `bot` tinyint(4) default '0',
  PRIMARY KEY  (`session_id`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `#__ysearch_plugin_weights`
#

CREATE TABLE IF NOT EXISTS `#__ysearch_plugin_weights` (
  `plugin` varchar(20) NOT NULL,
  `weight` float NOT NULL,
  PRIMARY KEY  (`plugin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Dumping data for table `#__ysearch_plugin_weights`
#

INSERT INTO `#__ysearch_plugin_weights` VALUES ('content',0.8);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('events',0.8);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('groups',0.8);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('kb',0.8);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('members',0.8);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('resources',0.8);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('topics',1);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('weighttitle',1);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('sortrelevance',1);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('sortnewer',0.2);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('tagmod',1.3);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('documentation',1);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('weightcontributor',0.2);

#
# Table structure for table `#__ysearch_site_map`
#

CREATE TABLE IF NOT EXISTS `#__ysearch_site_map` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `link` varchar(200) NOT NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `ysearch_site_map_title_description_ftidx` (`title`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `orgtypes`
#

CREATE TABLE `orgtypes` (
  `name` varchar(64) NOT NULL,
  `orgtype` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`orgtype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `session`
#

CREATE TABLE `session` (
  `sessnum` bigint(20) unsigned NOT NULL auto_increment,
  `username` varchar(32) NOT NULL default '',
  `remoteip` varchar(40) NOT NULL default '',
  `exechost` varchar(40) NOT NULL default '',
  `dispnum` int(10) unsigned default '0',
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  `accesstime` datetime NOT NULL default '0000-00-00 00:00:00',
  `timeout` int(11) default '86400',
  `appname` varchar(80) NOT NULL default '',
  `sessname` varchar(80) NOT NULL default '',
  `sesstoken` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`sessnum`),
  UNIQUE KEY `sessnum` (`sessnum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `sessionlog`
#

CREATE TABLE `sessionlog` (
  `sessnum` bigint(20) unsigned NOT NULL,
  `username` varchar(32) NOT NULL default '',
  `remoteip` varchar(40) NOT NULL default '',
  `remotehost` varchar(40) NOT NULL default '',
  `exechost` varchar(40) NOT NULL default '',
  `dispnum` int(10) unsigned default '0',
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  `appname` varchar(80) NOT NULL default '',
  `walltime` float unsigned default '0',
  `viewtime` float unsigned default '0',
  `cputime` float unsigned default '0',
  `status` smallint(5) unsigned default '0',
  PRIMARY KEY  (`sessnum`),
  UNIQUE KEY `sessnum` (`sessnum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `sessionpriv`
#

CREATE TABLE `sessionpriv` (
  `privid` bigint(20) unsigned NOT NULL auto_increment,
  `sessnum` bigint(20) unsigned NOT NULL default '0',
  `privilege` varchar(40) NOT NULL default '',
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `privid` (`privid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `summary_andmore`
#

CREATE TABLE `summary_andmore` (
  `id` tinyint(4) NOT NULL default '0',
  `label` varchar(255) NOT NULL default 'no_name',
  `plot` int(1) default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `summary_andmore_vals`
#

CREATE TABLE `summary_andmore_vals` (
  `colid` tinyint(4) NOT NULL default '0',
  `datetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL default '1',
  `rowid` tinyint(4) NOT NULL default '0',
  `valfmt` tinyint(4) NOT NULL default '0',
  `value` bigint(20) default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `summary_misc`
#

CREATE TABLE `summary_misc` (
  `id` tinyint(4) NOT NULL default '0',
  `label` varchar(255) NOT NULL default 'no_name',
  `plot` int(1) default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `summary_misc_vals`
#

CREATE TABLE `summary_misc_vals` (
  `colid` tinyint(4) NOT NULL default '0',
  `datetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL default '1',
  `rowid` tinyint(4) NOT NULL default '0',
  `valfmt` tinyint(4) NOT NULL default '0',
  `value` varchar(200) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `summary_simusage`
#

CREATE TABLE `summary_simusage` (
  `id` tinyint(4) NOT NULL default '0',
  `label` varchar(255) NOT NULL default 'no_name',
  `plot` int(1) default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `summary_simusage_vals`
#

CREATE TABLE `summary_simusage_vals` (
  `colid` tinyint(4) NOT NULL default '0',
  `datetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL default '1',
  `rowid` tinyint(4) NOT NULL default '0',
  `valfmt` tinyint(4) NOT NULL default '0',
  `value` bigint(20) default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `summary_user`
#

CREATE TABLE `summary_user` (
  `id` tinyint(4) NOT NULL default '0',
  `label` varchar(255) NOT NULL default 'no_name',
  `plot` int(1) default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `user_map`
#

CREATE TABLE `user_map` (
  `countryLONG` varchar(64) NOT NULL,
  `countrySHORT` char(2) NOT NULL,
  `datetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `ip` int(10) unsigned zerofill NOT NULL default '0000000000',
  `ipCITY` varchar(128) NOT NULL,
  `ipLAT` double default NULL,
  `ipLONG` double default NULL,
  `ipREGION` varchar(128) NOT NULL,
  `type` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`ip`),
  KEY `ip` USING BTREE (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `view`
#

CREATE TABLE `view` (
  `viewid` bigint(20) unsigned NOT NULL auto_increment,
  `sessnum` bigint(20) unsigned NOT NULL default '0',
  `username` varchar(32) NOT NULL default '',
  `remoteip` varchar(40) NOT NULL default '',
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  `heartbeat` datetime NOT NULL default '0000-00-00 00:00:00',
  `referrer` char(255) default NULL,
  UNIQUE KEY `viewid` (`viewid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `viewlog`
#

CREATE TABLE `viewlog` (
  `sessnum` bigint(20) unsigned NOT NULL default '0',
  `username` varchar(32) NOT NULL default '',
  `remoteip` varchar(40) NOT NULL default '',
  `remotehost` varchar(40) NOT NULL default '',
  `time` datetime NOT NULL default '0000-00-00 00:00:00',
  `duration` float unsigned default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Table structure for table `viewperm`
#

CREATE TABLE `viewperm` (
  `sessnum` bigint(20) unsigned NOT NULL default '0',
  `viewuser` varchar(32) NOT NULL default '',
  `viewtoken` varchar(32) NOT NULL default '',
  `geometry` varchar(9) NOT NULL default '0',
  `fwhost` varchar(40) NOT NULL default '',
  `fwport` smallint(5) unsigned NOT NULL default '0',
  `vncpass` varchar(16) NOT NULL default '',
  `readonly` varchar(4) NOT NULL default 'Yes',
  PRIMARY KEY  (`sessnum`,`viewuser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

